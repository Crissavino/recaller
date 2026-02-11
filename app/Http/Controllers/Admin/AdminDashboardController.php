<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_clinics' => Clinic::count(),
            'total_users' => User::count(),
            'total_phone_numbers' => ClinicPhoneNumber::count(),
            'active_phone_numbers' => ClinicPhoneNumber::where('is_active', true)->count(),
            'total_leads' => Lead::count(),
            'leads_today' => Lead::whereDate('created_at', today())->count(),
        ];

        $recentClinics = Clinic::with('phoneNumbers')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $phoneNumbersByProvider = ClinicPhoneNumber::selectRaw('provider, count(*) as count')
            ->groupBy('provider')
            ->pluck('count', 'provider')
            ->toArray();

        return view('admin.dashboard', compact('stats', 'recentClinics', 'phoneNumbersByProvider'));
    }
}
