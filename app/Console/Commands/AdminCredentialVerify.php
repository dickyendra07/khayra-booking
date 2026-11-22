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
    protected $signature = 'khayra:admin-verify {--account=admin : Akun yang diverifikasi: admin atau owner}';

    protected $description = 'Verifikasi email dan password admin/owner terhadap konfigurasi aktif (prompt tersembunyi; tidak mencetak kredensial).';

    public function handle(): int
    {
        $account = (string) $this->option('account');

        if (! in_array($account, ['admin', 'owner'], true)) {
            $this->error('Opsi --account harus admin atau owner.');

            return self::FAILURE;
        }

        $prefix = strtoupper($account);

        if (! AdminCredentials::isConfigured($account)) {
            $this->error("{$prefix}_EMAIL / {$prefix}_PASSWORD(_HASH) belum terbaca dari config (jalankan config:cache setelah mengubah .env).");

            return self::FAILURE;
        }

        if (filled(config("khayra_auth.{$account}.password_hash")) && ! AdminCredentials::hashIsValid($account)) {
            $this->error("{$prefix}_PASSWORD_HASH bukan hash bcrypt yang valid. Login {$account} akan ditolak.");

            return self::FAILURE;
        }

        $email = (string) $this->ask("Email {$account}");
        $password = (string) $this->secret("Password {$account} (tidak ditampilkan)");

        if (! AdminCredentials::attempt($email, $password, $account)) {
            $this->error('GAGAL: kombinasi email dan password tidak cocok dengan konfigurasi aktif.');

            return self::FAILURE;
        }

        $this->info('OK: kredensial cocok dengan konfigurasi aktif.');

        return self::SUCCESS;
    }
}
