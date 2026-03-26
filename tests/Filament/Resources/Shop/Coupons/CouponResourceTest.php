<?php

use App\Enums\DiscountType;
use App\Filament\Resources\Shop\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Shop\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Shop\Coupons\Pages\ListCoupons;
use App\Models\Shop\Coupon;
use Livewire\Livewire;

it('can list coupons', function () {
    $coupons = Coupon::factory(3)->create();

    Livewire::test(ListCoupons::class)
        ->assertOk()
        ->assertCanSeeTableRecords($coupons);
});

it('can create a percentage coupon', function () {
    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'code' => 'SAVE20',
            'description' => '20% off your order',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 20,
            'minimum_order_value' => 50,
            'usage_limit' => 100,
            'is_active' => true,
            'expires_at' => now()->addMonths(3)->toDateString(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Coupon::class, [
        'code' => 'SAVE20',
        'discount_type' => DiscountType::Percentage,
        'discount_value' => 20,
    ]);
});

it('can create a fixed discount coupon', function () {
    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'code' => 'FLAT10',
            'discount_type' => DiscountType::Fixed,
            'discount_value' => 10,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Coupon::class, [
        'code' => 'FLAT10',
        'discount_type' => DiscountType::Fixed,
        'discount_value' => 10,
    ]);
});

it('validates required fields on create', function () {
    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'code' => null,
            'discount_type' => null,
            'discount_value' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'code' => 'required',
            'discount_value' => 'required',
        ]);
});

it('validates coupon code must be unique', function () {
    Coupon::factory()->create(['code' => 'EXISTING']);

    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'code' => 'EXISTING',
            'discount_type' => DiscountType::Fixed,
            'discount_value' => 5,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasFormErrors(['code' => 'unique']);
});

it('can edit a coupon', function () {
    $coupon = Coupon::factory()->create([
        'discount_type' => DiscountType::Percentage,
        'discount_value' => 10,
        'is_active' => true,
    ]);

    Livewire::test(EditCoupon::class, ['record' => $coupon->id])
        ->fillForm([
            'discount_value' => 25,
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Coupon::class, [
        'id' => $coupon->id,
        'discount_value' => 25,
        'is_active' => false,
    ]);
});

it('can filter coupons by discount type', function () {
    $percentage = Coupon::factory()->create(['discount_type' => DiscountType::Percentage]);
    $fixed = Coupon::factory()->create(['discount_type' => DiscountType::Fixed]);

    Livewire::test(ListCoupons::class)
        ->filterTable('discount_type', DiscountType::Percentage->value)
        ->assertCanSeeTableRecords([$percentage])
        ->assertCanNotSeeTableRecords([$fixed]);
});
