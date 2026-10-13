<?php

namespace App\Support;

use DomainException;

/** Periode keuangan sudah tutup buku (terdeteksi di dalam transaction, setelah kunci periode diambil). */
class PeriodClosedException extends DomainException {}
