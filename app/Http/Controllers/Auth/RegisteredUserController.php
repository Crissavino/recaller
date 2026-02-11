<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Store plan selection in session for after registration
        if ($request->has('plan')) {
            session([
                'pending_plan' => $request->get('plan'),
                'pending_interval' => $request->get('interval', 'monthly'),
            ]);
        }

        // If no plan selected, redirect to pricing
        if (!session('pending_plan')) {
            return redirect()->route('pricing')
                ->with('info', __('subscription.select_plan_first'));
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            'terms.required' => __('legal.must_accept_terms'),
            'terms.accepted' => __('legal.must_accept_terms'),
        ]);

        $user = User::create([
            'name' => $request->clinic_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'terms_accepted_at' => now(),
        ]);

        // Create a default clinic for the user
        $clinic = Clinic::create([
            'name' => $request->clinic_name,
            'slug' => Str::slug($request->clinic_name . '-' . Str::random(6)),
            'timezone' => 'America/New_York',
            'is_active' => true,
        ]);

        // Attach user to clinic as owner
        $clinic->users()->attach($user->id, ['role' => 'owner']);

        event(new Registered($user));

        $user->notify(new WelcomeNotification());

        Auth::login($user);

        // Redirect to checkout with the selected plan
        $pendingPlan = session('pending_plan');
        $pendingInterval = session('pending_interval', 'monthly');

        if ($pendingPlan) {
            session()->forget(['pending_plan', 'pending_interval']);

            return redirect()->route('subscription.checkout', [
                'plan' => $pendingPlan,
                'interval' => $pendingInterval,
            ]);
        }

        // Fallback to pricing if somehow no plan (shouldn't happen with new flow)
        return redirect()->route('pricing');
    }
}
