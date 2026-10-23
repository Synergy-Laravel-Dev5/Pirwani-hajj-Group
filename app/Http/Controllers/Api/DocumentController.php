<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingPerson;
use App\Models\PersonDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function upload(Request $request, int $personId): JsonResponse
    {
        // 1. Authorization: Only Family Head can upload
        if (!$request->user()->tokenCan('role:head')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only the Family Head can upload documents.'
            ], 403);
        }

        $booking = $request->user();

        // 2. Ensure the person belongs to this authenticated booking
        $person = BookingPerson::where('id', $personId)
            ->where('booking_id', $booking->id)
            ->first();

        if (!$person) {
            return response()->json([
                'success' => false,
                'message' => 'Family member not found in this booking.'
            ], 404);
        }

        // 3. Validation: All fields are optional, accept images or PDFs up to 5MB
        $validator = Validator::make($request->all(), [
            'nic_front' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'nic_back'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'visa'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'passport'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'ticket'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 4. Retrieve or instantiate existing record
        $document = PersonDocument::firstOrNew([
            'booking_id' => $booking->id,
            'person_id'  => $person->id,
        ]);

        // 5. Store uploaded files and delete old replacements
        $fileFields = ['nic_front', 'nic_back', 'visa', 'passport', 'ticket'];
        $folder = "documents/{$booking->id}/{$person->id}";

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                if ($document->$field && Storage::disk('public')->exists($document->$field)) {
                    Storage::disk('public')->delete($document->$field);
                }
                $document->$field = $request->file($field)->store($folder, 'public');
            }
        }

        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Documents updated successfully.',
            'data'    => $this->formatDocumentUrls($document)
        ], 200);
    }

    /**
     * Retrieve documents for a member (Head and Member both can view).
     */
    public function show(Request $request, int $personId): JsonResponse
    {
        $booking = $request->user();

        $person = BookingPerson::where('id', $personId)
            ->where('booking_id', $booking->id)
            ->first();

        if (!$person) {
            return response()->json([
                'success' => false,
                'message' => 'Family member not found in this booking.'
            ], 404);
        }

        $document = PersonDocument::where('person_id', $personId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Documents retrieved successfully.',
            'data'    => $document ? $this->formatDocumentUrls($document) : null
        ], 200);
    }

    private function formatDocumentUrls(PersonDocument $doc): array
    {
        return [
            'id'        => $doc->id,
            'person_id' => $doc->person_id,
            'nic_front' => $doc->nic_front ? asset('storage/' . $doc->nic_front) : null,
            'nic_back'  => $doc->nic_back ? asset('storage/' . $doc->nic_back) : null,
            'visa'      => $doc->visa ? asset('storage/' . $doc->visa) : null,
            'passport'  => $doc->passport ? asset('storage/' . $doc->passport) : null,
            'ticket'    => $doc->ticket ? asset('storage/' . $doc->ticket) : null,
        ];
    }
}
