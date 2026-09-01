@php

$content = [
    'hash' => $getRecord()->hash
];

$qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
    ->encoding('UTF-8')
    ->size(200)
    ->generate(json_encode($content));

$base64 = 'data:image/png;base64,' . base64_encode($qr);

@endphp

<!--suppress JSDeprecatedSymbols -->
<div class="flex justify-center">
    <img src="{{ $base64 }}" alt="QR Code"
        class="qr-thumbnail cursor-pointer rounded shadow"
        style="width:100px;height:100px;"
        onclick="event.preventDefault(); event.stopPropagation(); this.classList.toggle('fullscreen') ? this.style.cssText='image-rendering: pixelated;image-rendering: crisp-edges;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);height:80vh;z-index:9999;background:#fff;' : this.style.cssText='width:100px;height:100px;'" />
</div>
