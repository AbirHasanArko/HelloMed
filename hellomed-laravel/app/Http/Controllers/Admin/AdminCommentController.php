<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCommentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = ArticleComment::query()->with(['user', 'article']);

        $result = ArticleComment::handleSearchAndFilters($request, $query, function ($comment) {
            return [
                'id' => $comment->id,
                'title' => $comment->user->name,
                'subtitle' => Str::limit($comment->comment, 40)
            ];
        });

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return view('admin.comments.index', [
            'comments' => $result->latest()->paginate(20)->withQueryString(),
            'routePrefix' => 'admin',
        ]);
    }

    public function edit(ArticleComment $comment)
    {
        return view('admin.comments.edit', [
            'comment' => $comment,
            'routePrefix' => 'admin',
        ]);
    }

    public function update(Request $request, ArticleComment $comment)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        $comment->update($validated);

        return redirect()->route('admin.comments.index')->with('success', 'Comment updated successfully.');
    }

    public function destroy(ArticleComment $comment)
    {
        $comment->delete();
        return redirect()->route('admin.comments.index')->with('success', 'Comment deleted successfully.');
    }
}
