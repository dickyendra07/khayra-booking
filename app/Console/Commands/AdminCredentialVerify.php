<?php

namespace App\Console\Commands;

use App\Support\AdminCredentials;
use Illuminate\Console\Command;

/**
 * Memverifikasi email + password terhadap konfigurasi yang SEDANG berlaku (config cache bila aktif) tanpa membuat sesi,
 * tanpa throttle, dan tanpa mencetak kredensial. Dipakai operator untuk memastikan rotasi kredensial benar sebelum
 * sesi admin yang lama ditutup.
 */
class AdminCredentialVerify extends Command
{
    protected $signature = 'khayra:admin-verify';

    protected $description = 'Verifikasi email dan password admin terhadap konfigurasi aktif (prompt tersembunyi; tidak mencetak kredensial).';

    public function handle(): int
    {
        if (! AdminCredentials::isConfigured()) {
            $this->error('ADMIN_EMAIL / ADMIN_PASSWORD(_HASH) belum terbaca dari config (jalankan config:cache setelah mengubah .env).');

            return self::FAILURE;
        }

        if (filled(config('khayra_auth.admin.password_hash')) && ! AdminCredentials::hashIsValid()) {
            $this->error('ADMIN_PASSWORD_HASH bukan hash bcrypt yang valid. Login admin akan ditolak.');

            return self::FAILURE;
        }

        $email = (string) $this->ask('Email admin');
        $password = (string) $this->secret('Password admin (tidak ditampilkan)');

        if (! AdminCredentials::attempt($email, $password)) {
            $this->error('GAGAL: kombinasi email dan password tidak cocok dengan konfigurasi aktif.');

            return self::FAILURE;
        }

        $this->info('OK: kredensial cocok dengan konfigurasi aktif.');

        return self::SUCCESS;
    }
}
