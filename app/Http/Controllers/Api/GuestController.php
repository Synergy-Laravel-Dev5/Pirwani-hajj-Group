<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function getPackages(Request $request): JsonResponse
    {
        $packages = Package::with(['accommodations', 'company'])
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Packages retrieved successfully.',
            'data'    => $packages->map(function ($pkg) {
                return [
                    'id'                      => $pkg->id,
                    'name'                    => $pkg->package_name ?? $pkg->title ?? $pkg->name ?? 'Hajj Package',
                    'type'                    => ucfirst($pkg->package_type ?? 'Hajj'),
                    'package_code'            => $pkg->package_code ?? $pkg->code ?? null,
                    'price'                   => (float) ($pkg->price ?? $pkg->total_price ?? $pkg->cost ?? 0),
                    'currency'                => $pkg->currency ?? 'PKR',
                    'duration_days'           => $pkg->duration_days ?? $pkg->days ?? null,
                    'start_date'              => $pkg->start_date ?? null,
                    'end_date'                => $pkg->end_date ?? null,
                    'feature_icons'           => $pkg->feature_icons ?? [],
                    'included_points'         => $pkg->package_included_points ?? [],
                    'company'                 => $pkg->company ? [
                        'id'   => $pkg->company->id,
                        'name' => $pkg->company->name ?? null,
                        'logo' => !empty($pkg->company->logo) ? asset($pkg->company->logo) : null,
                    ] : null,
                    'makkah_details'          => $pkg->makkah_a ?? $pkg->makkah_b ?? null,
                    'madinah_details'         => $pkg->madinah_a ?? $pkg->madinah_b ?? null,
                    'banner_image'            => !empty($pkg->image ?? $pkg->banner_image) ? asset($pkg->image ?? $pkg->banner_image) : null,
                ];
            })
        ], 200);
    }

    /**
     * Get Full Deep Details of a Single Package for Guest.
     */
    public function getPackageById(int $id): JsonResponse
    {
        $package = Package::with([
            'accommodations',
            'itinerary',
            'terms',
            'maktabAddress',
            'transports',
            'transportFlights',
            'transportTrains',
            'giveaways',
            'company',
        ])->find($id);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Package not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Package details retrieved successfully.',
            'data'    => [
                'id'                      => $package->id,
                'name'                    => $package->package_name ?? $package->title ?? $package->name ?? 'Hajj Package',
                'type'                    => ucfirst($package->package_type ?? 'Hajj'),
                'package_code'            => $package->package_code ?? $package->code ?? null,
                'price'                   => (float) ($package->price ?? $package->total_price ?? $package->cost ?? 0),
                'currency'                => $package->currency ?? 'PKR',
                'duration_days'           => $package->duration_days ?? $package->days ?? null,
                'start_date'              => $package->start_date ?? null,
                'end_date'                => $package->end_date ?? null,

                // Casted JSON Arrays
                'makkah_a'                => $package->makkah_a,
                'makkah_b'                => $package->makkah_b,
                'madinah_a'               => $package->madinah_a,
                'madinah_b'               => $package->madinah_b,
                'feature_icons'           => $package->feature_icons ?? [],
                'included_points'         => $package->package_included_points ?? [],
                'instructions'            => $package->instructions_points ?? [],

                // Relational Sections
                'accommodations'          => $package->accommodations,
                'itinerary'               => $package->itinerary,
                'terms_and_conditions'    => $package->terms,
                'maktab_address'          => $package->maktabAddress,
                'transports'              => $package->transports,
                'flights'                 => $package->transportFlights,
                'trains'                  => $package->transportTrains,
                'giveaways'               => $package->giveaways->map(function ($item) {
                    return [
                        'id'    => $item->id,
                        'name'  => $item->name ?? $item->title ?? null,
                        'image' => !empty($item->image) ? asset($item->image) : null,
                    ];
                }),
                'company'                 => $package->company ? [
                    'id'      => $package->company->id,
                    'name'    => $package->company->name ?? null,
                    'phone'   => $package->company->phone ?? null,
                    'email'   => $package->company->email ?? null,
                    'logo'    => !empty($package->company->logo) ? asset($package->company->logo) : null,
                ] : null,
            ]
        ], 200);
    }

    public function submitInquiry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'package_id'           => 'required|exists:packages,id',
            'full_name'            => 'required|string|max:150',
            'contact'              => 'required|string|max:30',
            'email'                => 'required|email|max:150',
            'passport'             => 'nullable|string|max:50',
            'cnic'                 => 'nullable|string|max:30',
            'pax'                  => 'required|integer|min:1',
            'room_type'            => 'required|in:sharing,quad,triple,double',
            'special_request'      => 'nullable|string|max:1000',
            'passport_document'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'nic_or_visa_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Handle file uploads
        $passportPath = $request->hasFile('passport_document')
            ? $request->file('passport_document')->store('inquiries/passports', 'public')
            : null;

        $nicVisaPath = $request->hasFile('nic_or_visa_document')
            ? $request->file('nic_or_visa_document')->store('inquiries/documents', 'public')
            : null;

        $inquiry = PackageInquiry::create([
            'package_id'            => $request->package_id,
            'full_name'             => $request->full_name,
            'contact'               => $request->contact,
            'email'                 => $request->email,
            'passport_number'       => $request->passport,
            'cnic_number'           => $request->cnic,
            'pax'                   => $request->pax,
            'room_type'             => $request->room_type,
            'special_request'       => $request->special_request,
            'passport_document'     => $passportPath,
            'nic_or_visa_document'  => $nicVisaPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry submitted successfully. Our team will contact you soon.',
            'data'    => [
                'inquiry_id' => $inquiry->id,
                'package_id' => $inquiry->package_id,
                'status'     => $inquiry->status,
            ]
        ], 201);
    }
}
