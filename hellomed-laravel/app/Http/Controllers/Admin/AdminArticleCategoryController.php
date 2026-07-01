<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminArticleCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ArticleCategory::query()
            ->withCount('articles')
            ->orderBy('name')
            ->paginate(15);
            
        $routePrefix = auth()->user()?->role === 'staff' ? 'staff' : 'admin';

        return view('admin.article_categories.index', compact('categories', 'routePrefix'));
    }

    public function create(): View
    {
        $routePrefix = auth()->user()?->role === 'staff' ? 'staff' : 'admin';
        return view('admin.article_categories.create', compact('routePrefix'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        ArticleCategory::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $routePrefix = auth()->user()?->role === 'staff' ? 'staff' : 'admin';
        return redirect()->route($routePrefix . '.article-categories.index')
            ->with('success', 'Article category created successfully.');
    }

    public function edit(ArticleCategory $articleCategory): View
    {
        $routePrefix = auth()->user()?->role === 'staff' ? 'staff' : 'admin';
        return view('admin.article_categories.edit', compact('articleCategory', 'routePrefix'));
    }

    public function update(Request $request, ArticleCategory $articleCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:article_categories,name,' . $articleCategory->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $articleCategory->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $routePrefix = auth()->user()?->role === 'staff' ? 'staff' : 'admin';
        return redirect()->route($routePrefix . '.article-categories.index')
            ->with('success', 'Article category updated successfully.');
    }

    public function destroy(ArticleCategory $articleCategory): RedirectResponse
    {
        $routePrefix = auth()->user()?->role === 'staff' ? 'staff' : 'admin';
        
        if ($articleCategory->articles()->exists()) {
            return redirect()->route($routePrefix . '.article-categories.index')
                ->with('error', 'Cannot delete category with associated articles.');
        }

        $articleCategory->delete();

        return redirect()->route($routePrefix . '.article-categories.index')
            ->with('success', 'Article category deleted successfully.');
    }
}
