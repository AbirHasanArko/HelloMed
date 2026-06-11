<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        return view('doctor.articles.index', [
            'articles' => Article::query()
                ->with(['category', 'reviewer'])
                ->where('user_id', request()->user()->id)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('doctor.articles.create', [
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $status = $request->input('submit_action') === 'save_draft' ? 'draft' : 'pending_review';
        $validated = $request->validated();

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('article-covers', 'public');
        }

        Article::query()->create([
            ...collect($validated)->except('cover_image')->all(),
            'user_id' => $request->user()->id,
            'cover_image_path' => $coverImagePath,
            'is_featured' => false,
            'is_published' => false,
            'publication_status' => $status,
        ]);

        return redirect()->route('doctor.articles.index')->with('status', $status === 'draft' ? 'Draft saved.' : 'Article submitted for review.');
    }

    public function edit(Article $article): View
    {
        abort_unless($article->user_id === request()->user()->id, 403);
        abort_if($article->publication_status === 'published', 403, 'Published article cannot be edited from doctor panel.');

        return view('doctor.articles.edit', [
            'article' => $article,
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(StoreArticleRequest $request, Article $article): RedirectResponse
    {
        abort_unless($article->user_id === $request->user()->id, 403);
        abort_if($article->publication_status === 'published', 403, 'Published article cannot be edited from doctor panel.');

        $status = $request->input('submit_action') === 'save_draft' ? 'draft' : 'pending_review';
        $validated = $request->validated();

        $coverImagePath = $article->cover_image_path;
        if ($request->hasFile('cover_image')) {
            if (filled($article->cover_image_path)) {
                Storage::disk('public')->delete($article->cover_image_path);
            }
            $coverImagePath = $request->file('cover_image')->store('article-covers', 'public');
        }

        $article->update([
            ...collect($validated)->except('cover_image')->all(),
            'cover_image_path' => $coverImagePath,
            'is_featured' => false,
            'is_published' => false,
            'publication_status' => $status,
            'reviewed_by_user_id' => null,
            'reviewed_at' => null,
            'published_at' => null,
        ]);

        return redirect()->route('doctor.articles.index')->with('status', $status === 'draft' ? 'Draft updated.' : 'Article submitted for review.');
    }
}
