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
        $query = Booking::with(['user', 'car', 'payment', 'document'])->latest();

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

    public function confirmBalancePayment(Booking $booking)
    {
        if ($booking->payment?->balance_payment_status !== 'pending_confirmation') {
            return back()->with('error', 'No pending balance payment to confirm for this booking.');
        }

        $booking->payment->update([
            'balance_payment_status' => 'confirmed',
            'payment_status'   => 'Completed',
            'remaining_balance' => 0,
        ]);

        return back()->with('success', 'Balance payment confirmed for ' . $booking->user->name . '. Booking is now fully settled.');
    }

    public function settleBalance(Booking $booking)
    {
        if (! $booking->payment || $booking->payment->payment_status !== 'Pending Balance') {
            return back()->with('error', 'Only bookings with a pending balance can be settled.');
        }

        $booking->payment->update([
            'amount_paid'      => $booking->payment->amount_paid + $booking->payment->remaining_balance,
            'remaining_balance'=> 0,
            'payment_status'   => 'Completed',
            'balance_payment_status' => 'confirmed',
        ]);

        return back()->with('success', 'Booking balance settled and marked as completed.');
    }

    public function markReturn(Booking $booking)
    {
        if (! $booking->payment || ! in_array($booking->payment->payment_status, ['Payment Confirmed', 'Partial Payment'], true)) {
            return back()->with('error', 'Only confirmed or partially confirmed bookings can be marked as returned.');
        }

        $nextStatus = $booking->payment->remaining_balance > 0 ? 'Pending Balance' : 'Completed';
        $booking->payment->update(['payment_status' => $nextStatus]);
        $booking->car->update(['status' => 'available']);

        return back()->with('success', 'Booking marked as returned. Car is now available.');
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
