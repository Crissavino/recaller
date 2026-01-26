<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $clinicId = $this->getClinicId($request);

        $conversations = Conversation::forClinic($clinicId)
            ->with(['lead.caller', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->where('is_active', true)
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('inbox.index', compact('conversations'));
    }

    private function getClinicId(Request $request): int
    {
        return $request->user()->clinics()->first()?->id ?? 0;
    }
}
