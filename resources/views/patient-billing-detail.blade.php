<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Detail - Khayra Physio</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background:
                radial-gradient(circle at top left, rgba(15,118,110,.10), transparent 28%),
                linear-gradient(180deg, #f6fbfa 0%, #eef7f5 100%);
        }

        .page {
            min-height: 100vh;
            padding: 24px 18px 40px;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 28px;
            font-weight: 800;
            color: #0f766e;
        }

        .back-link {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 12px;
            background: white;
            color: #0f766e;
            border: 1px solid #d7ebe6;
            font-weight: 700;
            font-size: 14px;
        }

        .hero-card {
            background: linear-gradient(135deg, #0f766e 0%, #2f7f74 100%);
            color: white;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 18px 42px rgba(15,118,110,.08);
            margin-bottom: 20px;
        }

        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.16);
            color: white;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .hero-title {
            margin: 0;
            font-size: 36px;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,.92);
            font-size: 15px;
            line-height: 1.8;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .card-title {
            margin: 0 0 14px;
            font-size: 20px;
            color: #0f766e;
        }

        .info-stack {
            display: grid;
            gap: 12px;
        }

        .info-box {
            background: #f9fdfc;
            border: 1px solid #e5efec;
            border-radius: 16px;
            padding: 14px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 15px;
            color: #111827;
            line-height: 1.6;
            word-break: break-word;
        }

        .amount-box {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 18px;
        }

        .amount-label {
            font-size: 12px;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 8px;
        }

        .amount-value {
            font-size: 34px;
            font-weight: 800;
            color: #166534;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .billing-paid { background: #dcfce7; color: #166534; }
        .billing-unpaid { background: #fee2e2; color: #b91c1c; }
        .billing-partial { background: #fef3c7; color: #92400e; }

        .notes-card {
            background: white;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 16px 40px rgba(15,118,110,.08);
            border: 1px solid #edf5f3;
        }

        .notes-text {
            color: #4b5563;
            line-height: 1.8;
            font-size: 15px;
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page {
                padding: 16px 14px 32px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-title {
                font-size: 28px;
            }

            .hero-card,
            .card,
            .notes-card {
                padding: 18px;
                border-radius: 22px;
            }
        }
    </style>
    <!-- Khayra PWA -->
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#2f7c7a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Khayra ERM">
    <meta name="apple-mobile-web-app-title" content="Khayra ERM">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/khayra-logo.png">
</head>
<body>
    <div class="page">
        <div class="container">
            <div class="topbar">
                <div class="brand">Khayra Patient Portal</div>
                <a href="/patient" class="back-link">← Kembali ke Portal</a>
            </div>

            <section class="hero-card">
                <div class="hero-badge">Invoice Detail</div>
                <h1 class="hero-title">{{ $billing->invoice_number ?: 'Invoice' }}</h1>
                <p class="hero-subtitle">
                    Detail invoice patient yang tercatat di sistem Khayra Physio.
                </p>
            </section>

            <section class="grid">
                <div class="card">
                    <h2 class="card-title">Informasi Invoice</h2>

                    <div class="info-stack">
                        <div class="info-box">
                            <div class="info-label">Invoice Number</div>
                            <div class="info-value">{{ $billing->invoice_number ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Invoice Date</div>
                            <div class="info-value">{{ $billing->invoice_date ?: '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Payment Status</div>
                            <div class="info-value">
                                <span class="status-pill billing-{{ $billing->payment_status }}">
                                    {{ $billing->payment_status }}
                                </span>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Payment Method</div>
                            <div class="info-value">{{ $billing->payment_method ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Informasi Patient</h2>

                    <div class="info-stack">
                        <div class="info-box">
                            <div class="info-label">Patient Name</div>
                            <div class="info-value">{{ $billing->patient->full_name ?? '-' }}</div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Visit Reference</div>
                            <div class="info-value">{{ $billing->visit_id ? '#' . $billing->visit_id : '-' }}</div>
                        </div>

                        <div class="amount-box">
                            <div class="amount-label">Total Amount</div>
                            <div class="amount-value">Rp {{ number_format($billing->amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="notes-card">
                <h2 class="card-title">Catatan Invoice</h2>
                <div class="notes-text">{{ $billing->notes ?: '-' }}</div>
            </section>
        </div>
    </div>
<script>
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    var deferredInstallPrompt = null;

    window.addEventListener('beforeinstallprompt', function (event) {
        event.preventDefault();
        deferredInstallPrompt = event;

        if (document.getElementById('khayraInstallAppButton')) {
            return;
        }

        var button = document.createElement('button');
        button.id = 'khayraInstallAppButton';
        button.type = 'button';
        button.innerText = 'Install App';
        button.style.position = 'fixed';
        button.style.right = '18px';
        button.style.bottom = '18px';
        button.style.zIndex = '99999';
        button.style.border = '0';
        button.style.borderRadius = '999px';
        button.style.padding = '12px 16px';
        button.style.background = '#2f7c7a';
        button.style.color = '#ffffff';
        button.style.fontWeight = '900';
        button.style.fontFamily = 'Arial, sans-serif';
        button.style.fontSize = '13px';
        button.style.boxShadow = '0 14px 30px rgba(47,124,122,.24)';
        button.style.cursor = 'pointer';

        button.addEventListener('click', function () {
            if (!deferredInstallPrompt) {
                return;
            }

            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.finally(function () {
                deferredInstallPrompt = null;
                button.remove();
            });
        });

        document.body.appendChild(button);
    });
})();
</script>

</body>
</html>