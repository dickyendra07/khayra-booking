<?php

namespace App\Services;

use App\Models\Billing;

/**
 * Builds the WhatsApp text an admin sends to a patient about an invoice.
 *
 * It contains only what the patient themself is billed for. It never carries
 * a link to the invoice itself: the full invoice stays behind the patient
 * portal login (WhatsApp number + birth date).
 */
class InvoiceWhatsappMessage
{
    public function build(Billing $billing): string
    {
        $billing->loadMissing(['patient', 'items']);

        $name = optional($billing->patient)->full_name ?: 'Bapak/Ibu';
        $invoiceDate = $billing->invoice_date ? $billing->invoice_date->format('d/m/Y') : '-';

        $lines = [
            "Halo {$name}, berikut ringkasan invoice Anda di Khayra Physio.",
            '',
            'No. Invoice: '.($billing->invoice_number ?: ('#'.$billing->id)),
            'Tanggal: '.$invoiceDate,
        ];

        if ($billing->items->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Rincian:';

            foreach ($billing->items as $item) {
                $lines[] = '- '.$item->description.' x'.$item->quantity.' = '.$this->rupiah($item->line_total);
            }
        }

        $lines[] = '';

        if ((float) $billing->discount_amount > 0) {
            $lines[] = 'Subtotal: '.$this->rupiah($billing->subtotal_amount);
            $lines[] = 'Diskon: -'.$this->rupiah($billing->discount_amount);
        }

        $lines[] = 'Total tagihan: '.$this->rupiah($billing->amount);
        $lines[] = 'Status: '.$this->statusLabel($billing->payment_status);

        if ($billing->payment_status === 'partial' && (float) $billing->remaining_amount > 0) {
            $lines[] = 'Sudah dibayar: '.$this->rupiah($billing->paid_amount);
            $lines[] = 'Sisa tagihan: '.$this->rupiah($billing->remaining_amount);
        } elseif ($billing->payment_status === 'unpaid') {
            $lines[] = 'Sisa tagihan: '.$this->rupiah($billing->amount);
        }

        $lines[] = '';
        $lines[] = 'Invoice lengkap dapat dilihat di Portal Pasien: '.url('/patient').' (masuk dengan nomor WhatsApp dan tanggal lahir Anda).';
        $lines[] = '';
        $lines[] = 'Terima kasih telah mempercayakan perawatan Anda kepada Khayra Physio.';

        return implode("\n", $lines);
    }

    private function rupiah($amount): string
    {
        return 'Rp'.number_format((float) $amount, 0, ',', '.');
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'paid' => 'Lunas',
            'partial' => 'Dibayar sebagian',
            'unpaid' => 'Belum dibayar',
            default => ucfirst((string) $status),
        };
    }
}
