<?php

namespace App\Support;

use DomainException;

/** Invoice void sumber tidak (lagi) dapat digantikan: sudah punya pengganti aktif, bukan void, atau pasien berbeda. */
class ReplacementConflictException extends DomainException {}
