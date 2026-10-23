<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        // 1. Validate Incoming Request Data
        $validator = Validator::make($request->all(), [
            'is_head'     => 'required|boolean', // Accepts 1, 0, true, false, "1", "0"
            'identifier'  => 'required|string|max:50',
            'password'    => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Cast and Sanitize Inputs
        $isHead      = filter_var($request->input('is_head'), FILTER_VALIDATE_BOOLEAN);
        $identifier  = trim($request->input('identifier'));
        $password    = trim($request->input('password'));
        $deviceName  = trim($request->input('device_name')) ?: 'Mobile Device';

        $booking = null;

        // 3. Branch: Head (1) vs Member (0)
        if ($isHead) {
            // --- FAMILY HEAD LOGIN (PHONE MATCHING) ---
            $inputDigits = preg_replace('/[^0-9]/', '', $identifier);

            if (empty($inputDigits) || strlen($inputDigits) < 7) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid phone number with at least 7 digits.'
                ], 422);
            }

            $coreDigits = strlen($inputDigits) >= 10 ? substr($inputDigits, -10) : substr($inputDigits, -7);

            $client = Client::where(function ($q) use ($identifier, $inputDigits, $coreDigits) {
                $q->where('phone', $identifier)
                    ->orWhereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') = ?", [$inputDigits])
                    ->orWhereRaw("REGEXP_REPLACE(phone, '[^0-9]', '') LIKE ?", ['%' . $coreDigits]);
            })->first();

            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'No client registered with this mobile number.'
                ], 404);
            }

            $booking = Booking::where('client_id', $client->id)
                ->latest('id')
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active bookings found for this client.'
                ], 404);
            }
        } else {
            // --- MEMBER LOGIN (HB NUMBER / BOOKING ID MATCHING) ---
            $cleanIdentifier = strtoupper(trim($identifier));

            // Check HB number across linked persons
            $booking = Booking::whereHas('persons', function ($q) use ($cleanIdentifier) {
                $q->where('hb_number', $cleanIdentifier);
            })->first();

            // Fallback: If numeric, search by primary key ID
            if (!$booking && is_numeric($cleanIdentifier)) {
                $booking = Booking::find((int)$cleanIdentifier);
            }

            // Fallback: Check direct column if present on bookings table
            if (!$booking && \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'hb_number')) {
                $booking = Booking::where('hb_number', $cleanIdentifier)->first();
            }

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found for the provided Booking / HB Number.'
                ], 404);
            }
        }

        // 4. Verify Booking Password
        if (empty($booking->app_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile access has not been configured for this booking.'
            ], 403);
        }

        if (!Hash::check($password, $booking->app_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password.'
            ], 401);
        }

        // 5. Update Last Login Timestamp
        if (\Illuminate\Support\Facades\Schema::hasColumn('bookings', 'app_last_login_at')) {
            $booking->update(['app_last_login_at' => now()]);
        }

        // 6. Generate Sanctum Bearer Token stamped with role ability
        $roleAbility = $isHead ? 'role:head' : 'role:member';
        $token = $booking->createToken($deviceName, [$roleAbility])->plainTextToken;

        // 7. Eager load relations
        $booking->loadMissing(['client', 'persons', 'hotels', 'transports']);

        // 8. Return JSON Response with `is_head` as boolean
        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'is_head'    => $isHead,
                'role'       => $isHead ? 'head' : 'member',
                'booking_id' => $booking->id,
                'client_name' => $booking->client?->name ?? ($booking->persons->first()?->full_name ?? 'Guest'),
            ]
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\Booking $booking */
        $booking = $request->user();
        $isHead = $request->user()->tokenCan('role:head');

        // Eager load persons along with their uploaded documents
        $booking->loadMissing(['client', 'persons.documents']);

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'data'    => [
                'role'           => $isHead ? 'head' : 'member',
                'is_head'        => $isHead,
                'booking_id'     => $booking->id,
                'hb_number'      => $booking->persons->first()?->hb_number ?? ('HB' . str_pad($booking->id, 4, '0', STR_PAD_LEFT)),
                'package_type'   => ucfirst($booking->package_type ?? 'Hajj'),
                'camp'           => $booking->camp,
                'head_name'      => $booking->client?->name ?? ($booking->persons->first()?->full_name ?? 'N/A'),
                'total_pax'      => $booking->persons->count(),

                // Financials (Head only)
                'financials'     => $isHead ? [
                    'package_cost'   => (float) $booking->package_cost,
                    'total_amount'   => (float) $booking->total_amount,
                    'total_received' => (float) $booking->total_received,
                    'balance'        => (float) $booking->balance,
                ] : null,

                'family_members' => $booking->persons->values()->map(function ($person, $index) {
                    $doc = $person->documents;

                    // If 'is_head' column exists on person, use it; otherwise fallback to first person
                    $isMemberHead = isset($person->is_head)
                        ? (bool) $person->is_head
                        : ($index === 0);

                    return [
                        'id'              => $person->id,
                        'is_head'         => $isMemberHead,
                        'full_name'       => $person->full_name,
                        'hajj_id'         => $person->hajj_id,
                        'hb_number'       => $person->hb_number,
                        'passport_number' => $person->passport_number,
                        'ticket_status'   => $person->ticket_status,
                        'photo_url'       => $person->photo ? asset($person->photo) : null,

                        // Document Object
                        'documents'       => [
                            'nic_front'   => $doc?->nic_front ? asset('storage/' . $doc->nic_front) : null,
                            'nic_back'    => $doc?->nic_back ? asset('storage/' . $doc->nic_back) : null,
                            'visa'        => $doc?->visa ? asset('storage/' . $doc->visa) : null,
                            'passport'    => $doc?->passport ? asset('storage/' . $doc->passport) : null,
                            'ticket'      => $doc?->ticket ? asset('storage/' . $doc->ticket) : null,
                            'is_complete' => (bool) ($doc && $doc->nic_front && $doc->nic_back && $doc->visa && $doc->passport && $doc->ticket),
                        ],
                    ];
                }),
            ]
        ], 200);
    }
    /**
     * Invalidate Current Device Token (Logout).
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete only the token used for the current session/device
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully. Device session terminated.'
        ], 200);
    }
}
