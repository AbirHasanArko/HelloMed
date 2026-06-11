<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\QnaQuestion;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QnaController extends Controller
{
    public function index(): View
    {
        return view('qna.index', [
            'questions' => QnaQuestion::query()
                ->with(['user', 'answers.user'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(QnaQuestion $question): View
    {
        return view('qna.show', [
            'question' => $question->load(['user', 'answers.user']),
        ]);
    }

    public function storeQuestion(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'patient', 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string', 'max:5000'],
        ]);

        $question = QnaQuestion::query()->create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'question' => $validated['question'],
            'status' => 'open',
        ]);

        AuditLogger::log('qna.question_created', $question, [], ['status' => 'open']);

        return redirect()->route('qna.show', $question)->with('status', 'Question posted successfully.');
    }

    public function storeAnswer(Request $request, QnaQuestion $question): RedirectResponse
    {
        abort_unless(in_array($request->user()?->role, ['doctor', 'staff', 'admin'], true), 403);

        $validated = $request->validate([
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        $answer = $question->answers()->create([
            'user_id' => $request->user()->id,
            'answer' => $validated['answer'],
            'is_official' => true,
        ]);

        if ($question->status !== 'answered') {
            $question->update(['status' => 'answered']);
        }

        AuditLogger::log('qna.answer_created', $question, [], [
            'answer_id' => $answer->id,
            'status' => $question->status,
        ]);

        return back()->with('status', 'Answer submitted successfully.');
    }
}
