<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request;

class AdminTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'car'])->latest();

        if ($request->filled('status')) {
            $query->whereHas('payment', fn($q) => $q->where('payment_status', $request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('car', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        return view('admin.tracking', compact('bookings'));
    }

    public function confirmPayment(Booking $booking)
    {
        if (! $booking->payment || $booking->payment->payment_status !== 'Pending Payment') {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $newStatus = $booking->payment_type === 'partial' ? 'Partial Payment' : 'Payment Confirmed';
        $booking->payment->update(['payment_status' => $newStatus]);

        return back()->with('success', 'Payment confirmed successfully.');
    }

    public function markReturn(Booking $booking)
    {
        if (! $booking->payment || ! in_array($booking->payment->payment_status, ['Payment Confirmed', 'Partial Payment'], true)) {
            return back()->with('error', 'Only confirmed or partially confirmed bookings can be marked as returned.');
        }

        $booking->payment->update(['payment_status' => 'Completed']);
        $booking->car->update(['status' => 'available']);
        return back()->with('success', 'Booking marked as completed. Car is now available.');
    }

    public function cancel(Booking $booking)
    {
        if (! $booking->payment || $booking->payment->payment_status !== 'Pending Payment') {
            return back()->with('error', 'Only pending bookings can be cancelled.');
        }

        $booking->payment->update(['payment_status' => 'Cancelled']);
        if ($booking->car->status === 'rented') {
            $booking->car->update(['status' => 'available']);
        }
        return back()->with('success', 'Booking cancelled.');
    }
}
