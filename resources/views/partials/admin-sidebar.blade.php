@php
    $active = $activeMenu ?? '';

    $groups = [
        [
            'key' => 'overview',
            'label' => 'Overview',
            'items' => [
                ['key' => 'dashboard', 'label' => 'Dashboard ERM', 'url' => '/admin/dashboard'],
                ['key' => 'reports', 'label' => 'Reporting Center', 'url' => '/admin/reports'],
            ],
        ],
        [
            'key' => 'appointment-care',
            'label' => 'Appointment & Care',
            'items' => [
                ['key' => 'bookings', 'label' => 'Booking / Appointment', 'url' => '/admin/bookings'],
                ['key' => 'patients', 'label' => 'Biodata Pasien', 'url' => '/admin/patients'],
                ['key' => 'visits', 'label' => 'Visit & Rekam Medis', 'url' => '/admin/visits'],
                ['key' => 'therapists', 'label' => 'Tim Fisioterapis', 'url' => '/admin/therapists'],
                ['key' => 'therapist-availabilities', 'label' => 'Jadwal Kerja Terapis', 'url' => '/admin/therapist-availabilities'],
                ['key' => 'staff-leaves', 'label' => 'Staff Leave / Cuti', 'url' => '/admin/staff-leaves'],
            ],
        ],
        [
            'key' => 'billing-commercial',
            'label' => 'Billing & Commercial',
            'items' => [
                ['key' => 'billings', 'label' => 'Kasir / Billing Ledger', 'url' => '/admin/billings'],
                ['key' => 'cashier', 'label' => 'Kasir Checkout', 'url' => '/admin/cashier'],
                ['key' => 'promos', 'label' => 'Promo Setting', 'url' => '/admin/promos'],
                ['key' => 'services', 'label' => 'Master Layanan', 'url' => '/admin/services'],
                ['key' => 'package-treatments', 'label' => 'Dokumen Pembelian Paket', 'url' => '/admin/package-treatments'],
            ],
        ],
        [
            'key' => 'clinical-documents',
            'label' => 'Clinical Documents',
            'items' => [
                ['key' => 'documents', 'label' => 'Surat & Dokumen Klinik', 'url' => '/admin/documents'],
                ['key' => 'exercise-library', 'label' => 'Exercise Library', 'url' => '/admin/exercise-library'],
                ['key' => 'referral-letters', 'label' => 'Surat Rujukan', 'url' => '/admin/referral-letters'],
                ['key' => 'rest-letter', 'label' => 'Surat Izin / Istirahat', 'url' => '/admin/rest-letter/create'],
                ['key' => 'informed-consent', 'label' => 'Informed Consent', 'url' => '/admin/informed-consent/create'],
                ['key' => 'consent-archive', 'label' => 'Arsip Consent', 'url' => '/admin/consent-archive'],
                ['key' => 'satu-sehat', 'label' => 'Satu Sehat Ready', 'url' => '/admin/satu-sehat-readiness'],
            ],
        ],
        [
            'key' => 'inventory',
            'label' => 'Inventory',
            'items' => [
                ['key' => 'inventory', 'label' => 'Inventory / Stok Barang', 'url' => '/admin/inventory'],
                ['key' => 'inventory-movements', 'label' => 'Mutasi Inventory', 'url' => '/admin/inventory/movements'],
                ['key' => 'inventory-stock-movements', 'label' => 'Stock Movements', 'url' => '/admin/inventory/stock-movements'],
                ['key' => 'inventory-stock-opname', 'label' => 'Stock Opname', 'url' => '/admin/inventory/stock-opname'],
                ['key' => 'inventory-monthly-summary', 'label' => 'Monthly Summary', 'url' => '/admin/inventory/monthly-summary'],
            ],
        ],
        [
            'key' => 'public-tools',
            'label' => 'Public Access',
            'items' => [
                ['key' => 'form-booking', 'label' => 'Form Booking Publik', 'url' => '/booking'],
                ['key' => 'patient-portal', 'label' => 'Patient Portal', 'url' => '/patient'],
                ['key' => 'therapist-portal', 'label' => 'Therapist Portal', 'url' => '/therapist/login'],
            ],
        ],
    ];

    $openGroups = collect($groups)
        ->filter(fn ($group) => collect($group['items'])->contains(fn ($item) => $item['key'] === $active))
        ->pluck('key')
        ->values()
        ->all();
@endphp

<aside class="admin-sidebar">
    <style>
        .admin-sidebar {
            width: 292px;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.14), transparent 28%),
                linear-gradient(180deg, #3f7f7e 0%, #2d6268 54%, #21484f 100%);
            color: #ffffff;
            padding: 20px 16px;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            align-self: flex-start;
            box-shadow: inset -1px 0 0 rgba(255,255,255,0.08);
            overflow-y: auto;
            max-height: 100vh;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.28) transparent;
        }

        .admin-sidebar * {
            box-sizing: border-box;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.22);
            border-radius: 999px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding: 4px 2px;
        }

        .sidebar-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 16px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 5px;
            flex-shrink: 0;
            box-shadow: 0 10px 24px rgba(0,0,0,0.10);
        }

        .sidebar-brand-name {
            font-size: 16px;
            font-weight: 900;
            line-height: 1.15;
            color: #ffffff;
        }

        .sidebar-brand-sub {
            margin-top: 4px;
            font-size: 11px;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.76);
            font-weight: 850;
        }

        .sidebar-intro {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.11);
            border-radius: 20px;
            padding: 15px 14px;
            margin-bottom: 16px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.08);
        }

        .sidebar-intro-title {
            font-size: 12px;
            font-weight: 950;
            margin-bottom: 6px;
            color: #ffffff;
            letter-spacing: .2px;
        }

        .sidebar-intro-text {
            font-size: 12px;
            line-height: 1.7;
            color: rgba(255,255,255,0.88);
            font-weight: 650;
        }

        .sidebar-groups {
            display: grid;
            gap: 9px;
        }

        .sidebar-group {
            border-radius: 18px;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.085);
            overflow: hidden;
        }

        .sidebar-group[open] {
            background: rgba(255,255,255,0.07);
            border-color: rgba(255,255,255,0.15);
        }

        .sidebar-group summary {
            list-style: none;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 13px 14px;
            color: rgba(255,255,255,0.96);
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .75px;
            text-transform: uppercase;
        }

        .sidebar-group summary::-webkit-details-marker {
            display: none;
        }

        .sidebar-chevron {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.11);
            color: rgba(255,255,255,0.88);
            font-size: 12px;
            font-weight: 950;
            transition: transform .18s ease;
            flex-shrink: 0;
        }

        .sidebar-group[open] .sidebar-chevron {
            transform: rotate(90deg);
        }

        .sidebar-menu-list {
            display: grid;
            gap: 7px;
            padding: 0 10px 12px;
        }

        .sidebar-link {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 15px;
            font-size: 13px;
            line-height: 1.35;
            font-weight: 850;
            transition: .2s ease;
            color: rgba(255,255,255,0.91);
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.055);
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.14);
            transform: translateX(1px);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: rgba(255,255,255,0.21);
            border-color: rgba(255,255,255,0.25);
            box-shadow: 0 10px 20px rgba(0,0,0,0.11);
        }

        .sidebar-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,0.94);
            flex-shrink: 0;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.10);
        }

        .sidebar-logout {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,0.10);
        }

        .sidebar-logout button {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.13);
            cursor: pointer;
            padding: 13px 14px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 950;
            color: #ffffff;
            background: rgba(255,255,255,0.12);
            font-family: Arial, sans-serif;
        }

        .sidebar-logout button:hover {
            background: rgba(255,255,255,0.18);
        }

        @media (max-width: 980px) {
            .admin-sidebar {
                width: 100%;
                min-height: auto;
                max-height: none;
                position: relative;
                border-radius: 0 0 26px 26px;
            }

            .sidebar-groups {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sidebar-brand">
        <img src="/images/khayra-logo.png" alt="Khayra Logo" class="sidebar-logo">
        <div>
            <div class="sidebar-brand-name">Khayra Physio</div>
            <div class="sidebar-brand-sub">ERM Workspace</div>
        </div>
    </div>

    <div class="sidebar-intro">
        <div class="sidebar-intro-title">Clinic Operating System</div>
        <div class="sidebar-intro-text">
            Booking, patient timeline, rekam medis, billing, inventory, dokumen klinik, dan laporan owner dalam satu workspace.
        </div>
    </div>

    <div class="sidebar-groups">
        @foreach($groups as $group)
            @php
                $groupIsOpen = in_array($group['key'], $openGroups, true) || ($active === '' && $group['key'] === 'overview');
            @endphp

            <details class="sidebar-group" {{ $groupIsOpen ? 'open' : '' }}>
                <summary>
                    <span>{{ $group['label'] }}</span>
                    <span class="sidebar-chevron">›</span>
                </summary>

                <div class="sidebar-menu-list">
                    @foreach($group['items'] as $item)
                        @php
                            $isActive = $active === $item['key'];
                        @endphp

                        <a href="{{ $item['url'] }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
                            <span>{{ $item['label'] }}</span>
                            @if($isActive)
                                <span class="sidebar-dot"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </details>
        @endforeach
    </div>

    <div class="sidebar-logout">
        <form method="POST" action="/admin/logout">
            @csrf
            <button type="submit">Logout Admin</button>
        </form>
    </div>
</aside>
