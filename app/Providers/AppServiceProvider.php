<?php
namespace App\Providers;

use App\Models\Booking;
use App\Models\Car;
use App\Policies\BookingPolicy;
use App\Policies\CarPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class => BookingPolicy::class,
        Car::class     => CarPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Paginator::useTailwind();
    }
}