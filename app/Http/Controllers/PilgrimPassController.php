<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use Illuminate\Http\Request;

class PilgrimPassController extends Controller
{
    /**
     * Public Live Mobile E-Pass / Itinerary view.
     * Anyone scanning the QR code on the ID card will open this mobile pass.
     *
     * @param int|string $id BookingPerson ID or Booking ID
     */
    public function show($id)
    {
        $person = BookingPerson::with([
            'booking.client',
            'booking.company',
            'booking.package',
            'booking.hotels',
            'booking.transports',
            'booking.visas'
        ])->find($id);

        $booking = null;
        if ($person) {
            $booking = $person->booking;
        } else {
            $booking = Booking::with([
                'client',
                'company',
                'package',
                'persons',
                'hotels',
                'transports',
                'visas'
            ])->find($id);

            if ($booking) {
                $person = $booking->persons->first();
            }
        }

        if (!$booking) {
            abort(404, 'Pilgrim / Booking record not found.');
        }

        // Categorize Hotels
        $makkahHotels = $booking->hotels->filter(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'makkah') || str_contains($name, 'makkah');
        });

        $aziziaHotels = $booking->hotels->filter(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($name, 'azizia') || str_contains($name, 'aziziya');
        });

        $madinahHotels = $booking->hotels->filter(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($name, 'madinah') || str_contains($name, 'medina');
        });

        $otherHotels = $booking->hotels->filter(function ($h) use ($makkahHotels, $aziziaHotels, $madinahHotels) {
            return !$makkahHotels->contains('id', $h->id) &&
                   !$aziziaHotels->contains('id', $h->id) &&
                   !$madinahHotels->contains('id', $h->id);
        });

        return view('pilgrim.epass', compact('person', 'booking', 'makkahHotels', 'aziziaHotels', 'madinahHotels', 'otherHotels'));
    }

    /**
     * Single Printable Luxury Lanyard ID Badge.
     */
    public function badge($id)
    {
        $person = BookingPerson::with([
            'booking.client',
            'booking.company',
            'booking.package',
            'booking.hotels',
            'booking.transports',
            'booking.visas'
        ])->findOrFail($id);

        $booking = $person->booking;

        $makkahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'makkah') || str_contains($name, 'makkah');
        }) ?? $booking->hotels->first();

        $aziziaHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($name, 'azizia') || str_contains($name, 'aziziya');
        });

        $madinahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($name, 'madinah') || str_contains($name, 'medina');
        });

        return view('pilgrim.badge', compact('person', 'booking', 'makkahHotel', 'aziziaHotel', 'madinahHotel'));
    }

    /**
     * Bulk Printable Lanyard ID Badges Sheet (A4 2-Column Grid) for all pilgrims in a booking.
     */
    public function bulkBadges($bookingId)
    {
        $booking = Booking::with([
            'client',
            'company',
            'package',
            'persons',
            'hotels',
            'transports',
            'visas'
        ])->findOrFail($bookingId);

        $makkahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'makkah') || str_contains($name, 'makkah');
        }) ?? $booking->hotels->first();

        $aziziaHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($name, 'azizia') || str_contains($name, 'aziziya');
        });

        $madinahHotel = $booking->hotels->first(function ($h) {
            $loc = strtolower($h->location ?? '');
            $name = strtolower($h->hotel_name ?? '');
            return str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($name, 'madinah') || str_contains($name, 'medina');
        });

        return view('pilgrim.bulk_badges', compact('booking', 'makkahHotel', 'aziziaHotel', 'madinahHotel'));
    }
}
