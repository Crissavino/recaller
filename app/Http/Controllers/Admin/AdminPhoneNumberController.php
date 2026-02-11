<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPhoneNumberController extends Controller
{
    public function index(Request $request): View
    {
        $query = ClinicPhoneNumber::with(['clinic', 'linkedWhatsAppNumber']);

        // Filters
        if ($clinicId = $request->get('clinic')) {
            $query->where('clinic_id', $clinicId);
        }

        if ($country = $request->get('country')) {
            $query->where('country', strtoupper($country));
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($provider = $request->get('provider')) {
            $query->where('provider', $provider);
        }

        $phoneNumbers = $query->orderBy('clinic_id')
            ->orderBy('type')
            ->paginate(25);

        $clinics = Clinic::orderBy('name')->get();

        // Get WhatsApp numbers for linking dropdown
        $whatsAppNumbers = ClinicPhoneNumber::where('type', 'whatsapp')
            ->where('is_active', true)
            ->with('clinic')
            ->get()
            ->groupBy('clinic_id');

        return view('admin.phone-numbers.index', compact('phoneNumbers', 'clinics', 'whatsAppNumbers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'phone_number' => 'required|string|max:30',
            'country' => 'nullable|string|max:2',
            'type' => 'required|in:voice,whatsapp',
            'provider' => 'required|string|in:twilio,vonage,messagebird',
            'friendly_name' => 'nullable|string|max:255',
            'forward_to_phone' => 'nullable|string|max:30',
            'forward_timeout_seconds' => 'nullable|integer|min:5|max:60',
            'linked_whatsapp_number_id' => 'nullable|exists:clinic_phone_numbers,id',
        ]);

        // Normalize phone number
        $phoneNumber = $validated['phone_number'];
        if (!str_starts_with($phoneNumber, '+')) {
            $phoneNumber = '+' . ltrim($phoneNumber, '+');
        }

        // For WhatsApp numbers, add prefix
        if ($validated['type'] === 'whatsapp' && !str_starts_with($phoneNumber, 'whatsapp:')) {
            $phoneNumber = 'whatsapp:' . $phoneNumber;
        }

        // Check if number already exists
        $existing = ClinicPhoneNumber::where('phone_number', $phoneNumber)->first();
        if ($existing) {
            return back()->with('error', "This phone number is already registered to clinic: {$existing->clinic?->name}");
        }

        $clinic = Clinic::find($validated['clinic_id']);

        ClinicPhoneNumber::create([
            'clinic_id' => $validated['clinic_id'],
            'phone_number' => $phoneNumber,
            'country' => $validated['country'] ? strtoupper($validated['country']) : null,
            'type' => $validated['type'],
            'provider' => $validated['provider'],
            'friendly_name' => $validated['friendly_name'] ?? $clinic->name,
            'is_active' => true,
            'voice_enabled' => $validated['type'] === 'voice',
            'sms_enabled' => true,
            'forward_to_phone' => $validated['forward_to_phone'] ?? null,
            'forward_timeout_seconds' => $validated['forward_timeout_seconds'] ?? 20,
            'linked_whatsapp_number_id' => $validated['linked_whatsapp_number_id'] ?? null,
        ]);

        return back()->with('success', "Phone number added to {$clinic->name}");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $phoneNumber = ClinicPhoneNumber::findOrFail($id);

        $validated = $request->validate([
            'friendly_name' => 'nullable|string|max:255',
            'forward_to_phone' => 'nullable|string|max:30',
            'forward_timeout_seconds' => 'nullable|integer|min:5|max:60',
            'linked_whatsapp_number_id' => 'nullable|exists:clinic_phone_numbers,id',
        ]);

        $phoneNumber->update([
            'friendly_name' => $validated['friendly_name'],
            'forward_to_phone' => $validated['forward_to_phone'],
            'forward_timeout_seconds' => $validated['forward_timeout_seconds'] ?? 20,
            'linked_whatsapp_number_id' => $validated['linked_whatsapp_number_id'],
        ]);

        return back()->with('success', 'Phone number updated.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $phoneNumber = ClinicPhoneNumber::findOrFail($id);
        $phoneNumber->update(['is_active' => !$phoneNumber->is_active]);

        $status = $phoneNumber->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Phone number {$status}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $phoneNumber = ClinicPhoneNumber::findOrFail($id);

        // Check if there are linked numbers pointing to this one
        $linkedCount = ClinicPhoneNumber::where('linked_whatsapp_number_id', $id)->count();
        if ($linkedCount > 0) {
            return back()->with('error', 'Cannot delete: this number is linked to other voice numbers. Unlink them first.');
        }

        $phoneNumber->delete();

        return back()->with('success', 'Phone number deleted.');
    }
}
