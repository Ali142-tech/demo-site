<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\QuoteRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuoteFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('local');
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk()->assertSee('PVC ID Cards');
        $this->get('/services')->assertOk();
        $this->get('/services/pvc-cards')->assertOk();
        $this->get('/how-it-works')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_guests_are_sent_to_login_and_customers_cannot_open_admin(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->get('/admin')->assertForbidden();
    }

    public function test_school_registers_requests_gets_quote_and_accepts(): void
    {
        $this->post('/register', [
            'account_type' => 'school', 'organization' => 'Green Valley School', 'name' => 'Ms Hina',
            'email' => 'hina@school.test', 'phone' => '0300111222', 'password' => 'secret-pass-1', 'password_confirmation' => 'secret-pass-1',
        ])->assertRedirect(route('customer.quotes.create'));

        $school = User::where('email', 'hina@school.test')->firstOrFail();
        $this->assertSame('customer', $school->role);
        $card = Product::where('slug', 'pvc-cards')->firstOrFail();

        $this->post('/dashboard/quotes', [
            'title' => 'ID cards 2027',
            'items' => [['product_id' => $card->id, 'quantity' => 100, 'specs' => 'Sides: Double sided']],
            'files' => [UploadedFile::fake()->create('students.xlsx', 100)],
        ])->assertRedirect();

        $quote = QuoteRequest::firstOrFail();
        $this->assertSame('pending', $quote->status);
        $this->assertCount(1, $quote->files);

        // Admin prepares the quotation.
        $admin = User::where('role', 'admin')->firstOrFail();
        $item = $quote->items->first();
        $this->actingAs($admin)->put("/admin/quotes/{$quote->id}/quotation", [
            'prices' => [$item->id => 100], 'delivery_fee' => 500, 'discount' => 200,
        ])->assertSessionHasNoErrors();

        $quote->refresh();
        $this->assertSame('quoted', $quote->status);
        $this->assertEquals(10300, $quote->quoted_total); // 100*100 + 500 - 200
        $this->actingAs($admin)->get("/admin/quotes/{$quote->id}")->assertOk()->assertSee('Quotation sent')->assertSee('Revise quotation');

        // Another school cannot see it.
        $other = User::factory()->create(['role' => 'customer']);
        $this->actingAs($other)->get("/dashboard/quotes/{$quote->id}")->assertNotFound();

        // The owner can, and accepts.
        $this->actingAs($school)->get("/dashboard/quotes/{$quote->id}")->assertOk()->assertSee('Accept quotation');
        $this->actingAs($school)->post("/dashboard/quotes/{$quote->id}/respond", ['decision' => 'accept']);
        $this->assertSame('accepted', $quote->fresh()->status);

        // Admin moves the job along; the customer can download their file.
        $this->actingAs($admin)->put("/admin/quotes/{$quote->id}/status", ['status' => 'in_production']);
        $this->assertSame('in_production', $quote->fresh()->status);
        $this->actingAs($school)->get("/dashboard/quotes/{$quote->id}/files/{$quote->files->first()->id}")->assertOk();
        $this->actingAs($admin)->get("/admin/quotes/{$quote->id}/files/{$quote->files->first()->id}?view=1")->assertOk();
        $this->actingAs($other)->get("/dashboard/quotes/{$quote->id}/files/{$quote->files->first()->id}")->assertNotFound();
    }

    public function test_new_request_page_renders_for_customer(): void
    {
        $u = User::factory()->create(['role' => 'customer']);
        $this->actingAs($u)->get('/dashboard/quotes/new?product=ties')->assertOk()->assertSee('products-data', false);
        $this->actingAs($u)->get('/dashboard')->assertOk();
        $this->actingAs($u)->get('/dashboard/quotes')->assertOk();
        $this->actingAs($u)->get('/dashboard/profile')->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $c = User::factory()->create(['role' => 'customer']);
        $q = $c->quoteRequests()->create(['title' => 'T']);
        $q->items()->create(['name' => 'Ties', 'quantity' => 10]);
        foreach (['/admin', '/admin/quotes', "/admin/quotes/{$q->id}", '/admin/customers', "/admin/customers/{$c->id}", '/admin/products', '/admin/products/create', '/admin/products/ties/edit', '/admin/messages'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_customer_can_upload_many_images_on_create_and_later(): void
    {
        $u = User::factory()->create(['role' => 'customer']);
        $imgs = fn ($n) => collect(range(1, $n))->map(fn ($i) => UploadedFile::fake()->image("photo{$i}.jpg"))->all();

        $this->actingAs($u)->post('/dashboard/quotes', [
            'title' => 'Badges', 'items' => [['name' => 'Custom', 'quantity' => 5]], 'files' => $imgs(4),
        ])->assertSessionHasNoErrors();
        $quote = QuoteRequest::firstOrFail();
        $this->assertCount(4, $quote->files);

        $this->actingAs($u)->post("/dashboard/quotes/{$quote->id}/files", ['files' => $imgs(3)])->assertSessionHasNoErrors();
        $this->assertCount(7, $quote->fresh()->files);
    }

    public function test_reorder_and_messages(): void
    {
        $school = User::factory()->create(['role' => 'customer']);
        $quote = $school->quoteRequests()->create(['title' => 'Exam sheets']);
        $quote->items()->create(['name' => 'Test sheets', 'quantity' => 500]);

        $this->actingAs($school)->post("/dashboard/quotes/{$quote->id}/messages", ['body' => 'Can you do A3?'])->assertRedirect();
        $this->assertSame(1, $quote->messages()->count());

        $this->actingAs($school)->post("/dashboard/quotes/{$quote->id}/reorder")->assertRedirect();
        $this->assertSame(2, QuoteRequest::count());
        $this->assertSame(500, QuoteRequest::latest('id')->first()->items->first()->quantity);
    }

    public function test_contact_form_stores_message_and_honeypot_blocks_bots(): void
    {
        $this->post('/contact', ['name' => 'Ali', 'phone' => '0300', 'message' => 'Hello'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('contact_messages', 1);

        $this->post('/contact', ['name' => 'Bot', 'email' => 'b@b.test', 'message' => 'spam', 'website' => 'http://spam'])->assertRedirect();
        $this->assertDatabaseCount('contact_messages', 1);
    }
}
