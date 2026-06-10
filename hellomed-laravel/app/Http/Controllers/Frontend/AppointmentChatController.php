<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentChatController extends Controller
{
    public function index(Request $request, Appointment $appointment): JsonResponse
    {
        $user = $request->user();
        $this->assertParticipant($appointment, $user->id);

        if ($appointment->status !== 'confirmed') {
            return response()->json([
                'enabled' => false,
                'messages' => [],
            ]);
        }

        $messages = $appointment->chatMessages()
            ->with('user')
            ->orderBy('created_at')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->user_id,
                    'sender_name' => $message->user?->name,
                    'is_mine' => $message->user_id === $user->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->format('M d, Y h:i A'),
                    'read_at' => $message->read_at?->format('M d, Y h:i A'),
                    'attachment_url' => $message->attachment_path ? Storage::disk('public')->url($message->attachment_path) : null,
                    'attachment_name' => $message->attachment_name,
                ];
            })
            ->values();

        return response()->json([
            'enabled' => true,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();
        $this->assertParticipant($appointment, $user->id);

        if ($appointment->status !== 'confirmed') {
            return back()->withErrors([
                'message' => 'Chat is available only after appointment confirmation.',
            ]);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120', 'required_without:message'],
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('appointment-chat-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentMime = $file->getMimeType();
            $attachmentSize = $file->getSize();
        }

        $appointment->chatMessages()->create([
            'user_id' => $user->id,
            'message' => $validated['message'] ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
        ]);

        return back()->with('status', 'Message sent.');
    }

    public function markRead(Request $request, Appointment $appointment): JsonResponse
    {
        $user = $request->user();
        $this->assertParticipant($appointment, $user->id);

        if ($appointment->status !== 'confirmed') {
            return response()->json(['updated' => 0]);
        }

        $updated = $appointment->chatMessages()
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['updated' => $updated]);
    }

    private function assertParticipant(Appointment $appointment, int $userId): void
    {
        $doctorUserId = $appointment->doctor?->user_id;
        $isPatient = $appointment->user_id === $userId;
        $isAssignedDoctor = $doctorUserId && $doctorUserId === $userId;

        abort_unless($isPatient || $isAssignedDoctor, 403);
    }
}
