<?php

use App\Filament\Widgets\LowStockAlert;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use Livewire\Livewire;

it('renders the low stock alert widget', function () {
    $category = ProductCategory::factory()->create();

    $lowStockProduct = Product::factory()->create(['qty' => 5]);
    $lowStockProduct->productCategories()->attach($category->id, ['created_at' => now(), 'updated_at' => now()]);

    $outOfStockProduct = Product::factory()->create(['qty' => 0]);

    $inStockProduct = Product::factory()->create(['qty' => 20]);

    Livewire::test(LowStockAlert::class)
        ->assertOk()
        ->assertCanSeeTableRecords(Product::where('qty', '<', 10)->get())
        ->assertCanNotSeeTableRecords(Product::where('qty', '>=', 10)->get());
});

it('does not show products with stock of 10 or more', function () {
    Product::factory()->create(['qty' => 10]);
    Product::factory()->create(['qty' => 50]);

    Livewire::test(LowStockAlert::class)
        ->assertOk()
        ->assertCountTableRecords(0);
});

it('shows products ordered by lowest stock first', function () {
    $first = Product::factory()->create(['qty' => 1]);
    $second = Product::factory()->create(['qty' => 5]);
    $third = Product::factory()->create(['qty' => 9]);

    Livewire::test(LowStockAlert::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$first, $second, $third]);
});
