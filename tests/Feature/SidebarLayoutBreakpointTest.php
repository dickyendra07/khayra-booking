<?php

/*
 * Sidebar admin berubah ke mode mobile (blok penuh) tepat di <=980px dan khayra-ui.css ikut menumpuk
 * `.layout` di titik itu. Bila sebuah halaman menumpuk `.layout` LEBIH AWAL (mis. max-width:1180px),
 * sidebar 292px memenuhi layar dan konten terdorong jauh ke bawah di lebar 981-1180px (tampak kosong).
 */
const SIDEBAR_STACK_BREAKPOINT = 980;

it('tidak ada view ber-sidebar yang menumpuk .layout di atas breakpoint sidebar (980px)', function () {
    $offenders = [];

    foreach (glob(resource_path('views/*.blade.php')) as $file) {
        $source = file_get_contents($file);

        if (! str_contains($source, "partials.admin-sidebar")) {
            continue;
        }

        preg_match_all('/@media\s*\(\s*max-width\s*:\s*(\d+)px\s*\)\s*\{\s*\.layout\s*\{\s*display\s*:\s*block\s*;?\s*\}/', $source, $matches);

        foreach ($matches[1] as $width) {
            if ((int) $width > SIDEBAR_STACK_BREAKPOINT) {
                $offenders[] = basename($file).' => max-width:'.$width.'px';
            }
        }
    }

    expect($offenders)->toBe([]);
});
