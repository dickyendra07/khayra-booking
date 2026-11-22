<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat hash bcrypt untuk ADMIN_PASSWORD_HASH. Password dibaca lewat prompt tersembunyi (tidak ada di argumen,
 * riwayat shell, atau log); yang dicetak hanya hash, untuk ditempel operator ke .env.
 */
class AdminCredentialHash extends Command
{
    protected $signature = 'khayra:admin-hash {--account=admin : Akun yang dibuatkan hash: admin atau owner}';

    protected $description = 'Buat hash bcrypt untuk ADMIN_PASSWORD_HASH / OWNER_PASSWORD_HASH dari password yang diketik (prompt tersembunyi).';

    public function handle(): int
    {
        $account = (string) $this->option('account');

        if (! in_array($account, ['admin', 'owner'], true)) {
            $this->error('Opsi --account harus admin atau owner.');

            return self::FAILURE;
        }

        $password = (string) $this->secret("Password {$account} baru (tidak ditampilkan)");
        $confirmation = (string) $this->secret('Ulangi password');

        if ($password === '' || ! hash_equals($password, $confirmation)) {
            $this->error('Password kosong atau konfirmasi tidak sama.');

            return self::FAILURE;
        }

        if (mb_strlen($password) < 12) {
            $this->error('Gunakan password minimal 12 karakter.');

            return self::FAILURE;
        }

        $prefix = strtoupper($account);

        $this->line("Salin baris berikut ke .env (kutip tunggal wajib), lalu hapus {$prefix}_PASSWORD:");
        $this->line("{$prefix}_PASSWORD_HASH='".Hash::driver('bcrypt')->make($password)."'");

        return self::SUCCESS;
    }
}
