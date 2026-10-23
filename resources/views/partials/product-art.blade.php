@php
    $photo = collect(['jpg', 'jpeg', 'webp', 'png'])->map(fn ($e) => 'images/products/'.($icon ?? 'sheet').'.'.$e)->first(fn ($p) => file_exists(public_path($p)));
@endphp
@if ($photo)
<img class="art-photo" src="{{ asset($photo) }}" alt="" loading="lazy">
@else
@switch($icon ?? 'sheet')
@case('copies')
<svg viewBox="0 0 200 160" fill="none" stroke="#12142b" stroke-width="3" stroke-linejoin="round" aria-hidden="true">
    <rect x="52" y="22" width="92" height="118" rx="6" fill="#fff" transform="rotate(8 98 81)"/>
    <rect x="46" y="18" width="92" height="118" rx="6" fill="#fff" transform="rotate(-2 92 77)"/>
    <rect x="38" y="14" width="92" height="118" rx="6" fill="#fff"/>
    <path d="M52 38h60M52 52h60M52 66h44" stroke-linecap="round"/>
    <rect x="52" y="82" width="34" height="34" rx="4" fill="#00aeef"/>
    <rect x="92" y="82" width="22" height="34" rx="4" fill="#ec008c"/>
    <circle cx="150" cy="112" r="26" fill="#ffd400"/><path d="M150 98v28M136 112h28" stroke-linecap="round"/>
</svg>
@break
@case('sheet')
<svg viewBox="0 0 200 160" fill="none" stroke="#12142b" stroke-width="3" stroke-linejoin="round" aria-hidden="true">
    <rect x="40" y="10" width="104" height="138" rx="6" fill="#fff" transform="rotate(-5 92 79)"/>
    <rect x="48" y="14" width="104" height="138" rx="6" fill="#fff"/>
    <path d="M64 36h46" stroke-linecap="round" stroke-width="5"/>
    <path d="M64 54h72M64 68h72M64 82h50" stroke-linecap="round"/>
    <circle cx="70" cy="106" r="7" fill="#ffd400"/><circle cx="92" cy="106" r="7" fill="#fff"/><circle cx="114" cy="106" r="7" fill="#fff"/><circle cx="136" cy="106" r="7" fill="#fff"/>
    <circle cx="70" cy="128" r="7" fill="#fff"/><circle cx="92" cy="128" r="7" fill="#ec008c"/><circle cx="114" cy="128" r="7" fill="#fff"/><circle cx="136" cy="128" r="7" fill="#fff"/>
    <path d="m150 28 18-18 14 14-18 18-20 6z" fill="#00aeef"/><path d="m162 16 14 14"/>
</svg>
@break
@case('tie')
<svg viewBox="0 0 200 160" fill="none" stroke="#12142b" stroke-width="3" stroke-linejoin="round" aria-hidden="true">
    <path d="M80 8h40l8 22H72z" fill="#ec008c"/>
    <path d="M72 30h56l-4 6 20 100-44 20-44-20 20-100z" fill="#00aeef"/>
    <path d="M62 62 134 40M56 90l88-24M48 118l104-26" stroke="#fff" stroke-width="9" opacity=".95"/>
    <path d="M80 8h40l8 22H72z" fill="#ec008c"/>
    <circle cx="100" cy="112" r="14" fill="#ffd400"/><path d="m100 104 3 6 6 1-4.5 4 1.2 6-5.7-3-5.7 3 1.2-6-4.5-4 6-1z" fill="#12142b" stroke="none"/>
</svg>
@break
@case('badge')
<svg viewBox="0 0 200 160" fill="none" stroke="#12142b" stroke-width="3" stroke-linejoin="round" aria-hidden="true">
    <path d="M70 6h28l14 46-28 6z" fill="#ec008c"/><path d="M130 6h-28l-14 46 28 6z" fill="#00aeef"/>
    <circle cx="100" cy="98" r="52" fill="#ffd400"/>
    <circle cx="100" cy="98" r="40" fill="#fff"/>
    <path d="m100 66 9.5 19.5 21 3-15.2 15 3.6 21.2L100 115l-18.9 9.7 3.6-21.2-15.2-15 21-3z" fill="#ec008c"/>
</svg>
@break
@case('tape')
<svg viewBox="0 0 200 160" fill="none" stroke="#12142b" stroke-width="3" stroke-linejoin="round" aria-hidden="true">
    <ellipse cx="100" cy="120" rx="76" ry="22" fill="#ffd400"/>
    <path d="M24 70v50a76 22 0 0 0 152 0V70z" fill="#ffd400"/>
    <ellipse cx="100" cy="70" rx="76" ry="22" fill="#ffe66b"/>
    <ellipse cx="100" cy="70" rx="40" ry="11" fill="#fbf8f1"/>
    <path d="M44 92c14 4 28 5 40 5M116 97c14 0 30-2 42-6" stroke="#ec008c" stroke-width="6" stroke-linecap="round"/>
    <path d="M150 64 188 40l-4 24-30 12z" fill="#fff" opacity=".9"/>
</svg>
@break
@case('card')
<svg viewBox="0 0 200 160" fill="none" stroke="#12142b" stroke-width="3" stroke-linejoin="round" aria-hidden="true">
    <rect x="46" y="62" width="130" height="82" rx="12" fill="#ec008c" transform="rotate(8 111 103)"/>
    <rect x="22" y="22" width="130" height="82" rx="12" fill="#fff"/>
    <rect x="22" y="22" width="130" height="24" rx="12" fill="#00aeef"/><rect x="22" y="34" width="130" height="12" fill="#00aeef" stroke="none"/><path d="M22 46h130"/>
    <circle cx="54" cy="72" r="14" fill="#ffd400"/><path d="M34 100c2-12 10-16 20-16s18 4 20 16" fill="#ffd400"/>
    <path d="M86 66h50M86 80h36M86 92h44" stroke-linecap="round"/>
</svg>
@break
@endswitch
@endif
