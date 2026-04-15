<div style="
    width: 276px;
    min-height: 100vh;
    background: linear-gradient(180deg, #3f7f7e 0%, #2d5f66 100%);
    color: #ffffff;
    padding: 20px 16px;
    box-sizing: border-box;
    position: sticky;
    top: 0;
    align-self: flex-start;
    box-shadow: inset -1px 0 0 rgba(255,255,255,0.06);
">
    <div style="
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    ">
        <img
            src="/images/khayra-logo.png"
            alt="Khayra Logo"
            style="
                width: 46px;
                height: 46px;
                object-fit: contain;
                border-radius: 14px;
                background: rgba(255,255,255,0.10);
                border: 1px solid rgba(255,255,255,0.16);
                padding: 4px;
                flex-shrink: 0;
            "
        >
        <div>
            <div style="
                font-size: 16px;
                font-weight: 800;
                line-height: 1.15;
                color: #ffffff;
            ">
                Khayra Physio
            </div>
            <div style="
                margin-top: 4px;
                font-size: 11px;
                letter-spacing: .6px;
                text-transform: uppercase;
                color: rgba(255,255,255,0.78);
                font-weight: 700;
            ">
                Admin Workspace
            </div>
        </div>
    </div>

    <div style="
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.10);
        border-radius: 18px;
        padding: 16px 14px;
        margin-bottom: 22px;
    ">
        <div style="
            font-size: 14px;
            line-height: 1.75;
            color: rgba(255,255,255,0.94);
            font-weight: 500;
        ">
            Dashboard utama untuk memantau aktivitas klinik, booking, patient, visit, physiotherapy team, billing, dan form booking.
        </div>
    </div>

    @php
        $menus = [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['key' => 'bookings', 'label' => 'Daftar Booking', 'url' => '/admin/bookings'],
            ['key' => 'patients', 'label' => 'Patients', 'url' => '/admin/patients'],
            ['key' => 'visits', 'label' => 'Visits', 'url' => '/admin/visits'],
            ['key' => 'therapists', 'label' => 'Physiotherapy Team', 'url' => '/admin/therapists'],
            ['key' => 'billings', 'label' => 'Billings', 'url' => '/admin/billings'],
            ['key' => 'form-booking', 'label' => 'Form Booking', 'url' => '/booking'],
            ['key' => 'home', 'label' => 'Home', 'url' => '/'],
        ];
    @endphp

    <div style="
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.70);
        font-weight: 800;
        margin: 0 10px 12px;
    ">
        Main Navigation
    </div>

    <div style="display: grid; gap: 10px;">
        @foreach ($menus as $menu)
            @php
                $isActive = ($activeMenu ?? '') === $menu['key'];
            @endphp

            <a
                href="{{ $menu['url'] }}"
                style="
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    padding: 13px 14px;
                    border-radius: 16px;
                    font-size: 14px;
                    font-weight: 800;
                    transition: .2s ease;
                    color: {{ $isActive ? '#ffffff' : 'rgba(255,255,255,0.96)' }};
                    background: {{ $isActive ? 'rgba(255,255,255,0.16)' : 'rgba(255,255,255,0.06)' }};
                    border: 1px solid {{ $isActive ? 'rgba(255,255,255,0.18)' : 'rgba(255,255,255,0.05)' }};
                    box-shadow: {{ $isActive ? '0 8px 18px rgba(0,0,0,0.10)' : 'none' }};
                "
            >
                <span>{{ $menu['label'] }}</span>
                @if($isActive)
                    <span style="
                        width: 8px;
                        height: 8px;
                        border-radius: 999px;
                        background: rgba(255,255,255,0.92);
                        flex-shrink: 0;
                    "></span>
                @endif
            </a>
        @endforeach
    </div>

    <div style="margin-top: 18px;">
        <form method="POST" action="/admin/logout">
            @csrf
            <button
                type="submit"
                style="
                    width: 100%;
                    border: 1px solid rgba(255,255,255,0.10);
                    cursor: pointer;
                    padding: 13px 14px;
                    border-radius: 16px;
                    font-size: 14px;
                    font-weight: 800;
                    color: #ffffff;
                    background: rgba(255,255,255,0.12);
                "
            >
                Logout
            </button>
        </form>
    </div>
</div>