<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use app\Models\SiteTourBooking;
use app\Models\Client;

class UpdateSiteTourBookings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = SiteTourBooking::all();
        if($bookings->count() > 0) {
            foreach($bookings as $booking) {
                if($booking->client) {
                    $booking->firstname = $booking->client->firstname;
                    $booking->lastname = $booking->client->lastname;
                    $booking->email = $booking->client->email;
                    $booking->phone_number = $booking->client?->phone_number;
                    $booking->update();
                }
            }
        }
    }
}
