<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f7f8f8; color: #0f172a; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 460px; background: #fff; border: 1px solid #ecefef; border-radius: 24px; padding: 32px; box-shadow: 0 18px 40px rgba(15,23,42,.06); text-align: center; }
        .code { font-size: 13px; font-weight: 900; letter-spacing: .8px; color: #2d6268; text-transform: uppercase; }
        h1 { margin: 10px 0 8px; font-size: 24px; }
        p { margin: 0 0 22px; line-height: 1.7; color: #475569; font-size: 14px; }
        a { display: inline-block; padding: 12px 18px; border-radius: 14px; background: #2d6268; color: #fff; text-decoration: none; font-weight: 800; font-size: 14px; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <div class="code">Error 403</div>
            <h1>Akses ditolak</h1>
            <p>{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk membuka halaman ini.' }}</p>
            <a href="{{ \App\Support\PortalAuth::homeFor(\App\Support\PortalAuth::role()) }}">Kembali ke halaman utama</a>
        </div>
    </div>
</body>
</html>
