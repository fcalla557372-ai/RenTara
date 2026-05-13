<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Car;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_creation_with_normalized_data()
    {
        // Create a user and car for testing
        $user = User::factory()->create();
        $car = Car::factory()->create(['daily_rate' => 1000]);

        // Act as the user
        $this->actingAs($user);

        // Test data
        $bookingData = [
            'car_id' => $car->id,
            'pickup_date' => now()->addDay()->format('Y-m-d'),
            'return_date' => now()->addDays(3)->format('Y-m-d'),
            'payment_type' => 'partial',
            'amount_paid' => 900, // 30% of 3 days * 1000 = 900
            'driver_license_no' => 'DL123456789',
            'license_expiry' => now()->addYear()->format('Y-m-d'),
            'national_id_no' => 'ID123456789',
            'id_type' => 'Passport',
        ];

        // Make the request
        $response = $this->post(route('customer.booking.store'), $bookingData);

        // Assert redirect to success page
        $response->assertRedirect(route('customer.my-bookings'));

        // Assert booking was created
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'pickup_date' => $bookingData['pickup_date'],
            'return_date' => $bookingData['return_date'],
            'total_amount' => 3000, // 3 days * 1000
        ]);

        // Assert user document was created
        $this->assertDatabaseHas('user_documents', [
            'user_id' => $user->id,
            'driver_license_no' => 'DL123456789',
            'national_id_no' => 'ID123456789',
            'id_type' => 'Passport',
        ]);

        // Assert booking payment was created
        $this->assertDatabaseHas('booking_payments', [
            'amount_paid' => 900,
            'remaining_balance' => 2100,
            'payment_status' => 'Partial Payment',
        ]);

        // Assert relationships work
        $booking = Booking::where('user_id', $user->id)->first();
        $this->assertNotNull($booking->payment);
        $this->assertNotNull($booking->user->document);
    }
}