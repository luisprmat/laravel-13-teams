<?php

use App\Enums\TeamRole;
use App\Models\Category;
use App\Models\Post;
use App\Models\Team;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->team = $this->user->currentTeam;
    $this->category = Category::factory()->create(['team_id' => $this->team->id]);
});

test('authenticated users can view posts index', function () {
    Post::factory()->create(['team_id' => $this->team->id, 'category_id' => $this->category->id]);

    $this->actingAs($this->user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertViewHas('posts');
});

test('posts are scoped to current team', function () {
    $ownPost = Post::factory()->create(['team_id' => $this->team->id, 'category_id' => $this->category->id]);
    $otherPost = Post::factory()->create();

    $response = $this->actingAs($this->user)
        ->get(route('posts.index'));

    $posts = $response->viewData('posts');
    expect($posts->pluck('id')->toArray())->toContain($ownPost->id);
    expect($posts->pluck('id')->toArray())->not->toContain($otherPost->id);
});

test('authenticated users can create a post', function () {
    $this->actingAs($this->user)
        ->post(route('posts.store'), [
            'category_id' => $this->category->id,
            'title' => 'My Post',
            'post_text' => 'Post content here.',
        ])
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'team_id' => $this->team->id,
        'title' => 'My Post',
    ]);
});

test('authenticated users can update a post', function () {
    $post = Post::factory()->create(['team_id' => $this->team->id, 'category_id' => $this->category->id]);

    $this->actingAs($this->user)
        ->put(route('posts.update', $post), [
            'category_id' => $this->category->id,
            'title' => 'Updated Title',
            'post_text' => 'Updated content.',
        ])
        ->assertRedirect(route('posts.index'));

    expect($post->fresh()->title)->toBe('Updated Title');
});

test('members cannot delete posts', function () {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);
    $member->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);
    $post = Post::factory()->create(['team_id' => $team->id, 'category_id' => $category->id]);

    $this->actingAs($member)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();
});

test('admins can delete posts', function () {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $admin->switchTeam($team);

    $category = Category::factory()->create(['team_id' => $team->id]);
    $post = Post::factory()->create(['team_id' => $team->id, 'category_id' => $category->id]);

    $this->actingAs($admin)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('owners can delete posts', function () {
    $post = Post::factory()->create(['team_id' => $this->team->id, 'category_id' => $this->category->id]);

    $this->actingAs($this->user)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('posts.index'));

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('store requires all fields', function () {
    $this->actingAs($this->user)
        ->post(route('posts.store'), [])
        ->assertSessionHasErrors(['category_id', 'title', 'post_text']);
});
