<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Team $currentTeam): View
    {
        $categories = Category::withCount('posts')->latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Team $currentTeam): View
    {
        Gate::authorize('create', Category::class);

        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request, Team $currentTeam): RedirectResponse
    {
        Category::create([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
        ]);

        return redirect()->route('categories.index')->with('success', __('Category created.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $currentTeam, Category $category)
    {
        Gate::authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Team $currentTeam, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
        ]);

        return redirect()->route('categories.index')->with('success', __('Category updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $currentTeam, Category $category)
    {
        Gate::authorize('delete', $category);

        $category->delete();

        return redirect()->route('categories.index')->with('success', __('Category deleted.'));
    }
}
