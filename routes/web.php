<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\MedicalRecord;
use App\Models\Billing;
use App\Models\Therapist;
use App\Models\InformedConsent;
use App\Models\MedicalRecordHistory;
use App\Models\MedicalRecordComorbidity;
use App\Models\MedicalRecordSupportingData;
use App\Models\MedicalRecordHomeExercise;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    if (session('admin_logged_in')) {
        return redirect('/admin/dashboard');
    }

    return redirect('/admin/login');
});

Route::get('/booking', function () {
    return view('booking');
});

Route::post('/booking', function (Request $request) {
    $request->validate([
        'full_name' => 'required|string|max:255',
        'whatsapp' => 'required|string|max:50',
        'service' => 'required|string|max:255',
        'booking_date' => 'required|date',
        'booking_time' => 'required',
        'complaint' => 'nullable|string',
    ]);

    Booking::create([
        'patient_id' => null,
        'full_name' => $request->full_name,
        'whatsapp' => $request->whatsapp,
        'service' => $request->service,
        'booking_date' => $request->booking_date,
        'booking_time' => $request->booking_time,
        'complaint' => $request->complaint,
        'status' => 'pending',
    ]);

    return redirect('/booking')->with('success', 'Booking berhasil dikirim!');
});

Route::get('/patient', function () {
    return view('patient-portal');
});

Route::post('/patient/search', function (Request $request) {
    $request->validate([
        'whatsapp' => 'required|string|max:50',
        'birth_date' => 'required|date',
    ]);

    $patient = Patient::with([
        'visits' => function ($query) {
            $query->with([
                'medicalRecord.homeExercises'
            ])->latest();
        },
        'billings' => function ($query) {
            $query->latest();
        }
    ])
    ->where('whatsapp', $request->whatsapp)
    ->whereDate('birth_date', $request->birth_date)
    ->first();

    if (!$patient) {
        return redirect('/patient')->with('error', 'Data patient tidak ditemukan. Pastikan nomor WhatsApp dan tanggal lahir sesuai.');
    }

    return view('patient-portal-detail', compact('patient'));
});

Route::get('/patient/billings/{id}', function ($id) {
    $billing = Billing::with(['patient', 'visit'])->findOrFail($id);

    return view('patient-billing-detail', compact('billing'));
});

Route::get('/admin/login', function () {
    if (session('admin_logged_in')) {
        return redirect('/admin/dashboard');
    }

    return view('admin-login');
});

Route::post('/admin/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($request->email === 'admin@khayra.com' && $request->password === '12345678') {
        session(['admin_logged_in' => true]);
        return redirect('/admin/dashboard');
    }

    return redirect('/admin/login')->with('error', 'Email atau password salah.');
});

Route::post('/admin/logout', function () {
    session()->forget('admin_logged_in');
    return redirect('/admin/login');
});

Route::get('/admin/dashboard', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $totalBookings = Booking::count();
    $totalPatients = Patient::count();
    $totalVisits = Visit::count();
    $totalTherapists = Therapist::count();
    $totalBillings = Billing::count();
    $paidBillings = Billing::where('payment_status', 'paid')->count();
    $unpaidBillings = Billing::where('payment_status', 'unpaid')->count();
    $partialBillings = Billing::where('payment_status', 'partial')->count();

    $recentBookings = Booking::latest()->take(5)->get();
    $recentVisits = Visit::with(['patient', 'therapistRelation'])->latest()->take(5)->get();
    $recentBillings = Billing::with('patient')->latest()->take(5)->get();

    return view('admin-dashboard', compact(
        'totalBookings',
        'totalPatients',
        'totalVisits',
        'totalTherapists',
        'totalBillings',
        'paidBillings',
        'unpaidBillings',
        'partialBillings',
        'recentBookings',
        'recentVisits',
        'recentBillings'
    ));
});

Route::get('/admin/bookings', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $status = $request->query('status');
    $search = $request->query('search');

    $query = Booking::with('patient')->latest();

    if ($status && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        $query->where('status', $status);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')
              ->orWhere('whatsapp', 'like', '%' . $search . '%')
              ->orWhere('service', 'like', '%' . $search . '%');
        });
    }

    $bookings = $query->get();

    return view('admin-bookings', [
        'bookings' => $bookings,
        'currentStatus' => $status,
        'currentSearch' => $search,
    ]);
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
    return view('admin-booking-edit', compact('booking'));
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
        'complaint' => 'nullable|string',
        'status' => 'required|in:pending,confirmed,completed,cancelled',
    ]);

    $booking->full_name = $request->full_name;
    $booking->whatsapp = $request->whatsapp;
    $booking->service = $request->service;
    $booking->booking_date = $request->booking_date;
    $booking->booking_time = $request->booking_time;
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
        'status' => 'required|in:pending,confirmed,completed,cancelled',
    ]);

    $booking = Booking::findOrFail($id);
    $booking->status = $request->status;
    $booking->save();

    return redirect('/admin/bookings')->with('success', 'Status booking berhasil diperbarui!');
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
        'visits' => function ($query) {
            $query->with(['medicalRecord', 'therapistRelation'])->latest();
        },
        'billings' => function ($query) {
            $query->latest();
        },
        'informedConsents' => function ($query) {
            $query->with('visit')->latest();
        }
    ])->findOrFail($id);

    return view('admin-patient-detail', compact('patient'));
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

Route::get('/admin/visits', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $visits = Visit::with(['patient', 'booking', 'medicalRecord', 'therapistRelation'])->latest()->get();
    return view('admin-visits', compact('visits'));
});

Route::get('/admin/visits/create', function () {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::latest()->get();
    $bookings = Booking::with('patient')->latest()->get();
    $therapists = Therapist::where('status', 'active')->latest()->get();

    return view('admin-visit-create', compact('patients', 'bookings', 'therapists'));
});

Route::post('/admin/visits', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'therapist_id' => 'required|exists:therapists,id',
        'visit_date' => 'required|date',
        'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        'notes' => 'nullable|string',
        'booking_id' => 'nullable|exists:bookings,id',
    ]);

    $therapist = Therapist::where('status', 'active')->findOrFail($request->therapist_id);

    Visit::create([
        'patient_id' => $request->patient_id,
        'therapist_id' => $therapist->id,
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

    return view('admin-visit-edit', compact('visit', 'patients', 'bookings', 'therapists'));
});

Route::post('/admin/visits/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'therapist_id' => 'required|exists:therapists,id',
        'visit_date' => 'required|date',
        'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        'notes' => 'nullable|string',
        'booking_id' => 'nullable|exists:bookings,id',
    ]);

    $visit = Visit::findOrFail($id);
    $therapist = Therapist::findOrFail($request->therapist_id);

    $visit->patient_id = $request->patient_id;
    $visit->therapist_id = $therapist->id;
    $visit->booking_id = $request->booking_id ?: null;
    $visit->visit_date = $request->visit_date;
    $visit->therapist = $therapist->full_name;
    $visit->notes = $request->notes;
    $visit->status = $request->status;
    $visit->save();

    return redirect('/admin/visits')->with('success', 'Visit berhasil diperbarui!');
});

Route::get('/admin/visits/{id}/medical-record', function ($id) {
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

    return view('admin-medical-record', compact('visit'));
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

    $visits = Visit::with(['patient', 'medicalRecord'])
        ->where('therapist_id', $therapistId)
        ->latest()
        ->get();

    $totalVisits = Visit::where('therapist_id', $therapistId)->count();
    $scheduledVisits = Visit::where('therapist_id', $therapistId)->where('status', 'scheduled')->count();
    $inProgressVisits = Visit::where('therapist_id', $therapistId)->where('status', 'in_progress')->count();
    $completedVisits = Visit::where('therapist_id', $therapistId)->where('status', 'completed')->count();

    return view('therapist-dashboard', compact(
        'visits',
        'totalVisits',
        'scheduledVisits',
        'inProgressVisits',
        'completedVisits'
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
    ])->where('therapist_id', $therapistId)->findOrFail($id);

    return view('therapist-medical-record', compact('visit'));
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

        'subjective_examination' => 'nullable|string',
        'objective_examination' => 'nullable|string',
        'severity_level' => 'nullable|string|max:255',
        'irritability_level' => 'nullable|string|max:255',
        'nature_type' => 'nullable|string|max:255',
        'easing_factors' => 'nullable|string',
        'aggravating_factors' => 'nullable|string',
        'special_test_notes' => 'nullable|string',

        'physiotherapy_diagnosis' => 'nullable|string',
        'impairment' => 'nullable|string',
        'functional_limitation_clinical' => 'nullable|string',
        'patient_goal' => 'nullable|string',
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
        'response_to_treatment' => 'nullable|string',
        'next_session_plan' => 'nullable|string',

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

        'home_exercise_name.*' => 'nullable|string|max:255',
        'home_exercise_dosage.*' => 'nullable|string|max:255',
        'home_exercise_note.*' => 'nullable|string',
    ]);

    $therapistId = session('therapist_id');

    $visit = Visit::with('medicalRecord')->where('therapist_id', $therapistId)->findOrFail($id);

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

            'subjective_examination' => $request->subjective_examination,
            'objective_examination' => $request->objective_examination,
            'severity_level' => $request->severity_level,
            'irritability_level' => $request->irritability_level,
            'nature_type' => $request->nature_type,
            'easing_factors' => $request->easing_factors,
            'aggravating_factors' => $request->aggravating_factors,
            'special_test_notes' => $request->special_test_notes,

            'physiotherapy_diagnosis' => $request->physiotherapy_diagnosis,
            'impairment' => $request->impairment,
            'functional_limitation_clinical' => $request->functional_limitation_clinical,
            'patient_goal' => $request->patient_goal,
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
            'response_to_treatment' => $request->response_to_treatment,
            'next_session_plan' => $request->next_session_plan,

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

            if ($type || $date || $interpretation) {
                MedicalRecordSupportingData::create([
                    'medical_record_id' => $medicalRecord->id,
                    'data_date' => $date ?: null,
                    'data_type' => $type,
                    'interpretation' => $interpretation,
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

    return redirect('/therapist/visits/' . $visit->id . '/medical-record')
        ->with('success', 'Medical Record V2 berhasil disimpan oleh therapist!');
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
    ])->where('therapist_id', $therapistId)->findOrFail($id);

    return view('therapist-report', compact('visit'));
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
    ])->where('therapist_id', $therapistId)->findOrFail($id);

    return view('therapist-report-print', compact('visit'));
});

Route::get('/admin/billings', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $status = $request->query('status');
    $search = $request->query('search');

    $query = Billing::with(['patient', 'visit'])->latest();

    if ($status && in_array($status, ['paid', 'unpaid', 'partial'])) {
        $query->where('payment_status', $status);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', '%' . $search . '%')
              ->orWhere('payment_method', 'like', '%' . $search . '%')
              ->orWhereHas('patient', function ($patientQuery) use ($search) {
                  $patientQuery->where('full_name', 'like', '%' . $search . '%');
              });
        });
    }

    $billings = $query->get();

    $totalBillings = Billing::count();
    $paidBillings = Billing::where('payment_status', 'paid')->count();
    $unpaidBillings = Billing::where('payment_status', 'unpaid')->count();
    $partialBillings = Billing::where('payment_status', 'partial')->count();

    return view('admin-billings', compact(
        'billings',
        'totalBillings',
        'paidBillings',
        'unpaidBillings',
        'partialBillings',
        'status',
        'search'
    ));
});

Route::get('/admin/billings/create', function (Request $request) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $patients = Patient::latest()->get();
    $visits = Visit::with('patient')->latest()->get();

    $selectedVisit = null;
    $selectedPatientId = null;

    if ($request->visit_id) {
        $selectedVisit = Visit::with('patient')->find($request->visit_id);
        if ($selectedVisit) {
            $selectedPatientId = $selectedVisit->patient_id;
        }
    }

    return view('admin-billing-create', compact(
        'patients',
        'visits',
        'selectedVisit',
        'selectedPatientId'
    ));
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

    $billing = Billing::with(['patient', 'visit'])->findOrFail($id);
    return view('admin-billing-detail', compact('billing'));
});

Route::get('/admin/billings/{id}/edit', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::findOrFail($id);
    $patients = Patient::latest()->get();
    $visits = Visit::with('patient')->latest()->get();

    return view('admin-billing-edit', compact('billing', 'patients', 'visits'));
});

Route::post('/admin/billings/{id}/update', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::findOrFail($id);

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

    $billing = Billing::with(['patient', 'visit'])->findOrFail($id);
    return view('admin-billing-print', compact('billing'));
});

Route::post('/admin/billings/{id}/status', function (Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $request->validate([
        'payment_status' => 'required|in:paid,unpaid,partial',
    ]);

    $billing = Billing::findOrFail($id);
    $billing->payment_status = $request->payment_status;
    $billing->save();

    return redirect('/admin/billings')->with('success', 'Status billing berhasil diperbarui!');
});

Route::post('/admin/billings/{id}/delete', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $billing = Billing::findOrFail($id);
    $billing->delete();

    return redirect('/admin/billings')->with('success', 'Billing berhasil dihapus.');
});