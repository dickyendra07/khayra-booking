<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingAuditLog;

/**
 * Pencatat jejak audit perubahan finansial. Hanya menambah baris (append-only).
 */
class BillingAudit
{
    private const SNAPSHOT_FIELDS = [
        'patient_id', 'visit_id', 'invoice_number', 'invoice_date', 'subtotal_amount',
        'discount_amount', 'promo_code', 'amount', 'paid_amount', 'remaining_amount',
        'change_amount', 'payment_status', 'payment_method', 'payment_detail_notes',
        'notes', 'void_reason', 'replaces_billing_id',
    ];

    public static function record(
        string $action,
        ?Billing $billing = null,
        ?array $before = null,
        ?array $after = null,
        ?string $reason = null,
        ?string $financeMonth = null,
    ): BillingAuditLog {
        return BillingAuditLog::create([
            'billing_id' => $billing?->id,
            'finance_month' => $financeMonth,
            'action' => $action,
            'actor' => session('admin_email') ?: 'admin',
            'ip_address' => request()?->ip(),
            'reason' => $reason,
            'before' => $before,
            'after' => $after,
        ]);
    }

    /** Potret field finansial sebuah invoice (nilai saat ini). */
    public static function snapshot(Billing $billing): array
    {
        $snapshot = [];

        foreach (self::SNAPSHOT_FIELDS as $field) {
            $value = $billing->getAttribute($field);
            $snapshot[$field] = $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : $value;
        }

        return $snapshot;
    }

    /**
     * Hanya field yang berubah: [before, after].
     *
     * @return array{0: array, 1: array}
     */
    public static function diff(array $before, array $after): array
    {
        $changedBefore = [];
        $changedAfter = [];

        foreach ($after as $field => $value) {
            if ((string) ($before[$field] ?? '') !== (string) $value) {
                $changedBefore[$field] = $before[$field] ?? null;
                $changedAfter[$field] = $value;
            }
        }

        return [$changedBefore, $changedAfter];
    }
}
