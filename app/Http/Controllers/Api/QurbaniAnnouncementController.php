<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QurbaniBatch;
use App\Models\QurbaniBatchHajji;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QurbaniAnnouncementController extends Controller
{
    /**
     * Get the Qurbani schedule and details for the authenticated Hajji / Booking.
     * Route: GET /api/qurbani/announcements
     */
    public function getMyAnnouncements(Request $request): JsonResponse
    {
        /** @var \App\Models\Booking $booking */
        $booking = $request->user();

        // Check user session abilities
        $isHead = $request->user()->tokenCan('role:head');

        // Extract individual person ID if logged in as a specific member
        $specificPersonId = null;
        $currentToken = $request->user()->currentAccessToken();
        if ($currentToken && is_array($currentToken->abilities)) {
            foreach ($currentToken->abilities as $ability) {
                if (str_starts_with($ability, 'person:')) {
                    $specificPersonId = (int) substr($ability, 7);
                    break;
                }
            }
        }

        // Query batch assignments strictly scoped to this booking and member
        $query = QurbaniBatchHajji::with(['batch', 'person'])
            ->where('booking_id', $booking->id)
            ->whereHas('batch');

        // If authenticated as a specific individual member, only show that member's slot
        if (!$isHead && $specificPersonId) {
            $query->where('booking_person_id', $specificPersonId);
        }

        $batchHajjis = $query->get();

        $currentTime = Carbon::now();

        $slots = $batchHajjis->map(function ($item) use ($booking, $currentTime) {
            $batch = $item->batch;
            $batchDate = Carbon::parse($batch->qurbani_date)->toDateString();
            $batchStart = Carbon::parse("{$batchDate} {$batch->start_time}");
            $batchEnd   = Carbon::parse("{$batchDate} {$batch->end_time}");

            $isCompleted = ($item->status === 'completed' || $batch->status === 'completed');

            // Time has arrived on the clock AND is not yet marked as completed
            $isTimeArrived = $currentTime->greaterThanOrEqualTo($batchStart) && !$isCompleted;

            $personName = $item->person?->given_name
                ? ($item->person->given_name . ' ' . $item->person->surname)
                : ($item->person?->full_name ?? 'Pilgrim');

            $hbNumber = $item->person?->hb_number ?: ($booking->hb_number ?: ('HB' . str_pad($booking->id, 4, '0', STR_PAD_LEFT)));

            return [
                'batch_id'           => $batch->id,
                'batch_name'         => $batch->batch_name,
                'person_id'          => $item->booking_person_id,
                'hajji_name'         => $personName,
                'hajji_hb_number'    => $hbNumber,
                'hajji_hajj_id'      => $item->person?->hajj_id,
                'qurbani_date'       => $batchDate,
                'start_time'         => substr((string)$batch->start_time, 0, 5),
                'end_time'           => substr((string)$batch->end_time, 0, 5),
                'is_time_arrived'    => $isTimeArrived,
                'is_completed'       => $isCompleted,
                'completed_at'       => $item->completed_at ? $item->completed_at->toDateTimeString() : null,
                'announcement_title' => $batch->title ?: "Qurbani Announcement: {$batch->batch_name}",
                'announcement_msg'   => $batch->message ?: "Dear {$personName}, your Qurbani time slot has been scheduled from " . substr((string)$batch->start_time, 0, 5) . " to " . substr((string)$batch->end_time, 0, 5) . " on {$batchDate}.",
            ];
        });

        return response()->json([
            'success'              => true,
            'message'              => 'Qurbani schedule retrieved successfully.',
            'is_head'              => $isHead,
            'server_time'          => $currentTime->toDateTimeString(),
            'server_timestamp'     => $currentTime->timestamp,
            'assigned_batches'     => $slots->values(),
        ]);
    }

    /**
     * Mark Qurbani as completed from the mobile app.
     * Route: POST /api/qurbani/mark-completed
     */
    public function markCompleted(Request $request): JsonResponse
    {
        /** @var \App\Models\Booking $booking */
        $booking = $request->user();

        $request->validate([
            'batch_id'  => 'required|integer',
            'person_id' => 'nullable|integer',
        ]);

        $query = QurbaniBatchHajji::where('booking_id', $booking->id)
            ->where('qurbani_batch_id', $request->batch_id);

        if ($request->filled('person_id')) {
            $query->where('booking_person_id', $request->person_id);
        }

        $records = $query->get();

        if ($records->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No matching Qurbani assignment found for this booking.',
            ], 404);
        }

        foreach ($records as $record) {
            $record->update([
                'status'       => 'completed',
                'completed_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Qurbani marked as completed successfully.',
        ]);
    }
}
