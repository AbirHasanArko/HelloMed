<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\QnaQuestion;
use App\Models\QnaAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffQnaController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = QnaQuestion::query()->with(['user']);

        $result = QnaQuestion::handleSearchAndFilters($request, $query, function ($q) {
            return [
                'id' => $q->id,
                'title' => Str::limit($q->title, 40),
                'subtitle' => 'By: ' . $q->user->name . ' | ' . $q->status
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.qna.index', [
            'questions' => $result->orderByRaw("FIELD(status, 'pending', 'answered', 'resolved', 'rejected')")->latest()->paginate(20)->withQueryString(),
            'routePrefix' => 'staff',
        ]);
    }

    public function edit(QnaQuestion $qna)
    {
        return view('admin.qna.edit', [
            'question' => $qna->load(['user', 'answers.user']),
            'routePrefix' => 'staff',
        ]);
    }

    public function update(Request $request, QnaQuestion $qna)
    {
        $request->validate([
            'status' => 'required|in:open,resolved,closed',
            'new_answer' => 'nullable|string|max:2000',
        ]);

        $qna->update([
            'status' => $request->status,
        ]);

        if ($request->filled('new_answer')) {
            $qna->answers()->create([
                'user_id' => auth()->id(),
                'answer' => $request->new_answer,
                'is_official' => true,
            ]);
        }

        return back()->with('success', 'Q&A updated successfully.');
    }

    public function destroy(QnaQuestion $qna)
    {
        $qna->delete();
        return redirect()->route('staff.qna.index')->with('success', 'Question deleted successfully.');
    }
}
