<?php
namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->isStaff() || $booking->user_id === $user->id;
    }

    public function confirmPayment(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function markReturn(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isAdmin() || $user->isStaff()) return true;
        return $booking->user_id === $user->id && ($booking->payment?->payment_status ?? 'Unknown') === 'Pending Payment';
    }
}