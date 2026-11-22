<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Nilai uji lokal saja (bukan kredensial production). Sesi admin di test wajib membawa sidik jari kredensial ini.
        config([
            'khayra_auth.admin.email' => 'admin@uji.test',
            'khayra_auth.admin.password' => 'kata-sandi-uji-lokal',
            'khayra_auth.admin.password_hash' => null,
            'khayra_auth.owner.email' => 'owner@uji.test',
            'khayra_auth.owner.password' => 'kata-sandi-owner-uji',
            'khayra_auth.owner.password_hash' => null,
        ]);
    }
}
