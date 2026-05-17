@php
    $active = $activeMenu ?? '';

    $groups = [
        [
            'key' => 'core',
            'label' => 'Core ERM',
            'items' => [
                ['key' => 'dashboard', 'label' => 'Dashboard ERM', 'url' => '/admin/dashboard'],
                ['key' => 'reports', 'label' => 'Reporting', 'url' => '/admin/reports'],
            ],
        ],
        [
            'key' => 'patient-care',
            'label' => 'Patient & Visit',
            'items' => [
                ['key' => 'bookings', 'label' => 'Booking / Appointment', 'url' => '/admin/bookings'],
                ['key' => 'patients', 'label' => 'Biodata Pasien', 'url' => '/admin/patients'],
                ['key' => 'visits', 'label' => 'Visit & Rekam Medis', 'url' => '/admin/visits'],
                ['key' => 'therapists', 'label' => 'Tim Fisioterapis', 'url' => '/admin/therapists'],
                ['key' => 'therapist-availabilities', 'label' => 'Therapist Availability', 'url' => '/admin/therapist-availabilities'],
            ],
        ],
        [
            'key' => 'clinical-tools',
            'label' => 'Clinical Tools',
            'items' => [
                ['key' => 'exercise-library', 'label' => 'Exercise Library', 'url' => '/admin/exercise-library'],
                ['key' => 'documents', 'label' => 'Surat & Dokumen Klinik', 'url' => '/admin/documents'],
                ['key' => 'satu-sehat', 'label' => 'Satu Sehat Ready', 'url' => '/admin/satu-sehat-readiness'],
            ],
        ],
        [
            'key' => 'cashier-stock',
            'label' => 'Kasir & Inventory',
            'items' => [
                ['key' => 'billings', 'label' => 'Kasir / Billing', 'url' => '/admin/billings'],
                ['key' => 'cashier', 'label' => 'Kasir Checkout', 'url' => '/admin/cashier'],
                ['key' => 'promos', 'label' => 'Promo Setting', 'url' => '/admin/promos'],
                ['key' => 'inventory', 'label' => 'Inventory / Stok Barang', 'url' => '/admin/inventory'],
            ],
        ],
        [
            'key' => 'public-tools',
            'label' => 'Public Access',
            'items' => [
                ['key' => 'form-booking', 'label' => 'Form Booking Publik', 'url' => '/booking'],
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
            width: 286px;
            min-height: 100vh;
            background: linear-gradient(180deg, #3f7f7e 0%, #2d5f66 58%, #244f55 100%);
            color: #ffffff;
            padding: 20px 16px;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            align-self: flex-start;
            box-shadow: inset -1px 0 0 rgba(255,255,255,0.06);
            overflow-y: auto;
            max-height: 100vh;
        }

        .admin-sidebar * {
            box-sizing: border-box;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .sidebar-logo {
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 14px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.16);
            padding: 4px;
            flex-shrink: 0;
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
            letter-spacing: .6px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.78);
            font-weight: 800;
        }

        .sidebar-intro {
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 18px;
            padding: 15px 14px;
            margin-bottom: 18px;
        }

        .sidebar-intro-text {
            font-size: 12px;
            line-height: 1.75;
            color: rgba(255,255,255,0.94);
            font-weight: 650;
        }

        .sidebar-groups {
            display: grid;
            gap: 10px;
        }

        .sidebar-group {
            border-radius: 18px;
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.08);
            overflow: hidden;
        }

        .sidebar-group[open] {
            background: rgba(255,255,255,0.065);
            border-color: rgba(255,255,255,0.13);
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
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .7px;
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
            background: rgba(255,255,255,0.10);
            color: rgba(255,255,255,0.88);
            font-size: 12px;
            font-weight: 900;
            transition: transform .18s ease;
            flex-shrink: 0;
        }

        .sidebar-group[open] .sidebar-chevron {
            transform: rotate(90deg);
        }

        .sidebar-menu-list {
            display: grid;
            gap: 8px;
            padding: 0 10px 12px;
        }

        .sidebar-link {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 13px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 850;
            transition: .2s ease;
            color: rgba(255,255,255,0.94);
            background: rgba(255,255,255,0.045);
            border: 1px solid rgba(255,255,255,0.055);
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.11);
            border-color: rgba(255,255,255,0.14);
            transform: translateX(1px);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: rgba(255,255,255,0.20);
            border-color: rgba(255,255,255,0.24);
            box-shadow: 0 8px 18px rgba(0,0,0,0.10);
        }

        .sidebar-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,0.92);
            flex-shrink: 0;
        }

        .sidebar-logout {
            margin-top: 16px;
        }

        .sidebar-logout button {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.10);
            cursor: pointer;
            padding: 13px 14px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 900;
            color: #ffffff;
            background: rgba(255,255,255,0.12);
            font-family: Arial, sans-serif;
        }

        .sidebar-logout button:hover {
            background: rgba(255,255,255,0.17);
        }

        @media (max-width: 760px) {
            .admin-sidebar {
                width: 100%;
                min-height: auto;
                max-height: none;
                position: relative;
                border-radius: 0 0 24px 24px;
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
        <div class="sidebar-intro-text">
            Sistem ERM untuk booking fisio, biodata pasien, rekam medis, kasir, dokumen klinik, dan kesiapan Satu Sehat.
        </div>
    </div>

    <div class="sidebar-groups">
        @foreach($groups as $group)
            @php
                $groupIsOpen = in_array($group['key'], $openGroups, true) || ($active === '' && $group['key'] === 'core');
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
            <button type="submit">Logout</button>
        </form>
    </div>
</aside>
