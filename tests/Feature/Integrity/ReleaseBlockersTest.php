<?php

/*
|--------------------------------------------------------------------------
| RELEASE BLOCKERS (belum diperbaiki; wajib diuji/diperbaiki sebelum production)
|--------------------------------------------------------------------------
| Temuan code review yang membutuhkan uji konkurensi nyata (dua koneksi database paralel, mis. MySQL) dan
| bukan perubahan kecil. Detail dan rekomendasi perbaikan: docs/06-laporan-sprint-revisi-klien.md bagian "Release blockers".
*/

it('BLOCKER 1 — closing race: pembayaran yang commit di antara perhitungan laporan dan penulisan closing tidak boleh lolos dari snapshot')
    ->todo();

it('BLOCKER 2 — dua checkout Terbitkan Ulang paralel untuk invoice void yang sama tidak boleh menghasilkan dua invoice pengganti non-void')
    ->todo();
