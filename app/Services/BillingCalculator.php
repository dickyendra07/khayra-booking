<?php

namespace App\Services;

use App\Models\ClinicService;
use App\Models\Promo;

/**
 * Single source of truth for invoice totals.
 *
 * Discounts apply only to lines whose category is explicitly listed as
 * discountable (config clinic.billing.discountable_categories). Add-ons,
 * products, fees and room charges stay at full price.
 */
class BillingCalculator
{
    public static function categories(): array
    {
        return config('clinic.billing.categories', []);
    }

    public static function categoryLabel(?string $category): string
    {
        return self::categories()[$category] ?? '-';
    }

    public static function isValidCategory(?string $category): bool
    {
        return $category !== null && array_key_exists($category, self::categories());
    }

    public static function isDiscountable(?string $category): bool
    {
        return in_array($category, config('clinic.billing.discountable_categories', []), true);
    }

    /**
     * Map a ClinicService master category (Program, Add-on, ...) to a billing category.
     */
    public static function categoryForService(?string $serviceCategory): string
    {
        $key = ClinicService::canonicalCategoryKey($serviceCategory);

        return config('clinic.billing.service_category_map', [])[$key] ?? 'other';
    }

    /**
     * @param  array<int, array{category: string, quantity: int|float, unit_price: int|float}>  $lines
     * @return array{
     *     lines: array<int, array>,
     *     subtotal: float,
     *     discount_base: float,
     *     discount_amount: float,
     *     grand_total: float,
     *     promo_applied: bool,
     *     promo_error: ?string
     * }
     */
    public function calculate(array $lines, ?Promo $promo = null): array
    {
        $subtotal = 0.0;
        $discountBase = 0.0;
        $result = [];

        foreach ($lines as $key => $line) {
            $lineTotal = round((float) $line['quantity'] * (float) $line['unit_price'], 2);
            $eligible = self::isDiscountable($line['category'] ?? null);

            $subtotal += $lineTotal;

            if ($eligible) {
                $discountBase += $lineTotal;
            }

            $result[$key] = array_merge($line, [
                'line_total' => $lineTotal,
                'is_discount_eligible' => $eligible,
                'discount_amount' => 0.0,
            ]);
        }

        $discountAmount = 0.0;
        $promoApplied = false;
        $promoError = null;

        if ($promo) {
            if ($discountBase <= 0) {
                $promoError = 'Promo hanya berlaku untuk item Program / Treatment. Tidak ada item program pada transaksi ini.';
            } elseif (! $promo->isAvailableForSubtotal($discountBase)) {
                $promoError = 'Promo tidak aktif, tidak sesuai tanggal, atau total item program belum memenuhi minimum transaksi.';
            } else {
                $discountAmount = round($promo->calculateDiscount($discountBase), 2);
                $promoApplied = true;
            }
        }

        if ($promoApplied && $discountAmount > 0) {
            $result = $this->allocate($result, $discountBase, $discountAmount);
        }

        return [
            'lines' => $result,
            'subtotal' => round($subtotal, 2),
            'discount_base' => round($discountBase, 2),
            'discount_amount' => $discountAmount,
            'grand_total' => round(max($subtotal - $discountAmount, 0), 2),
            'promo_applied' => $promoApplied,
            'promo_error' => $promoError,
        ];
    }

    /**
     * Split the discount across eligible lines proportionally; the last
     * eligible line absorbs rounding so the allocation always sums exactly.
     */
    private function allocate(array $lines, float $discountBase, float $discountAmount): array
    {
        $eligibleKeys = array_keys(array_filter($lines, fn ($line) => $line['is_discount_eligible']));
        $lastKey = end($eligibleKeys);
        $allocated = 0.0;

        foreach ($eligibleKeys as $key) {
            if ($key === $lastKey) {
                $share = round($discountAmount - $allocated, 2);
            } else {
                $share = round($discountAmount * $lines[$key]['line_total'] / $discountBase, 2);
            }

            $lines[$key]['discount_amount'] = $share;
            $allocated += $share;
        }

        return $lines;
    }
}
