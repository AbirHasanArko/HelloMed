<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Http\Requests\StoreArticleRequest;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Storage;

class AdminArticleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Article::class);

        return view('admin.articles.index', [
            'articles' => Article::query()->with(['category', 'author', 'reviewer'])->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Article::class);

        return view('admin.articles.create', [
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreArticleRequest $request)
    {
        $isPublished = $request->boolean('is_published');
        $validated = $request->validated();

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('article-covers', 'public');
        }

        $article = Article::create([
            ...collect($validated)->except('cover_image')->all(),
            'user_id' => $request->user()->id,
            'cover_image_path' => $coverImagePath,
            'is_featured' => $request->boolean('is_featured'),
            'featured_order' => $request->integer('featured_order', 0),
            'is_published' => $isPublished,
            'publication_status' => $isPublished ? 'published' : 'draft',
            'reviewed_by_user_id' => $isPublished ? $request->user()->id : null,
            'reviewed_at' => $isPublished ? now() : null,
        ]);

        AuditLogger::log('article.created', $article, [], $article->only(['title', 'publication_status', 'is_published', 'is_featured']));

        return redirect()->route('admin.articles.index')->with('status', 'Article created.');
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        return view('admin.articles.edit', [
            'article' => $article,
            'categories' => ArticleCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(StoreArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        $old = $article->only(['title', 'publication_status', 'is_published', 'is_featured']);

        $isPublished = $request->boolean('is_published');
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
            'is_featured' => $request->boolean('is_featured'),
            'featured_order' => $request->integer('featured_order', 0),
            'is_published' => $isPublished,
            'publication_status' => $isPublished ? 'published' : 'draft',
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        AuditLogger::log('article.updated', $article, $old, $article->only(['title', 'publication_status', 'is_published', 'is_featured']));

        return redirect()->route('admin.articles.index')->with('status', 'Article updated.');
    }

    public function review(\Illuminate\Http\Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $old = [
            'publication_status' => $article->publication_status,
            'is_published' => $article->is_published,
        ];

        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
        ]);

        if ($validated['decision'] === 'approve') {
            $article->update([
                'is_published' => true,
                'publication_status' => 'published',
                'reviewed_by_user_id' => $request->user()->id,
                'reviewed_at' => now(),
                'published_at' => $article->published_at ?? now(),
            ]);

            AuditLogger::log('article.reviewed', $article, [
                ...$old,
            ], [
                'publication_status' => 'published',
                'is_published' => true,
            ], [
                'decision' => 'approve',
            ]);

            return back()->with('status', 'Article approved and published.');
        }

        $article->update([
            'is_published' => false,
            'publication_status' => 'rejected',
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
            'published_at' => null,
        ]);

        AuditLogger::log('article.reviewed', $article, [
            ...$old,
        ], [
            'publication_status' => 'rejected',
            'is_published' => false,
        ], [
            'decision' => 'reject',
        ]);

        return back()->with('status', 'Article rejected. Doctor can edit and resubmit.');
    }
}
