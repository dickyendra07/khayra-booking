<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\BillingItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\MedicalRecord;
use App\Models\Billing;
use App\Models\Promo;
use App\Models\Therapist;
use App\Models\TherapistAvailability;
use App\Models\InformedConsent;
use App\Models\MedicalRecordHistory;
use App\Models\MedicalRecordComorbidity;
use App\Models\MedicalRecordSupportingData;
use App\Models\MedicalRecordHomeExercise;
use App\Models\MedicalRecordUpdateLog;
use App\Models\ReferralLetter;
use App\Models\InventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\PatientProgressEntry;
use App\Models\HomeExerciseTemplate;
use App\Models\ClinicService;
use App\Models\PackageTreatmentDocument;
use App\Models\TherapistLeaveRequest;

Route::get('/', function (Request $request) {
    $host = $request->getHost();

    if ($host === 'app.khayraphysio.com') {
        return redirect('/admin/login');
    }

    if ($host === 'physio.khayraphysio.com') {
        return redirect('/therapist/login');
    }

    if ($host === 'patient.khayraphysio.com') {
        return redirect('/patient');
    }

    return view('welcome');
});

Route::get('/admin', function () {
    if (session('admin_logged_in')) {
        return redirect('/admin/dashboard');
    }

    return redirect('/admin/login');
});


/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', function () {
    if (session('admin_logged_in')) {
        return redirect('/admin/dashboard');
    }

    return view('admin-login');
});

Route::post('/admin/login', function (Request $request) {
    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
    ]);

    $adminEmail = env('ADMIN_EMAIL', 'admin@khayraphysio.com');
    $adminPassword = env('ADMIN_PASSWORD', 'password');

    if ($request->email !== $adminEmail || $request->password !== $adminPassword) {
        return back()
            ->withInput()
            ->withErrors(['email' => 'Email atau password admin salah.']);
    }

    session([
        'admin_logged_in' => true,
        'admin_email' => $request->email,
    ]);

    return redirect('/admin/dashboard');
});

Route::post('/admin/logout', function () {
    session()->forget([
        'admin_logged_in',
        'admin_email',
    ]);

    return redirect('/admin/login')->with('success', 'Anda berhasil logout.');
});

Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});


Route::get('/admin/dashboard', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $today = now()->toDateString();
    $monthStart = now()->copy()->startOfMonth()->toDateString();
    $monthEnd = now()->copy()->endOfMonth()->toDateString();

    $totalBookings = Booking::count();
    $totalPatients = Patient::count();
    $totalVisits = Visit::count();
    $totalTherapists = Therapist::count();
    $totalBillings = Billing::count();

    $todayBookings = Booking::whereDate('booking_date', $today)->count();
    $todayVisits = Visit::whereDate('visit_date', $today)->count();

    $newPatientsThisMonth = Patient::whereBetween('created_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])->count();

    $completedVisitsThisMonth = Visit::where('status', 'completed')
        ->whereBetween('visit_date', [$monthStart, $monthEnd])
        ->count();

    $pendingBookings = Booking::where('status', 'pending')->count();
    $confirmedBookings = Booking::where('status', 'confirmed')->count();

    $needActionBookings = Booking::whereIn('status', ['pending', 'confirmed'])
        ->latest()
        ->take(6)
        ->get();

    $scheduledVisits = Visit::where('status', 'scheduled')->count();
    $inProgressVisits = Visit::where('status', 'in_progress')->count();
    $completedVisits = Visit::where('status', 'completed')->count();

    $monthlyPaidAmount = Billing::whereBetween('invoice_date', [$monthStart, $monthEnd])
        ->where('payment_status', '!=', 'void')
        ->sum('paid_amount');

    $monthlyOutstanding = Billing::whereBetween('invoice_date', [$monthStart, $monthEnd])
        ->whereIn('payment_status', ['unpaid', 'partial'])
        ->sum('remaining_amount');

    $monthlyDiscount = Billing::whereBetween('invoice_date', [$monthStart, $monthEnd])
        ->where('payment_status', '!=', 'void')
        ->sum('discount_amount');

    $monthlyNetRevenue = max($monthlyPaidAmount - $monthlyDiscount, 0);

    $monthRevenue = $monthlyPaidAmount;
    $discountMonth = $monthlyDiscount;

    $paidBillings = Billing::where('payment_status', 'paid')->count();
    $unpaidBillings = Billing::where('payment_status', 'unpaid')->count();
    $partialBillings = Billing::where('payment_status', 'partial')->count();
    $voidBillings = Billing::where('payment_status', 'void')->count();

    $needActionBillings = Billing::with('patient')
        ->whereIn('payment_status', ['unpaid', 'partial'])
        ->latest()
        ->take(6)
        ->get();

    $activePromos = Promo::where('status', 'active')->count();

    $inventoryItems = InventoryItem::where('status', 'active')->get();

    $lowStockItems = $inventoryItems
        ->filter(fn ($item) => $item->stock_status === 'low')
        ->count();

    $emptyStockItems = $inventoryItems
        ->filter(fn ($item) => $item->stock_status === 'empty')
        ->count();

    $needActionItems = $inventoryItems
        ->filter(fn ($item) => in_array($item->stock_status, ['low', 'empty']))
        ->take(6)
        ->values();

    $recentVisits = Visit::with(['patient', 'therapistRelation', 'medicalRecord'])
        ->latest()
        ->take(6)
        ->get();

    $latestVitalSigns = MedicalRecord::with(['visit.patient', 'visit.therapistRelation'])
        ->where(function ($query) {
            $query->whereNotNull('blood_pressure')
                ->orWhereNotNull('temperature')
                ->orWhereNotNull('respiration_rate')
                ->orWhereNotNull('heart_rate')
                ->orWhereNotNull('weight')
                ->orWhereNotNull('height')
                ->orWhereNotNull('bmi')
                ->orWhereNotNull('pain_scale');
        })
        ->latest()
        ->take(6)
        ->get();

    $recentBillings = Billing::with('patient')
        ->latest()
        ->take(6)
        ->get();

    $arrivalReminderBookings = Booking::with('patient')
        ->whereIn('status', ['pending', 'confirmed', 'arrived', 'in_treatment'])
        ->whereBetween('booking_date', [now()->toDateString(), now()->addDay()->toDateString()])
        ->orderBy('booking_date')
        ->orderBy('booking_time')
        ->get();

    $today = now()->startOfDay();

    $upcomingBirthdayPatients = Patient::whereNotNull('birth_date')
        ->get()
        ->map(function ($patient) use ($today) {
            $birthDate = \Carbon\Carbon::parse($patient->birth_date);
            $nextBirthday = $birthDate->copy()->year($today->year);

            if ($nextBirthday->lt($today)) {
                $nextBirthday->addYear();
            }

            $patient->next_birthday_date = $nextBirthday;
            $patient->birthday_days_left = $today->diffInDays($nextBirthday, false);
            $patient->birthday_age = $nextBirthday->year - $birthDate->year;

            return $patient;
        })
        ->filter(function ($patient) {
            return $patient->birthday_days_left >= 0 && $patient->birthday_days_left <= 30;
        })
        ->sortBy('birthday_days_left')
        ->values();

    $patientSourceStats = Patient::selectRaw("COALESCE(NULLIF(referral_source, ''), 'Belum diisi') as source, COUNT(*) as total")
        ->groupBy('source')
        ->orderByDesc('total')
        ->get();

    $patientSourceTotal = $patientSourceStats->sum('total');

    return view('admin-dashboard', compact(
        'totalBookings',
        'totalPatients',
        'totalVisits',
        'totalTherapists',
        'totalBillings',
        'todayBookings',
        'todayVisits',
        'newPatientsThisMonth',
        'completedVisitsThisMonth',
        'pendingBookings',
        'confirmedBookings',
        'needActionBookings',
        'scheduledVisits',
        'inProgressVisits',
        'completedVisits',
        'monthlyPaidAmount',
        'monthlyOutstanding',
        'monthlyDiscount',
        'monthlyNetRevenue',
        'monthRevenue',
        'discountMonth',
        'paidBillings',
        'unpaidBillings',
        'partialBillings',
        'voidBillings',
        'needActionBillings',
        'activePromos',
        'lowStockItems',
        'emptyStockItems',
        'needActionItems',
        'recentVisits',
        'latestVitalSigns',
        'recentBillings', 'patientSourceStats', 'patientSourceTotal', 'upcomingBirthdayPatients', 'arrivalReminderBookings'));
});

Route::get('/booking', function (Request $request) {
    $selectedDate = $request->query('date') ?: now()->format('Y-m-d');
    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate);
    $dayOfWeek = (int) $selectedDateCarbon->isoWeekday();

    $activeBookingStatuses = [
        'pending',
        'confirmed',
        'arrived',
        'in_treatment',
    ];

    $availabilityRules = TherapistAvailability::where('status', 'active')
        ->where('day_of_week', $dayOfWeek)
        ->where(function ($query) use ($selectedDate) {
            $query->whereNull('valid_from')
                ->orWhereDate('valid_from', '<=', $selectedDate);
        })
        ->where(function ($query) use ($selectedDate) {
            $query->whereNull('valid_until')
                ->orWhereDate('valid_until', '>=', $selectedDate);
        })
        ->get();

    $slotCapacity = [];

    foreach ($availabilityRules as $rule) {
        $startTime = \Carbon\Carbon::parse($selectedDate . ' ' . $rule->start_time);
        $endTime = \Carbon\Carbon::parse($selectedDate . ' ' . $rule->end_time);
        $duration = (int) ($rule->slot_duration_minutes ?: 60);

        while ($startTime->copy()->addMinutes($duration)->lte($endTime)) {
            $slot = $startTime->format('H:i');
            $slotCapacity[$slot] = ($slotCapacity[$slot] ?? 0) + (int) ($rule->capacity ?: 1);
            $startTime->addMinutes($duration);
        }
    }

    ksort($slotCapacity);

    $bookedCounts = Booking::whereDate('booking_date', $selectedDate)
        ->whereIn('status', $activeBookingStatuses)
        ->get()
        ->groupBy(function ($booking) {
            return substr((string) $booking->booking_time, 0, 5);
        })
        ->map(fn ($items) => $items->count());

    $timeSlots = collect($slotCapacity)->map(function ($capacity, $time) use ($bookedCounts) {
        $booked = (int) ($bookedCounts[$time] ?? 0);

        return [
            'time' => $time,
            'capacity' => $capacity,
            'booked' => $booked,
            'available' => $booked < $capacity,
            'remaining' => max($capacity - $booked, 0),
        ];
    })->values();

    return view('booking', compact('selectedDate', 'timeSlots'));
});

Route::post('/booking', function (Request $request) {
    $request->validate([
        'full_name' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:50',
        'service' => 'required|string|max:255',
        'booking_date' => 'required|date',
        'booking_time' => 'required',
        'therapist_id' => 'nullable|exists:therapists,id',
        'room_name' => 'nullable|string|max:255',
        'complaint' => 'nullable|string',
    ]);

    $selectedDate = $request->booking_date;
    $selectedTime = substr((string) $request->booking_time, 0, 5);
    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate);
    $dayOfWeek = (int) $selectedDateCarbon->isoWeekday();

    $activeBookingStatuses = [
        'pending',
        'confirmed',
        'arrived',
        'in_treatment',
    ];

    $availabilityRules = TherapistAvailability::where('status', 'active')
        ->where('day_of_week', $dayOfWeek)
        ->where(function ($query) use ($selectedDate) {
            $query->whereNull('valid_from')
                ->orWhereDate('valid_from', '<=', $selectedDate);
        })
        ->where(function ($query) use ($selectedDate) {
            $query->whereNull('valid_until')
                ->orWhereDate('valid_until', '>=', $selectedDate);
        })
        ->get();

    $capacity = 0;

    foreach ($availabilityRules as $rule) {
        $startTime = \Carbon\Carbon::parse($selectedDate . ' ' . $rule->start_time);
        $endTime = \Carbon\Carbon::parse($selectedDate . ' ' . $rule->end_time);
        $duration = (int) ($rule->slot_duration_minutes ?: 60);

        while ($startTime->copy()->addMinutes($duration)->lte($endTime)) {
            if ($startTime->format('H:i') === $selectedTime) {
                $capacity += (int) ($rule->capacity ?: 1);
            }

            $startTime->addMinutes($duration);
        }
    }

    if ($capacity <= 0) {
        return back()
            ->withInput()
            ->withErrors(['booking_time' => 'Slot jam ini tidak tersedia berdasarkan jadwal therapist. Silakan pilih jam lain.']);
    }

    $bookedCount = Booking::whereDate('booking_date', $selectedDate)
        ->whereTime('booking_time', $selectedTime)
        ->whereIn('status', $activeBookingStatuses)
        ->count();

    if ($bookedCount >= $capacity) {
        return back()
            ->withInput()
            ->withErrors(['booking_time' => 'Slot jam ini sudah penuh. Silakan pilih jam lain.']);
    }

    $normalizedWhatsapp = preg_replace('/[^0-9]/', '', (string) $request->whatsapp);
    $normalizedName = trim(strtolower((string) $request->full_name));

    $matchedPatient = Patient::query()
        ->where(function ($query) use ($request, $normalizedWhatsapp) {
            $query->where('whatsapp', $request->whatsapp);

            if ($normalizedWhatsapp !== '') {
                $query->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(whatsapp, ' ', ''), '-', ''), '+', ''), '.', '') = ?", [$normalizedWhatsapp]);
            }
        })
        ->first();

    if (!$matchedPatient && $normalizedName !== '') {
        $matchedPatient = Patient::whereRaw('LOWER(TRIM(full_name)) = ?', [$normalizedName])->first();
    }

    Booking::create([
        'patient_id' => $matchedPatient ? $matchedPatient->id : null,
        'full_name' => $request->full_name,
        'whatsapp' => $request->whatsapp,
        'service' => $request->service,
        'booking_date' => $selectedDate,
        'booking_time' => $selectedTime,
        'complaint' => $request->complaint,
        'status' => 'pending',
    ]);

    return redirect('/booking?date=' . $selectedDate)
        ->with('success', 'Booking berhasil dikirim. Admin akan menghubungi Anda untuk konfirmasi.');
});



Route::get('/admin/bookings', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = $request->query('search');
    $status = $request->query('status');
    $selectedDate = $request->query('date') ?: now()->toDateString();

    $query = Booking::with(['patient', 'visits'])->latest();

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')
              ->orWhere('whatsapp', 'like', '%' . $search . '%')
              ->orWhere('service', 'like', '%' . $search . '%')
              ->orWhere('complaint', 'like', '%' . $search . '%');
        });
    }

    if ($status && in_array($status, ['pending', 'confirmed', 'arrived', 'in_treatment', 'completed', 'cancelled', 'no_show'])) {
        $query->where('status', $status);
    }

    $bookings = $query->get();

    $allBookings = Booking::with(['patient', 'visits'])->latest()->get();

    $selectedDateBookings = Booking::with(['patient', 'visits'])
        ->whereDate('booking_date', $selectedDate)
        ->orderBy('booking_time')
        ->get();

    $todayBookings = Booking::whereDate('booking_date', now()->toDateString())->get();
    $tomorrowBookings = Booking::whereDate('booking_date', now()->addDay()->toDateString())->get();

    $pendingCount = $allBookings->where('status', 'pending')->count();
    $confirmedCount = $allBookings->where('status', 'confirmed')->count();
    $arrivedCount = $allBookings->where('status', 'arrived')->count();
    $inTreatmentCount = $allBookings->where('status', 'in_treatment')->count();
    $completedCount = $allBookings->where('status', 'completed')->count();
    $cancelledCount = $allBookings->where('status', 'cancelled')->count();
    $noShowCount = $allBookings->where('status', 'no_show')->count();

    $statusCounts = [
        'pending' => $allBookings->where('status', 'pending')->count(),
        'confirmed' => $allBookings->where('status', 'confirmed')->count(),
        'arrived' => $allBookings->where('status', 'arrived')->count(),
        'in_treatment' => $allBookings->where('status', 'in_treatment')->count(),
        'completed' => $allBookings->where('status', 'completed')->count(),
        'cancelled' => $allBookings->where('status', 'cancelled')->count(),
        'no_show' => $allBookings->where('status', 'no_show')->count(),
    ];

    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate);

    $weekStart = $selectedDateCarbon->copy()->startOfWeek();
    $weekDays = collect(range(0, 6))->map(function ($day) use ($weekStart) {
        $date = $weekStart->copy()->addDays($day);
        $dayBookings = Booking::whereDate('booking_date', $date->toDateString())->get();

        return [
            'date' => $date->toDateString(),
            'label' => $date->format('D'),
            'day' => $date->format('d'),
            'count' => $dayBookings->count(),
            'pending' => $dayBookings->where('status', 'pending')->count(),
            'confirmed' => $dayBookings->where('status', 'confirmed')->count(),
            'completed' => $dayBookings->where('status', 'completed')->count(),
        ];
    });

    $monthStart = $selectedDateCarbon->copy()->startOfMonth()->startOfWeek();
    $monthEnd = $selectedDateCarbon->copy()->endOfMonth()->endOfWeek();

    $monthDays = collect();
    $cursor = $monthStart->copy();

    while ($cursor->lte($monthEnd)) {
        $dateString = $cursor->toDateString();
        $dayBookings = Booking::whereDate('booking_date', $dateString)->get();

        $monthDays->push([
            'date' => $dateString,
            'day' => $cursor->format('d'),
            'label' => $cursor->format('D'),
            'is_current_month' => $cursor->month === $selectedDateCarbon->month,
            'is_selected' => $dateString === $selectedDate,
            'count' => $dayBookings->count(),
            'pending' => $dayBookings->where('status', 'pending')->count(),
            'confirmed' => $dayBookings->where('status', 'confirmed')->count(),
            'completed' => $dayBookings->where('status', 'completed')->count(),
        ]);

        $cursor->addDay();
    }

    $monthLabel = $selectedDateCarbon->format('F Y');
    $weeklyTotal = $weekDays->sum('count');
    $monthlyTotal = $monthDays->where('is_current_month', true)->sum('count');

    return view('admin-bookings', compact(
        'bookings',
        'allBookings',
        'selectedDate',
        'selectedDateBookings',
        'todayBookings',
        'tomorrowBookings',
        'pendingCount',
        'confirmedCount',
        'arrivedCount',
        'inTreatmentCount',
        'completedCount',
        'cancelledCount',
        'noShowCount',
        'statusCounts',
        'weekDays',
        'monthDays',
        'monthLabel',
        'weeklyTotal',
        'monthlyTotal'
    ));
});


Route::get('/admin/bookings/calendar', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $selectedDate = $request->query('date', now()->toDateString());
    $date = \Carbon\Carbon::parse($selectedDate);

    $therapists = Therapist::where('status', 'active')
        ->orderBy('full_name')
        ->get();

    $bookings = Booking::with(['patient', 'therapist'])
        ->whereDate('booking_date', $date->toDateString())
        ->orderBy('booking_time')
        ->get();

    $startHour = 8;
    $endHour = 20;
    $slotMinutes = 30;

    $timeSlots = collect();
    $cursor = $date->copy()->setTime($startHour, 0);
    $end = $date->copy()->setTime($endHour, 0);

    while ($cursor <= $end) {
        $timeSlots->push($cursor->format('H:i'));
        $cursor->addMinutes($slotMinutes);
    }

    $statusLabels = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'arrived' => 'Arrived',
        'in_treatment' => 'In Treatment',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
    ];

    $calendarStats = [
        'total' => $bookings->count(),
        'confirmed' => $bookings->where('status', 'confirmed')->count(),
        'arrived' => $bookings->where('status', 'arrived')->count(),
        'in_treatment' => $bookings->where('status', 'in_treatment')->count(),
        'completed' => $bookings->where('status', 'completed')->count(),
    ];

    return view('admin-booking-calendar', compact(
        'selectedDate',
        'date',
        'therapists',
        'bookings',
        'timeSlots',
        'statusLabels',
        'calendarStats'
    ));
});

Route::get('/admin/bookings/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::orderBy('full_name')->get();
    $therapists = Therapist::where('status', 'active')->orderBy('full_name')->get();
    $services = ClinicService::where('status', 'active')->orderBy('name')->get();
    $roomOptions = [
        'VIP 1',
        'VIP 2',
        'R.1.1',
        'R.2.1',
        'R.2.2',
        'R.2.3',
        'R.2.4',
        'R.2.5',
        'R. GYM',
        'R. Studio Pilates',
    ];


    $prefill = [
        'booking_date' => $request->query('date', now()->toDateString()),
        'booking_time' => $request->query('time', now()->format('H:i')),
        'therapist_id' => $request->query('therapist_id'),
        'room_name' => $request->query('room_name'),
    ];

    return view('admin-booking-create', compact('patients', 'therapists', 'services', 'roomOptions', 'prefill'));
});

Route::post('/admin/bookings/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $data = $request->validate([
        'patient_id' => 'nullable|exists:patients,id',
        'therapist_id' => 'nullable|exists:therapists,id',
        'room_name' => 'nullable|string|max:255',
        'full_name' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:50',
        'service' => 'required|string|max:255',
        'booking_date' => 'required|date',
        'booking_time' => 'required',
        'complaint' => 'nullable|string',
        'status' => 'required|in:pending,confirmed,arrived,in_treatment,completed,cancelled,no_show',
    ]);

    $allowedBookingSlots = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00'];
    $data['booking_time'] = substr((string) $data['booking_time'], 0, 5);

    if (!in_array($data['booking_time'], $allowedBookingSlots, true)) {
        return back()
            ->withInput()
            ->withErrors(['booking_time' => 'Jam appointment harus mengikuti slot operasional 08:00-20:00 per 30 menit.']);
    }

    $patient = !empty($data['patient_id']) ? Patient::find($data['patient_id']) : null;

    if ($patient) {
        $data['full_name'] = $patient->full_name;
        $data['whatsapp'] = $patient->whatsapp ?: $data['whatsapp'];
    }

    $booking = Booking::create([
        'patient_id' => $data['patient_id'] ?? null,
        'therapist_id' => $data['therapist_id'] ?? null,
        'room_name' => $data['room_name'] ?? null,
        'full_name' => $data['full_name'],
        'whatsapp' => $data['whatsapp'],
        'service' => $data['service'],
        'booking_date' => $data['booking_date'],
        'booking_time' => $data['booking_time'],
        'complaint' => $data['complaint'] ?? null,
        'status' => $data['status'] ?? 'confirmed',
    ]);

    return redirect('/admin/bookings/calendar?date=' . $booking->booking_date)
        ->with('success', 'Appointment berhasil dibuat.');
});


Route::get('/admin/bookings/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $booking = Booking::with('patient')->findOrFail($id);
    return view('admin-booking-detail', compact('booking'));
});

Route::get('/admin/bookings/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $booking = Booking::findOrFail($id);
    $therapists = Therapist::where('status', 'active')
        ->orWhere('id', $booking->therapist_id)
        ->orderBy('full_name')
        ->get();

    $roomOptions = [
        'VIP 1',
        'VIP 2',
        'R.1.1',
        'R.2.1',
        'R.2.2',
        'R.2.3',
        'R.2.4',
        'R.2.5',
        'R. GYM',
        'R. Studio Pilates',
    ];

    return view('admin-booking-edit', compact('booking', 'therapists', 'roomOptions'));
});

Route::get('/admin/bookings/{id}/update', function ($id) {
    return redirect('/admin/bookings/' . $id . '/edit');
});

Route::post('/admin/bookings/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $booking = Booking::findOrFail($id);

    $request->validate([
        'full_name' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:50',
        'service' => 'required|string|max:255',
        'booking_date' => 'required|date',
        'booking_time' => 'required',
        'therapist_id' => 'nullable|exists:therapists,id',
        'room_name' => 'nullable|string|max:255',
        'complaint' => 'nullable|string',
        'status' => 'required|in:pending,confirmed,arrived,in_treatment,completed,cancelled,no_show',
    ]);

    $allowedBookingSlots = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00'];
    $normalizedBookingTime = substr((string) $request->booking_time, 0, 5);

    if (!in_array($normalizedBookingTime, $allowedBookingSlots, true)) {
        return back()
            ->withInput()
            ->withErrors(['booking_time' => 'Jam appointment harus mengikuti slot operasional 08:00-20:00 per 30 menit.']);
    }

    $booking->full_name = $request->full_name;
    $booking->whatsapp = $request->whatsapp;
    $booking->service = $request->service;
    $booking->therapist_id = $request->therapist_id;
    $booking->room_name = $request->room_name;
    $booking->booking_date = $request->booking_date;
    $booking->booking_time = $normalizedBookingTime;
    $booking->complaint = $request->complaint;
    $booking->status = $request->status;
    $booking->save();

    return redirect('/admin/bookings/' . $booking->id)->with('success', 'Booking berhasil diperbarui!');
});

Route::post('/admin/bookings/{id}/status', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'status' => 'required|in:pending,confirmed,arrived,in_treatment,completed,cancelled,no_show',
    ]);

    $booking = Booking::findOrFail($id);
    $booking->status = $request->status;
    $booking->save();

    return redirect('/admin/bookings')->with('success', 'Status booking berhasil diperbarui!');
});

Route::get('/admin/bookings/{id}/create-patient', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return redirect('/admin/bookings/' . $id)
        ->with('error', 'Gunakan tombol Create Patient dari halaman booking agar proses berjalan aman.');
});

Route::post('/admin/bookings/{id}/create-patient', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $booking = Booking::findOrFail($id);

    if (!$booking->patient_id) {
        $patient = new Patient();
        $patient->full_name = $booking->full_name;
        $patient->gender = null;
        $patient->birth_date = null;
        $patient->whatsapp = $booking->whatsapp;
        $patient->address = null;
        $patient->nik = null;
        $patient->religion = null;
        $patient->occupation = null;
        $patient->education = null;
        $patient->marital_status = null;
        $patient->medical_record_number = null;
        $patient->save();

        $booking->patient_id = $patient->id;
        $booking->save();
    }

    return redirect('/admin/bookings/' . $booking->id)
        ->with('success', 'Booking berhasil dihubungkan ke data patient.');
});

Route::get('/admin/patients', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = $request->query('search');
    $gender = $request->query('gender');
    $birthDate = $request->query('birth_date');

    $query = Patient::with(['visits', 'billings', 'informedConsents'])->latest();

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')
              ->orWhere('whatsapp', 'like', '%' . $search . '%')
              ->orWhere('address', 'like', '%' . $search . '%')
              ->orWhere('nik', 'like', '%' . $search . '%')
              ->orWhere('religion', 'like', '%' . $search . '%')
              ->orWhere('occupation', 'like', '%' . $search . '%')
              ->orWhere('education', 'like', '%' . $search . '%')
              ->orWhere('medical_record_number', 'like', '%' . $search . '%');
        });
    }

    if ($gender && in_array($gender, ['male', 'female'])) {
        $query->where('gender', $gender);
    }

    if ($birthDate) {
        $query->whereDate('birth_date', $birthDate);
    }

    $patients = $query->get();

    return view('admin-patients', compact('patients', 'search', 'gender', 'birthDate'));
});

Route::get('/admin/patients/create', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return view('admin-patient-create');
});

Route::post('/admin/patients', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'full_name' => 'required|string|max:255',
        'gender' => 'nullable|in:male,female',
        'birth_date' => 'nullable|date',
        'whatsapp' => 'required|string|max:50',
        'address' => 'nullable|string',
        'nik' => 'nullable|string|max:30',
        'religion' => 'nullable|string|max:100',
        'occupation' => 'nullable|string|max:100',
        'education' => 'nullable|string|max:100',
        'marital_status' => 'nullable|in:Cerai hidup,Cerai mati,Kawin,Belum kawin',
    ]);

    $patient = new Patient();
    $patient->full_name = $request->full_name;
    $patient->gender = $request->gender;
    $patient->birth_date = $request->birth_date;
    $patient->whatsapp = $request->whatsapp;
    $patient->address = $request->address;
    $patient->nik = $request->nik;
    $patient->religion = $request->religion;
    $patient->occupation = $request->occupation;
    $patient->education = $request->education;
    $patient->marital_status = $request->marital_status;
    $patient->referral_source = $request->referral_source;
    $patient->referral_source_other = $request->referral_source === 'Lainnya' ? $request->referral_source_other : null;
    $patient->documentation_consent = $request->documentation_consent;
    $patient->documentation_consent_notes = $request->documentation_consent_notes;

    if (empty($patient->medical_record_number)) {
        $patient->medical_record_number = $patient->generateMedicalRecordNumber();
    }

    $patient->save();

    return redirect('/admin/patients')->with('success', 'Data pasien berhasil ditambahkan!');
});

Route::get('/admin/patients/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::with([
        'bookings' => function ($query) {
            $query->latest();
        },
        'visits' => function ($query) {
            $query->with(['booking', 'therapistRelation', 'medicalRecord.homeExercises'])->latest();
        },
        'billings' => function ($query) {
            $query->with(['visit', 'items.inventoryItem'])->latest();
        },
        'informedConsents' => function ($query) {
            $query->with('visit')->latest();
        },
    ])->findOrFail($id);

    $timeline = collect();

    foreach ($patient->bookings as $booking) {
        $timeline->push([
            'date' => $booking->created_at,
            'type' => 'Booking',
            'title' => $booking->service ?: 'Booking appointment',
            'meta' => ($booking->booking_date ?: '-') . ' ' . ($booking->booking_time ?: ''),
            'status' => $booking->status ?: 'pending',
            'url' => '/admin/bookings/' . $booking->id,
            'description' => $booking->complaint ?: 'Booking dibuat dari form appointment.',
        ]);
    }

    foreach ($patient->visits as $visit) {
        $timeline->push([
            'date' => $visit->created_at,
            'type' => 'Visit',
            'title' => 'Visit #' . $visit->id,
            'meta' => ($visit->visit_date ?: '-') . ' · ' . (optional($visit->therapistRelation)->full_name ?: $visit->therapist ?: 'Fisioterapis belum dipilih'),
            'status' => $visit->status ?: 'scheduled',
            'url' => '/admin/visits/' . $visit->id . '/medical-record',
            'description' => $visit->notes ?: 'Visit fisioterapi terhubung ke patient.',
        ]);

        if ($visit->medicalRecord) {
            $timeline->push([
                'date' => $visit->medicalRecord->updated_at ?: $visit->medicalRecord->created_at,
                'type' => 'Rekam Medis',
                'title' => 'Rekam Medis Visit #' . $visit->id,
                'meta' => 'Clinical record tersedia',
                'status' => 'completed',
                'url' => '/admin/visits/' . $visit->id . '/medical-record',
                'description' => $visit->medicalRecord->main_complaint ?? $visit->medicalRecord->assessment ?? 'Rekam medis sudah dibuat.',
            ]);
        }
    }

    foreach ($patient->billings as $billing) {
        $timeline->push([
            'date' => $billing->created_at,
            'type' => 'Billing',
            'title' => $billing->invoice_number ?: 'Billing #' . $billing->id,
            'meta' => 'Rp ' . number_format($billing->amount, 0, ',', '.') . ' · ' . ($billing->payment_method_label ?? '-'),
            'status' => $billing->payment_status ?: 'unpaid',
            'url' => '/admin/billings/' . $billing->id,
            'description' => $billing->payment_status === 'void'
                ? 'Invoice sudah di-void. ' . ($billing->void_reason ?: '')
                : ($billing->notes ?: 'Invoice pasien tersimpan di kasir ledger.'),
        ]);
    }

    foreach ($patient->informedConsents as $consent) {
        $timeline->push([
            'date' => $consent->created_at,
            'type' => 'Consent',
            'title' => 'Informed Consent #' . $consent->id,
            'meta' => $consent->consent_date ? $consent->consent_date->format('Y-m-d') : '-',
            'status' => $consent->status ?: 'signed',
            'url' => '/admin/informed-consents/' . $consent->id . '/print',
            'description' => $consent->agreement_text ?: 'Dokumen informed consent tersimpan.',
        ]);
    }

    $timeline = $timeline
        ->filter(fn ($item) => $item['date'])
        ->sortByDesc('date')
        ->values();

    $paidTotal = $patient->billings->where('payment_status', '!=', 'void')->sum('paid_amount');
    $outstandingTotal = $patient->billings->where('payment_status', '!=', 'void')->sum('remaining_amount');
    $revenueTotal = $patient->billings->where('payment_status', '!=', 'void')->sum('amount');
    $voidTotal = $patient->billings->where('payment_status', 'void')->count();

    $progressEntries = PatientProgressEntry::with('visit')
        ->where('patient_id', $patient->id)
        ->latest('entry_date')
        ->latest()
        ->get();

    return view('admin-patient-detail', compact(
        'patient',
        'timeline',
        'paidTotal',
        'outstandingTotal',
        'revenueTotal',
        'voidTotal',
        'progressEntries'
    ));
});

Route::get('/admin/patients/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::findOrFail($id);
    return view('admin-patient-edit', compact('patient'));
});

Route::post('/admin/patients/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::findOrFail($id);

    $request->validate([
        'full_name' => 'required|string|max:255',
        'gender' => 'nullable|in:male,female',
        'birth_date' => 'nullable|date',
        'whatsapp' => 'required|string|max:50',
        'address' => 'nullable|string',
        'nik' => 'nullable|string|max:30',
        'religion' => 'nullable|string|max:100',
        'occupation' => 'nullable|string|max:100',
        'education' => 'nullable|string|max:100',
        'marital_status' => 'nullable|in:Cerai hidup,Cerai mati,Kawin,Belum kawin',
    ]);

    $patient->full_name = $request->full_name;
    $patient->gender = $request->gender;
    $patient->birth_date = $request->birth_date;
    $patient->whatsapp = $request->whatsapp;
    $patient->address = $request->address;
    $patient->nik = $request->nik;
    $patient->religion = $request->religion;
    $patient->occupation = $request->occupation;
    $patient->education = $request->education;
    $patient->marital_status = $request->marital_status;
    $patient->referral_source = $request->referral_source;
    $patient->referral_source_other = $request->referral_source === 'Lainnya' ? $request->referral_source_other : null;
    $patient->documentation_consent = $request->documentation_consent;
    $patient->documentation_consent_notes = $request->documentation_consent_notes;

    if (empty($patient->medical_record_number)) {
        $patient->medical_record_number = $patient->generateMedicalRecordNumber();
    }

    $patient->save();

    return redirect('/admin/patients/' . $patient->id)->with('success', 'Data patient berhasil diperbarui!');
});

Route::get('/admin/patients/{id}/informed-consent', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::with([
        'visits' => function ($query) {
            $query->latest();
        }
    ])->findOrFail($id);

    $latestConsent = InformedConsent::with('visit')
        ->where('patient_id', $patient->id)
        ->latest()
        ->first();

    return view('admin-informed-consent-form', compact('patient', 'latestConsent'));
});

Route::post('/admin/patients/{id}/informed-consent', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::findOrFail($id);

    $request->validate([
        'visit_id' => 'nullable|exists:visits,id',
        'consent_date' => 'required|date',
        'status' => 'required|in:pending,signed',
        'physiotherapy_name' => 'required|string|max:255',
        'treatment_location' => 'required|string|max:255',
        'representative_name' => 'nullable|string|max:255',
        'relationship_to_patient' => 'nullable|string|max:255',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:50',
        'emergency_contact_relation' => 'nullable|string|max:255',
        'agreement_text' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    InformedConsent::create([
        'patient_id' => $patient->id,
        'visit_id' => $request->visit_id ?: null,
        'consent_date' => $request->consent_date,
        'status' => $request->status,
        'physiotherapy_name' => $request->physiotherapy_name,
        'treatment_location' => $request->treatment_location,
        'representative_name' => $request->representative_name,
        'relationship_to_patient' => $request->relationship_to_patient,
        'emergency_contact_name' => $request->emergency_contact_name,
        'emergency_contact_phone' => $request->emergency_contact_phone,
        'emergency_contact_relation' => $request->emergency_contact_relation,
        'agreement_text' => $request->agreement_text,
        'notes' => $request->notes,
    ]);

    return redirect('/admin/patients/' . $patient->id)->with('success', 'Informed consent berhasil disimpan!');
});

Route::get('/admin/informed-consents/{id}/print', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $consent = InformedConsent::with(['patient', 'visit'])->findOrFail($id);

    return view('admin-informed-consent-print', compact('consent'));
});



Route::get('/admin/inventory', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = $request->query('search');
    $stockStatus = $request->query('stock_status');
    $status = $request->query('status');
    $category = $request->query('category');

    $query = InventoryItem::query()->latest();

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('sku', 'like', '%' . $search . '%')
              ->orWhere('category', 'like', '%' . $search . '%')
              ->orWhere('supplier', 'like', '%' . $search . '%')
              ->orWhere('storage_location', 'like', '%' . $search . '%');
        });
    }

    if ($status && in_array($status, ['active', 'inactive'])) {
        $query->where('status', $status);
    }

    if ($category) {
        $query->where('category', $category);
    }

    $items = $query->get();

    if ($stockStatus) {
        $items = $items->filter(function ($item) use ($stockStatus) {
            return $item->stock_status === $stockStatus;
        })->values();
    }

    $categories = InventoryItem::whereNotNull('category')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    $allItems = InventoryItem::all();

    $totalItems = $allItems->count();
    $activeItems = $allItems->where('status', 'active')->count();
    $safeStockItems = $allItems->filter(fn ($item) => $item->status === 'active' && $item->stock_status === 'safe')->count();
    $lowStockItems = $allItems->filter(fn ($item) => $item->status === 'active' && $item->stock_status === 'low')->count();
    $emptyStockItems = $allItems->filter(fn ($item) => $item->status === 'active' && $item->stock_status === 'empty')->count();
    $stockValue = $allItems->sum(fn ($item) => (float) $item->stock * (float) $item->purchase_price);
    $potentialSalesValue = $allItems->sum(fn ($item) => (float) $item->stock * (float) $item->selling_price);
    $needActionItems = $allItems->filter(fn ($item) => $item->status === 'active' && in_array($item->stock_status, ['low', 'empty']))->values();

    $monthStart = now()->startOfMonth();
    $monthEnd = now()->endOfMonth();

    $monthlyMovements = InventoryStockMovement::whereBetween('created_at', [$monthStart, $monthEnd])->get();
    $monthlyMovementCount = $monthlyMovements->count();
    $monthlyStockIn = $monthlyMovements->where('type', 'in')->sum('quantity');
    $monthlyStockOut = $monthlyMovements->where('type', 'out')->sum('quantity');
    $monthlyAdjustmentCount = $monthlyMovements->where('type', 'adjustment')->count();

    $recentMovements = InventoryStockMovement::with('item')->latest()->take(8)->get();

    return view('admin-inventory', compact(
        'items',
        'categories',
        'totalItems',
        'activeItems',
        'safeStockItems',
        'lowStockItems',
        'emptyStockItems',
        'stockValue',
        'potentialSalesValue',
        'monthlyMovementCount',
        'monthlyStockIn',
        'monthlyStockOut',
        'monthlyAdjustmentCount',
        'needActionItems',
        'recentMovements',
        'search',
        'status',
        'stockStatus',
        'category'
    ));
});



Route::get('/admin/inventory/stock-movements', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $itemId = $request->query('item_id');
    $type = $request->query('type');
    $dateFrom = $request->query('date_from');
    $dateTo = $request->query('date_to');
    $search = $request->query('search');

    $query = InventoryStockMovement::with('item')->latest();

    if ($itemId) {
        $query->where('inventory_item_id', $itemId);
    }

    if ($type && in_array($type, ['in', 'out', 'adjustment'])) {
        $query->where('type', $type);
    }

    if ($dateFrom) {
        $query->whereDate('created_at', '>=', $dateFrom);
    }

    if ($dateTo) {
        $query->whereDate('created_at', '<=', $dateTo);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('reference', 'like', '%' . $search . '%')
              ->orWhere('notes', 'like', '%' . $search . '%')
              ->orWhereHas('item', function ($sub) use ($search) {
                  $sub->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%');
              });
        });
    }

    $movements = $query->get();
    $items = InventoryItem::orderBy('name')->get();

    $totalIn = $movements->where('type', 'in')->sum('quantity');
    $totalOut = $movements->where('type', 'out')->sum('quantity');
    $totalAdjustment = $movements->where('type', 'adjustment')->count();

    return view('admin-inventory-stock-movements', compact(
        'movements',
        'items',
        'itemId',
        'type',
        'dateFrom',
        'dateTo',
        'search',
        'totalIn',
        'totalOut',
        'totalAdjustment'
    ));
});



Route::get('/admin/inventory/import', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return view('admin-inventory-import');
});

Route::get('/admin/inventory/export/csv', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $filename = 'khayra_inventory_export_' . now()->format('Ymd_His') . '.csv';

    return response()->streamDownload(function () {
        $handle = fopen('php://output', 'w');

        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($handle, [
            'sku',
            'name',
            'category',
            'unit',
            'stock',
            'minimum_stock',
            'purchase_price',
            'selling_price',
            'supplier',
            'storage_location',
            'status',
            'notes',
        ]);

        InventoryItem::orderBy('name')->chunk(200, function ($items) use ($handle) {
            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->sku,
                    $item->name,
                    $item->category,
                    $item->unit,
                    $item->stock,
                    $item->minimum_stock,
                    $item->purchase_price,
                    $item->selling_price,
                    $item->supplier,
                    $item->storage_location,
                    $item->status,
                    $item->notes,
                ]);
            }
        });

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
});

Route::get('/admin/inventory/import/template', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $filename = 'khayra_inventory_import_template.csv';

    return response()->streamDownload(function () {
        $handle = fopen('php://output', 'w');

        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($handle, [
            'sku',
            'name',
            'category',
            'unit',
            'stock',
            'minimum_stock',
            'purchase_price',
            'selling_price',
            'supplier',
            'storage_location',
            'status',
            'notes',
        ]);

        fputcsv($handle, [
            'PHY-001',
            'Kinesio Tape',
            'Consumable',
            'pcs',
            '20',
            '5',
            '35000',
            '55000',
            'Supplier A',
            'Lemari A',
            'active',
            'Contoh barang klinik',
        ]);

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv; charset=UTF-8',
    ]);
});

Route::post('/admin/inventory/import', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = fopen($request->file('csv_file')->getRealPath(), 'r');

    $header = fgetcsv($file);
    if (!$header) {
        return redirect('/admin/inventory/import')->with('error', 'CSV kosong atau format tidak terbaca.');
    }

    $header = array_map(function ($value) {
        return trim(strtolower(str_replace("\xEF\xBB\xBF", '', $value)));
    }, $header);

    $created = 0;
    $updated = 0;
    $skipped = 0;

    while (($row = fgetcsv($file)) !== false) {
        $data = array_combine($header, array_pad($row, count($header), null));

        if (!$data || empty(trim($data['sku'] ?? '')) || empty(trim($data['name'] ?? ''))) {
            $skipped++;
            continue;
        }

        $sku = trim($data['sku']);
        $stock = (int) ($data['stock'] ?? 0);

        $item = InventoryItem::where('sku', $sku)->first();

        if ($item) {
            $before = (int) $item->stock;

            $item->update([
                'name' => trim($data['name']),
                'category' => trim($data['category'] ?? ''),
                'unit' => trim($data['unit'] ?? 'pcs') ?: 'pcs',
                'stock' => $stock,
                'minimum_stock' => (int) ($data['minimum_stock'] ?? 0),
                'purchase_price' => (float) ($data['purchase_price'] ?? 0),
                'selling_price' => (float) ($data['selling_price'] ?? 0),
                'supplier' => trim($data['supplier'] ?? ''),
                'storage_location' => trim($data['storage_location'] ?? ''),
                'status' => in_array(($data['status'] ?? 'active'), ['active', 'inactive']) ? $data['status'] : 'active',
                'notes' => trim($data['notes'] ?? ''),
            ]);

            if ($before !== $stock) {
                InventoryStockMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => 'adjustment',
                    'quantity' => abs($stock - $before),
                    'stock_before' => $before,
                    'stock_after' => $stock,
                    'reference' => 'CSV Import',
                    'notes' => 'Update stok dari import CSV.',
                ]);
            }

            $updated++;
        } else {
            $item = InventoryItem::create([
                'sku' => $sku,
                'name' => trim($data['name']),
                'category' => trim($data['category'] ?? ''),
                'unit' => trim($data['unit'] ?? 'pcs') ?: 'pcs',
                'stock' => $stock,
                'minimum_stock' => (int) ($data['minimum_stock'] ?? 0),
                'purchase_price' => (float) ($data['purchase_price'] ?? 0),
                'selling_price' => (float) ($data['selling_price'] ?? 0),
                'supplier' => trim($data['supplier'] ?? ''),
                'storage_location' => trim($data['storage_location'] ?? ''),
                'status' => in_array(($data['status'] ?? 'active'), ['active', 'inactive']) ? $data['status'] : 'active',
                'notes' => trim($data['notes'] ?? ''),
            ]);

            if ($stock > 0) {
                InventoryStockMovement::create([
                    'inventory_item_id' => $item->id,
                    'type' => 'in',
                    'quantity' => $stock,
                    'stock_before' => 0,
                    'stock_after' => $stock,
                    'reference' => 'CSV Import',
                    'notes' => 'Stok awal dari import CSV.',
                ]);
            }

            $created++;
        }
    }

    fclose($file);

    return redirect('/admin/inventory')->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
});

Route::get('/admin/inventory/monthly-summary', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $month = $request->query('month', now()->format('Y-m'));
    $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $end = (clone $start)->endOfMonth();

    $items = InventoryItem::orderBy('name')->get();
    $movements = InventoryStockMovement::with('item')
        ->whereBetween('created_at', [$start, $end])
        ->get();

    $rows = $items->map(function ($item) use ($movements) {
        $itemMovements = $movements->where('inventory_item_id', $item->id);

        $stockIn = $itemMovements->where('type', 'in')->sum('quantity');
        $stockOut = $itemMovements->where('type', 'out')->sum('quantity');
        $adjustments = $itemMovements->where('type', 'adjustment');

        $adjustmentNet = 0;
        foreach ($adjustments as $movement) {
            $adjustmentNet += ((int) $movement->stock_after - (int) $movement->stock_before);
        }

        $closing = (int) $item->stock;
        $opening = $closing - $stockIn + $stockOut - $adjustmentNet;

        return [
            'item' => $item,
            'opening' => $opening,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'adjustment' => $adjustmentNet,
            'closing' => $closing,
            'value' => $closing * (float) $item->purchase_price,
        ];
    });

    $summary = [
        'total_items' => $items->count(),
        'total_in' => $rows->sum('stock_in'),
        'total_out' => $rows->sum('stock_out'),
        'total_value' => $rows->sum('value'),
    ];

    return view('admin-inventory-monthly-summary', compact('month', 'rows', 'summary'));
});

Route::post('/admin/inventory/bulk-create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'bulk_items' => 'required|string',
    ]);

    $lines = preg_split('/\r\n|\r|\n/', trim($request->bulk_items));
    $created = 0;
    $skipped = 0;

    foreach ($lines as $line) {
        $parts = array_map('trim', explode('|', $line));

        if (count($parts) < 6) {
            $skipped++;
            continue;
        }

        [$sku, $name, $category, $unit, $stock, $minimumStock] = array_pad($parts, 10, '');

        if (!$sku || !$name || InventoryItem::where('sku', $sku)->exists()) {
            $skipped++;
            continue;
        }

        $item = InventoryItem::create([
            'sku' => $sku,
            'name' => $name,
            'category' => $category,
            'unit' => $unit ?: 'pcs',
            'stock' => (int) $stock,
            'minimum_stock' => (int) $minimumStock,
            'purchase_price' => (float) ($parts[6] ?? 0),
            'selling_price' => (float) ($parts[7] ?? 0),
            'supplier' => $parts[8] ?? '',
            'storage_location' => $parts[9] ?? '',
            'status' => 'active',
            'notes' => 'Bulk input',
        ]);

        if ((int) $stock > 0) {
            InventoryStockMovement::create([
                'inventory_item_id' => $item->id,
                'type' => 'in',
                'quantity' => (int) $stock,
                'stock_before' => 0,
                'stock_after' => (int) $stock,
                'reference' => 'Bulk Input',
                'notes' => 'Stok awal dari bulk input.',
            ]);
        }

        $created++;
    }

    return redirect('/admin/inventory')->with('success', "Bulk input selesai. Created: {$created}, Skipped: {$skipped}.");
});



Route::post('/admin/inventory/bulk-action', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'item_ids' => 'required|array',
        'item_ids.*' => 'exists:inventory_items,id',
        'bulk_action' => 'required|in:set_active,set_inactive,update_category,update_location',
        'bulk_value' => 'nullable|string|max:255',
    ]);

    $items = InventoryItem::whereIn('id', $request->item_ids)->get();
    $count = 0;

    foreach ($items as $item) {
        if ($request->bulk_action === 'set_active') {
            $item->update(['status' => 'active']);
        }

        if ($request->bulk_action === 'set_inactive') {
            $item->update(['status' => 'inactive']);
        }

        if ($request->bulk_action === 'update_category') {
            if (!$request->bulk_value) {
                return redirect('/admin/inventory')->with('error', 'Isi kategori baru terlebih dahulu.');
            }

            $item->update(['category' => $request->bulk_value]);
        }

        if ($request->bulk_action === 'update_location') {
            if (!$request->bulk_value) {
                return redirect('/admin/inventory')->with('error', 'Isi lokasi penyimpanan baru terlebih dahulu.');
            }

            $item->update(['storage_location' => $request->bulk_value]);
        }

        $count++;
    }

    return redirect('/admin/inventory')->with('success', "Bulk action berhasil diterapkan ke {$count} barang.");
});

Route::get('/admin/inventory/create', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return view('admin-inventory-create');
});

Route::post('/admin/inventory', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'sku' => 'required|string|max:80|unique:inventory_items,sku',
        'name' => 'required|string|max:255',
        'category' => 'nullable|string|max:120',
        'unit' => 'required|string|max:50',
        'stock' => 'required|integer|min:0',
        'minimum_stock' => 'required|integer|min:0',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'supplier' => 'nullable|string|max:255',
        'storage_location' => 'nullable|string|max:255',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
    ]);

    $item = InventoryItem::create($request->only([
        'sku',
        'name',
        'category',
        'unit',
        'stock',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'supplier',
        'storage_location',
        'status',
        'notes',
    ]));

    if ($item->stock > 0) {
        InventoryStockMovement::create([
            'inventory_item_id' => $item->id,
            'type' => 'in',
            'quantity' => $item->stock,
            'stock_before' => 0,
            'stock_after' => $item->stock,
            'reference' => 'Initial Stock',
            'notes' => 'Stok awal saat barang dibuat.',
        ]);
    }

    return redirect('/admin/inventory/' . $item->id)->with('success', 'Barang inventory berhasil ditambahkan.');
});


Route::get('/admin/inventory/export', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $filename = 'khayra-inventory-export-' . now()->format('Ymd-His') . '.csv';

    $items = InventoryItem::orderBy('name')->get();

    return response()->streamDownload(function () use ($items) {
        $handle = fopen('php://output', 'w');

        fputcsv($handle, [
            'sku',
            'name',
            'category',
            'unit',
            'stock',
            'minimum_stock',
            'purchase_price',
            'selling_price',
            'supplier',
            'storage_location',
            'status',
            'notes',
        ]);

        foreach ($items as $item) {
            fputcsv($handle, [
                $item->sku,
                $item->name,
                $item->category,
                $item->unit,
                $item->stock,
                $item->minimum_stock,
                $item->purchase_price,
                $item->selling_price,
                $item->supplier,
                $item->storage_location,
                $item->status,
                $item->notes,
            ]);
        }

        fclose($handle);
    }, $filename, [
        'Content-Type' => 'text/csv',
    ]);
});

Route::post('/admin/inventory/import', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'inventory_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('inventory_file');
    $handle = fopen($file->getRealPath(), 'r');

    if (!$handle) {
        return redirect('/admin/inventory')->with('error', 'File CSV tidak bisa dibaca.');
    }

    $header = fgetcsv($handle);

    if (!$header) {
        fclose($handle);
        return redirect('/admin/inventory')->with('error', 'CSV kosong atau format tidak valid.');
    }

    $header = array_map(fn ($value) => trim(strtolower($value)), $header);

    $requiredColumns = ['sku', 'name'];
    foreach ($requiredColumns as $column) {
        if (!in_array($column, $header)) {
            fclose($handle);
            return redirect('/admin/inventory')->with('error', "Kolom wajib {$column} belum ada di CSV.");
        }
    }

    $created = 0;
    $updated = 0;
    $skipped = 0;

    while (($row = fgetcsv($handle)) !== false) {
        if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
            continue;
        }

        $data = [];

        foreach ($header as $index => $column) {
            $data[$column] = isset($row[$index]) ? trim($row[$index]) : null;
        }

        if (empty($data['sku']) || empty($data['name'])) {
            $skipped++;
            continue;
        }

        $existingItem = InventoryItem::where('sku', $data['sku'])->first();
        $stockBefore = $existingItem ? (int) $existingItem->stock : 0;

        $stock = isset($data['stock']) && $data['stock'] !== '' ? (int) $data['stock'] : $stockBefore;

        $payload = [
            'sku' => $data['sku'],
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'unit' => $data['unit'] ?: 'pcs',
            'stock' => $stock,
            'minimum_stock' => isset($data['minimum_stock']) && $data['minimum_stock'] !== '' ? (int) $data['minimum_stock'] : 0,
            'purchase_price' => isset($data['purchase_price']) && $data['purchase_price'] !== '' ? (float) $data['purchase_price'] : 0,
            'selling_price' => isset($data['selling_price']) && $data['selling_price'] !== '' ? (float) $data['selling_price'] : 0,
            'supplier' => $data['supplier'] ?? null,
            'storage_location' => $data['storage_location'] ?? null,
            'status' => in_array(($data['status'] ?? 'active'), ['active', 'inactive']) ? $data['status'] : 'active',
            'notes' => $data['notes'] ?? null,
        ];

        $item = InventoryItem::updateOrCreate(
            ['sku' => $data['sku']],
            $payload
        );

        if ($existingItem) {
            $updated++;
        } else {
            $created++;
        }

        $stockAfter = (int) $item->stock;

        if ($stockBefore !== $stockAfter) {
            InventoryStockMovement::create([
                'inventory_item_id' => $item->id,
                'type' => 'adjustment',
                'quantity' => abs($stockAfter - $stockBefore),
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference' => 'CSV Import',
                'notes' => 'Stock adjusted from inventory CSV import.',
            ]);
        }
    }

    fclose($handle);

    return redirect('/admin/inventory')->with('success', "Import selesai. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
});


Route::get('/admin/inventory/stock-opname', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $items = InventoryItem::orderBy('name')->get();
    $selectedItemId = $request->query('item_id');

    return view('admin-inventory-stock-opname', compact('items', 'selectedItemId'));
});

Route::post('/admin/inventory/stock-opname', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'inventory_item_id' => 'required|exists:inventory_items,id',
        'physical_stock' => 'required|integer|min:0',
        'reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    $item = InventoryItem::findOrFail($request->inventory_item_id);

    $stockBefore = (int) $item->stock;
    $stockAfter = (int) $request->physical_stock;
    $difference = $stockAfter - $stockBefore;

    if ($difference !== 0 && !trim((string) $request->notes)) {
        return back()
            ->withInput()
            ->withErrors(['notes' => 'Catatan wajib diisi jika ada selisih antara stok sistem dan stok fisik.']);
    }

    $reference = $request->reference ?: 'Stock Opname ' . now()->format('Y-m-d');

    if ($stockBefore !== $stockAfter) {
        $item->update([
            'stock' => $stockAfter,
        ]);
    }

    InventoryStockMovement::create([
        'inventory_item_id' => $item->id,
        'type' => 'adjustment',
        'quantity' => abs($difference),
        'stock_before' => $stockBefore,
        'stock_after' => $stockAfter,
        'reference' => $reference,
        'notes' => $request->notes ?: 'Stock opname dicatat tanpa perubahan stok.',
    ]);

    if ($difference === 0) {
        return redirect('/admin/inventory/' . $item->id)
            ->with('success', 'Stock opname tersimpan. Tidak ada selisih stok.');
    }

    return redirect('/admin/inventory/' . $item->id)
        ->with('success', 'Stock opname berhasil disimpan. Stok sistem diperbarui dari ' . $stockBefore . ' menjadi ' . $stockAfter . '.');
});

Route::get('/admin/inventory/monthly-summary', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $month = $request->query('month', now()->format('Y-m'));
    $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    $movements = InventoryStockMovement::with('inventoryItem')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->latest()
        ->get();

    $items = InventoryItem::orderBy('name')->get();

    $summary = [
        'month' => $startDate->format('F Y'),
        'total_items' => $items->count(),
        'active_items' => $items->where('status', 'active')->count(),
        'low_stock_items' => $items->filter(fn ($item) => (int) $item->stock <= (int) $item->minimum_stock)->count(),
        'stock_value' => $items->sum(fn ($item) => (float) $item->stock * (float) $item->purchase_price),
        'potential_sales' => $items->sum(fn ($item) => (float) $item->stock * (float) $item->selling_price),
        'stock_in' => $movements->where('type', 'in')->sum('quantity'),
        'stock_out' => $movements->where('type', 'out')->sum('quantity'),
        'adjustments' => $movements->where('type', 'adjustment')->count(),
    ];

    $byItem = $movements
        ->groupBy('inventory_item_id')
        ->map(function ($rows) {
            $item = $rows->first()->inventoryItem;

            return [
                'item' => $item,
                'stock_in' => $rows->where('type', 'in')->sum('quantity'),
                'stock_out' => $rows->where('type', 'out')->sum('quantity'),
                'adjustments' => $rows->where('type', 'adjustment')->count(),
                'last_stock' => $item ? $item->stock : 0,
            ];
        })
        ->values();

    return view('admin-inventory-monthly-summary', compact('summary', 'movements', 'byItem', 'month'));
});


Route::get('/admin/inventory/product-usage', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = $request->query('search');
    $month = $request->query('month', now()->format('Y-m'));
    $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    $query = BillingItem::with([
            'billing.patient',
            'billing.visit.therapistRelation',
            'inventoryItem',
        ])
        ->where('item_type', 'inventory')
        ->whereNotNull('inventory_item_id')
        ->whereHas('billing', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()])
              ->where(function ($statusQuery) {
                  $statusQuery->whereNull('payment_status')
                      ->orWhere('payment_status', '!=', 'void');
              });
        });

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('description', 'like', '%' . $search . '%')
              ->orWhereHas('inventoryItem', function ($itemQuery) use ($search) {
                  $itemQuery->where('name', 'like', '%' . $search . '%')
                      ->orWhere('sku', 'like', '%' . $search . '%')
                      ->orWhere('category', 'like', '%' . $search . '%');
              })
              ->orWhereHas('billing.patient', function ($patientQuery) use ($search) {
                  $patientQuery->where('full_name', 'like', '%' . $search . '%');
              });
        });
    }

    $usageItems = $query
        ->latest()
        ->get();

    $totalUsageLines = $usageItems->count();
    $totalQuantityUsed = $usageItems->sum('quantity');
    $totalUsageValue = $usageItems->sum('line_total');

    $usageByProduct = $usageItems
        ->groupBy('inventory_item_id')
        ->map(function ($rows) {
            $first = $rows->first();
            return [
                'item' => $first->inventoryItem,
                'description' => $first->description,
                'quantity' => $rows->sum('quantity'),
                'value' => $rows->sum('line_total'),
                'lines' => $rows->count(),
            ];
        })
        ->sortByDesc('quantity')
        ->values();

    return view('admin-inventory-product-usage', compact(
        'usageItems',
        'usageByProduct',
        'totalUsageLines',
        'totalQuantityUsed',
        'totalUsageValue',
        'month',
        'search',
        'startDate',
        'endDate'
    ));
});


Route::get('/admin/inventory/{id}/movements', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $item = InventoryItem::findOrFail($id);

    $movements = InventoryStockMovement::where('inventory_item_id', $item->id)
        ->latest()
        ->get();

    $summary = [
        'current_stock' => (int) $item->stock,
        'stock_in' => $movements->where('type', 'in')->sum('quantity'),
        'stock_out' => $movements->where('type', 'out')->sum('quantity'),
        'adjustments' => $movements->where('type', 'adjustment')->count(),
    ];

    return view('admin-inventory-movements', compact('item', 'movements', 'summary'));
})->whereNumber('id');

Route::get('/admin/inventory/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $item = InventoryItem::with(['movements' => function ($query) {
        $query->latest();
    }])->findOrFail($id);

    return view('admin-inventory-detail', compact('item'));
});

Route::get('/admin/inventory/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $item = InventoryItem::findOrFail($id);

    return view('admin-inventory-edit', compact('item'));
});

Route::post('/admin/inventory/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $item = InventoryItem::findOrFail($id);

    $request->validate([
        'sku' => 'required|string|max:80|unique:inventory_items,sku,' . $item->id,
        'name' => 'required|string|max:255',
        'category' => 'nullable|string|max:120',
        'unit' => 'required|string|max:50',
        'minimum_stock' => 'required|integer|min:0',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'supplier' => 'nullable|string|max:255',
        'storage_location' => 'nullable|string|max:255',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
    ]);

    $item->update($request->only([
        'sku',
        'name',
        'category',
        'unit',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'supplier',
        'storage_location',
        'status',
        'notes',
    ]));

    return redirect('/admin/inventory/' . $item->id)->with('success', 'Data barang berhasil diperbarui.');
});

Route::post('/admin/inventory/{id}/movement', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $item = InventoryItem::findOrFail($id);

    $request->validate([
        'type' => 'required|in:in,out,adjustment',
        'quantity' => 'required|integer|min:1',
        'reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    $stockBefore = $item->stock;
    $quantity = (int) $request->quantity;

    if ($request->type === 'in') {
        $stockAfter = $stockBefore + $quantity;
    } elseif ($request->type === 'out') {
        if ($quantity > $stockBefore) {
            return redirect('/admin/inventory/' . $item->id)->with('error', 'Stok keluar melebihi stok tersedia.');
        }

        $stockAfter = $stockBefore - $quantity;
    } else {
        $stockAfter = $quantity;
    }

    $item->update(['stock' => $stockAfter]);

    InventoryStockMovement::create([
        'inventory_item_id' => $item->id,
        'type' => $request->type,
        'quantity' => $quantity,
        'stock_before' => $stockBefore,
        'stock_after' => $stockAfter,
        'reference' => $request->reference,
        'notes' => $request->notes,
    ]);

    return redirect('/admin/inventory/' . $item->id)->with('success', 'Pergerakan stok berhasil disimpan.');
});


Route::get('/admin/referral-letters', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $letters = ReferralLetter::with(['patient', 'visit'])->latest()->get();

    return view('admin-referral-letters', compact('letters'));
});

Route::get('/admin/referral-letters/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::latest()->get();
    $visits = Visit::with('patient')->latest()->get();

    $selectedVisit = null;
    $selectedPatientId = $request->query('patient_id');

    if ($request->query('visit_id')) {
        $selectedVisit = Visit::with(['patient', 'medicalRecord'])->find($request->query('visit_id'));

        if ($selectedVisit) {
            $selectedPatientId = $selectedVisit->patient_id;
        }
    }

    return view('admin-referral-letter-create', compact('patients', 'visits', 'selectedVisit', 'selectedPatientId'));
});

Route::post('/admin/referral-letters', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'letter_date' => 'required|date',
        'referral_to' => 'nullable|string|max:255',
        'referral_reason' => 'nullable|string',
        'clinical_summary' => 'nullable|string',
        'recommendation' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $letterNumber = 'REF-' . now()->format('Ymd') . '-' . str_pad((ReferralLetter::count() + 1), 4, '0', STR_PAD_LEFT);

    $letter = ReferralLetter::create([
        'patient_id' => $request->patient_id,
        'visit_id' => $request->visit_id ?: null,
        'letter_number' => $letterNumber,
        'letter_date' => $request->letter_date,
        'referral_to' => $request->referral_to,
        'referral_reason' => $request->referral_reason,
        'clinical_summary' => $request->clinical_summary,
        'recommendation' => $request->recommendation,
        'notes' => $request->notes,
    ]);

    return redirect('/admin/referral-letters/' . $letter->id)->with('success', 'Surat rujukan berhasil dibuat.');
});

Route::get('/admin/referral-letters/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $letter = ReferralLetter::with(['patient', 'visit'])->findOrFail($id);

    return view('admin-referral-letter-detail', compact('letter'));
});

Route::get('/admin/referral-letters/{id}/print', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $letter = ReferralLetter::with(['patient', 'visit'])->findOrFail($id);

    return view('admin-referral-letter-print', compact('letter'));
});


Route::get('/admin/visits', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $visits = Visit::with(['patient', 'booking', 'medicalRecord', 'therapistRelation'])->latest()->get();
    return view('admin-visits', compact('visits'));
});

Route::get('/admin/visits/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::latest()->get();
    $bookings = Booking::latest()->get();
    $therapists = Therapist::latest()->get();

    $selectedBooking = null;

    if ($request->query('booking_id')) {
        $selectedBooking = Booking::with('patient')->find($request->query('booking_id'));
    }

    $roomOptions = [
        'VIP 1',
        'VIP 2',
        'R.1.1',
        'R.2.1',
        'R.2.2',
        'R.2.3',
        'R.2.4',
        'R.2.5',
        'R. GYM',
        'R. Studio Pilates',
    ];

    return view('admin-visit-create', compact('patients', 'bookings', 'therapists', 'selectedBooking', 'roomOptions'));
});

Route::post('/admin/visits', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'therapist_id' => 'required|exists:therapists,id',
        'room_name' => 'nullable|string|max:255',
        'visit_date' => 'required|date',
        'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        'notes' => 'nullable|string',
        'booking_id' => 'nullable|exists:bookings,id',
    ]);

    $therapist = Therapist::where('status', 'active')->findOrFail($request->therapist_id);
    $linkedBooking = $request->booking_id ? Booking::find($request->booking_id) : null;

    Visit::create([
        'patient_id' => $request->patient_id,
        'therapist_id' => $therapist->id,
        'room_name' => $request->room_name ?: optional($linkedBooking)->room_name,
        'booking_id' => $request->booking_id ?: null,
        'visit_date' => $request->visit_date,
        'therapist' => $therapist->full_name,
        'notes' => $request->notes,
        'status' => $request->status,
    ]);

    return redirect('/admin/visits')->with('success', 'Visit berhasil ditambahkan!');
});

Route::get('/admin/visits/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $visit = Visit::findOrFail($id);
    $patients = Patient::latest()->get();
    $bookings = Booking::with('patient')->latest()->get();
    $therapists = Therapist::where('status', 'active')
        ->orWhere('id', $visit->therapist_id)
        ->latest()
        ->get();

    $roomOptions = [
        'VIP 1',
        'VIP 2',
        'R.1.1',
        'R.2.1',
        'R.2.2',
        'R.2.3',
        'R.2.4',
        'R.2.5',
        'R. GYM',
        'R. Studio Pilates',
    ];

    return view('admin-visit-edit', compact('visit', 'patients', 'bookings', 'therapists', 'roomOptions'));
});

Route::post('/admin/visits/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'therapist_id' => 'required|exists:therapists,id',
        'room_name' => 'nullable|string|max:255',
        'visit_date' => 'required|date',
        'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        'notes' => 'nullable|string',
        'booking_id' => 'nullable|exists:bookings,id',
    ]);

    $visit = Visit::findOrFail($id);
    $therapist = Therapist::findOrFail($request->therapist_id);

    $visit->patient_id = $request->patient_id;
    $visit->therapist_id = $therapist->id;
    $visit->room_name = $request->room_name;
    $visit->booking_id = $request->booking_id ?: null;
    $visit->visit_date = $request->visit_date;
    $visit->therapist = $therapist->full_name;
    $visit->notes = $request->notes;
    $visit->status = $request->status;
    $visit->save();

    return redirect('/admin/visits')->with('success', 'Visit berhasil diperbarui!');
});



Route::get('/admin/visits/{id}/report', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $visit = Visit::with([
        'patient',
        'therapistRelation',
        'medicalRecord.histories',
        'medicalRecord.comorbidities',
        'medicalRecord.supportingData',
        'medicalRecord.homeExercises',
    ])->findOrFail($id);

    return view('therapist-report', compact('visit') + ['reportContext' => 'therapist']);
});

Route::get('/admin/visits/{id}/report/print', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $visit = Visit::with([
        'patient',
        'therapistRelation',
        'medicalRecord.histories',
        'medicalRecord.comorbidities',
        'medicalRecord.supportingData',
        'medicalRecord.homeExercises',
    ])->findOrFail($id);

    return view('therapist-report-print', compact('visit') + ['reportContext' => 'therapist']);
});

Route::get('/admin/visits/{id}/medical-record', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $visit = Visit::with(['patient', 'therapistRelation'])->findOrFail($id);

    $record = MedicalRecord::firstOrCreate(
        ['visit_id' => $visit->id],
        [
            'patient_id' => $visit->patient_id,
            'subjective' => '',
            'objective' => '',
            'assessment' => '',
            'plan' => '',
        ]
    );

    $record->load([
        'histories',
        'comorbidities',
        'supportingData',
        'homeExercises',
    ]);

    return view('admin-medical-record', compact('visit', 'record'));
});

Route::post('/admin/visits/{id}/medical-record', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'complaint' => 'nullable|string',
        'assessment' => 'nullable|string',
        'treatment' => 'nullable|string',
        'progress_note' => 'nullable|string',
        'recommendation' => 'nullable|string',
    ]);

    $visit = Visit::findOrFail($id);

    MedicalRecord::updateOrCreate(
        ['visit_id' => $visit->id],
        [
            'complaint' => $request->complaint,
            'assessment' => $request->assessment,
            'treatment' => $request->treatment,
            'progress_note' => $request->progress_note,
            'recommendation' => $request->recommendation,
        ]
    );

    return redirect('/admin/visits/' . $visit->id . '/medical-record')
        ->with('success', 'Medical record berhasil disimpan!');
});

Route::get('/admin/therapists', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $therapists = Therapist::latest()->get();
    return view('admin-therapists', compact('therapists'));
});

Route::get('/admin/therapists/create', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return view('admin-therapist-create');
});

Route::post('/admin/therapists', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:therapists,email',
        'whatsapp' => 'nullable|string|max:50',
        'specialty' => 'nullable|string|max:255',
        'password' => 'required|string|min:6',
        'status' => 'required|in:active,inactive',
    ]);

    Therapist::create([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'whatsapp' => $request->whatsapp,
        'specialty' => $request->specialty,
        'password' => Hash::make($request->password),
        'status' => $request->status ?: 'active',
    ]);

    return redirect('/admin/therapists')->with('success', 'Therapist berhasil ditambahkan!');
});

Route::get('/admin/therapists/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $therapist = Therapist::findOrFail($id);
    return view('admin-therapist-edit', compact('therapist'));
});

Route::post('/admin/therapists/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $therapist = Therapist::findOrFail($id);

    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:therapists,email,' . $therapist->id,
        'whatsapp' => 'nullable|string|max:50',
        'specialty' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:6',
        'status' => 'required|in:active,inactive',
    ]);

    $therapist->full_name = $request->full_name;
    $therapist->email = $request->email;
    $therapist->whatsapp = $request->whatsapp;
    $therapist->specialty = $request->specialty;
    $therapist->status = $request->status ?: 'active';

    if ($request->password) {
        $therapist->password = Hash::make($request->password);
    }

    $therapist->save();

    return redirect('/admin/therapists')->with('success', 'Data therapist berhasil diperbarui!');
});

Route::post('/admin/therapists/{id}/status', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'status' => 'required|in:active,inactive',
    ]);

    $therapist = Therapist::findOrFail($id);
    $therapist->status = $request->status;
    $therapist->save();

    return redirect('/admin/therapists')->with('success', 'Status therapist berhasil diperbarui!');
});

Route::get('/admin/therapist-availabilities', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $selectedMonth = $request->query('month') ?: now()->format('Y-m');
    $monthStart = \Carbon\Carbon::parse($selectedMonth . '-01')->startOfMonth();
    $monthEnd = $monthStart->copy()->endOfMonth();
    $monthLabel = $monthStart->format('F Y');

    $therapists = Therapist::where('status', 'active')->orderBy('full_name')->get();

    $availabilities = TherapistAvailability::with('therapist')
        ->where(function ($query) use ($monthEnd) {
            $query->whereNull('valid_from')
                ->orWhereDate('valid_from', '<=', $monthEnd->toDateString());
        })
        ->where(function ($query) use ($monthStart) {
            $query->whereNull('valid_until')
                ->orWhereDate('valid_until', '>=', $monthStart->toDateString());
        })
        ->orderBy('therapist_id')
        ->orderBy('valid_from')
        ->orderBy('valid_until')
        ->orderBy('day_of_week')
        ->orderBy('start_time')
        ->get();

    $availabilityGroups = $availabilities->groupBy(function ($item) {
        return implode('|', [
            $item->therapist_id,
            optional($item->valid_from)->format('Y-m-d') ?: 'open',
            optional($item->valid_until)->format('Y-m-d') ?: 'open',
            substr((string) $item->start_time, 0, 5),
            substr((string) $item->end_time, 0, 5),
            $item->slot_duration_minutes,
            $item->capacity,
            $item->status,
            $item->notes,
        ]);
    });

    $dayLabels = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    return view('admin-therapist-availabilities', compact(
        'therapists',
        'availabilities',
        'availabilityGroups',
        'dayLabels',
        'selectedMonth',
        'monthLabel',
        'monthStart',
        'monthEnd'
    ));
});

Route::post('/admin/therapist-availabilities', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'therapist_id' => 'required|exists:therapists,id',
        'valid_from' => 'required|date',
        'valid_until' => 'required|date|after_or_equal:valid_from',
        'day_of_week' => 'required|array|min:1',
        'day_of_week.*' => 'required|integer|min:1|max:7',
        'start_time' => 'required',
        'end_time' => 'required',
        'slot_duration_minutes' => 'required|integer|min:15|max:240',
        'capacity' => 'required|integer|min:1|max:20',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
        'replace_existing' => 'nullable|boolean',
    ]);

    if ($request->start_time >= $request->end_time) {
        return back()
            ->withInput()
            ->withErrors(['end_time' => 'Jam selesai harus lebih besar dari jam mulai.']);
    }

    $selectedDays = collect($request->day_of_week)
        ->map(fn ($day) => (int) $day)
        ->unique()
        ->values();

    DB::transaction(function () use ($request, $selectedDays) {
        if ($request->boolean('replace_existing')) {
            TherapistAvailability::where('therapist_id', $request->therapist_id)
                ->whereIn('day_of_week', $selectedDays)
                ->whereDate('valid_from', $request->valid_from)
                ->whereDate('valid_until', $request->valid_until)
                ->delete();
        }

        foreach ($selectedDays as $day) {
            TherapistAvailability::create([
                'therapist_id' => $request->therapist_id,
                'valid_from' => $request->valid_from,
                'valid_until' => $request->valid_until,
                'day_of_week' => $day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'slot_duration_minutes' => $request->slot_duration_minutes,
                'capacity' => $request->capacity,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);
        }
    });

    return redirect('/admin/therapist-availabilities?month=' . substr($request->valid_from, 0, 7))
        ->with('success', 'Availability therapist berhasil ditambahkan untuk ' . $selectedDays->count() . ' hari.');
});

Route::post('/admin/therapist-availabilities/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $availability = TherapistAvailability::findOrFail($id);
    $redirectMonth = $availability->valid_from ? $availability->valid_from->format('Y-m') : now()->format('Y-m');

    $availability->delete();

    return redirect('/admin/therapist-availabilities?month=' . $redirectMonth)
        ->with('success', 'Availability therapist berhasil dihapus.');
});


Route::get('/therapist', function () {
    if (session('therapist_logged_in')) {
        return redirect('/therapist/dashboard');
    }

    return redirect('/therapist/login');
});

Route::get('/therapist/login', function () {
    if (session('therapist_logged_in')) {
        return redirect('/therapist/dashboard');
    }

    return view('therapist-login');
});

Route::post('/therapist/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $therapist = Therapist::where('email', $request->email)
        ->where('status', 'active')
        ->first();

    if ($therapist && Hash::check($request->password, $therapist->password)) {
        session([
            'therapist_logged_in' => true,
            'therapist_id' => $therapist->id,
            'therapist_name' => $therapist->full_name,
        ]);

        return redirect('/therapist/dashboard');
    }

    return redirect('/therapist/login')->with('error', 'Email atau password therapist salah.');
});

Route::post('/therapist/logout', function () {
    session()->forget('therapist_logged_in');
    session()->forget('therapist_id');
    session()->forget('therapist_name');

    return redirect('/therapist/login');
});

Route::get('/therapist/dashboard', function () {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    $therapistId = session('therapist_id');
    $today = now()->toDateString();

    $assignedBookings = Booking::with(['patient', 'visits'])
        ->where('therapist_id', $therapistId)
        ->whereIn('status', ['pending', 'confirmed', 'arrived', 'in_treatment'])
        ->orderBy('booking_date')
        ->orderBy('booking_time')
        ->get()
        ->map(function ($booking) {
            $booking->linked_visit = $booking->visits->first();
            return $booking;
        });

    $clinicScheduleBookings = Booking::with(['patient', 'visits'])
        ->whereIn('status', ['pending', 'confirmed', 'arrived', 'in_treatment'])
        ->orderBy('booking_date')
        ->orderBy('booking_time')
        ->get()
        ->map(function ($booking) {
            $booking->linked_visit = $booking->visits->first();
            return $booking;
        });

    $todayAppointments = $clinicScheduleBookings
        ->filter(fn ($booking) => $booking->booking_date == now()->toDateString())
        ->values();

    $upcomingAppointments = $clinicScheduleBookings
        ->filter(fn ($booking) => $booking->booking_date >= now()->toDateString())
        ->take(12)
        ->values();


    $visits = Visit::with(['patient', 'medicalRecord.homeExercises'])
        ->where('therapist_id', $therapistId)
        ->latest()
        ->get();

    $todayVisits = $visits->filter(fn ($visit) => $visit->visit_date == $today)->values();

    $totalVisits = $visits->count();
    $scheduledVisits = $visits->where('status', 'scheduled')->count();
    $inProgressVisits = $visits->where('status', 'in_progress')->count();
    $completedVisits = $visits->where('status', 'completed')->count();

    $recordRequiredFields = [
        'complaint',
        'pain_scale',
        'subjective_examination',
        'objective_examination',
        'physiotherapy_diagnosis',
        'impairment',
        'patient_goal',
        'program_patient',
        'treatment_given',
        'response_to_treatment',
        'next_session_plan',
    ];

    $visits = $visits->map(function ($visit) use ($recordRequiredFields) {
        $record = $visit->medicalRecord;

        $completedFields = collect($recordRequiredFields)->filter(function ($field) use ($record) {
            return $record && !blank($record->{$field});
        })->count();

        $completion = count($recordRequiredFields) > 0
            ? round(($completedFields / count($recordRequiredFields)) * 100)
            : 0;

        $visit->record_completion = $completion;
        $visit->record_completed_fields = $completedFields;
        $visit->record_total_fields = count($recordRequiredFields);
        $visit->record_status_label = $completion >= 90 ? 'Complete' : ($completion > 0 ? 'In Progress' : 'Not Started');

        return $visit;
    });

    $todayVisits = $visits->filter(fn ($visit) => $visit->visit_date == $today)->values();
    $needCompletionVisits = $visits->filter(fn ($visit) => $visit->record_completion < 90)->take(6)->values();
    $completedRecordVisits = $visits->filter(fn ($visit) => $visit->record_completion >= 90)->count();

    return view('therapist-dashboard', compact(
        'visits',
        'todayVisits',
        'needCompletionVisits',
        'totalVisits',
        'scheduledVisits',
        'inProgressVisits',
        'completedVisits',
        'completedRecordVisits',
        'assignedBookings',
        'todayAppointments',
        'upcomingAppointments'
    ));
});

Route::get('/therapist/visits/{id}/medical-record', function ($id) {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    $therapistId = session('therapist_id');

    $visit = Visit::with([
        'patient',
        'therapistRelation',
        'medicalRecord.histories',
        'medicalRecord.comorbidities',
        'medicalRecord.supportingData',
        'medicalRecord.homeExercises',
    ])->findOrFail($id);

    $homeExerciseTemplates = HomeExerciseTemplate::where('status', 'active')
        ->orderBy('name')
        ->get();

    $inventoryItems = InventoryItem::where('status', 'active')
        ->where(function ($query) {
            $query->where('name', 'like', '%dry%')
                ->orWhere('name', 'like', '%needle%')
                ->orWhere('name', 'like', '%jarum%')
                ->orWhere('category', 'like', '%dry%')
                ->orWhere('category', 'like', '%needle%')
                ->orWhere('category', 'like', '%jarum%')
                ->orWhere('sku', 'like', '%DN%');
        })
        ->orderBy('name')
        ->get();

    return view('therapist-medical-record', compact('visit', 'homeExerciseTemplates', 'inventoryItems'));
});

Route::post('/therapist/visits/{id}/medical-record', function (Request $request, $id) {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    $request->validate([
        'complaint' => 'nullable|string',
        'assessment' => 'nullable|string',
        'treatment' => 'nullable|string',
        'progress_note' => 'nullable|string',
        'recommendation' => 'nullable|string',

        'onset' => 'nullable|string|max:255',
        'condition_felt' => 'nullable|string',
        'pain_scale' => 'nullable|integer|min:0|max:10',
        'pain_type' => 'nullable|string|max:255',
        'functional_limitation_initial' => 'nullable|string',
        'pain_body_chart_note' => 'nullable|string',
        'pain_body_area' => 'nullable|string|max:255',
        'pain_body_side' => 'nullable|string|max:50',
        'pain_body_type' => 'nullable|string|max:255',
        'pain_body_intensity' => 'nullable|integer|min:0|max:10',
        'pain_body_areas' => 'nullable|string',
        'pain_quality_tags' => 'nullable|array',
        'pain_quality_tags.*' => 'nullable|string|max:100',
        'pain_aggravating_activity' => 'nullable|string',
        'pain_easing_activity' => 'nullable|string',

        'subjective_examination' => 'nullable|string',
        'objective_examination' => 'nullable|string',
        'severity_level' => 'nullable|string|max:255',
        'irritability_level' => 'nullable|string|max:255',
        'nature_type' => 'nullable|string|max:255',
        'easing_factors' => 'nullable|string',
        'aggravating_factors' => 'nullable|string',
        'special_test_notes' => 'nullable|string',

        'physiotherapy_diagnosis' => 'nullable|string',
        'icd_code' => 'nullable|string|max:50',
        'icd_diagnosis' => 'nullable|string|max:255',
        'impairment' => 'nullable|string',
        'functional_limitation_clinical' => 'nullable|string',
        'icf_body_function' => 'nullable|string',
        'icf_body_structure' => 'nullable|string',
        'icf_activities_participation' => 'nullable|string',
        'icf_personal_factors' => 'nullable|string',
        'icf_environmental_factors' => 'nullable|string',
        'patient_goal' => 'nullable|string',
        'goal_phase' => 'nullable|string|max:50',
        'phase_1_goal' => 'nullable|string',
        'phase_2_goal' => 'nullable|string',
        'phase_3_goal' => 'nullable|string',
        'referral' => 'nullable|string',

        'program_patient' => 'nullable|string',
        'date_of_control' => 'nullable|date',
        'total_session' => 'nullable|integer|min:0',
        'frequency_per_week' => 'nullable|string|max:255',
        'control_plan' => 'nullable|string',

        'diet_nutrition' => 'nullable|string',
        'lifestyle' => 'nullable|string',
        'flare_up_management' => 'nullable|string',

        'treatment_given' => 'nullable|string',
        'dry_needling_done' => 'nullable|boolean',
        'dry_needling_inventory_item_id' => 'nullable|exists:inventory_items,id',
        'dry_needling_quantity' => 'nullable|integer|min:0',
        'response_to_treatment' => 'nullable|string',
        'next_session_plan' => 'nullable|string',
        'session_focus' => 'nullable|string',
        'session_progress_note' => 'nullable|string',
        'session_pain_after' => 'nullable|integer|min:0|max:10',
        'session_homework_status' => 'nullable|string|max:255',
        'rom_cervical_rotation' => 'nullable|string|max:255',
        'rom_shoulder_elevation' => 'nullable|string|max:255',
        'functional_score' => 'nullable|integer|min:0|max:100',
        'activity_tolerance' => 'nullable|string|max:255',

        'blood_pressure' => 'nullable|string|max:255',
        'temperature' => 'nullable|string|max:255',
        'respiration_rate' => 'nullable|string|max:255',
        'heart_rate' => 'nullable|string|max:255',
        'weight' => 'nullable|string|max:255',
        'height' => 'nullable|string|max:255',
        'bmi' => 'nullable|string|max:255',

        'history_type.*' => 'nullable|string|max:255',
        'history_note.*' => 'nullable|string',
        'history_date.*' => 'nullable|date',

        'comorbidity_name.*' => 'nullable|string|max:255',
        'comorbidity_checked.*' => 'nullable',
        'comorbidity_measurement_date.*' => 'nullable|date',
        'comorbidity_final_value.*' => 'nullable|string|max:255',
        'comorbidity_note.*' => 'nullable|string',

        'supporting_data_date.*' => 'nullable|date',
        'supporting_data_type.*' => 'nullable|string|max:255',
        'supporting_data_interpretation.*' => 'nullable|string',
        'supporting_data_file.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx',

        'home_exercise_name.*' => 'nullable|string|max:255',
        'home_exercise_dosage.*' => 'nullable|string|max:255',
        'home_exercise_note.*' => 'nullable|string',
    ]);

    $therapistId = session('therapist_id');

    $visit = Visit::with('medicalRecord')->findOrFail($id);

    
    $previousDryNeedlingInventoryItemId = optional($visit->medicalRecord)->dry_needling_inventory_item_id;
    $previousDryNeedlingQuantity = (int) (optional($visit->medicalRecord)->dry_needling_quantity ?? 0);

$medicalRecord = MedicalRecord::updateOrCreate(
        ['visit_id' => $visit->id],
        [
            'created_by_therapist_id' => optional($visit->medicalRecord)->created_by_therapist_id ?? $therapistId,
            'updated_by_therapist_id' => $therapistId,

            'complaint' => $request->complaint,
            'assessment' => $request->assessment,
            'treatment' => $request->treatment,
            'progress_note' => $request->progress_note,
            'recommendation' => $request->recommendation,

            'onset' => $request->onset,
            'condition_felt' => $request->condition_felt,
            'pain_scale' => $request->pain_scale,
            'pain_type' => $request->pain_type,
            'functional_limitation_initial' => $request->functional_limitation_initial,
            'pain_body_chart_note' => $request->pain_body_chart_note,
            'pain_body_area' => $request->pain_body_area,
            'pain_body_side' => $request->pain_body_side,
            'pain_body_type' => $request->pain_body_type,
            'pain_body_intensity' => $request->pain_body_intensity,
            'pain_body_areas' => $request->pain_body_areas,
            'pain_quality_tags' => $request->has('pain_quality_tags') ? json_encode(array_values(array_filter($request->pain_quality_tags))) : null,
            'pain_aggravating_activity' => $request->pain_aggravating_activity,
            'pain_easing_activity' => $request->pain_easing_activity,

            'subjective_examination' => $request->subjective_examination,
            'objective_examination' => $request->objective_examination,
            'severity_level' => $request->severity_level,
            'irritability_level' => $request->irritability_level,
            'nature_type' => $request->nature_type,
            'easing_factors' => $request->easing_factors,
            'aggravating_factors' => $request->aggravating_factors,
            'special_test_notes' => $request->special_test_notes,

            'physiotherapy_diagnosis' => $request->physiotherapy_diagnosis,
            'icd_code' => $request->icd_code,
            'icd_diagnosis' => $request->icd_diagnosis,
            'impairment' => $request->impairment,
            'functional_limitation_clinical' => $request->functional_limitation_clinical,
            'icf_body_function' => $request->icf_body_function,
            'icf_body_structure' => $request->icf_body_structure,
            'icf_activities_participation' => $request->icf_activities_participation,
            'icf_personal_factors' => $request->icf_personal_factors,
            'icf_environmental_factors' => $request->icf_environmental_factors,
            'patient_goal' => $request->patient_goal,
            'goal_phase' => $request->goal_phase,
            'phase_1_goal' => $request->phase_1_goal,
            'phase_2_goal' => $request->phase_2_goal,
            'phase_3_goal' => $request->phase_3_goal,
            'referral' => $request->referral,

            'program_patient' => $request->program_patient,
            'date_of_control' => $request->date_of_control,
            'total_session' => $request->total_session,
            'frequency_per_week' => $request->frequency_per_week,
            'control_plan' => $request->control_plan,

            'diet_nutrition' => $request->diet_nutrition,
            'lifestyle' => $request->lifestyle,
            'flare_up_management' => $request->flare_up_management,

            'treatment_given' => $request->treatment_given,
            'dry_needling_done' => $request->boolean('dry_needling_done'),
            'dry_needling_inventory_item_id' => $request->boolean('dry_needling_done') ? $request->dry_needling_inventory_item_id : null,
            'dry_needling_quantity' => $request->boolean('dry_needling_done') ? (int) $request->dry_needling_quantity : null,
            'response_to_treatment' => $request->response_to_treatment,
            'next_session_plan' => $request->next_session_plan,
            'session_focus' => $request->session_focus,
            'session_progress_note' => $request->session_progress_note,
            'session_pain_after' => $request->session_pain_after,
            'session_homework_status' => $request->session_homework_status,
            'rom_cervical_rotation' => $request->rom_cervical_rotation,
            'rom_shoulder_elevation' => $request->rom_shoulder_elevation,
            'functional_score' => $request->functional_score,
            'activity_tolerance' => $request->activity_tolerance,

            'blood_pressure' => $request->blood_pressure,
            'temperature' => $request->temperature,
            'respiration_rate' => $request->respiration_rate,
            'heart_rate' => $request->heart_rate,
            'weight' => $request->weight,
            'height' => $request->height,
            'bmi' => $request->bmi,
        ]
    );

    MedicalRecordHistory::where('medical_record_id', $medicalRecord->id)->delete();
    MedicalRecordComorbidity::where('medical_record_id', $medicalRecord->id)->delete();
    $existingSupportingData = MedicalRecordSupportingData::where('medical_record_id', $medicalRecord->id)
        ->get()
        ->keyBy('id');

    MedicalRecordSupportingData::where('medical_record_id', $medicalRecord->id)->delete();
    MedicalRecordHomeExercise::where('medical_record_id', $medicalRecord->id)->delete();

    if ($request->has('history_type')) {
        foreach ($request->history_type as $index => $type) {
            $note = $request->history_note[$index] ?? null;
            $date = $request->history_date[$index] ?? null;

            if ($type || $note || $date) {
                MedicalRecordHistory::create([
                    'medical_record_id' => $medicalRecord->id,
                    'history_type' => $type ?: 'other',
                    'history_note' => $note,
                    'history_date' => $date ?: null,
                ]);
            }
        }
    }

    if ($request->has('comorbidity_name')) {
        foreach ($request->comorbidity_name as $index => $name) {
            $checked = isset($request->comorbidity_checked[$index]);
            $measurementDate = $request->comorbidity_measurement_date[$index] ?? null;
            $finalValue = $request->comorbidity_final_value[$index] ?? null;
            $note = $request->comorbidity_note[$index] ?? null;

            if ($name || $checked || $measurementDate || $finalValue || $note) {
                MedicalRecordComorbidity::create([
                    'medical_record_id' => $medicalRecord->id,
                    'name' => $name ?: 'other',
                    'is_checked' => $checked,
                    'measurement_date' => $measurementDate ?: null,
                    'final_value' => $finalValue,
                    'note' => $note,
                ]);
            }
        }
    }

    if ($request->has('supporting_data_type')) {
        foreach ($request->supporting_data_type as $index => $type) {
            $date = $request->supporting_data_date[$index] ?? null;
            $interpretation = $request->supporting_data_interpretation[$index] ?? null;
            $existingId = $request->supporting_data_id[$index] ?? null;
            $existingItem = $existingId ? ($existingSupportingData[$existingId] ?? null) : null;

            $filePath = optional($existingItem)->file_path;
            $fileName = optional($existingItem)->file_name;
            $fileMime = optional($existingItem)->file_mime;
            $fileSize = optional($existingItem)->file_size;

            if ($request->hasFile("supporting_data_file.$index")) {
                $file = $request->file("supporting_data_file.$index");
                $storedPath = $file->store('medical-record-supporting-data', 'public');

                $filePath = $storedPath;
                $fileName = $file->getClientOriginalName();
                $fileMime = $file->getClientMimeType();
                $fileSize = $file->getSize();
            }

            if ($type || $date || $interpretation || $filePath) {
                MedicalRecordSupportingData::create([
                    'medical_record_id' => $medicalRecord->id,
                    'data_date' => $date ?: null,
                    'data_type' => $type,
                    'interpretation' => $interpretation,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_mime' => $fileMime,
                    'file_size' => $fileSize,
                ]);
            }
        }
    }

    if ($request->has('home_exercise_name')) {
        foreach ($request->home_exercise_name as $index => $exercise) {
            $dosage = $request->home_exercise_dosage[$index] ?? null;
            $note = $request->home_exercise_note[$index] ?? null;

            if ($exercise || $dosage || $note) {
                MedicalRecordHomeExercise::create([
                    'medical_record_id' => $medicalRecord->id,
                    'exercise' => $exercise,
                    'dosage' => $dosage,
                    'note_caution' => $note,
                ]);
            }
        }
    }


    $newDryNeedlingInventoryItemId = $medicalRecord->dry_needling_inventory_item_id;
    $newDryNeedlingQuantity = $medicalRecord->dry_needling_done ? (int) ($medicalRecord->dry_needling_quantity ?? 0) : 0;

    if ($previousDryNeedlingInventoryItemId || $newDryNeedlingInventoryItemId) {
        DB::transaction(function () use (
            $previousDryNeedlingInventoryItemId,
            $previousDryNeedlingQuantity,
            $newDryNeedlingInventoryItemId,
            $newDryNeedlingQuantity,
            $medicalRecord,
            $visit
        ) {
            if ($previousDryNeedlingInventoryItemId && $previousDryNeedlingQuantity > 0) {
                $previousItem = InventoryItem::lockForUpdate()->find($previousDryNeedlingInventoryItemId);

                if ($previousItem) {
                    $stockBefore = (int) $previousItem->stock;
                    $stockAfter = $stockBefore + $previousDryNeedlingQuantity;
                    $previousItem->stock = $stockAfter;
                    $previousItem->save();

                    InventoryStockMovement::create([
                        'inventory_item_id' => $previousItem->id,
                        'type' => 'in',
                        'quantity' => $previousDryNeedlingQuantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference' => 'Dry Needling MR #' . $medicalRecord->id,
                        'notes' => 'Auto reverse previous dry needling usage for Medical Record #' . $medicalRecord->id . ' / Visit #' . $visit->id,
                    ]);
                }
            }

            if ($newDryNeedlingInventoryItemId && $newDryNeedlingQuantity > 0) {
                $newItem = InventoryItem::lockForUpdate()->findOrFail($newDryNeedlingInventoryItemId);

                if ((int) $newItem->stock < $newDryNeedlingQuantity) {
                    throw new \RuntimeException('Stok ' . $newItem->name . ' tidak cukup untuk dry needling. Stok tersedia: ' . $newItem->stock);
                }

                $stockBefore = (int) $newItem->stock;
                $stockAfter = $stockBefore - $newDryNeedlingQuantity;
                $newItem->stock = $stockAfter;
                $newItem->save();

                InventoryStockMovement::create([
                    'inventory_item_id' => $newItem->id,
                    'type' => 'out',
                    'quantity' => $newDryNeedlingQuantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => 'Dry Needling MR #' . $medicalRecord->id,
                    'notes' => 'Dry Needling usage for Medical Record #' . $medicalRecord->id . ' / Visit #' . $visit->id,
                ]);
            }
        });
    }

    MedicalRecordUpdateLog::create([
        'medical_record_id' => $medicalRecord->id,
        'visit_id' => $visit->id,
        'patient_id' => $visit->patient_id,
        'therapist_id' => $therapistId,
        'updated_by_name' => session('therapist_name'),
        'snapshot_date' => now(),
        'complaint' => $medicalRecord->complaint,
        'pain_scale' => $medicalRecord->pain_scale,
        'assessment' => $medicalRecord->assessment ?: $medicalRecord->physiotherapy_diagnosis,
        'treatment_given' => $medicalRecord->treatment_given ?: $medicalRecord->treatment,
        'response_to_treatment' => $medicalRecord->response_to_treatment ?: $medicalRecord->progress_note,
        'next_session_plan' => $medicalRecord->next_session_plan ?: $medicalRecord->recommendation,
        'date_of_control' => $medicalRecord->date_of_control,
        'frequency_per_week' => $medicalRecord->frequency_per_week,
        'total_session' => $medicalRecord->total_session,
        'control_plan' => $medicalRecord->control_plan,
        'summary' => collect([
            $medicalRecord->complaint ? 'Keluhan: ' . $medicalRecord->complaint : null,
            !is_null($medicalRecord->pain_scale) ? 'Pain scale: ' . $medicalRecord->pain_scale . '/10' : null,
            $medicalRecord->response_to_treatment ? 'Response: ' . $medicalRecord->response_to_treatment : null,
            $medicalRecord->next_session_plan ? 'Next plan: ' . $medicalRecord->next_session_plan : null,
        ])->filter()->implode("\n"),
    ]);

    return redirect('/therapist/visits/' . $visit->id . '/medical-record')
        ->with('success', 'Medical Record V2 berhasil disimpan dan update history tercatat.');
});

Route::get('/therapist/visits/{id}/report', function ($id) {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    $therapistId = session('therapist_id');

    $visit = Visit::with([
        'patient',
        'therapistRelation',
        'medicalRecord.histories',
        'medicalRecord.comorbidities',
        'medicalRecord.supportingData',
        'medicalRecord.homeExercises',
    ])->findOrFail($id);

    return view('therapist-report', compact('visit') + ['reportContext' => 'admin']);
});

Route::get('/therapist/visits/{id}/report/print', function ($id) {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    $therapistId = session('therapist_id');

    $visit = Visit::with([
        'patient',
        'therapistRelation',
        'medicalRecord.histories',
        'medicalRecord.comorbidities',
        'medicalRecord.supportingData',
        'medicalRecord.homeExercises',
    ])->findOrFail($id);

    return view('therapist-report-print', compact('visit') + ['reportContext' => 'admin']);
});




Route::get('/admin/staff-leaves', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $leaveRequests = TherapistLeaveRequest::with('therapist')
        ->latest()
        ->get();

    $pendingCount = $leaveRequests->where('status', 'pending')->count();
    $approvedCount = $leaveRequests->where('status', 'approved')->count();
    $rejectedCount = $leaveRequests->where('status', 'rejected')->count();

    return view('admin-staff-leaves', compact(
        'leaveRequests',
        'pendingCount',
        'approvedCount',
        'rejectedCount'
    ));
});

Route::post('/admin/staff-leaves/{id}/review', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $data = $request->validate([
        'status' => 'required|in:approved,rejected',
        'admin_note' => 'nullable|string',
    ]);

    $leave = TherapistLeaveRequest::findOrFail($id);
    $leave->status = $data['status'];
    $leave->admin_note = $data['admin_note'] ?? null;
    $leave->reviewed_at = now();
    $leave->save();

    return redirect('/admin/staff-leaves')->with('success', 'Request cuti berhasil diperbarui.');
});

Route::get('/therapist/leaves/create', function () {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    return view('therapist-leave-create');
});

Route::post('/therapist/leaves', function (Request $request) {
    if (!session('therapist_logged_in')) {
        return redirect('/therapist/login');
    }

    $data = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'leave_type' => 'nullable|string|max:255',
        'reason' => 'nullable|string',
    ]);

    TherapistLeaveRequest::create([
        'therapist_id' => session('therapist_id'),
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
        'leave_type' => $data['leave_type'] ?? null,
        'reason' => $data['reason'] ?? null,
        'status' => 'pending',
    ]);

    return redirect('/therapist/dashboard')->with('success', 'Request cuti berhasil dikirim dan menunggu approval admin.');
});

Route::get('/admin/cashier', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::orderBy('full_name')->get();
    $visits = Visit::with(['patient', 'therapistRelation'])->latest()->get();
    $inventoryItems = InventoryItem::where('status', 'active')->orderBy('name')->get();
    $promos = Promo::where('status', 'active')->orderBy('code')->get();

    $selectedPatientId = $request->query('patient_id');
    $selectedVisitId = $request->query('visit_id');

        $clinicServices = ClinicService::where('status', 'active')
        ->orderByRaw("CASE WHEN category = 'Program' THEN 1 WHEN category = 'Specialist' THEN 2 WHEN category = 'Consultation' THEN 3 WHEN category = 'Add-on' THEN 4 ELSE 5 END")
        ->orderBy('name')
        ->get();

return view('admin-cashier-create', compact(
        'patients',
        'visits',
        'inventoryItems',
        'promos',
        'selectedPatientId',
        'selectedVisitId', 'clinicServices'));
});

Route::post('/admin/cashier/checkout', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'invoice_date' => 'required|date',
        'payment_method' => 'nullable|string|max:100',
        'payment_detail_notes' => 'nullable|string',
        'notes' => 'nullable|string',
        'promo_id' => 'nullable|exists:promos,id',
        'paid_amount' => 'nullable|numeric|min:0',
        'item_type' => 'required|array|min:1',
        'description' => 'required|array|min:1',
        'quantity' => 'required|array|min:1',
        'unit_price' => 'required|array|min:1',
        'inventory_item_id' => 'nullable|array',
    ]);

    $rows = [];
    $subtotal = 0;

    foreach ($request->item_type as $index => $type) {
        $type = $type === 'inventory' ? 'inventory' : 'service';
        $description = trim($request->description[$index] ?? '');
        $quantity = (int) ($request->quantity[$index] ?? 0);
        $unitPrice = (float) ($request->unit_price[$index] ?? 0);
        $inventoryItemId = $request->inventory_item_id[$index] ?? null;

        if ($description === '' || $quantity <= 0 || $unitPrice < 0) {
            continue;
        }

        if ($type === 'inventory' && !$inventoryItemId) {
            return back()->withInput()->withErrors(['inventory_item_id' => 'Item inventory wajib dipilih untuk baris produk.']);
        }

        $lineTotal = $quantity * $unitPrice;
        $subtotal += $lineTotal;

        $rows[] = [
            'item_type' => $type,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
            'inventory_item_id' => $type === 'inventory' ? $inventoryItemId : null,
        ];
    }

    if (count($rows) === 0 || $subtotal <= 0) {
        return back()->withInput()->withErrors(['items' => 'Minimal isi 1 item transaksi dengan nominal lebih dari 0.']);
    }

    $promo = null;
    $discountType = 'none';
    $discountValue = 0;
    $discountAmount = 0;
    $promoCode = null;

    if ($request->promo_id) {
        $promo = Promo::findOrFail($request->promo_id);

        if (!$promo->isAvailableForSubtotal($subtotal)) {
            return back()->withInput()->withErrors(['promo_id' => 'Promo tidak aktif, tidak sesuai tanggal, atau subtotal belum memenuhi minimum transaksi.']);
        }

        $discountType = $promo->discount_type;
        $discountValue = $promo->discount_value;
        $discountAmount = $promo->calculateDiscount($subtotal);
        $promoCode = $promo->code;
    }

    $grandTotal = max($subtotal - $discountAmount, 0);

    $paidAmount = (float) ($request->paid_amount ?: 0);
    $changeAmount = max($paidAmount - $grandTotal, 0);
    $remainingAmount = max($grandTotal - $paidAmount, 0);

    if ($paidAmount <= 0) {
        $paymentStatus = 'unpaid';
    } elseif ($paidAmount < $grandTotal) {
        $paymentStatus = 'partial';
    } else {
        $paymentStatus = 'paid';
    }

    try {
        $billing = DB::transaction(function () use (
            $request,
            $rows,
            $subtotal,
            $discountType,
            $discountValue,
            $discountAmount,
            $promoCode,
            $grandTotal,
            $paidAmount,
            $changeAmount,
            $remainingAmount,
            $paymentStatus
        ) {
            $invoiceNumber = 'INV-' . now()->format('Ymd-His');

            $billing = Billing::create([
                'patient_id' => $request->patient_id,
                'visit_id' => $request->visit_id ?: null,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->invoice_date,
                'subtotal_amount' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'promo_code' => $promoCode,
                'amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $request->payment_method,
                'payment_detail_notes' => $request->payment_detail_notes,
                'notes' => $request->notes,
            ]);

            foreach ($rows as $row) {
                $billing->items()->create($row);

                if ($row['item_type'] === 'inventory' && $row['inventory_item_id']) {
                    $item = InventoryItem::lockForUpdate()->findOrFail($row['inventory_item_id']);

                    if ($item->stock < $row['quantity']) {
                        throw new \RuntimeException('Stok ' . $item->name . ' tidak cukup. Stok tersedia: ' . $item->stock);
                    }

                    $stockBefore = $item->stock;
                    $stockAfter = $stockBefore - $row['quantity'];

                    $item->stock = $stockAfter;
                    $item->save();

                    InventoryStockMovement::create([
                        'inventory_item_id' => $item->id,
                        'billing_id' => $billing->id,
                        'type' => 'out',
                        'quantity' => $row['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference' => $billing->invoice_number,
                        'notes' => 'Keluar dari Kasir Checkout untuk invoice ' . $billing->invoice_number,
                    ]);
                }
            }

            return $billing;
        });
    } catch (\RuntimeException $exception) {
        return back()->withInput()->withErrors(['stock' => $exception->getMessage()]);
    }

    return redirect('/admin/billings/' . $billing->id)->with('success', 'Checkout berhasil. Billing dibuat, promo dihitung, dan stok inventory diperbarui.');
});





/*
|--------------------------------------------------------------------------
| Reporting Premium
|--------------------------------------------------------------------------
*/



Route::get('/admin/services', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = $request->query('search');
    $category = $request->query('category');

    $servicesQuery = ClinicService::query();

    if ($search) {
        $servicesQuery->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%');
        });
    }

    if ($category) {
        $servicesQuery->where('category', $category);
    }

    $services = $servicesQuery
        ->orderByRaw("CASE WHEN category = 'Program' THEN 1 WHEN category = 'Specialist' THEN 2 WHEN category = 'Consultation' THEN 3 WHEN category = 'Add-on' THEN 4 ELSE 5 END")
        ->orderBy('name')
        ->get();

    $categories = ClinicService::select('category')
        ->whereNotNull('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    $activeServices = ClinicService::where('status', 'active')->count();
    $packageReadyServices = ClinicService::where(function ($query) {
        $query->whereNotNull('package_3x_price')
            ->orWhereNotNull('package_6x_price')
            ->orWhereNotNull('package_12x_price');
    })->count();

    return view('admin-services', compact(
        'services',
        'categories',
        'activeServices',
        'packageReadyServices',
        'search',
        'category'
    ));
});

Route::post('/admin/services', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price_per_visit' => 'required|integer|min:0',
        'package_3x_price' => 'nullable|integer|min:0',
        'package_6x_price' => 'nullable|integer|min:0',
        'package_12x_price' => 'nullable|integer|min:0',
        'category' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ]);

    ClinicService::create($data);

    return redirect('/admin/services')->with('success', 'Layanan berhasil ditambahkan.');
});

Route::post('/admin/services/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $service = ClinicService::findOrFail($id);

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'price_per_visit' => 'required|integer|min:0',
        'package_3x_price' => 'nullable|integer|min:0',
        'package_6x_price' => 'nullable|integer|min:0',
        'package_12x_price' => 'nullable|integer|min:0',
        'category' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'status' => 'required|in:active,inactive',
    ]);

    $service->update($data);

    return redirect('/admin/services')->with('success', 'Layanan berhasil diperbarui.');
});

Route::post('/admin/services/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    ClinicService::findOrFail($id)->delete();

    return redirect('/admin/services')->with('success', 'Layanan berhasil dihapus.');
});



Route::get('/admin/owner-dashboard', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $month = $request->query('month', now()->format('Y-m'));
    $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();
    $monthLabel = $startDate->format('F Y');

    $bookings = Booking::with(['patient', 'therapist'])
        ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $visits = Visit::with(['patient', 'booking', 'therapistRelation', 'medicalRecord'])
        ->whereBetween('visit_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $patients = Patient::whereBetween('created_at', [$startDate, $endDate])->get();

    $billings = Billing::with(['patient', 'visit'])
        ->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $validBillings = $billings->where('payment_status', '!=', 'void');

    $moneyPaid = function ($billing) {
        if (($billing->payment_status ?? null) === 'void') {
            return 0;
        }

        $paid = (float) ($billing->paid_amount ?? 0);
        $amount = (float) ($billing->amount ?? 0);

        if (($billing->payment_status ?? null) === 'paid' && $paid <= 0) {
            return $amount;
        }

        return $paid;
    };

    $moneyOutstanding = function ($billing) {
        if (($billing->payment_status ?? null) === 'void') {
            return 0;
        }

        $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
        $paid = (float) ($billing->paid_amount ?? 0);
        $status = $billing->payment_status ?: 'unpaid';

        if ($status === 'paid') {
            return 0;
        }

        return max($total - $paid, 0);
    };

    $leaveRequests = class_exists(\App\Models\TherapistLeaveRequest::class)
        ? \App\Models\TherapistLeaveRequest::with('therapist')->latest()->take(8)->get()
        : collect();

    $summary = [
        'bookings' => $bookings->count(),
        'booking_pending' => $bookings->where('status', 'pending')->count(),
        'booking_confirmed' => $bookings->where('status', 'confirmed')->count(),
        'visits' => $visits->count(),
        'completed_visits' => $visits->where('status', 'completed')->count(),
        'new_patients' => $patients->count(),
        'invoice_count' => $billings->count(),
        'void_invoice' => $billings->where('payment_status', 'void')->count(),
        'net_revenue' => $validBillings->sum('amount'),
        'discount' => $validBillings->sum('discount_amount'),
        'paid_amount' => $validBillings->sum(fn ($billing) => $moneyPaid($billing)),
        'outstanding' => $validBillings->sum(fn ($billing) => $moneyOutstanding($billing)),
        'active_staff' => Therapist::where('status', 'active')->count(),
        'pending_leave' => $leaveRequests->where('status', 'pending')->count(),
    ];

    $recentBillings = $billings
        ->sortByDesc('invoice_date')
        ->take(8)
        ->values();

    $recentBookings = $bookings
        ->sortByDesc(fn ($booking) => ($booking->booking_date ?: '') . ' ' . ($booking->booking_time ?: ''))
        ->take(8)
        ->values();

    $recentVisits = $visits
        ->sortByDesc('visit_date')
        ->take(8)
        ->values();

    return view('admin-owner-dashboard', compact(
        'month',
        'monthLabel',
        'summary',
        'recentBillings',
        'recentBookings',
        'recentVisits',
        'leaveRequests'
    ));
});

Route::get('/admin/reports', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $month = now()->format('Y-m');

    $cards = [
        [
            'title' => 'Monthly Clinic Report',
            'subtitle' => 'Owner snapshot bulanan: booking, visit, pasien baru, revenue, outstanding, product usage, movement stok, dan void.',
            'url' => '/admin/reports/monthly-clinic?month=' . $month,
            'badge' => 'Owner value',
            'priority' => 'High',
        ],
        [
            'title' => 'Revenue Report',
            'subtitle' => 'Analisis pendapatan berdasarkan tanggal, status pembayaran, metode bayar, pasien, promo, outstanding, dan void.',
            'url' => '/admin/reports/revenue',
            'badge' => 'Finance',
            'priority' => 'High',
        ],
        [
            'title' => 'Inventory Report',
            'subtitle' => 'Laporan stok, stock value, potential sales, stock in/out, adjustment, low stock, empty stock, dan product usage.',
            'url' => '/admin/reports/inventory?month=' . $month,
            'badge' => 'Stock control',
            'priority' => 'Medium',
        ],
        [
            'title' => 'Therapist Performance Report',
            'subtitle' => 'Performa therapist: jumlah visit, completed visit, pasien ditangani, rekam medis, completion rate, dan revenue terkait visit.',
            'url' => '/admin/reports/therapist-performance?month=' . $month,
            'badge' => 'Performance',
            'priority' => 'Premium',
        ],
    ];

    return view('admin-reports', compact('cards'));
});

Route::get('/admin/reports/monthly-clinic', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $month = $request->query('month', now()->format('Y-m'));
    $export = $request->query('export');

    $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    $bookings = Booking::with(['patient'])
        ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $visits = Visit::with(['patient', 'therapistRelation', 'medicalRecord'])
        ->whereBetween('visit_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $patients = Patient::whereBetween('created_at', [$startDate, $endDate])->get();

    $billings = Billing::with(['patient', 'visit', 'items.inventoryItem'])
        ->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $validBillings = $billings->where('payment_status', '!=', 'void');


    $moneyPaid = function ($billing) {
        if ($billing->payment_status === 'void') {
            return 0;
        }

        $paid = (float) ($billing->paid_amount ?? 0);
        $amount = (float) ($billing->amount ?? 0);

        if ($billing->payment_status === 'paid' && $paid <= 0) {
            return $amount;
        }

        return $paid;
    };

    $moneyOutstanding = function ($billing) {
        if (($billing->payment_status ?? null) === 'void') {
            return 0;
        }

        $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
        $paid = (float) ($billing->paid_amount ?? 0);
        $status = $billing->payment_status ?: 'unpaid';

        if ($status === 'paid') {
            return 0;
        }

        return max($total - $paid, 0);
    };

    $movements = InventoryStockMovement::with('item')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

    $summary = [
        'month_label' => $startDate->format('F Y'),
        'month' => $month,
        'bookings' => $bookings->count(),
        'booking_pending' => $bookings->where('status', 'pending')->count(),
        'booking_confirmed' => $bookings->where('status', 'confirmed')->count(),
        'booking_completed' => $bookings->where('status', 'completed')->count(),
        'visits' => $visits->count(),
        'completed_visits' => $visits->where('status', 'completed')->count(),
        'new_patients' => $patients->count(),
        'invoices' => $billings->count(),
        'void_invoices' => $billings->where('payment_status', 'void')->count(),
        'gross_revenue' => $validBillings->sum('subtotal_amount'),
        'discount' => $validBillings->sum('discount_amount'),
        'net_revenue' => $validBillings->sum('amount'),
        'paid_amount' => $validBillings->sum(fn ($billing) => $moneyPaid($billing)),
        'outstanding' => $validBillings->sum(fn ($billing) => $moneyOutstanding($billing)),
        'stock_in' => $movements->where('type', 'in')->sum('quantity'),
        'stock_out' => $movements->where('type', 'out')->sum('quantity'),
        'adjustments' => $movements->where('type', 'adjustment')->count(),
    ];

    $productUsage = BillingItem::with(['billing.patient', 'billing.visit.therapistRelation', 'inventoryItem'])
        ->where('item_type', 'inventory')
        ->whereHas('billing', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->where('payment_status', '!=', 'void');
        })
        ->get();

    $topProducts = $productUsage
        ->groupBy('inventory_item_id')
        ->map(function ($rows) {
            $item = $rows->first()->inventoryItem;

            return [
                'item' => $item,
                'name' => $item ? $item->name : $rows->first()->description,
                'qty' => $rows->sum('quantity'),
                'value' => $rows->sum('line_total'),
                'lines' => $rows->count(),
            ];
        })
        ->sortByDesc('qty')
        ->values();

    $dailyRows = collect();
    $cursor = $startDate->copy();
    while ($cursor->lte($endDate)) {
        $date = $cursor->toDateString();

        $dayBillings = $billings->filter(function ($billing) use ($date) {
            return $billing->invoice_date && $billing->invoice_date->toDateString() === $date;
        });
        $dayValidBillings = $dayBillings->where('payment_status', '!=', 'void');

        $dailyRows->push([
            'date' => $date,
            'bookings' => $bookings->where('booking_date', $date)->count(),
            'visits' => $visits->where('visit_date', $date)->count(),
            'new_patients' => $patients->filter(fn ($p) => $p->created_at && $p->created_at->toDateString() === $date)->count(),
            'paid_amount' => $dayValidBillings->sum(fn ($billing) => $moneyPaid($billing)),
            'paid' => $dayValidBillings->sum(fn ($billing) => $moneyPaid($billing)),
            'outstanding' => $dayValidBillings->sum(fn ($billing) => $moneyOutstanding($billing)),
            'outstanding_amount' => $dayValidBillings->sum(fn ($billing) => $moneyOutstanding($billing)),
            'void' => $dayBillings->where('payment_status', 'void')->count(),
        ]);

        $cursor->addDay();
    }

    if ($export === 'csv') {
        $filename = 'monthly-clinic-report-' . $month . '.csv';

        return response()->streamDownload(function () use ($dailyRows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['date', 'bookings', 'visits', 'new_patients', 'paid_amount', 'outstanding', 'void']);
            foreach ($dailyRows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    return view('admin-report-monthly-clinic', compact(
        'summary',
        'month',
        'dailyRows',
        'topProducts',
        'bookings',
        'visits',
        'billings'
    ));
});

Route::get('/admin/reports/revenue', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
    $dateTo = $request->query('date_to', now()->endOfMonth()->toDateString());
    $paymentMethod = $request->query('payment_method');
    $status = $request->query('status');
    $search = trim((string) $request->query('search'));
    $export = $request->query('export');

    $query = Billing::with(['patient', 'visit.therapistRelation', 'items.inventoryItem'])
        ->whereBetween('invoice_date', [$dateFrom, $dateTo])
        ->latest();

    if ($paymentMethod) {
        $query->where('payment_method', $paymentMethod);
    }

    if ($status && in_array($status, ['paid', 'partial', 'unpaid', 'void'])) {
        $query->where('payment_status', $status);
    }

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', '%' . $search . '%')
                ->orWhere('promo_code', 'like', '%' . $search . '%')
                ->orWhereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('full_name', 'like', '%' . $search . '%')
                        ->orWhere('medical_record_number', 'like', '%' . $search . '%')
                        ->orWhere('whatsapp', 'like', '%' . $search . '%');
                });
        });
    }

    $billings = $query->get();
    $validBillings = $billings->where('payment_status', '!=', 'void');


    $moneyPaid = function ($billing) {
        if ($billing->payment_status === 'void') {
            return 0;
        }

        $paid = (float) ($billing->paid_amount ?? 0);
        $amount = (float) ($billing->amount ?? 0);

        if ($billing->payment_status === 'paid' && $paid <= 0) {
            return $amount;
        }

        return $paid;
    };

    $moneyOutstanding = function ($billing) {
        if (($billing->payment_status ?? null) === 'void') {
            return 0;
        }

        $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
        $paid = (float) ($billing->paid_amount ?? 0);
        $status = $billing->payment_status ?: 'unpaid';

        if ($status === 'paid') {
            return 0;
        }

        return max($total - $paid, 0);
    };

    $summary = [
        'transactions' => $billings->count(),
        'gross_revenue' => $validBillings->sum('subtotal_amount'),
        'discount' => $validBillings->sum('discount_amount'),
        'net_revenue' => $validBillings->sum('amount'),
        'paid_amount' => $validBillings->sum(fn ($billing) => $moneyPaid($billing)),
        'outstanding' => $validBillings->sum(fn ($billing) => $moneyOutstanding($billing)),
        'paid_count' => $billings->where('payment_status', 'paid')->count(),
        'partial_count' => $billings->where('payment_status', 'partial')->count(),
        'unpaid_count' => $billings->where('payment_status', 'unpaid')->count(),
        'void_count' => $billings->where('payment_status', 'void')->count(),
    ];

    $byMethod = $validBillings
        ->groupBy(fn ($billing) => $billing->payment_method ?: 'unknown')
        ->map(function ($rows, $method) use ($moneyPaid, $moneyOutstanding) {
            return [
                'method' => $method,
                'count' => $rows->count(),
                'paid' => $rows->sum(fn ($billing) => $moneyPaid($billing)),
                'net' => $rows->sum('amount'),
                'outstanding' => $rows->sum(fn ($billing) => $moneyOutstanding($billing)),
            ];
        })
        ->sortByDesc('paid')
        ->values();

    $paymentMethods = Billing::whereNotNull('payment_method')
        ->where('payment_method', '!=', '')
        ->distinct()
        ->pluck('payment_method')
        ->values();

    if ($export === 'csv') {
        $filename = 'revenue-report-' . $dateFrom . '-to-' . $dateTo . '.csv';

        return response()->streamDownload(function () use ($billings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['invoice_date', 'invoice_number', 'patient', 'status', 'method', 'subtotal', 'discount', 'amount', 'paid', 'remaining', 'promo_code']);
            foreach ($billings as $billing) {
                fputcsv($handle, [
                    optional($billing->invoice_date)->format('Y-m-d'),
                    $billing->invoice_number,
                    optional($billing->patient)->full_name,
                    $billing->payment_status,
                    $billing->payment_method,
                    $billing->subtotal_amount,
                    $billing->discount_amount,
                    $billing->amount,
                    $billing->paid_amount,
                    $billing->remaining_amount,
                    $billing->promo_code,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    return view('admin-report-revenue', compact(
        'billings',
        'summary',
        'byMethod',
        'paymentMethods',
        'dateFrom',
        'dateTo',
        'paymentMethod',
        'status',
        'search'
    ));
});

Route::get('/admin/reports/inventory', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $month = $request->query('month', now()->format('Y-m'));
    $export = $request->query('export');

    $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    $items = InventoryItem::orderBy('name')->get();
    $movements = InventoryStockMovement::with('item')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->latest()
        ->get();

    $productUsage = BillingItem::with(['billing.patient', 'inventoryItem'])
        ->where('item_type', 'inventory')
        ->whereHas('billing', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->where('payment_status', '!=', 'void');
        })
        ->get();

    $summary = [
        'month_label' => $startDate->format('F Y'),
        'total_items' => $items->count(),
        'active_items' => $items->where('status', 'active')->count(),
        'low_stock_items' => $items->filter(fn ($item) => $item->status === 'active' && $item->stock_status === 'low')->count(),
        'empty_stock_items' => $items->filter(fn ($item) => $item->status === 'active' && $item->stock_status === 'empty')->count(),
        'stock_value' => $items->sum(fn ($item) => (float) $item->stock * (float) $item->purchase_price),
        'potential_sales' => $items->sum(fn ($item) => (float) $item->stock * (float) $item->selling_price),
        'stock_in' => $movements->where('type', 'in')->sum('quantity'),
        'stock_out' => $movements->where('type', 'out')->sum('quantity'),
        'adjustments' => $movements->where('type', 'adjustment')->count(),
        'product_usage_qty' => $productUsage->sum('quantity'),
        'product_usage_value' => $productUsage->sum('line_total'),
    ];

    $itemRows = $items->map(function ($item) use ($movements, $productUsage) {
        $itemMovements = $movements->where('inventory_item_id', $item->id);
        $itemUsage = $productUsage->where('inventory_item_id', $item->id);

        return [
            'item' => $item,
            'stock_in' => $itemMovements->where('type', 'in')->sum('quantity'),
            'stock_out' => $itemMovements->where('type', 'out')->sum('quantity'),
            'adjustments' => $itemMovements->where('type', 'adjustment')->count(),
            'usage_qty' => $itemUsage->sum('quantity'),
            'usage_value' => $itemUsage->sum('line_total'),
            'stock_value' => (float) $item->stock * (float) $item->purchase_price,
            'potential_sales' => (float) $item->stock * (float) $item->selling_price,
        ];
    });

    if ($export === 'csv') {
        $filename = 'inventory-report-' . $month . '.csv';

        return response()->streamDownload(function () use ($itemRows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['sku', 'name', 'category', 'stock', 'minimum_stock', 'stock_status', 'stock_in', 'stock_out', 'adjustments', 'usage_qty', 'usage_value', 'stock_value', 'potential_sales']);
            foreach ($itemRows as $row) {
                $item = $row['item'];
                fputcsv($handle, [
                    $item->sku,
                    $item->name,
                    $item->category,
                    $item->stock,
                    $item->minimum_stock,
                    $item->stock_status,
                    $row['stock_in'],
                    $row['stock_out'],
                    $row['adjustments'],
                    $row['usage_qty'],
                    $row['usage_value'],
                    $row['stock_value'],
                    $row['potential_sales'],
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    return view('admin-report-inventory', compact(
        'summary',
        'month',
        'itemRows',
        'movements',
        'productUsage'
    ));
});

Route::get('/admin/reports/therapist-performance', function () {
    $therapists = \App\Models\Therapist::orderBy('full_name')->get();

    $clinicalRequiredFields = [
        'complaint',
        'pain_scale',
        'subjective_examination',
        'objective_examination',
        'physiotherapy_diagnosis',
        'impairment',
        'patient_goal',
        'program_patient',
        'treatment_given',
        'response_to_treatment',
        'next_session_plan',
    ];

    $hasBookingsTherapistId = \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'therapist_id');
    $hasVisitsTherapistId = \Illuminate\Support\Facades\Schema::hasColumn('visits', 'therapist_id');
    $hasVisitsTherapistText = \Illuminate\Support\Facades\Schema::hasColumn('visits', 'therapist');

    $hasBillings = \Illuminate\Support\Facades\Schema::hasTable('billings');
    $hasInvoices = \Illuminate\Support\Facades\Schema::hasTable('invoices');
    $hasPayments = \Illuminate\Support\Facades\Schema::hasTable('payments');
    $hasPackageTreatments = \Illuminate\Support\Facades\Schema::hasTable('package_treatments');
    $hasBookingsPackageColumn = \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'package_treatment_id')
        || \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'package_id')
        || \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'is_package');

    $therapistPerformanceRows = $therapists->map(function ($therapist) use (
        $clinicalRequiredFields,
        $hasBookingsTherapistId,
        $hasVisitsTherapistId,
        $hasVisitsTherapistText,
        $hasBillings,
        $hasInvoices,
        $hasPayments,
        $hasPackageTreatments,
        $hasBookingsPackageColumn
    ) {
        $visitQuery = \App\Models\Visit::query();

        if ($hasVisitsTherapistId) {
            $visitQuery->where('therapist_id', $therapist->id);
        } elseif ($hasVisitsTherapistText) {
            $visitQuery->where('therapist', $therapist->full_name);
        }

        $visits = $visitQuery->with(['patient', 'medicalRecord'])->get();

        $visitIds = $visits->pluck('id')->filter()->values();
        $patientIds = $visits->pluck('patient_id')->filter()->unique()->values();

        $medicalRecords = $visits->pluck('medicalRecord')->filter();

        $completedRecords = $medicalRecords->filter(function ($record) use ($clinicalRequiredFields) {
            $completed = collect($clinicalRequiredFields)->filter(function ($field) use ($record) {
                return !blank($record->{$field});
            })->count();

            return $completed >= count($clinicalRequiredFields);
        })->count();

        $averageCompleteness = $medicalRecords->count()
            ? round($medicalRecords->map(function ($record) use ($clinicalRequiredFields) {
                $completed = collect($clinicalRequiredFields)->filter(function ($field) use ($record) {
                    return !blank($record->{$field});
                })->count();

                return ($completed / count($clinicalRequiredFields)) * 100;
            })->avg())
            : 0;

        $completionRate = $medicalRecords->count()
            ? round(($completedRecords / $medicalRecords->count()) * 100)
            : 0;

        $bookingCount = 0;

        if (\Illuminate\Support\Facades\Schema::hasTable('bookings') && $hasBookingsTherapistId) {
            $bookingCount = \Illuminate\Support\Facades\DB::table('bookings')
                ->where('therapist_id', $therapist->id)
                ->count();
        }

        $packagePatientCount = 0;

        if ($hasPackageTreatments && \Illuminate\Support\Facades\Schema::hasColumn('package_treatments', 'therapist_id')) {
            $packagePatientQuery = \Illuminate\Support\Facades\DB::table('package_treatments')
                ->where('therapist_id', $therapist->id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('package_treatments', 'patient_id')) {
                $packagePatientCount = $packagePatientQuery->distinct('patient_id')->count('patient_id');
            } else {
                $packagePatientCount = $packagePatientQuery->count();
            }
        } elseif (\Illuminate\Support\Facades\Schema::hasTable('bookings') && $hasBookingsTherapistId && $hasBookingsPackageColumn) {
            $packageQuery = \Illuminate\Support\Facades\DB::table('bookings')
                ->where('therapist_id', $therapist->id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'package_treatment_id')) {
                $packageQuery->whereNotNull('package_treatment_id');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'package_id')) {
                $packageQuery->whereNotNull('package_id');
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'is_package')) {
                $packageQuery->where('is_package', 1);
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'patient_id')) {
                $packagePatientCount = $packageQuery->distinct('patient_id')->count('patient_id');
            } else {
                $packagePatientCount = $packageQuery->count();
            }
        }

        $revenue = 0;
        $paidAmount = 0;
        $outstandingAmount = 0;

        if ($hasBillings && \Illuminate\Support\Facades\Schema::hasColumn('billings', 'therapist_id')) {
            $billingQuery = \Illuminate\Support\Facades\DB::table('billings')
                ->where('therapist_id', $therapist->id);

            foreach (['total_amount', 'grand_total', 'amount'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('billings', $column)) {
                    $revenue = (clone $billingQuery)->sum($column);
                    break;
                }
            }

            foreach (['paid_amount', 'payment_amount'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('billings', $column)) {
                    $paidAmount = (clone $billingQuery)->sum($column);
                    break;
                }
            }

            foreach (['outstanding_amount', 'remaining_amount', 'balance_due'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('billings', $column)) {
                    $outstandingAmount = (clone $billingQuery)->sum($column);
                    break;
                }
            }
        } elseif ($hasInvoices && \Illuminate\Support\Facades\Schema::hasColumn('invoices', 'therapist_id')) {
            $invoiceQuery = \Illuminate\Support\Facades\DB::table('invoices')
                ->where('therapist_id', $therapist->id);

            foreach (['total_amount', 'grand_total', 'amount'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('invoices', $column)) {
                    $revenue = (clone $invoiceQuery)->sum($column);
                    break;
                }
            }

            foreach (['paid_amount', 'payment_amount'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('invoices', $column)) {
                    $paidAmount = (clone $invoiceQuery)->sum($column);
                    break;
                }
            }

            foreach (['outstanding_amount', 'remaining_amount', 'balance_due'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('invoices', $column)) {
                    $outstandingAmount = (clone $invoiceQuery)->sum($column);
                    break;
                }
            }
        } elseif ($hasPayments && \Illuminate\Support\Facades\Schema::hasColumn('payments', 'therapist_id')) {
            $paymentQuery = \Illuminate\Support\Facades\DB::table('payments')
                ->where('therapist_id', $therapist->id);

            foreach (['amount', 'paid_amount', 'payment_amount'] as $column) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('payments', $column)) {
                    $paidAmount = (clone $paymentQuery)->sum($column);
                    $revenue = $paidAmount;
                    break;
                }
            }
        }

        return (object) [
            'therapist' => $therapist,
            'visit_count' => $visits->count(),
            'booking_count' => $bookingCount,
            'patient_count' => $patientIds->count(),
            'program_count' => $medicalRecords->filter(fn ($record) => !blank($record->program_patient))->count(),
            'package_patient_count' => $packagePatientCount,
            'medical_record_count' => $medicalRecords->count(),
            'completed_record_count' => $completedRecords,
            'completion_rate' => $completionRate,
            'average_completeness' => $averageCompleteness,
            'revenue' => $revenue,
            'paid_amount' => $paidAmount,
            'outstanding_amount' => $outstandingAmount,
        ];
    });

    $therapistPerformanceSummary = (object) [
        'therapist_count' => $therapistPerformanceRows->count(),
        'patient_count' => $therapistPerformanceRows->sum('patient_count'),
        'visit_count' => $therapistPerformanceRows->sum('visit_count'),
        'program_count' => $therapistPerformanceRows->sum('program_count'),
        'package_patient_count' => $therapistPerformanceRows->sum('package_patient_count'),
        'medical_record_count' => $therapistPerformanceRows->sum('medical_record_count'),
        'completed_record_count' => $therapistPerformanceRows->sum('completed_record_count'),
        'completion_rate' => $therapistPerformanceRows->sum('medical_record_count') > 0
            ? round(($therapistPerformanceRows->sum('completed_record_count') / $therapistPerformanceRows->sum('medical_record_count')) * 100)
            : 0,
        'average_completeness' => $therapistPerformanceRows->count()
            ? round($therapistPerformanceRows->avg('average_completeness'))
            : 0,
        'revenue' => $therapistPerformanceRows->sum('revenue'),
        'paid_amount' => $therapistPerformanceRows->sum('paid_amount'),
        'outstanding_amount' => $therapistPerformanceRows->sum('outstanding_amount'),
    ];

    $topTherapistsByPatients = $therapistPerformanceRows
        ->sortByDesc('patient_count')
        ->take(5)
        ->values();

    $topTherapistsByCompletion = $therapistPerformanceRows
        ->sortByDesc('completion_rate')
        ->take(5)
        ->values();

    return view('admin-report-therapist-performance', compact(
        'therapistPerformanceRows',
        'therapistPerformanceSummary',
        'topTherapistsByPatients',
        'topTherapistsByCompletion'
    ));
});




/*
|--------------------------------------------------------------------------
| Phase 6 — Surat & Dokumen Klinik
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Package Treatment Documents
|--------------------------------------------------------------------------
*/

Route::get('/admin/package-treatments', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = trim((string) $request->query('search', ''));

    $documents = PackageTreatmentDocument::with(['patient', 'therapist', 'billing'])
        ->when($search, function ($query) use ($search) {
            $query->where('document_number', 'like', '%' . $search . '%')
                ->orWhere('package_name', 'like', '%' . $search . '%')
                ->orWhereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('full_name', 'like', '%' . $search . '%')
                        ->orWhere('medical_record_number', 'like', '%' . $search . '%')
                        ->orWhere('whatsapp', 'like', '%' . $search . '%');
                });
        })
        ->latest()
        ->get();

    return view('admin-package-treatments', compact('documents', 'search'));
});

Route::get('/admin/package-treatments/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::orderBy('full_name')->get();
    $therapists = Therapist::orderBy('name')->get();
    $billings = Billing::with(['patient', 'items'])
        ->where('payment_status', '!=', 'void')
        ->latest()
        ->limit(100)
        ->get();

    $selectedPatientId = $request->query('patient_id');
    $selectedBillingId = $request->query('billing_id');

    $prefill = [
        'package_name' => '',
        'package_type' => '',
        'total_sessions' => 3,
        'package_price' => 0,
        'buying_date' => now()->format('Y-m-d'),
        'valid_until' => now()->addMonths(3)->format('Y-m-d'),
    ];

    if ($selectedBillingId) {
        $selectedBilling = Billing::with(['patient', 'items'])->find($selectedBillingId);

        if ($selectedBilling) {
            $selectedPatientId = $selectedBilling->patient_id;

            $packageItem = $selectedBilling->items
                ->first(function ($item) {
                    return preg_match('/paket\\s*(3x|6x|12x)/i', (string) $item->description);
                });

            if ($packageItem) {
                $description = (string) $packageItem->description;
                $sessions = 3;

                if (preg_match('/paket\\s*(3x|6x|12x)/i', $description, $matches)) {
                    $sessions = (int) str_replace('x', '', strtolower($matches[1]));
                }

                $prefill['package_name'] = $description;
                $prefill['package_type'] = $sessions === 3 ? 'Light Package' : ($sessions === 6 ? 'Medium Package' : ($sessions === 12 ? 'Full Package' : 'Custom Package'));
                $prefill['total_sessions'] = $sessions;
                $prefill['package_price'] = $packageItem->subtotal ?? (($packageItem->quantity ?? 1) * ($packageItem->unit_price ?? 0));
                $prefill['buying_date'] = optional($selectedBilling->invoice_date)->format('Y-m-d') ?: now()->format('Y-m-d');
                $prefill['valid_until'] = \Carbon\Carbon::parse($prefill['buying_date'])->addMonths($sessions >= 12 ? 6 : 3)->format('Y-m-d');
            } else {
                $prefill['package_price'] = $selectedBilling->grand_total ?? $selectedBilling->amount ?? 0;
                $prefill['buying_date'] = optional($selectedBilling->invoice_date)->format('Y-m-d') ?: now()->format('Y-m-d');
            }
        }
    }

    return view('admin-package-treatment-create', compact(
        'patients',
        'therapists',
        'billings',
        'selectedPatientId',
        'selectedBillingId',
        'prefill'
    ));
});

Route::post('/admin/package-treatments', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $data = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'billing_id' => 'nullable|exists:billings,id',
        'therapist_id' => 'nullable|exists:therapists,id',
        'document_date' => 'nullable|date',
        'package_name' => 'required|string|max:255',
        'package_type' => 'nullable|string|max:100',
        'total_sessions' => 'required|integer|min:1|max:24',
        'package_price' => 'required|numeric|min:0',
        'buying_date' => 'nullable|date',
        'valid_until' => 'nullable|date',
        'terms' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $documentNumber = 'PKG-' . now()->format('Ymd-His');

    $document = PackageTreatmentDocument::create(array_merge($data, [
        'document_number' => $documentNumber,
        'document_date' => $data['document_date'] ?: now()->toDateString(),
        'buying_date' => $data['buying_date'] ?: now()->toDateString(),
    ]));

    return redirect('/admin/package-treatments/' . $document->id . '/print')
        ->with('success', 'Dokumen pembelian paket berhasil dibuat.');
});

Route::get('/admin/package-treatments/{id}/print', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $document = PackageTreatmentDocument::with(['patient', 'billing', 'therapist'])->findOrFail($id);

    return view('admin-package-treatment-print', compact('document'));
});

Route::post('/admin/package-treatments/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    PackageTreatmentDocument::findOrFail($id)->delete();

    return redirect('/admin/package-treatments')->with('success', 'Dokumen pembelian paket berhasil dihapus.');
});


Route::get('/admin/documents', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $totalReferralLetters = \App\Models\ReferralLetter::count();
    $totalConsents = \App\Models\InformedConsent::count();

    return view('admin-documents', compact(
        'totalReferralLetters',
        'totalConsents'
    ));
});

Route::get('/admin/control-letter/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = \App\Models\Patient::orderBy('full_name')->get();
    $visits = \App\Models\Visit::with(['patient', 'therapistRelation', 'medicalRecord'])
        ->latest('visit_date')
        ->get();

    $selectedVisit = null;
    $selectedPatientId = $request->query('patient_id');

    if ($request->query('visit_id')) {
        $selectedVisit = \App\Models\Visit::with(['patient', 'therapistRelation', 'medicalRecord'])
            ->find($request->query('visit_id'));

        if ($selectedVisit) {
            $selectedPatientId = $selectedVisit->patient_id;
        }
    }

    return view('admin-control-letter-create', compact(
        'patients',
        'visits',
        'selectedVisit',
        'selectedPatientId'
    ));
});

Route::post('/admin/control-letter/print', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'letter_date' => 'required|date',
        'document_type' => 'required|in:control,therapy',
        'control_date' => 'nullable|date',
        'therapy_frequency' => 'nullable|string|max:255',
        'total_session' => 'nullable|string|max:255',
        'diagnosis' => 'nullable|string',
        'therapy_program' => 'nullable|string',
        'recommendation' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $patient = \App\Models\Patient::findOrFail($request->patient_id);
    $visit = $request->visit_id
        ? \App\Models\Visit::with(['patient', 'therapistRelation', 'medicalRecord'])->find($request->visit_id)
        : null;

    $record = optional($visit)->medicalRecord;

    $payload = [
        'letter_date' => $request->letter_date,
        'document_type' => $request->document_type,
        'control_date' => $request->control_date,
        'therapy_frequency' => $request->therapy_frequency,
        'total_session' => $request->total_session,
        'diagnosis' => $request->diagnosis,
        'therapy_program' => $request->therapy_program,
        'recommendation' => $request->recommendation,
        'notes' => $request->notes,
    ];

    return view('admin-control-letter-print', compact(
        'patient',
        'visit',
        'record',
        'payload'
    ));
});


/*
|--------------------------------------------------------------------------
| Patient Rest / Permission Letter
|--------------------------------------------------------------------------
*/

Route::get('/admin/rest-letter/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::orderBy('full_name')->get();
    $therapists = Therapist::orderBy('name')->get();
    $visits = Visit::with('patient')
        ->latest()
        ->limit(150)
        ->get();

    $selectedPatientId = $request->query('patient_id');
    $selectedVisitId = $request->query('visit_id');

    return view('admin-rest-letter-create', compact(
        'patients',
        'therapists',
        'visits',
        'selectedPatientId',
        'selectedVisitId'
    ));
});

Route::post('/admin/rest-letter/print', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $data = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'therapist_id' => 'nullable|exists:therapists,id',
        'letter_date' => 'nullable|date',
        'letter_type' => 'required|in:izin,istirahat',
        'diagnosis' => 'nullable|string|max:255',
        'activity_limitation' => 'nullable|string',
        'rest_start_date' => 'nullable|date',
        'rest_end_date' => 'nullable|date',
        'rest_days' => 'nullable|integer|min:1|max:60',
        'recipient' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    $patient = Patient::findOrFail($data['patient_id']);
    $visit = !empty($data['visit_id']) ? Visit::with('therapist')->find($data['visit_id']) : null;
    $therapist = !empty($data['therapist_id'])
        ? Therapist::find($data['therapist_id'])
        : null;

    $letterNumber = 'SIP-' . now()->format('Ymd-His');

    return view('admin-rest-letter-print', compact(
        'data',
        'patient',
        'visit',
        'therapist',
        'letterNumber'
    ));
});


Route::get('/admin/discharge-summary/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = \App\Models\Patient::orderBy('full_name')->get();
    $visits = \App\Models\Visit::with(['patient', 'therapistRelation', 'medicalRecord.homeExercises'])
        ->latest('visit_date')
        ->get();

    $selectedVisit = null;
    $selectedPatientId = $request->query('patient_id');

    if ($request->query('visit_id')) {
        $selectedVisit = \App\Models\Visit::with(['patient', 'therapistRelation', 'medicalRecord.homeExercises'])
            ->find($request->query('visit_id'));

        if ($selectedVisit) {
            $selectedPatientId = $selectedVisit->patient_id;
        }
    }

    return view('admin-discharge-summary-create', compact(
        'patients',
        'visits',
        'selectedVisit',
        'selectedPatientId'
    ));
});

Route::post('/admin/discharge-summary/print', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'summary_date' => 'required|date',
        'initial_condition' => 'nullable|string',
        'final_condition' => 'nullable|string',
        'therapy_summary' => 'nullable|string',
        'home_program' => 'nullable|string',
        'recommendation' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $patient = \App\Models\Patient::findOrFail($request->patient_id);
    $visit = $request->visit_id
        ? \App\Models\Visit::with(['patient', 'therapistRelation', 'medicalRecord.homeExercises'])->find($request->visit_id)
        : null;

    $record = optional($visit)->medicalRecord;

    $payload = [
        'summary_date' => $request->summary_date,
        'initial_condition' => $request->initial_condition,
        'final_condition' => $request->final_condition,
        'therapy_summary' => $request->therapy_summary,
        'home_program' => $request->home_program,
        'recommendation' => $request->recommendation,
        'notes' => $request->notes,
    ];

    return view('admin-discharge-summary-print', compact(
        'patient',
        'visit',
        'record',
        'payload'
    ));
});

Route::get('/admin/consent-archive', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = trim((string) $request->query('search'));
    $status = $request->query('status');

    $query = \App\Models\InformedConsent::with(['patient', 'visit'])->latest();

    if ($search !== '') {
        $query->whereHas('patient', function ($patientQuery) use ($search) {
            $patientQuery->where('full_name', 'like', '%' . $search . '%')
                ->orWhere('medical_record_number', 'like', '%' . $search . '%')
                ->orWhere('whatsapp', 'like', '%' . $search . '%');
        });
    }

    if ($status && in_array($status, ['pending', 'signed'])) {
        $query->where('status', $status);
    }

    $consents = $query->get();

    return view('admin-consent-archive', compact(
        'consents',
        'search',
        'status'
    ));
});





/*
|--------------------------------------------------------------------------
| Satu Sehat Readiness
|--------------------------------------------------------------------------
*/

Route::get('/admin/satu-sehat-readiness', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::with(['visits.medicalRecord', 'visits.therapistRelation'])->latest()->get();
    $visits = Visit::with(['patient', 'therapistRelation', 'medicalRecord'])->latest('visit_date')->get();
    $therapists = Therapist::orderBy('full_name')->get();

    $patientRows = $patients->map(function ($patient) {
        $missing = [];

        if (blank($patient->full_name)) $missing[] = 'Nama';
        if (blank($patient->nik)) $missing[] = 'NIK';
        if (blank($patient->birth_date)) $missing[] = 'Tanggal lahir';
        if (blank($patient->gender)) $missing[] = 'Gender';
        if (blank($patient->address)) $missing[] = 'Alamat';
        if (blank($patient->whatsapp)) $missing[] = 'WhatsApp';
        if (blank($patient->medical_record_number)) $missing[] = 'No. Rekam Medis';

        return [
            'patient' => $patient,
            'missing' => $missing,
            'score' => max(0, 100 - (count($missing) * 14)),
        ];
    });

    $visitRows = $visits->map(function ($visit) {
        $record = $visit->medicalRecord;
        $missing = [];

        if (blank($visit->patient_id)) $missing[] = 'Patient';
        if (blank($visit->therapist_id) && blank($visit->therapist)) $missing[] = 'Therapist';
        if (blank($visit->visit_date)) $missing[] = 'Tanggal visit';
        if (!$record) {
            $missing[] = 'Medical record';
            $missing[] = 'Diagnosis';
            $missing[] = 'Treatment';
        } else {
            if (blank($record->physiotherapy_diagnosis) && blank($record->assessment)) $missing[] = 'Diagnosis / assessment';
            if (blank($record->treatment_given) && blank($record->treatment)) $missing[] = 'Treatment';
            if (blank($record->complaint)) $missing[] = 'Keluhan';
            if (blank($record->pain_scale)) $missing[] = 'Pain scale';
            if (blank($record->program_patient)) $missing[] = 'Program pasien';
        }

        return [
            'visit' => $visit,
            'missing' => $missing,
            'score' => max(0, 100 - (count($missing) * 12)),
        ];
    });

    $therapistRows = $therapists->map(function ($therapist) {
        $missing = [];

        if (blank($therapist->full_name)) $missing[] = 'Nama';
        if (blank($therapist->email)) $missing[] = 'Email';
        if (blank($therapist->phone)) $missing[] = 'Phone';
        if (blank($therapist->specialty)) $missing[] = 'Specialty';

        return [
            'therapist' => $therapist,
            'missing' => $missing,
            'score' => max(0, 100 - (count($missing) * 20)),
        ];
    });

    $patientReady = $patientRows->where('score', '>=', 80)->count();
    $visitReady = $visitRows->where('score', '>=', 80)->count();
    $therapistReady = $therapistRows->where('score', '>=', 80)->count();

    $summary = [
        'patients' => $patients->count(),
        'patient_ready' => $patientReady,
        'visits' => $visits->count(),
        'visit_ready' => $visitReady,
        'therapists' => $therapists->count(),
        'therapist_ready' => $therapistReady,
        'overall_score' => round(collect([
            $patients->count() ? ($patientReady / max($patients->count(), 1)) * 100 : 0,
            $visits->count() ? ($visitReady / max($visits->count(), 1)) * 100 : 0,
            $therapists->count() ? ($therapistReady / max($therapists->count(), 1)) * 100 : 0,
        ])->avg()),
    ];

    $fhirMapping = [
        [
            'resource' => 'Patient',
            'local_data' => 'patients',
            'fields' => 'NIK, nama, tanggal lahir, gender, alamat, WhatsApp, MR number',
            'status' => 'readiness',
        ],
        [
            'resource' => 'Encounter',
            'local_data' => 'visits',
            'fields' => 'patient_id, therapist_id, visit_date, status',
            'status' => 'readiness',
        ],
        [
            'resource' => 'Practitioner',
            'local_data' => 'therapists',
            'fields' => 'nama therapist, kontak, specialty',
            'status' => 'readiness',
        ],
        [
            'resource' => 'Condition',
            'local_data' => 'medical_records',
            'fields' => 'complaint, diagnosis, assessment, pain scale',
            'status' => 'readiness',
        ],
        [
            'resource' => 'Procedure / CarePlan',
            'local_data' => 'medical_records + home_exercises',
            'fields' => 'treatment, program pasien, home exercise, next control',
            'status' => 'readiness',
        ],
    ];

    return view('admin-satu-sehat-readiness', compact(
        'summary',
        'patientRows',
        'visitRows',
        'therapistRows',
        'fhirMapping'
    ));
});


/*
|--------------------------------------------------------------------------
| Patient Portal Plus
|--------------------------------------------------------------------------
*/

Route::get('/patient', function () {
    if (session('patient_logged_in')) {
        return redirect('/patient/dashboard');
    }

    return view('patient-login');
});

Route::post('/patient/login', function (Request $request) {
    $request->validate([
        'whatsapp' => 'required|string|max:50',
        'birth_date' => 'required|date',
    ]);

    $rawWhatsapp = trim((string) $request->whatsapp);
    $digits = preg_replace('/\D+/', '', $rawWhatsapp);

    $withoutCountryPrefix = $digits;
    if (str_starts_with($withoutCountryPrefix, '62')) {
        $withoutCountryPrefix = '0' . substr($withoutCountryPrefix, 2);
    }

    $candidates = collect([
        $rawWhatsapp,
        $digits,
        '0' . ltrim($digits, '0'),
        '62' . ltrim($digits, '0'),
        '+62' . ltrim($digits, '0'),
        $withoutCountryPrefix,
        '62' . ltrim($withoutCountryPrefix, '0'),
        '+62' . ltrim($withoutCountryPrefix, '0'),
    ])->filter()->unique()->values();

    $patient = Patient::whereDate('birth_date', $request->birth_date)
        ->where(function ($query) use ($candidates) {
            foreach ($candidates as $candidate) {
                $query->orWhere('whatsapp', $candidate);
            }
        })
        ->first();

    if (!$patient) {
        return back()
            ->withInput()
            ->withErrors([
                'whatsapp' => 'Nomor WhatsApp atau tanggal lahir tidak cocok dengan data pasien. Hubungi admin Khayra Physio untuk pengecekan.',
            ]);
    }

    session([
        'patient_logged_in' => true,
        'patient_id' => $patient->id,
        'patient_name' => $patient->full_name,
    ]);

    return redirect('/patient/dashboard');
});

Route::post('/patient/logout', function () {
    session()->forget([
        'patient_logged_in',
        'patient_id',
        'patient_name',
    ]);

    return redirect('/patient')->with('success', 'Anda berhasil logout.');
});

Route::get('/patient/dashboard', function () {
    if (!session('patient_logged_in')) {
        return redirect('/patient');
    }

    $patient = Patient::with([
        'visits.therapistRelation',
        'visits.medicalRecord.homeExercises',
        'visits.medicalRecord.histories',
        'visits.medicalRecord.comorbidities',
        'visits.medicalRecord.supportingData',
        'billings.items.inventoryItem',
    ])->findOrFail(session('patient_id'));

    $visits = $patient->visits()
        ->with(['therapistRelation', 'medicalRecord.homeExercises', 'medicalRecord.histories', 'medicalRecord.comorbidities', 'medicalRecord.supportingData'])
        ->latest('visit_date')
        ->get();

    $billings = $patient->billings()
        ->with(['items.inventoryItem', 'visit'])
        ->latest('invoice_date')
        ->get();

    $latestVisit = $visits->first();

    $latestMedicalRecord = $visits
        ->map(fn ($visit) => $visit->medicalRecord)
        ->filter()
        ->sortByDesc(fn ($record) => optional($record->updated_at)->timestamp ?? 0)
        ->first();

    $latestControlRecord = $visits
        ->map(fn ($visit) => $visit->medicalRecord)
        ->filter()
        ->filter(fn ($record) => $record->date_of_control || $record->control_plan || $record->frequency_per_week || $record->total_session)
        ->sortByDesc(fn ($record) => optional($record->date_of_control)->timestamp ?? optional($record->updated_at)->timestamp ?? 0)
        ->first();

    if ($latestControlRecord) {
        $latestMedicalRecord = $latestControlRecord;
    }

    $completedVisits = $visits->where('status', 'completed')->count();
    $activeVisits = $visits->whereIn('status', ['scheduled', 'in_progress'])->count();

    $validBillings = $billings->where('payment_status', '!=', 'void');

    $moneyPaid = function ($billing) {
        if ($billing->payment_status === 'void') {
            return 0;
        }

        $paid = (float) ($billing->paid_amount ?? 0);
        $amount = (float) ($billing->amount ?? 0);

        if ($billing->payment_status === 'paid' && $paid <= 0) {
            return $amount;
        }

        return $paid;
    };

    $moneyOutstanding = function ($billing) {
        if (($billing->payment_status ?? null) === 'void') {
            return 0;
        }

        $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
        $paid = (float) ($billing->paid_amount ?? 0);
        $status = $billing->payment_status ?: 'unpaid';

        if ($status === 'paid') {
            return 0;
        }

        return max($total - $paid, 0);
    };

    $paidTotal = $validBillings->sum(fn ($billing) => $moneyPaid($billing));
    $outstandingTotal = $validBillings->sum(fn ($billing) => $moneyOutstanding($billing));

    $homeExercises = $visits
        ->flatMap(function ($visit) {
            if (!$visit->medicalRecord) {
                return collect();
            }

            return $visit->medicalRecord->homeExercises->map(function ($exercise) use ($visit) {
                return [
                    'visit' => $visit,
                    'exercise' => $exercise,
                ];
            });
        })
        ->values();

    $progressEntries = collect();

    if (class_exists(\App\Models\PatientProgressEntry::class)) {
        $progressEntries = \App\Models\PatientProgressEntry::where('patient_id', $patient->id)
            ->latest()
            ->take(8)
            ->get();
    }

    $therapyHighlights = [
        'complaint' => optional($latestMedicalRecord)->complaint,
        'assessment' => optional($latestMedicalRecord)->assessment ?: optional($latestMedicalRecord)->physiotherapy_diagnosis,
        'treatment' => optional($latestMedicalRecord)->treatment_given ?: optional($latestMedicalRecord)->treatment,
        'response' => optional($latestMedicalRecord)->response_to_treatment ?: optional($latestMedicalRecord)->progress_note,
        'recommendation' => optional($latestMedicalRecord)->recommendation ?: optional($latestMedicalRecord)->next_session_plan,
        'control_date' => optional(optional($latestMedicalRecord)->date_of_control)->format('Y-m-d'),
        'pain_scale' => optional($latestMedicalRecord)->pain_scale,
        'patient_goal' => optional($latestMedicalRecord)->patient_goal,
        'program_patient' => optional($latestMedicalRecord)->program_patient,
    ];

    return view('patient-dashboard-plus', compact(
        'patient',
        'visits',
        'billings',
        'latestVisit',
        'latestMedicalRecord',
        'completedVisits',
        'activeVisits',
        'paidTotal',
        'outstandingTotal',
        'homeExercises',
        'progressEntries',
        'therapyHighlights'
    ));
});

Route::get('/patient/invoices/{id}', function ($id) {
    if (!session('patient_logged_in')) {
        return redirect('/patient');
    }

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])
        ->where('patient_id', session('patient_id'))
        ->findOrFail($id);

    return view('patient-invoice-detail', compact('billing'));
});

Route::get('/patient/visits/{id}', function ($id) {
    if (!session('patient_logged_in')) {
        return redirect('/patient');
    }

    $visit = Visit::with([
        'patient',
        'therapistRelation',
        'medicalRecord.histories',
        'medicalRecord.comorbidities',
        'medicalRecord.supportingData',
        'medicalRecord.homeExercises',
    ])
        ->where('patient_id', session('patient_id'))
        ->findOrFail($id);

    $progressEntries = PatientProgressEntry::where('patient_id', session('patient_id'))
        ->where('visit_id', $visit->id)
        ->latest('entry_date')
        ->get();

    $medicalRecordUpdateLogs = collect();

    if ($visit->medicalRecord) {
        $medicalRecordUpdateLogs = MedicalRecordUpdateLog::where('medical_record_id', $visit->medicalRecord->id)
            ->latest('snapshot_date')
            ->get();
    }

    return view('patient-visit-detail', compact('visit', 'progressEntries', 'medicalRecordUpdateLogs'));
});

Route::get('/admin/exercise-library', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $search = trim((string) $request->query('search'));
    $category = $request->query('category');

    $query = HomeExerciseTemplate::query()->latest();

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('category', 'like', '%' . $search . '%')
                ->orWhere('instructions', 'like', '%' . $search . '%')
                ->orWhere('target_area', 'like', '%' . $search . '%');
        });
    }

    if ($category) {
        $query->where('category', $category);
    }

    $templates = $query->get();

    $categories = HomeExerciseTemplate::whereNotNull('category')
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return view('admin-exercise-library', compact('templates', 'categories', 'search', 'category'));
});

Route::post('/admin/exercise-library', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'category' => 'nullable|string|max:120',
        'target_area' => 'nullable|string|max:120',
        'difficulty' => 'required|in:easy,medium,hard',
        'instructions' => 'required|string',
        'dosage' => 'nullable|string|max:255',
        'video_url' => 'nullable|string|max:500',
        'status' => 'required|in:active,inactive',
    ]);

    HomeExerciseTemplate::create($request->only([
        'name',
        'category',
        'target_area',
        'difficulty',
        'instructions',
        'dosage',
        'video_url',
        'status',
    ]));

    return redirect('/admin/exercise-library')->with('success', 'Template home exercise berhasil ditambahkan.');
});

Route::post('/admin/exercise-library/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    HomeExerciseTemplate::findOrFail($id)->delete();

    return redirect('/admin/exercise-library')->with('success', 'Template home exercise berhasil dihapus.');
});


Route::post('/admin/patients/{patientId}/progress/{progressId}/delete', function ($patientId, $progressId) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $progress = PatientProgressEntry::where('patient_id', $patientId)->findOrFail($progressId);
    $progress->delete();

    return redirect('/admin/patients/' . $patientId)->with('success', 'Progress pasien berhasil dihapus.');
});


Route::get('/admin/patients/{patientId}/progress/{progressId}/edit', function ($patientId, $progressId) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::with(['visits.therapistRelation'])->findOrFail($patientId);

    $progress = PatientProgressEntry::where('patient_id', $patient->id)
        ->findOrFail($progressId);

    $visits = $patient->visits()
        ->with('therapistRelation')
        ->latest()
        ->get();

    return view('admin-patient-progress-edit', compact('patient', 'progress', 'visits'));
});

Route::post('/admin/patients/{patientId}/progress/{progressId}/update', function (Request $request, $patientId, $progressId) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::findOrFail($patientId);

    $progress = PatientProgressEntry::where('patient_id', $patient->id)
        ->findOrFail($progressId);

    $request->validate([
        'entry_date' => 'required|date',
        'visit_id' => 'nullable|exists:visits,id',
        'pain_scale' => 'nullable|integer|min:0|max:10',
        'rom_notes' => 'nullable|string',
        'functional_goal' => 'nullable|string',
        'progress_notes' => 'nullable|string',
    ]);

    $visitId = $request->visit_id ?: null;

    if ($visitId) {
        $visitBelongsToPatient = Visit::where('id', $visitId)
            ->where('patient_id', $patient->id)
            ->exists();

        if (!$visitBelongsToPatient) {
            return back()
                ->withInput()
                ->withErrors(['visit_id' => 'Visit yang dipilih tidak sesuai dengan patient ini.']);
        }
    }

    $progress->update([
        'visit_id' => $visitId,
        'entry_date' => $request->entry_date,
        'pain_scale' => $request->pain_scale,
        'rom_notes' => $request->rom_notes,
        'functional_goal' => $request->functional_goal,
        'progress_notes' => $request->progress_notes,
    ]);

    return redirect('/admin/patients/' . $patient->id)
        ->with('success', 'Progress pasien berhasil diperbarui.');
});

Route::post('/admin/patients/{patientId}/progress/{progressId}/delete', function ($patientId, $progressId) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::findOrFail($patientId);

    $progress = PatientProgressEntry::where('patient_id', $patient->id)
        ->findOrFail($progressId);

    $progress->delete();

    return redirect('/admin/patients/' . $patient->id)
        ->with('success', 'Progress pasien berhasil dihapus.');
});


Route::post('/admin/patients/{id}/progress', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patient = Patient::findOrFail($id);

    $request->validate([
        'visit_id' => 'nullable|exists:visits,id',
        'entry_date' => 'required|date',
        'pain_scale' => 'nullable|integer|min:0|max:10',
        'rom_notes' => 'nullable|string',
        'functional_goal' => 'nullable|string',
        'progress_notes' => 'nullable|string',
    ]);

    PatientProgressEntry::create([
        'patient_id' => $patient->id,
        'visit_id' => $request->visit_id ?: null,
        'entry_date' => $request->entry_date,
        'pain_scale' => $request->pain_scale,
        'rom_notes' => $request->rom_notes,
        'functional_goal' => $request->functional_goal,
        'progress_notes' => $request->progress_notes,
    ]);

    return redirect('/admin/patients/' . $patient->id . '#progress-tracking')->with('success', 'Progress pasien berhasil dicatat.');
});


Route::get('/admin/promos', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $promos = Promo::latest()->get();

    $activeNowPromos = $promos->filter(fn ($promo) => $promo->availability_status === 'active')->count();
    $inactivePromos = $promos->filter(fn ($promo) => $promo->availability_status === 'inactive')->count();
    $upcomingPromos = $promos->filter(fn ($promo) => $promo->availability_status === 'upcoming')->count();
    $expiredPromos = $promos->filter(fn ($promo) => $promo->availability_status === 'expired')->count();

    return view('admin-promos', compact(
        'promos',
        'activeNowPromos',
        'inactivePromos',
        'upcomingPromos',
        'expiredPromos'
    ));
});

Route::get('/admin/promos/create', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return view('admin-promo-create');
});

Route::post('/admin/promos', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'code' => 'required|string|max:100|unique:promos,code',
        'name' => 'required|string|max:255',
        'discount_type' => 'required|in:nominal,percent',
        'discount_value' => 'required|numeric|min:0',
        'minimum_purchase' => 'nullable|numeric|min:0',
        'maximum_discount' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
    ]);

    Promo::create([
        'code' => strtoupper(trim($request->code)),
        'name' => $request->name,
        'discount_type' => $request->discount_type,
        'discount_value' => $request->discount_value,
        'minimum_purchase' => $request->minimum_purchase ?: 0,
        'maximum_discount' => $request->maximum_discount ?: 0,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
        'notes' => $request->notes,
    ]);

    return redirect('/admin/promos')->with('success', 'Promo berhasil ditambahkan.');
});

Route::get('/admin/promos/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $promo = Promo::findOrFail($id);

    return view('admin-promo-edit', compact('promo'));
});

Route::post('/admin/promos/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $promo = Promo::findOrFail($id);

    $request->validate([
        'code' => 'required|string|max:100|unique:promos,code,' . $promo->id,
        'name' => 'required|string|max:255',
        'discount_type' => 'required|in:nominal,percent',
        'discount_value' => 'required|numeric|min:0',
        'minimum_purchase' => 'nullable|numeric|min:0',
        'maximum_discount' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
    ]);

    $promo->update([
        'code' => strtoupper(trim($request->code)),
        'name' => $request->name,
        'discount_type' => $request->discount_type,
        'discount_value' => $request->discount_value,
        'minimum_purchase' => $request->minimum_purchase ?: 0,
        'maximum_discount' => $request->maximum_discount ?: 0,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
        'notes' => $request->notes,
    ]);

    return redirect('/admin/promos')->with('success', 'Promo berhasil diperbarui.');
});

Route::post('/admin/promos/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    Promo::findOrFail($id)->delete();

    return redirect('/admin/promos')->with('success', 'Promo berhasil dihapus.');
});


Route::get('/admin/billings', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $status = $request->query('status');
    $paymentMethod = $request->query('payment_method');
    $search = trim((string) $request->query('search'));
    $dateFrom = $request->query('date_from');
    $dateTo = $request->query('date_to');

    $query = Billing::with(['patient', 'visit', 'items.inventoryItem'])->latest();

    if ($status && in_array($status, ['paid', 'unpaid', 'partial', 'void'])) {
        $query->where('payment_status', $status);
    }

    if ($paymentMethod) {
        $query->where('payment_method', $paymentMethod);
    }

    if ($dateFrom) {
        $query->whereDate('invoice_date', '>=', $dateFrom);
    }

    if ($dateTo) {
        $query->whereDate('invoice_date', '<=', $dateTo);
    }

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', '%' . $search . '%')
              ->orWhereHas('patient', function ($patientQuery) use ($search) {
                  $patientQuery->where('full_name', 'like', '%' . $search . '%')
                      ->orWhere('medical_record_number', 'like', '%' . $search . '%')
                      ->orWhere('whatsapp', 'like', '%' . $search . '%');
              });
        });
    }

    $billings = $query->get();

    $summaryQuery = Billing::query();

    if ($dateFrom) {
        $summaryQuery->whereDate('invoice_date', '>=', $dateFrom);
    }

    if ($dateTo) {
        $summaryQuery->whereDate('invoice_date', '<=', $dateTo);
    }

    $summaryBillings = $summaryQuery->get();

    $moneyPaid = function ($billing) {
        if ($billing->payment_status === 'void') {
            return 0;
        }

        $paid = (float) ($billing->paid_amount ?? 0);
        $amount = (float) ($billing->amount ?? 0);

        if ($billing->payment_status === 'paid' && $paid <= 0) {
            return $amount;
        }

        return $paid;
    };

    $moneyOutstanding = function ($billing) {
        if (($billing->payment_status ?? null) === 'void') {
            return 0;
        }

        $total = (float) ($billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0);
        $paid = (float) ($billing->paid_amount ?? 0);
        $status = $billing->payment_status ?: 'unpaid';

        if ($status === 'paid') {
            return 0;
        }

        return max($total - $paid, 0);
    };

    $totalTransactions = $summaryBillings->count();
    $grossRevenue = $summaryBillings->where('payment_status', '!=', 'void')->sum('subtotal_amount');
    $netRevenue = $summaryBillings->where('payment_status', '!=', 'void')->sum('amount');
    $totalDiscount = $summaryBillings->where('payment_status', '!=', 'void')->sum('discount_amount');
    $paidAmount = $summaryBillings->where('payment_status', '!=', 'void')->sum(fn ($billing) => $moneyPaid($billing));
    $remainingAmount = $summaryBillings->where('payment_status', '!=', 'void')->sum(fn ($billing) => $moneyOutstanding($billing));

    $paidCount = $summaryBillings->where('payment_status', 'paid')->count();
    $unpaidCount = $summaryBillings->where('payment_status', 'unpaid')->count();
    $partialCount = $summaryBillings->where('payment_status', 'partial')->count();
    $voidCount = $summaryBillings->where('payment_status', 'void')->count();

    $paymentMethods = Billing::query()
        ->whereNotNull('payment_method')
        ->where('payment_method', '!=', '')
        ->distinct()
        ->pluck('payment_method')
        ->values();

    return view('admin-billings', compact(
        'billings',
        'status',
        'paymentMethod',
        'search',
        'dateFrom',
        'dateTo',
        'totalTransactions',
        'grossRevenue',
        'netRevenue',
        'totalDiscount',
        'paidAmount',
        'remainingAmount',
        'paidCount',
        'unpaidCount',
        'partialCount',
        'voidCount',
        'paymentMethods'
    ));
});

Route::get('/admin/billings/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::latest()->get();
    $visits = Visit::with('patient')->latest()->get();

    $selectedVisit = null;
    $selectedPatientId = $request->query('patient_id');

    if ($request->query('visit_id')) {
        $selectedVisit = Visit::with('patient')->find($request->query('visit_id'));

        if ($selectedVisit) {
            $selectedPatientId = $selectedVisit->patient_id;
        }
    }

    return view('admin-billing-create', compact('patients', 'visits', 'selectedVisit', 'selectedPatientId'));
});

Route::post('/admin/billings', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'invoice_date' => 'required|date',
        'amount' => 'required|numeric|min:0',
        'payment_status' => 'required|in:paid,unpaid,partial',
        'payment_method' => 'nullable|in:qr,debit,credit,bank_bca,bank_bni,bank_mandiri,insurance',
        'payment_detail_notes' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $latestBilling = Billing::latest('id')->first();
    $nextNumber = $latestBilling ? $latestBilling->id + 1 : 1;

    Billing::create([
        'patient_id' => $request->patient_id,
        'visit_id' => $request->visit_id ?: null,
        'invoice_number' => 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
        'invoice_date' => $request->invoice_date,
        'amount' => $request->amount,
        'payment_status' => $request->payment_status ?: 'unpaid',
        'payment_method' => $request->payment_method,
        'payment_detail_notes' => $request->payment_detail_notes,
        'notes' => $request->notes,
    ]);

    return redirect('/admin/billings')->with('success', 'Billing berhasil ditambahkan!');
});

Route::get('/admin/billings/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])->findOrFail($id);
    return view('admin-billing-detail', compact('billing'));
});

Route::get('/admin/billings/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])->findOrFail($id);
    $patients = Patient::latest()->get();
    $visits = Visit::with('patient')->latest()->get();

    return view('admin-billing-edit', compact('billing', 'patients', 'visits'));
});

Route::post('/admin/billings/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])->findOrFail($id);

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'nullable|exists:visits,id',
        'invoice_date' => 'required|date',
        'amount' => 'required|numeric|min:0',
        'payment_status' => 'required|in:paid,unpaid,partial',
        'payment_method' => 'nullable|in:qr,debit,credit,bank_bca,bank_bni,bank_mandiri,insurance',
        'payment_detail_notes' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $billing->patient_id = $request->patient_id;
    $billing->visit_id = $request->visit_id ?: null;
    $billing->invoice_date = $request->invoice_date;
    $billing->amount = $request->amount;
    $billing->payment_status = $request->payment_status;
    $billing->payment_method = $request->payment_method;
    $billing->payment_detail_notes = $request->payment_detail_notes;
    $billing->notes = $request->notes;
    $billing->save();

    return redirect('/admin/billings/' . $billing->id)->with('success', 'Billing berhasil diperbarui.');
});

Route::get('/admin/billings/{id}/print', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])->findOrFail($id);
    return view('admin-billing-print', compact('billing'));
});

Route::post('/admin/billings/{id}/status', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'payment_status' => 'required|in:paid,unpaid,partial',
    ]);

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])->findOrFail($id);
    $billing->payment_status = $request->payment_status;

    // AUTO_SYNC_PAID_AMOUNT_FROM_STATUS
    $billingTotal = $billing->grand_total ?? $billing->total_amount ?? $billing->amount ?? 0;

    if ($request->payment_status === 'paid') {
        $billing->paid_amount = $billingTotal;
    } elseif ($request->payment_status === 'unpaid') {
        $billing->paid_amount = 0;
    } elseif ($request->payment_status === 'partial') {
        $billing->paid_amount = min((float) ($billing->paid_amount ?? 0), (float) $billingTotal);
    }

    $billing->save();

    return redirect('/admin/billings')->with('success', 'Status billing berhasil diperbarui!');
});


Route::get('/admin/billings/{id}/void', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    return redirect('/admin/billings/' . $id)->with('success', 'Untuk void billing, gunakan tombol VOID dari halaman detail billing.');
});

Route::post('/admin/billings/{id}/void', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'void_reason' => 'required|string|max:1000',
    ]);

    $billing = Billing::with(['items.inventoryItem'])->findOrFail($id);

    if ($billing->payment_status === 'void' || $billing->voided_at) {
        return redirect('/admin/billings/' . $billing->id)->with('success', 'Billing ini sudah di-void sebelumnya.');
    }

    DB::transaction(function () use ($billing, $request) {
        foreach ($billing->items as $billingItem) {
            if ($billingItem->item_type !== 'inventory' || !$billingItem->inventory_item_id) {
                continue;
            }

            $item = InventoryItem::lockForUpdate()->find($billingItem->inventory_item_id);

            if (!$item) {
                continue;
            }

            $stockBefore = $item->stock;
            $stockAfter = $stockBefore + $billingItem->quantity;

            $item->stock = $stockAfter;
            $item->save();

            InventoryStockMovement::create([
                'inventory_item_id' => $item->id,
                'voided_billing_id' => $billing->id,
                'type' => 'in',
                'quantity' => $billingItem->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference' => 'VOID ' . ($billing->invoice_number ?: ('Billing #' . $billing->id)),
                'notes' => 'Inventory dikembalikan karena billing di-void. Reason: ' . $request->void_reason,
            ]);
        }

        $billing->update([
            'original_payment_status' => $billing->payment_status,
            'payment_status' => 'void',
            'voided_at' => now(),
            'void_reason' => $request->void_reason,
            'paid_amount' => 0,
            'change_amount' => 0,
            'remaining_amount' => 0,
        ]);
    });

    return redirect('/admin/billings/' . $billing->id)->with('success', 'Billing berhasil di-void dan stok inventory sudah dikembalikan.');
});


Route::post('/admin/billings/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::with(['patient', 'visit', 'items.inventoryItem'])->findOrFail($id);
    $billing->delete();

    return redirect('/admin/billings')->with('success', 'Billing berhasil dihapus.');
});