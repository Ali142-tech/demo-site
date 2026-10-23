<?php

// Everything here can be changed from the .env file – no code edits needed.
return [
    'name' => env('BUSINESS_NAME', 'Fine Printing Supplies'),
    'tagline' => env('BUSINESS_TAGLINE', 'Printing that makes an impression'),
    'phone' => env('BUSINESS_PHONE', '+92 300 0000000'),
    'phone_2' => env('BUSINESS_PHONE_2'),
    'whatsapp' => env('BUSINESS_WHATSAPP', '923000000000'), // digits only, with country code
    'email' => env('BUSINESS_EMAIL', 'hello@printtime.example'),
    'address' => env('BUSINESS_ADDRESS', 'Your shop address, City'),
    'hours' => env('BUSINESS_HOURS', 'Mon – Sat, 9:00 am – 7:00 pm'),

    'currency' => env('BUSINESS_CURRENCY', 'Rs'),

    // How many days a quotation stays valid by default
    'quote_validity_days' => 14,
];
