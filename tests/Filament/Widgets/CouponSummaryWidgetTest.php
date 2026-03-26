<?php

use App\Filament\Widgets\CouponSummaryWidget;
use App\Models\Shop\Coupon;
use Livewire\Livewire;

it('renders the coupon summary widget', function () {
    Livewire::test(CouponSummaryWidget::class)
        ->assertOk();
});

it('shows only active coupons', function () {
    $active = Coupon::factory()->create(['is_active' => true, 'expires_at' => now()->addDays(30)]);
    $inactive = Coupon::factory()->create(['is_active' => false, 'expires_at' => now()->addDays(30)]);

    Livewire::test(CouponSummaryWidget::class)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$inactive]);
});

it('does not show expired coupons', function () {
    $expired = Coupon::factory()->create(['is_active' => true, 'expires_at' => now()->subDay()]);
    $valid = Coupon::factory()->create(['is_active' => true, 'expires_at' => now()->addDays(10)]);

    Livewire::test(CouponSummaryWidget::class)
        ->assertCanNotSeeTableRecords([$expired])
        ->assertCanSeeTableRecords([$valid]);
});

it('shows coupons with no expiry date', function () {
    $noExpiry = Coupon::factory()->create(['is_active' => true, 'expires_at' => null]);

    Livewire::test(CouponSummaryWidget::class)
        ->assertCanSeeTableRecords([$noExpiry]);
});

it('shows empty state when no active coupons', function () {
    Livewire::test(CouponSummaryWidget::class)
        ->assertSee('No active coupons');
});
