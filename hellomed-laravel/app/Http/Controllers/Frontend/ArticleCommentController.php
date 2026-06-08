<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArticleCommentController extends Controller
{
    public function store(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->is_published, 404);
        abort_unless($request->user()?->role === 'patient', 403);

        $validated = $request->validate([
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:3000'],
        ]);

        $comment = $article->comments()->create([
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'] ?? null,
            'comment' => $validated['comment'],
        ]);

        AuditLogger::log('article.comment_submitted', $article, [], [
            'comment_id' => $comment->id,
            'rating' => $comment->rating,
        ]);

        return back()->with('status', 'Comment posted successfully.');
    }
}
