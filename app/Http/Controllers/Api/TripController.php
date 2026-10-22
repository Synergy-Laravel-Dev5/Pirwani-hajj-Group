<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function getBookingDetails(Request $request): JsonResponse
    {
        /** @var \App\Models\Booking $booking */
        $booking = $request->user();

        // Check if the current device session was authenticated as Head
        $isHead = $request->user()->tokenCan('role:head');

        // Eager load package and all operational relations
        $booking->loadMissing([
            'client',
            'package',
            'persons',
            'hotels',
            'transports',
            'visas',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking details retrieved successfully.',
            'data'    => [
                'is_head' => $isHead,
                'booking' => [
                    'id'              => $booking->id,
                    'hb_number'       => $booking->persons->first()?->hb_number ?? ('HB' . str_pad($booking->id, 4, '0', STR_PAD_LEFT)),
                    'package_type'    => ucfirst($booking->package_type ?? 'Hajj'),
                    'camp'            => $booking->camp,
                    'departure_date'  => $booking->departure_date,
                    'arrival_date'    => $booking->arrival_date,
                    'departure_time'  => $booking->departure_time,
                    'arrival_time'    => $booking->arrival_time,
                    'remarks'         => $booking->remarks,

                    // Package Details (from packages table if linked)
                    'package'         => $booking->package ? [
                        'id'          => $booking->package->id,
                        'name'        => $booking->package->package_name ?? $booking->package->name,
                        'description' => $booking->package->description ?? null,
                    ] : null,

                    // Financials: Strictly exposed to Head only
                    'financials'      => $isHead ? [
                        'package_cost'   => (float) $booking->package_cost,
                        'visa_charges'   => (float) $booking->visa_charges,
                        'flight_charges' => (float) $booking->flight_charges,
                        'other_charges'  => (float) $booking->other_charges,
                        'discount'       => (float) $booking->discount,
                        'total_amount'   => (float) $booking->total_amount,
                        'total_received' => (float) $booking->total_received,
                        'balance'        => (float) $booking->balance,
                    ] : null,
                ],

                'members' => $booking->persons->map(function ($person) {
                    return [
                        'id'              => $person->id,
                        'full_name'       => $person->full_name,
                        'hajj_id'         => $person->hajj_id,
                        'hb_number'       => $person->hb_number,
                        'passport_number' => $person->passport_number,
                        'gender'          => $person->gender ?? 'N/A',
                        'ticket_status'   => $person->ticket_status,
                        'photo_url'       => $person->photo ? asset($person->photo) : null,
                    ];
                }),

                'hotels' => $booking->hotels->map(function ($hotel) {
                    return [
                        'id'              => $hotel->id,
                        'hotel_name'      => $hotel->hotel_name,
                        'city'            => ucfirst($hotel->location ?? 'Makkah'),
                        'room_number'     => $hotel->room_number ?? 'TBA',
                        'room_type'       => ucfirst($hotel->room_type ?? 'Quad'),
                        'meal_plan'       => $hotel->meal_plan ?? 'Half Board',
                        'check_in'        => $hotel->check_in,
                        'check_out'       => $hotel->check_out,
                        'hijri_check_in'  => $hotel->hijri_check_in,
                        'hijri_check_out' => $hotel->hijri_check_out,
                    ];
                }),

                'transports' => $booking->transports->map(function ($t) {
                    return [
                        'id'             => $t->id,
                        'route'          => $t->route,
                        'transport_type' => ucfirst($t->transport_type ?? 'Bus'),
                        'notes'          => $t->notes,
                    ];
                }),
            ]
        ], 200);
    }
    public function getPackageDetails(Request $request): JsonResponse
    {
        /** @var \App\Models\Booking $booking */
        $booking = $request->user();

        // Check if the current session has Head privileges
        $isHead = $request->user()->tokenCan('role:head');

        // Eager load package and its related package items/services if they exist
        $booking->loadMissing(['package']);

        $package = $booking->package;

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'No predefined package is linked to this booking.',
                'data'    => [
                    'package_type' => ucfirst($booking->package_type ?? 'Custom'),
                    'camp'         => $booking->camp,
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Package details retrieved successfully.',
            'data'    => [
                'is_head'      => $isHead,
                'booking_id'   => $booking->id,
                'package_info' => [
                    'id'               => $package->id,
                    'name'             => $package->package_name ?? $package->name,
                    'type'             => ucfirst($package->package_type ?? $booking->package_type ?? 'Hajj'),
                    'year'             => $package->year ?? null,
                    'camp'             => $booking->camp ?? $package->camp ?? null,
                    'duration_days'    => $package->duration_days ?? null,
                    'start_date'       => $package->start_date ?? $booking->departure_date,
                    'end_date'         => $package->end_date ?? $booking->arrival_date,
                    'description'      => $package->description ?? null,
                    'inclusions'       => $package->inclusions ?? null,
                    'exclusions'       => $package->exclusions ?? null,
                    'itinerary_notes'  => $package->itinerary ?? null,

                    // Package pricing strictly restricted to the Family Head
                    'pricing'          => $isHead ? [
                        'base_price'      => (float) ($package->price ?? $package->cost ?? $booking->package_cost),
                        'sharing_type'    => $booking->sharing_type ?? 'Standard',
                        'currency'        => $package->currency ?? 'PKR',
                    ] : null,
                ]
            ]
        ], 200);
    }
}
