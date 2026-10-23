@extends('layouts.site')

@section('content')
{{-- ============ HERO ============ --}}
<section class="hero">
    <span class="halftone a"></span><span class="halftone b"></span>
    <div class="container hero-grid">
        <div>
            <span class="eyebrow"><i><b></b><b></b><b></b><b></b></i> Print shop for schools &amp; business</span>
            <h1>Print it <span class="hl">sharp.</span><br>Get it <span class="hl m">fast.</span></h1>
            <p class="lead">Copies, test sheets, ties, badges, printed scotch tape and PVC cards — all in one place. Create an account, send us your requirement, and receive a clear quotation, usually the same day.</p>
            <div class="hero-actions">
                <a href="{{ auth()->check() ? route('customer.quotes.create') : route('register') }}" class="btn btn-accent">Request a quotation @include('partials.icon', ['name' => 'arrow'])</a>
                <a href="{{ route('services') }}" class="btn">See what we print</a>
            </div>
            <div class="hero-trust">
                <span>@include('partials.icon', ['name' => 'check']) Free quotations</span>
                <span>@include('partials.icon', ['name' => 'check']) School &amp; bulk pricing</span>
                <span>@include('partials.icon', ['name' => 'check']) Delivery available</span>
            </div>
        </div>

        <div class="hero-art" aria-hidden="true">
            <div class="float-card f1">@include('partials.product-art', ['icon' => 'card'])<b>PVC ID Cards</b></div>
            <div class="float-card f2">@include('partials.product-art', ['icon' => 'badge'])<b>Badges</b></div>
            <div class="float-card f3">@include('partials.product-art', ['icon' => 'tie'])<b>School Ties</b></div>
            <div class="sticker">Same-day<br>quotes</div>
        </div>
    </div>
</section>

{{-- ============ MARQUEE ============ --}}
<div class="marquee" aria-hidden="true">
    <div class="marquee-track">
        @foreach (['Photocopies', 'Test sheets', 'School ties', 'Badges', 'Scotch tape', 'PVC cards', 'Bulk orders', 'Fast delivery'] as $word)
            <span>{{ $word }}</span>
        @endforeach
    </div>
</div>

{{-- ============ PRODUCTS ============ --}}
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <span class="kicker">What we print</span>
            <h2>Everything your school or business needs, <span class="hl c">under one roof.</span></h2>
            <p class="muted">Pick a product to see its options and minimum quantities — then ask for an exact quotation for your order.</p>
        </div>

        <div class="grid grid-3">
            @foreach ($products as $i => $p)
                <a href="{{ route('product', $p) }}" class="pcard reveal">
                    <div class="pcard-art tone-{{ $i % 6 }}">@include('partials.product-art', ['icon' => $p->icon])</div>
                    <div class="pcard-body">
                        <h3>{{ $p->name }}</h3>
                        <p>{{ $p->tagline }}</p>
                        <div class="pcard-foot">
                            <span>
                                <span class="price" style="font-size:1rem">Get a quotation</span>
                            </span>
                            <span class="arrow">@include('partials.icon', ['name' => 'arrow'])</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ HOW IT WORKS ============ --}}
<section class="section alt">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker">How it works</span>
            <h2>From idea to delivery in <span class="hl">four easy steps</span></h2>
        </div>
        <div class="steps">
            <div class="step reveal">
                @include('partials.icon', ['name' => 'user'])
                <h3>Create your account</h3>
                <p>Register as a school, business or individual. Your details are saved for every future order.</p>
            </div>
            <div class="step reveal">
                @include('partials.icon', ['name' => 'upload'])
                <h3>Upload your requirement</h3>
                <p>Choose products, quantities and options, and attach your designs, student lists or photos.</p>
            </div>
            <div class="step reveal">
                @include('partials.icon', ['name' => 'tag'])
                <h3>Receive a quotation</h3>
                <p>We review and send an itemised price. Chat with us to adjust anything, then accept with one click.</p>
            </div>
            <div class="step reveal">
                @include('partials.icon', ['name' => 'truck'])
                <h3>We print &amp; deliver</h3>
                <p>Track progress from production to ready-for-delivery, right from your dashboard.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ FOR SCHOOLS ============ --}}
<section class="section dark">
    <span class="halftone" style="--c: var(--magenta); right: -140px; top: -80px; opacity: .35"></span>
    <div class="container split">
        <div class="reveal">
            <span class="kicker" style="color: var(--yellow)">Built for schools</span>
            <h2>One account for every order your school will ever place.</h2>
            <p style="color:#b9bcdc">Exam sheets every term, new-session ties and ID cards every year. Keep all your requests, quotations and files in one tidy place, and reorder in a single click.</p>
            <ul class="tick-list">
                <li>Your own private dashboard with full order history</li>
                <li>Itemised quotations you can print or share with the principal</li>
                <li>Upload student lists, photos and exam files securely</li>
                <li>Repeat last term's order in one click</li>
            </ul>
            <a href="{{ route('register') }}" class="btn btn-yellow mt-3">Create a school account @include('partials.icon', ['name' => 'arrow'])</a>
        </div>

        <div class="reveal">
            <div class="mock" aria-hidden="true">
                <div class="mock-head"><b style="font-family:var(--font-head);font-size:1.1rem">Sample quotation</b><span class="badge info">Quotation ready</span></div>
                <div class="mock-row"><span>PVC ID Cards × 450</span><b>Itemised</b></div>
                <div class="mock-row"><span>School Ties × 450</span><b>Itemised</b></div>
                <div class="mock-row"><span>Prefect Badges × 40</span><b>Itemised</b></div>
                <div class="mock-row"><span>Delivery</span><b>Itemised</b></div>
                <div class="mock-total"><span>Total</span><span>Your total</span></div>
                <div class="flex gap-1 mt-2"><span class="btn btn-ok btn-sm">Accept quotation</span><span class="btn btn-sm">Ask a question</span></div>
            </div>
        </div>
    </div>
</section>

{{-- ============ WHY US ============ --}}
<section class="section">
    <div class="container">
        <div class="section-head center reveal">
            <span class="kicker">Why choose us</span>
            <h2>Printing you can <span class="hl m">count on</span></h2>
        </div>
        <div class="grid grid-3">
            @foreach ([
                ['bolt', 'Fast turnaround', 'Quotations the same day and urgent jobs printed on tight deadlines.'],
                ['palette', 'True-to-colour', 'Careful colour matching so your logo and school colours look right every time.'],
                ['shield', 'Confidential handling', 'Exam papers and student data are handled securely and counted per class.'],
                ['tag', 'Honest pricing', 'Itemised quotes with no hidden charges and special rates for bulk orders.'],
                ['truck', 'Delivery to your door', 'We deliver to schools and offices, packed and labelled by section.'],
                ['chat', 'Real people to talk to', 'Message us on your order page or WhatsApp — we reply quickly.'],
            ] as [$ico, $title, $text])
                <div class="panel-card reveal" style="box-shadow:none">
                    <div style="width:46px;height:46px;border-radius:14px;background:var(--yellow);border:2px solid var(--ink);display:grid;place-items:center;margin-bottom:1rem">
                        <span style="width:24px;display:block">@include('partials.icon', ['name' => $ico])</span>
                    </div>
                    <h3>{{ $title }}</h3>
                    <p class="muted mb-0">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ STATS ============ --}}
<section class="section dark" style="padding:4rem 0">
    <div class="container stats-band">
        <div><b data-count="{{ $products->count() }}">{{ $products->count() }}</b><span>Product lines, one shop</span></div>
        <div><b>Free</b><span>Quotations, no obligation</span></div>
        <div><b data-count="30">30</b><span>Items per request, with file uploads</span></div>
        <div><b>1</b><span>Account for every order, forever</span></div>
    </div>
</section>

{{-- ============ FAQ ============ --}}
<section class="section">
    <div class="container" style="max-width:820px">
        <div class="section-head center reveal">
            <span class="kicker">Questions</span>
            <h2>Good to know</h2>
        </div>
        <div class="faq reveal">
            <details><summary>Is the quotation free?</summary><p>Yes. Requesting and receiving a quotation is always free and there is no obligation to accept.</p></details>
            <details><summary>What files can I upload?</summary><p>PDF, Word, Excel, images, ZIP and common design files (AI, EPS, PSD, CDR) up to 20&nbsp;MB each. For large student photo sets, upload a ZIP.</p></details>
            <details><summary>How long does a quotation take?</summary><p>Usually within a working day. Complex or very large orders may take a little longer — you will get an email when it is ready.</p></details>
            <details><summary>Can I change my order after requesting?</summary><p>Of course. Use the message box on your order page to ask for changes and we will send a revised quotation.</p></details>
            <details><summary>Do you deliver?</summary><p>Yes, we can deliver to your school or office. Delivery cost is shown separately on your quotation.</p></details>
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="section" style="padding-top:0">
    <div class="container">
        <div class="cta reveal">
            <span class="halftone"></span><span class="halftone l"></span>
            <h2>Ready to print something great?</h2>
            <p>Create your free account and send your first requirement in under five minutes.</p>
            <div class="flex gap-2 wrap items-center" style="justify-content:center;position:relative">
                <a href="{{ auth()->check() ? route('customer.quotes.create') : route('register') }}" class="btn btn-yellow">Request a quotation</a>
                <a href="https://wa.me/{{ config('business.whatsapp') }}" target="_blank" rel="noopener" class="btn" style="background:#fff">Chat on WhatsApp</a>
            </div>
        </div>
    </div>
</section>
@endsection
