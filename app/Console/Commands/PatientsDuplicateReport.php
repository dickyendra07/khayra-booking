<?php

namespace App\Console\Commands;

use App\Services\PatientDuplicateFinder;
use Illuminate\Console\Command;

/**
 * Laporan kandidat pasien ganda (read-only). Hanya mencetak id pasien, tingkat keyakinan, dan alasan; tidak mencetak nama,
 * NIK, atau nomor kontak, sehingga aman ditempel ke tiket. Tinjauan dan keputusan penggabungan tetap manual.
 */
class PatientsDuplicateReport extends Command
{
    protected $signature = 'khayra:patients-duplicates {--json : Keluaran JSON}';

    protected $description = 'Daftar kandidat pasien ganda untuk tinjauan manual (read-only; hanya id, tanpa data pribadi).';

    public function handle(): int
    {
        $pairs = PatientDuplicateFinder::scan();

        if ($this->option('json')) {
            $this->line($pairs->toJson());

            return self::SUCCESS;
        }

        if ($pairs->isEmpty()) {
            $this->info('Tidak ada kandidat pasien ganda.');

            return self::SUCCESS;
        }

        $this->table(['Pasien A', 'Pasien B', 'Keyakinan', 'Alasan'], $pairs->map(fn ($p) => [
            '#'.$p['a'], '#'.$p['b'], $p['level'], implode('; ', $p['reasons']),
        ])->all());

        $this->line($pairs->count().' pasangan kandidat. Ini hanya petunjuk: tinjau manual dan JANGAN menggabungkan otomatis.');

        return self::SUCCESS;
    }
}
