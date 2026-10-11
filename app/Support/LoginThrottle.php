<?php

namespace App\Support;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Pembatasan percobaan login gagal (admin / therapist / patient).
 *
 * Tiga lapis: identitas+IP, IP saja (percobaan massal lintas identitas),
 * dan identitas saja (percobaan terdistribusi lintas IP, jika diaktifkan).
 * Hanya kegagalan yang dihitung; login berhasil membersihkan lapis identitas+IP.
 */
class LoginThrottle
{
    private array $policy;

    private string $pairKey;

    private string $ipKey;

    private string $identityKey;

    private function __construct(string $scope, string $identity, string $ip)
    {
        $this->policy = config("khayra_auth.throttle.{$scope}");

        $identity = sha1($identity);
        $this->pairKey = "login:{$scope}:pair:{$identity}|{$ip}";
        $this->ipKey = "login:{$scope}:ip:{$ip}";
        $this->identityKey = "login:{$scope}:id:{$identity}";
    }

    public static function for(string $scope, string $identity, ?string $ip): self
    {
        return new self($scope, $identity, (string) $ip);
    }

    /** Normalisasi nomor WhatsApp agar 0812..., +62812..., 62812... dihitung sebagai identitas yang sama. */
    public static function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw);

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    /** Sisa detik penguncian; 0 jika boleh mencoba. */
    public function lockedOutFor(): int
    {
        $remaining = 0;

        foreach ($this->limits() as $key => $max) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                $remaining = max($remaining, RateLimiter::availableIn($key));
            }
        }

        return $remaining;
    }

    public function hit(): void
    {
        foreach (array_keys($this->limits()) as $key) {
            RateLimiter::hit($key, $this->policy['decay']);
        }
    }

    public function clear(): void
    {
        RateLimiter::clear($this->pairKey);
    }

    public static function message(int $seconds): string
    {
        $minutes = max(1, (int) ceil($seconds / 60));

        return "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.";
    }

    private function limits(): array
    {
        $limits = [
            $this->pairKey => $this->policy['pair'],
            $this->ipKey => $this->policy['ip'],
        ];

        if (! empty($this->policy['identity'])) {
            $limits[$this->identityKey] = $this->policy['identity'];
        }

        return $limits;
    }
}
