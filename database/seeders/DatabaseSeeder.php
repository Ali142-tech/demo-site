<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        $products = [
            [
                'name' => 'Copies & Photocopying',
                'slug' => 'copies',
                'icon' => 'copies',
                'tagline' => 'Sharp, fast bulk copies for classrooms and offices.',
                'description' => 'High-volume black & white and full-colour photocopying on A4, A3 and Legal paper. Perfect for notes, handouts, reports and forms — delivered collated, stapled or bound.',
                'features' => ['B/W & full colour', 'A4 · A3 · Legal', 'Collated, stapled or spiral bound', 'Same-day for urgent jobs'],
                'options' => ['Paper size' => ['A4', 'A3', 'Legal'], 'Colour' => ['Black & white', 'Full colour'], 'Sides' => ['Single sided', 'Double sided']],
                'starting_price' => 3, 'price_unit' => 'page', 'min_quantity' => 50,
            ],
            [
                'name' => 'Test & Exam Sheets',
                'slug' => 'test-sheets',
                'icon' => 'sheet',
                'tagline' => 'Exam papers, worksheets and answer sheets, printed securely.',
                'description' => 'Confidential printing of test papers, worksheets, OMR/answer sheets and question booklets. We print to your exact count per class, sealed and labelled per section.',
                'features' => ['Confidential handling', 'Counted & sealed per class', 'OMR / answer sheets', 'Numbered booklets'],
                'options' => ['Paper size' => ['A4', 'A3', 'Legal'], 'Colour' => ['Black & white', 'Full colour'], 'Finishing' => ['Loose sheets', 'Stapled booklet', 'Sealed per class']],
                'starting_price' => 4, 'price_unit' => 'sheet', 'min_quantity' => 100,
            ],
            [
                'name' => 'School & Corporate Ties',
                'slug' => 'ties',
                'icon' => 'tie',
                'tagline' => 'Custom printed and woven ties in your school colours.',
                'description' => 'Ready-to-wear ties in your colours with printed or woven logo and stripes. Available in student (pre-tied/elastic) and adult sizes, supplied in bulk for the whole school.',
                'features' => ['Your colours & stripes', 'Printed or woven logo', 'Pre-tied / elastic / adult', 'Bulk school orders'],
                'options' => ['Size' => ['Junior', 'Senior', 'Adult'], 'Style' => ['Standard', 'Pre-tied', 'Elastic'], 'Logo' => ['Printed', 'Woven']],
                'starting_price' => 250, 'price_unit' => 'piece', 'min_quantity' => 25,
            ],
            [
                'name' => 'Badges & Pins',
                'slug' => 'badges',
                'icon' => 'badge',
                'tagline' => 'Prefect, house and event badges with a premium finish.',
                'description' => 'Metal, enamel and printed badges with safety-pin, magnetic or clutch backing. Great for prefects, houses, staff, conferences and events.',
                'features' => ['Metal · enamel · printed', 'Pin / magnet / clutch', 'Round, shield or custom shape', 'Domed epoxy finish'],
                'options' => ['Type' => ['Metal', 'Enamel', 'Printed'], 'Shape' => ['Round', 'Square', 'Shield', 'Custom'], 'Backing' => ['Safety pin', 'Magnetic', 'Clutch']],
                'starting_price' => 60, 'price_unit' => 'piece', 'min_quantity' => 25,
            ],
            [
                'name' => 'Printed Scotch Tape',
                'slug' => 'scotch-tape',
                'icon' => 'tape',
                'tagline' => 'Branded packaging tape that advertises on every parcel.',
                'description' => 'Custom printed scotch / BOPP packaging tape with your logo, name or message. Ideal for shops, schools’ events and online sellers who want every parcel to look professional.',
                'features' => ['1-3 colour print', 'Clear, brown or coloured tape', '2 inch standard width', 'Strong industrial adhesive'],
                'options' => ['Tape colour' => ['Clear', 'Brown', 'White', 'Coloured'], 'Width' => ['1 inch', '2 inch', '3 inch'], 'Print colours' => ['1 colour', '2 colours', '3 colours']],
                'starting_price' => 180, 'price_unit' => 'roll', 'min_quantity' => 36,
            ],
            [
                'name' => 'PVC ID Cards',
                'slug' => 'pvc-cards',
                'icon' => 'card',
                'tagline' => 'Durable full-colour PVC ID & membership cards.',
                'description' => 'Credit-card size PVC cards with photo, name, class/ID number and QR or barcode. Single or double sided, with optional lanyards and holders. Send us a spreadsheet and photos — we handle the rest.',
                'features' => ['Photo ID & staff cards', 'QR / barcode / numbering', 'Single or double sided', 'Lanyards & holders available'],
                'options' => ['Sides' => ['Single sided', 'Double sided'], 'Extras' => ['None', 'Lanyard', 'Lanyard + holder'], 'Data' => ['Spreadsheet + photos', 'Design only']],
                'starting_price' => 90, 'price_unit' => 'card', 'min_quantity' => 20,
            ],
        ];

        foreach ($products as $i => $p) {
            Product::firstOrCreate(['slug' => $p['slug']], $p + ['sort_order' => $i, 'is_active' => true]);
        }
    }
}
