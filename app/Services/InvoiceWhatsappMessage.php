<?php

namespace App\Services;

use App\Models\Billing;

/**
 * Teks WhatsApp yang dikirim admin ke pasien tentang invoice.
 *
 * Sengaja minimal: hanya nomor invoice, tanggal, total, dan status pembayaran.
 * Tidak memuat nama layanan/treatment, rincian item, diskon, nama lengkap, maupun
 * data klinis, karena nomor tujuan bisa salah atau dipakai bersama. Tidak ada tautan
 * ke invoice; invoice lengkap hanya dapat dilihat di Portal Pasien setelah login.
 */
class InvoiceWhatsappMessage
{
    public function build(Billing $billing): string
    {
        $billing->loadMissing('patient');

        $lines = [
            'Halo '.$this->firstName(optional($billing->patient)->full_name).', berikut ringkasan invoice Anda di Khayra Physio.',
            '',
            'No. Invoice: '.($billing->invoice_number ?: ('#'.$billing->id)),
            'Tanggal: '.($billing->invoice_date ? $billing->invoice_date->format('d/m/Y') : '-'),
            'Total tagihan: '.$this->rupiah($billing->amount),
            'Status: '.$this->statusLabel($billing->payment_status),
        ];

        if ($billing->payment_status === 'partial' && (float) $billing->remaining_amount > 0) {
            $lines[] = 'Sisa tagihan: '.$this->rupiah($billing->remaining_amount);
        } elseif ($billing->payment_status === 'unpaid') {
            $lines[] = 'Sisa tagihan: '.$this->rupiah($billing->amount);
        }

        $lines[] = '';
        $lines[] = 'Invoice lengkap dapat dilihat di Portal Pasien: '.url('/patient').' (masuk dengan nomor WhatsApp dan tanggal lahir Anda).';
        $lines[] = '';
        $lines[] = 'Terima kasih.';

        return implode("\n", $lines);
    }

    private function firstName(?string $fullName): string
    {
        $first = trim((string) strtok(trim((string) $fullName), ' '));

        return $first !== '' ? $first : 'Bapak/Ibu';
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
