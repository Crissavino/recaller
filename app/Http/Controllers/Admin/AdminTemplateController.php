<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\MessageTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTemplateController extends Controller
{
    public function index(Request $request): View
    {
        $query = MessageTemplate::with('clinic');

        if ($clinicId = $request->get('clinic')) {
            $query->where('clinic_id', $clinicId);
        }

        if ($channel = $request->get('channel')) {
            $query->where('channel', $channel);
        }

        $templates = $query->orderBy('clinic_id')
            ->orderBy('trigger_event')
            ->paginate(25);

        $clinics = Clinic::orderBy('name')->get();

        return view('admin.templates.index', compact('templates', 'clinics'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $template = MessageTemplate::findOrFail($id);

        $validated = $request->validate([
            'content_sid' => 'nullable|string|max:50',
            'channel' => 'required|in:sms,whatsapp',
        ]);

        $template->update($validated);

        return back()->with('success', 'Template updated.');
    }
}
