<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biaya Admin Tertunda - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f6f8f8; color: #17232b; }
        .layout { min-height: 100vh; display: flex; }
        .main { flex: 1; min-width: 0; padding: 28px; }
        .container { max-width: 1180px; margin: 0 auto; }
        .section-card { background: #fff; border: 1px solid #ecefef; border-radius: 24px; padding: 24px; box-shadow: 0 10px 26px rgba(15, 23, 42, .04); margin-bottom: 18px; }
        .title { font-size: 32px; font-weight: 800; color: #22343a; margin: 0 0 10px; }
        .subtitle { font-size: 14px; line-height: 1.8; color: #6b7280; margin: 0 0 18px; }
        h2 { font-size: 18px; margin: 0 0 10px; }
        .box { padding: 14px 16px; border-radius: 14px; margin-bottom: 16px; line-height: 1.7; font-size: 13px; border: 1px solid; }
        .box.ok { background: #ecfdf5; border-color: #bbf7d0; color: #166534; }
        .box.warn { background: #fffbeb; border-color: #fde68a; color: #92400e; }
        .box.err { background: #fff1f2; border-color: #fecdd3; color: #be123c; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #eef2f1; vertical-align: top; }
        th { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }

        .btn[disabled] { background: #cbd5e1; cursor: not-allowed; }
        .link { color: #2c5b5a; font-weight: 700; text-decoration: none; }
        @media (max-width: 860px) { .layout { display: block; } .main { padding: 16px; } .title { font-size: 26px; } }
    </style>
    @include('partials.ui-assets')
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'registration-fees'])

    <main class="main">
        <div class="container">
            <section class="section-card">
                <h1 class="title">Biaya Admin Tertunda</h1>
                <p class="subtitle">
                    Biaya administrasi pasien baru (Rp35.000, sekali saat registrasi pertama) yang belum dapat ditagihkan karena periode sudah
                    tutup buku saat pasien didaftarkan. Kewajiban ini <strong>tidak hilang dan tidak ganda</strong> (satu per pasien). Invoice
                    diterbitkan dengan <strong>tanggal hari ini</strong>, tidak pernah mundur ke periode tertutup, dan terbit belum dibayar
                    (pembayaran lewat halaman invoice seperti biasa).
                </p>

                @if(session('success'))
                    <div class="box ok">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="box err">{{ session('error') }}</div>
                @endif

                @if($pending->isNotEmpty() && $todayClosed)
                    <div class="box warn">
                        Periode berjalan masih tutup buku, jadi penerbitan belum dapat dilakukan. Terbitkan setelah periode baru dimulai,
                        atau buka kembali periode lewat prosedur Buka Kembali Buku di Owner Finance.
                    </div>
                @endif

                <h2>Menunggu Diterbitkan ({{ $pending->count() }})</h2>
                @if($pending->isEmpty())
                    <div class="box ok">Tidak ada kewajiban administrasi yang tertunda.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Pasien</th><th>No. RM</th><th>Nominal</th><th>Periode tertutup</th><th>Dicatat</th><th></th></tr></thead>
                            <tbody>
                            @foreach($pending as $fee)
                                <tr>
                                    <td><a class="link" href="/admin/patients/{{ $fee->patient_id }}">{{ optional($fee->patient)->full_name ?: 'Pasien #'.$fee->patient_id }}</a></td>
                                    <td>{{ optional($fee->patient)->medical_record_number ?: '-' }}</td>
                                    <td>Rp {{ number_format($fee->amount, 0, ',', '.') }}</td>
                                    <td>{{ $fee->closed_month }}</td>
                                    <td>{{ \App\Support\ClinicTime::display($fee->created_at) }} WIB<br><span style="color:#6b7280;">{{ $fee->created_by ?: '-' }}</span></td>
                                    <td>
                                        <form method="POST" action="/admin/registration-fees/{{ $fee->id }}/issue" onsubmit="if (this.dataset.submitted) { return false; } this.dataset.submitted = '1'; this.querySelector('button').disabled = true;" data-once-form>
                                            @csrf
                                            <button type="submit" class="kx-btn kx-btn--primary btn" {{ $todayClosed ? 'disabled' : '' }}>Terbitkan Invoice</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="section-card">
                <h2>Sudah Diterbitkan (50 terakhir)</h2>
                @if($issued->isEmpty())
                    <p class="subtitle" style="margin:0;">Belum ada.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Pasien</th><th>Periode tertutup</th><th>Invoice</th><th>Diterbitkan</th></tr></thead>
                            <tbody>
                            @foreach($issued as $fee)
                                <tr>
                                    <td>{{ optional($fee->patient)->full_name ?: 'Pasien #'.$fee->patient_id }}</td>
                                    <td>{{ $fee->closed_month }}</td>
                                    <td>@if($fee->billing)<a class="link" href="/admin/billings/{{ $fee->billing_id }}">{{ $fee->billing->invoice_number }}</a>@else - @endif</td>
                                    <td>{{ \App\Support\ClinicTime::display($fee->issued_at) }} WIB<br><span style="color:#6b7280;">{{ $fee->issued_by ?: '-' }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </main>
</div>
<script>
    // Buka kunci tombol bila halaman dipulihkan dari tombol Back; penerbitan tetap idempoten di server.
    window.addEventListener('pageshow', function () {
        document.querySelectorAll('form[data-once-form]').forEach(function (form) {
            delete form.dataset.submitted;
            var button = form.querySelector('button');
            if (button) { button.disabled = {{ $todayClosed ? 'true' : 'false' }}; }
        });
    });
</script>
</body>
</html>
