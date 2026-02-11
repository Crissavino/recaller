<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicSubscription;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = ClinicSubscription::with(['clinic', 'plan']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($planId = $request->get('plan')) {
            $query->where('plan_id', $planId);
        }

        if ($clinicName = $request->get('clinic_name')) {
            $query->whereHas('clinic', function ($q) use ($clinicName) {
                $q->where('name', 'like', "%{$clinicName}%");
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(25);

        $stats = [
            'active' => ClinicSubscription::where('status', 'active')->count(),
            'trialing' => ClinicSubscription::where('status', 'trialing')->count(),
            'canceled' => ClinicSubscription::where('status', 'canceled')->count(),
            'mrr' => ClinicSubscription::where('status', 'active')
                ->where('interval', 'month')
                ->join('plans', 'clinic_subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.price_monthly_cents'),
        ];

        // Add annual subscriptions to MRR (divided by 12)
        $annualMrr = ClinicSubscription::where('status', 'active')
            ->where('interval', 'year')
            ->join('plans', 'clinic_subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price_annual_cents');
        $stats['mrr'] = ($stats['mrr'] + round($annualMrr / 12)) / 100;

        $plans = Plan::ordered()->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    public function transactions(Request $request): View
    {
        $query = PaymentTransaction::with(['clinic', 'subscription.plan']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(25);

        $stats = [
            'total_revenue' => PaymentTransaction::where('status', 'succeeded')
                ->where('type', 'charge')
                ->sum('amount_cents') / 100,
            'count' => PaymentTransaction::where('status', 'succeeded')->count(),
            'failed' => PaymentTransaction::where('status', 'failed')->count(),
        ];

        return view('admin.subscriptions.transactions', compact('transactions', 'stats'));
    }
}
