<?php

use App\Filament\Widgets\TopViewedPostsChart;
use App\Models\Blog\Post;
use Livewire\Livewire;

it('renders the top viewed posts chart', function () {
    Post::factory()->count(5)->create();

    Livewire::test(TopViewedPostsChart::class)
        ->assertOk();
});

it('shows only the top 5 most viewed posts', function () {
    Post::factory()->count(3)->create(['view_count' => 10]);
    Post::factory()->count(4)->create(['view_count' => 1]);

    Livewire::test(TopViewedPostsChart::class)
        ->assertOk();

    $posts = Post::query()->orderByDesc('view_count')->limit(5)->get();
    expect($posts)->toHaveCount(5);
    expect($posts->first()->view_count)->toBe(10);
});
