<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Car;
use App\Models\UserDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create()
    {
        $cars = Car::where('status', 'available')->get();
        $user = auth()->user();

        return view('customer.booking', compact('cars', 'user'));
    }

    public function store(StoreBookingRequest $request)
    {
        DB::transaction(function () use ($request) {
            $car = Car::whereKey($request->car_id)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (! $car) {
                throw ValidationException::withMessages([
                    'car_id' => 'The selected car is no longer available.',
                ]);
            }

            $pickupDate = $request->pickup_date;
            $returnDate = $request->return_date;

            if (! is_string($pickupDate) || ! is_string($returnDate)) {
                throw ValidationException::withMessages([
                    'pickup_date' => 'Invalid pickup or return date provided.',
                ]);
            }

            try {
                $pickup = Carbon::createFromFormat('Y-m-d', $pickupDate);
                $return = Carbon::createFromFormat('Y-m-d', $returnDate);
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'pickup_date' => 'Invalid pickup or return date provided.',
                ]);
            }

            if (! $pickup || ! $return) {
                throw ValidationException::withMessages([
                    'pickup_date' => 'Invalid pickup or return date provided.',
                ]);
            }
            $days = max(1, $pickup->diffInDays($return));
            $total = $car->daily_rate * $days;

            $validIdPath = null;
            $birthCertPath = null;
            $licensePhotoPath = null;
            $gcashReceiptPath = null;

            if ($request->hasFile('valid_id')) {
                $validId = $request->file('valid_id');
                $validIdFilename = uniqid('valid_id_', true).'.'.$validId->getClientOriginalExtension();
                $validIdPath = $validId->storeAs('ids', $validIdFilename, 'public');
            }
            if ($request->hasFile('license_photo')) {
                $licensePhoto = $request->file('license_photo');
                $licensePhotoFilename = uniqid('license_photo_', true).'.'.$licensePhoto->getClientOriginalExtension();
                $licensePhotoPath = $licensePhoto->storeAs('ids', $licensePhotoFilename, 'public');
            }
            if ($request->hasFile('birth_cert')) {
                $birthCert = $request->file('birth_cert');
                $birthCertFilename = uniqid('birth_cert_', true).'.'.$birthCert->getClientOriginalExtension();
                $birthCertPath = $birthCert->storeAs('ids', $birthCertFilename, 'public');
            }
            if ($request->hasFile('gcash_receipt')) {
                $receipt = $request->file('gcash_receipt');
                $receiptFilename = uniqid('gcash_receipt_', true).'.'.$receipt->getClientOriginalExtension();
                $gcashReceiptPath = $receipt->storeAs('gcash_receipts', $receiptFilename, 'public');
            }

            $paymentType = $request->payment_type;
            $amountPaid = $request->amount_paid;

            if ($paymentType === 'full') {
                $amountPaid = $total;
            }

            $remainingBalance = max(0, $total - $amountPaid);

            // Create or update user documents (normalized table)
            UserDocument::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'driver_license_no' => $request->driver_license_no,
                    'license_expiry' => $request->license_expiry,
                    'national_id_no' => $request->national_id_no,
                    'id_type' => $request->id_type,
                    'valid_id_path' => $validIdPath,
                    'license_photo_path' => $licensePhotoPath,
                    'birth_cert_path' => $birthCertPath,
                ]
            );

            // Create booking record (only booking-specific data)
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'car_id' => $car->id,
                'pickup_date' => $request->pickup_date,
                'return_date' => $request->return_date,
                'total_amount' => $total,
            ]);

            // Create booking payment record (normalized table)
            BookingPayment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'GCash',
                'payment_type' => $paymentType,
                'amount_paid' => $amountPaid,
                'remaining_balance' => $remainingBalance,
                'gcash_reference_no' => $request->gcash_reference_no,
                'gcash_receipt_path' => $gcashReceiptPath,
                'payment_status' => 'Pending Payment',
            ]);

            $car->update(['status' => 'rented']);
        });

        return redirect()->route('customer.my-bookings')
            ->with('success', 'Booking submitted! Awaiting payment confirmation.');
    }

    public function myBookings()
    {
        // Paginate 10 per page, newest first
        $bookings = Booking::with('car', 'payment')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.my-bookings', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || ! $booking->payment || $booking->payment->payment_status !== 'Pending Payment') {
            return back()->with('error', 'You cannot cancel this booking.');
        }

        $booking->payment->update(['payment_status' => 'Cancelled']);
        $booking->car->update(['status' => 'available']);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function submitBalancePayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()
            || !$booking->canPayBalance()) {
            return back()->with('error', 'This action is not allowed for this booking.');
        }

        $request->validate([
            'balance_gcash_reference_no' => 'required|string|max:30',
            'balance_gcash_receipt'      => 'required|file|mimes:jpg,jpeg,png|max:10240',
        ], [
            'balance_gcash_reference_no.required' => 'Please enter your GCash reference number for the balance payment.',
            'balance_gcash_receipt.required'      => 'Please upload your GCash receipt screenshot for the balance payment.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('balance_gcash_receipt')) {
            $receiptPath = $request->file('balance_gcash_receipt')->store('gcash_receipts/balance', 'public');
        }

        $booking->payment->update([
            'balance_gcash_reference_no' => $request->balance_gcash_reference_no,
            'balance_gcash_receipt_path' => $receiptPath,
            'balance_payment_status'     => 'pending_confirmation',
        ]);

        return back()->with('success', 'Your balance payment receipt has been submitted. Our staff will confirm your payment shortly.');
    }
}
