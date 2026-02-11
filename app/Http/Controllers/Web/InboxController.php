<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Enums\MessageDirection;
use Illuminate\Http\JsonResponse;
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

    public function unreadCount(Request $request): JsonResponse
    {
        $clinicId = $this->getClinicId($request);

        // Count conversations with inbound messages after last staff reply
        $count = Conversation::forClinic($clinicId)
            ->where('is_active', true)
            ->whereHas('messages', function ($query) {
                $query->where('direction', MessageDirection::INBOUND);
            })
            ->where(function ($query) {
                $query->whereNull('last_staff_reply_at')
                    ->orWhereColumn('last_message_at', '>', 'last_staff_reply_at');
            })
            ->count();

        return response()->json(['count' => $count]);
    }

    private function getClinicId(Request $request): int
    {
        return $request->user()->clinics()->first()?->id ?? 0;
    }
}
