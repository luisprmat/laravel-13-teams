<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Team $currentTeam): View
    {
        $posts = Post::with('category')->latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Team $currentTeam): View
    {
        Gate::authorize('create', Post::class);

        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, Team $currentTeam): RedirectResponse
    {
        Post::create($request->validated());

        return redirect()->route('posts.index')->with('success', __('Post created.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $currentTeam, Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Team $currentTeam, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()->route('posts.index')->with('success', __('Post updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $currentTeam, Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')->with('success', __('Post deleted.'));
    }
}
