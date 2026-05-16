<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use App\Models\BookingPayment;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $allCars = Car::count();
        $available = Car::where('status', 'available')->count();
        $rented = Car::where('status', 'rented')->count();
        $pending = BookingPayment::where('payment_status', 'Pending Payment')->count();
        $pendingBalance = BookingPayment::where('payment_status', 'Pending Balance')->count();
        $todayEarnings = BookingPayment::whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Pending Balance', 'Completed'])
            ->whereDate('updated_at', today())
            ->sum('amount_paid');

        // Fleet distribution for charts
        $types = ['Sedan', 'SUV', 'Luxury', 'Electric Car'];
        $fleetCounts = array_fill_keys($types, 0);

        foreach ($types as $type) {
            $fleetCounts[$type] = Car::where('type', $type)->count();
        }

        // Paginated recent bookings (10 per page)
        $recentBookings = Booking::with(['user', 'car'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact(
            'allCars', 'available', 'rented', 'pending', 'pendingBalance',
            'todayEarnings', 'fleetCounts', 'recentBookings'
        ));
    }
}
