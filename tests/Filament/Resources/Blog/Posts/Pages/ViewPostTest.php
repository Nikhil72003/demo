<?php

use App\Filament\Resources\Blog\Posts\Pages\ViewPost;
use App\Models\Blog\Post;
use Livewire\Livewire;

it('can render the view page', function () {
    $record = Post::factory()->create();

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()])
        ->assertOk();
});

it('increments view_count when the view page is mounted', function () {
    $record = Post::factory()->create(['view_count' => 0]);

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()]);

    expect($record->fresh()->view_count)->toBe(1);
});

it('increments view_count on each visit', function () {
    $record = Post::factory()->create(['view_count' => 5]);

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()]);
    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()]);

    expect($record->fresh()->view_count)->toBe(7);
});

it('can quick publish a draft post', function () {
    $record = Post::factory()->create(['published_at' => null]);

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()])
        ->callAction('quick_publish')
        ->assertNotified();

    $record->refresh();
    expect($record->published_at)->not->toBeNull();
});

it('quick publish is hidden for published posts', function () {
    $record = Post::factory()->create(['published_at' => now()->subDay()]);

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()])
        ->assertActionHidden('quick_publish');
});

it('can unpublish a published post', function () {
    $record = Post::factory()->create(['published_at' => now()->subDay()]);

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()])
        ->callAction('unpublish')
        ->assertNotified();

    $record->refresh();
    expect($record->published_at)->toBeNull();
});

it('unpublish is hidden for draft posts', function () {
    $record = Post::factory()->create(['published_at' => null]);

    Livewire::test(ViewPost::class, ['record' => $record->getRouteKey()])
        ->assertActionHidden('unpublish');
});
