<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Gerbang skema untuk rilis (dipakai khayra:preflight). HANYA MEMBACA: tidak memperbaiki skema atau data.
 *
 * Setiap persyaratan terikat pada migration yang membuatnya, sehingga keadaan database dapat dibedakan:
 *  - BELUM DIMIGRASI  : migration pembuat belum tercatat dan elemennya memang belum ada (keadaan normal sebelum `migrate`).
 *  - TIDAK LENGKAP    : migration SUDAH tercatat dijalankan tetapi elemennya tidak ada (kolom/tabel/index hilang, atau
 *                       unique index invoice_number tidak terpasang karena data duplikat) => selalu FAIL.
 *  - OK               : elemen ada.
 * Yang diperiksa bukan hanya keberadaan kolom, tetapi juga index dan constraint unik/primary yang dipakai pengaman
 * konkurensi dan idempotensi. Keluaran hanya memuat nama tabel/kolom dan angka; tidak ada nilai data.
 */
class SchemaGate
{
    public const NOT_MIGRATED = 'not_migrated';

    public const INCOMPLETE = 'incomplete';

    public const OK = 'ok';

    /**
     * Pemeriksaan: ['table', tabel] | ['columns', tabel, [kolom]] | ['unique'|'index'|'primary', tabel, [kolom]].
     *
     * @return array<int, array{label: string, migration: string, checks: array<int, array>, special?: string}>
     */
    public static function requirements(): array
    {
        return [
            ['label' => 'billing_items: kategori dan diskon', 'migration' => '2026_10_11_100000_add_category_and_discount_fields_to_billing_items_table',
                'checks' => [['columns', 'billing_items', ['item_category', 'is_discount_eligible', 'discount_amount']]]],
            ['label' => 'billing_audit_logs', 'migration' => '2026_10_12_100000_create_billing_audit_logs_table',
                'checks' => [['table', 'billing_audit_logs'], ['columns', 'billing_audit_logs', ['billing_id', 'finance_month', 'action', 'before', 'after']]]],
            ['label' => 'invoice_sequences (nomor invoice berurutan)', 'migration' => '2026_10_12_100100_create_invoice_sequences_table',
                'checks' => [['table', 'invoice_sequences'], ['unique', 'invoice_sequences', ['period']]]],
            ['label' => 'billings.replaces_billing_id', 'migration' => '2026_10_12_100200_add_replaces_billing_id_to_billings_table',
                'checks' => [['columns', 'billings', ['replaces_billing_id']]]],
            ['label' => 'finance_monthly_closings: buka kembali', 'migration' => '2026_10_12_100300_add_reopen_fields_to_finance_monthly_closings_table',
                'checks' => [['columns', 'finance_monthly_closings', ['reopened_at', 'reopened_by', 'reopen_reason']]]],
            ['label' => 'UNIQUE INDEX billings.invoice_number (SYARAT WAJIB RILIS)', 'migration' => '2026_10_12_100400_add_unique_index_to_billings_invoice_number',
                'checks' => [['unique', 'billings', ['invoice_number']]], 'special' => 'invoice_number'],
            ['label' => 'billing_payments (buku pembayaran)', 'migration' => '2026_10_13_100000_create_billing_payments_table',
                'checks' => [['table', 'billing_payments'], ['columns', 'billing_payments', ['billing_id', 'type', 'amount', 'paid_on']]]],
            ['label' => 'billings.payment_model dan checkout_token unik', 'migration' => '2026_10_13_100100_add_payment_model_and_checkout_token_to_billings_table',
                'checks' => [['columns', 'billings', ['payment_model', 'checkout_token']], ['unique', 'billings', ['checkout_token']]]],
            ['label' => 'rooms (master ruangan, nama unik)', 'migration' => '2026_10_13_100200_create_rooms_table',
                'checks' => [['table', 'rooms'], ['unique', 'rooms', ['name']]]],
            ['label' => 'billing_items: referensi biaya (ruangan/admin)', 'migration' => '2026_10_13_100300_add_reference_fields_to_billing_items_table',
                'checks' => [['columns', 'billing_items', ['reference_type', 'reference_id', 'rate_unit']], ['index', 'billing_items', ['reference_type', 'reference_id']]]],
            ['label' => 'informed_consents: versi template', 'migration' => '2026_10_13_100400_add_template_version_to_informed_consents_table',
                'checks' => [['columns', 'informed_consents', ['template_version', 'recorded_by']]]],
            ['label' => 'billing_payments.idempotency_key (kolom + unique)', 'migration' => '2026_10_14_100000_add_idempotency_key_to_billing_payments_table',
                'checks' => [['columns', 'billing_payments', ['idempotency_key']], ['unique', 'billing_payments', ['idempotency_key']]]],
            ['label' => 'patients.registration_token (kolom + unique)', 'migration' => '2026_10_14_110000_add_registration_token_to_patients_table',
                'checks' => [['columns', 'patients', ['registration_token']], ['unique', 'patients', ['registration_token']]]],
            ['label' => 'pending_registration_fees (tabel, unique patient_id)', 'migration' => '2026_10_14_120000_create_pending_registration_fees_table',
                'checks' => [['table', 'pending_registration_fees'], ['columns', 'pending_registration_fees', ['patient_id', 'amount', 'status', 'closed_month', 'billing_id']], ['unique', 'pending_registration_fees', ['patient_id']]]],
            ['label' => 'finance_period_locks (tabel, primary key month)', 'migration' => '2026_10_15_100000_create_finance_period_locks_table',
                'checks' => [['table', 'finance_period_locks'], ['primary', 'finance_period_locks', ['month']]]],
        ];
    }

    /** Nama migration yang sudah tercatat dijalankan; kosong bila tabel migrations belum ada. @return array<int, string> */
    public static function ranMigrations(): array
    {
        try {
            return Schema::hasTable('migrations') ? DB::table('migrations')->pluck('migration')->all() : [];
        } catch (Throwable) {
            return [];
        }
    }

    /** Bagian yang hilang dari sebuah persyaratan (kosong = lengkap). @return array<int, string> */
    public static function missing(array $requirement): array
    {
        $missing = [];

        foreach ($requirement['checks'] as $check) {
            [$kind, $table] = $check;
            $columns = $check[2] ?? [];

            if (! Schema::hasTable($table)) {
                $missing[] = "tabel {$table}";

                continue;
            }

            if ($kind === 'table') {
                continue;
            }

            $absent = array_values(array_filter($columns, fn ($column) => ! Schema::hasColumn($table, $column)));

            if ($absent !== []) {
                $missing[] = 'kolom '.$table.'.'.implode(', ', $absent);

                continue;
            }

            if ($kind === 'columns') {
                continue;
            }

            $present = match ($kind) {
                'unique' => Schema::hasIndex($table, $columns, 'unique'),
                'primary' => Schema::hasIndex($table, $columns, 'primary'),
                default => Schema::hasIndex($table, $columns),
            };

            if (! $present) {
                $missing[] = "{$kind} index {$table}(".implode(', ', $columns).')';
            }
        }

        return array_values(array_unique($missing));
    }

    /**
     * Evaluasi semua persyaratan.
     *
     * @return array<int, array{label: string, state: string, missing: array<int, string>, migration: string, special: ?string}>
     */
    public static function evaluate(): array
    {
        $ran = array_flip(self::ranMigrations());
        $results = [];

        foreach (self::requirements() as $requirement) {
            $missing = self::missing($requirement);
            $migrated = isset($ran[$requirement['migration']]);

            $results[] = [
                'label' => $requirement['label'],
                'migration' => $requirement['migration'],
                'special' => $requirement['special'] ?? null,
                'missing' => $missing,
                'state' => $missing === [] ? self::OK : ($migrated ? self::INCOMPLETE : self::NOT_MIGRATED),
            ];
        }

        return $results;
    }
}
