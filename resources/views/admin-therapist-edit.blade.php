<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Physiotherapy Staff - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #f7faf9 0%, #f3f7f6 100%);
            color: #17232b;
        }

        .layout {
            min-height: 100vh;
            display: flex;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 28px;
        }

        .container {
            max-width: 1180px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
        }

        .ghost-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 11px 14px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e5ece9;
            color: #2d6d69;
            font-size: 13px;
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.03);
        }

        .hero {
            background: #ffffff;
            border: 1px solid #e8efed;
            border-radius: 28px;
            padding: 26px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
            margin-bottom: 18px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            gap: 20px;
            align-items: stretch;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef6f4;
            color: #3d6567;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 14px;
        }

        .hero-title {
            margin: 0;
            font-size: 38px;
            line-height: 1.08;
            font-weight: 800;
            color: #17756f;
        }

        .hero-text {
            margin: 12px 0 0;
            max-width: 700px;
            font-size: 14px;
            line-height: 1.85;
            color: #667381;
        }

        .hero-side {
            background: linear-gradient(145deg, #467f83 0%, #346d73 52%, #244f55 100%);
            border-radius: 24px;
            padding: 24px;
            color: #ffffff;
        }

        .hero-side h3 {
            margin: 0 0 8px;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 800;
        }

        .hero-side p {
            margin: 0 0 14px;
            font-size: 13px;
            line-height: 1.8;
            color: rgba(255,255,255,.92);
        }

        .side-list {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            line-height: 1.9;
            color: rgba(255,255,255,.95);
        }

        .form-card {
            background: #ffffff;
            border: 1px solid #e8efed;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
        }

        .form-title {
            margin: 0;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 800;
            color: #20343a;
        }

        .form-subtitle {
            margin: 8px 0 20px;
            font-size: 14px;
            line-height: 1.8;
            color: #6b7280;
        }

        .error-box {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff4f4;
            border: 1px solid #ffd9d9;
            color: #b42318;
            font-size: 13px;
            line-height: 1.8;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 800;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 14px;
            border: 1px solid #dbe5e2;
            border-radius: 16px;
            background: #ffffff;
            color: #17232b;
            font-size: 14px;
            font-family: Arial, sans-serif;
            transition: .2s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2f7c7a;
            box-shadow: 0 0 0 4px rgba(47,124,122,.10);
        }

        .hint {
            margin-top: 7px;
            font-size: 12px;
            line-height: 1.6;
            color: #8b97a6;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }

        .submit-btn {
            border: none;
            cursor: pointer;
            padding: 14px 18px;
            border-radius: 16px;
            background: linear-gradient(135deg, #17857e 0%, #136b66 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(23, 117, 111, 0.18);
        }

        @media (max-width: 980px) {
            .layout { display: block; }
            .main { padding: 16px; }
            .hero-grid,
            .form-grid { grid-template-columns: 1fr; }
            .hero-title { font-size: 30px; }
        }
    </style>
</head>
<body>
<div class="layout">
    @include('partials.admin-sidebar', ['activeMenu' => 'therapists'])

    <main class="main">
        <div class="container">
            <div class="topbar">
                <a href="/admin/therapists" class="ghost-link">← Kembali ke Physiotherapy Team</a>
            </div>

            <section class="hero">
                <div class="hero-grid">
                    <div>
                        <div class="hero-badge">Edit Physiotherapy Staff</div>
                        <h1 class="hero-title">Update physiotherapy staff information with a cleaner admin flow.</h1>
                        <p class="hero-text">
                            Halaman ini dipakai untuk memperbarui identitas, kontak, specialty, status aktif, dan akses login staff physiotherapy yang sudah terdaftar.
                        </p>
                    </div>

                    <div class="hero-side">
                        <h3>Update Notes</h3>
                        <p>Gunakan form ini untuk merapikan data staff aktif agar assignment visit dan akses internal tetap konsisten.</p>
                        <ul class="side-list">
                            <li>Email harus tetap valid untuk login</li>
                            <li>Nomor WhatsApp bisa dipakai untuk kontak cepat</li>
                            <li>Password baru bersifat opsional</li>
                            <li>Status inactive akan menghentikan pemakaian operasional</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="form-card">
                <h2 class="form-title">Physiotherapy Staff Form</h2>
                <p class="form-subtitle">Perbarui data staff yang sudah tersimpan di sistem Khayra.</p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="/admin/therapists/{{ $therapist->id }}/update">
                    @csrf

                    <div class="form-grid">
                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $therapist->full_name) }}" placeholder="Masukkan nama staff physiotherapy" required>
                        </div>

                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $therapist->email) }}" placeholder="Masukkan email staff" required>
                        </div>

                        <div class="field">
                            <label>WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $therapist->whatsapp) }}" placeholder="Masukkan nomor WhatsApp">
                        </div>

                        <div class="field">
                            <label>Specialty</label>
                            <input type="text" name="specialty" value="{{ old('specialty', $therapist->specialty) }}" placeholder="Contoh: Sports Rehab">
                        </div>

                        <div class="field">
                            <label>New Password</label>
                            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin ganti password">
                            <div class="hint">Isi hanya jika ingin mengganti password login staff.</div>
                        </div>

                        <div class="field">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="active" {{ old('status', $therapist->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $therapist->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="submit-btn">Update Staff</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
</body>
</html>