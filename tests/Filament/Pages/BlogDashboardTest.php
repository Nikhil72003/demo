<?php

use App\Filament\Pages\BlogDashboard;
use App\Models\Blog\Post;

it('renders the blog dashboard page', function () {
    Post::factory()->count(5)->create();

    $this->get(BlogDashboard::getUrl())
        ->assertOk();
});
