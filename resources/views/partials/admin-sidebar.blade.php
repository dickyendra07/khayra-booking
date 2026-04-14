<aside style="
    width: 280px;
    min-height: 100vh;
    background: linear-gradient(180deg, #3f7f82 0%, #2f6d72 58%, #25545a 100%);
    color: #ffffff;
    padding: 22px 18px;
    position: sticky;
    top: 0;
    align-self: flex-start;
    box-shadow: inset -1px 0 0 rgba(255,255,255,.06);
">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:18px;">
        <img src="/images/khayra-logo.png" alt="Khayra Logo" style="
            width: 46px;
            height: 46px;
            object-fit: contain;
            border-radius: 12px;
            background: rgba(255,255,255,.14);
            padding: 5px;
            border: 1px solid rgba(255,255,255,.16);
        ">
        <div>
            <div style="font-size:28px; font-weight:800; line-height:1;">Khayra</div>
            <div style="font-size:14px; color:rgba(255,255,255,.78); margin-top:4px;">Admin Workspace</div>
        </div>
    </div>

    <div style="
        padding: 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.10);
        border: 1px solid rgba(255,255,255,.12);
        margin-bottom: 20px;
        line-height: 1.8;
        font-size: 13px;
        color: rgba(255,255,255,.88);
    ">
        Dashboard utama untuk memantau aktivitas klinik, booking, patient, visit, therapist, dan billing.
    </div>

    <div style="
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .8px;
        font-weight: 800;
        color: rgba(255,255,255,.68);
        margin: 0 8px 12px;
    ">
        Main Navigation
    </div>

    @php
        $menus = [
            'dashboard' => ['/admin/dashboard', 'Dashboard'],
            'bookings' => ['/admin/bookings', 'Daftar Booking'],
            'patients' => ['/admin/patients', 'Patients'],
            'visits' => ['/admin/visits', 'Visits'],
            'therapists' => ['/admin/therapists', 'Therapists'],
            'billings' => ['/admin/billings', 'Billings'],
            'form-booking' => ['/booking', 'Form Booking'],
            'home' => ['/', 'Home'],
        ];
    @endphp

    <nav style="display:grid; gap:10px;">
        @foreach($menus as $key => [$url, $label])
            @php $isActive = ($activeMenu ?? '') === $key; @endphp
            <a href="{{ $url }}" style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                text-decoration:none;
                padding: 14px 16px;
                border-radius: 16px;
                font-weight: 700;
                font-size: 15px;
                transition: all .18s ease;
                border: 1px solid {{ $isActive ? 'rgba(255,255,255,.24)' : 'rgba(255,255,255,.08)' }};
                background: {{ $isActive ? 'rgba(255,255,255,.16)' : 'rgba(255,255,255,.06)' }};
                color: #ffffff;
                box-shadow: {{ $isActive ? '0 10px 24px rgba(15,23,42,.10)' : 'none' }};
            "
            onmouseover="this.style.background='rgba(255,255,255,.14)'; this.style.transform='translateX(2px)';"
            onmouseout="this.style.background='{{ $isActive ? 'rgba(255,255,255,.16)' : 'rgba(255,255,255,.06)' }}'; this.style.transform='translateX(0)';">
                <span>{{ $label }}</span>
                @if($isActive)
                    <span style="
                        width: 8px;
                        height: 8px;
                        border-radius: 999px;
                        background: #ffffff;
                        opacity: .95;
                    "></span>
                @endif
            </a>
        @endforeach
    </nav>

    <div style="margin-top: 18px;">
        <form method="POST" action="/admin/logout">
            @csrf
            <button type="submit" style="
                width: 100%;
                border: 1px solid rgba(255,255,255,.14);
                background: rgba(255,255,255,.10);
                color: #ffffff;
                padding: 14px 16px;
                border-radius: 16px;
                font-size: 15px;
                font-weight: 800;
                cursor: pointer;
                transition: all .18s ease;
            "
            onmouseover="this.style.background='rgba(255,255,255,.16)'"
            onmouseout="this.style.background='rgba(255,255,255,.10)'">
                Logout
            </button>
        </form>
    </div>
</aside>