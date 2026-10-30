<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\HajjBook;
use App\Models\HujjajGuideline;
use App\Models\Package;
use App\Models\PackageInquiry;
use App\Models\TutorialVideo;
use App\Models\VisaCountry;
use App\Models\VisaInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    public function getPackages(Request $request): JsonResponse
    {
        $type = $request->query('type');

        $query = Package::with(['accommodations', 'company', 'transportFlights', 'transports']);

        if (Schema::hasColumn('packages', 'status')) {
            $query->where('status', 'active');
        }

        if (!empty($type)) {
            $query->where('package_type', strtolower(trim($type)));
        }

        $packages = $query->latest('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Packages retrieved successfully.',
            'data'    => $packages->map(function ($pkg) {
                // Determine starting price: check price_per_person, price_sharing, price_quad, etc., or hajj adult_pkr / price
                $startingPrice = 0.0;
                if (!empty($pkg->price_per_person) && $pkg->price_per_person > 0) {
                    $startingPrice = (float) $pkg->price_per_person;
                } elseif (!empty($pkg->price_sharing) && $pkg->price_sharing > 0) {
                    $startingPrice = (float) $pkg->price_sharing;
                } elseif (!empty($pkg->price_quad) && $pkg->price_quad > 0) {
                    $startingPrice = (float) $pkg->price_quad;
                } elseif (!empty($pkg->price_triple) && $pkg->price_triple > 0) {
                    $startingPrice = (float) $pkg->price_triple;
                } elseif (!empty($pkg->price_double) && $pkg->price_double > 0) {
                    $startingPrice = (float) $pkg->price_double;
                } elseif (!empty($pkg->adult_pkr) && $pkg->adult_pkr > 0) {
                    $startingPrice = (float) $pkg->adult_pkr;
                } elseif (!empty($pkg->price) && $pkg->price > 0) {
                    $startingPrice = (float) $pkg->price;
                }

                // Accommodations summary
                $makkah = $pkg->accommodations->firstWhere('place', 'makkah');
                $madinah = $pkg->accommodations->firstWhere('place', 'madinah');
                $firstAcc = $pkg->accommodations->first();

                // Flight overview summary
                $outboundFlight = $pkg->transportFlights->firstWhere('is_preferred', 1) ?? $pkg->transportFlights->first();
                $inboundFlight = $pkg->transportFlights->where('id', '!=', optional($outboundFlight)->id)->first();
                $flightOverview = null;
                if ($outboundFlight) {
                    $flightOverview = [
                        'airline'      => $outboundFlight->airline,
                        'flight_class' => $outboundFlight->flight_class,
                        'origin'       => $outboundFlight->origin,
                        'destination'  => $outboundFlight->destination,
                        'has_return'   => !is_null($inboundFlight),
                    ];
                }

                // Hotel stay overview
                $hotelSummary = [
                    'makkah'  => $makkah ? [
                        'hotel'         => $makkah->hotel,
                        'distance'      => $makkah->distance,
                        'duration'      => $makkah->days ? $makkah->days . ' Days' : null,
                        'meal_plan'     => $makkah->food_package,
                        'room_category' => $makkah->accommodation_type,
                    ] : null,
                    'madinah' => $madinah ? [
                        'hotel'         => $madinah->hotel,
                        'distance'      => $madinah->distance,
                        'duration'      => $madinah->days ? $madinah->days . ' Days' : null,
                        'meal_plan'     => $madinah->food_package,
                        'room_category' => $madinah->accommodation_type,
                    ] : null,
                    'primary' => ($pkg->primary_hotel || $firstAcc) ? [
                        'hotel'         => $pkg->primary_hotel ?? optional($firstAcc)->hotel,
                        'category'      => $pkg->hotel_star_rating ?? optional($firstAcc)->accommodation_type,
                        'meal_plan'     => $pkg->meal_plan ?? optional($firstAcc)->food_package,
                        'stay_nights'   => $pkg->stay_nights ?? optional($firstAcc)->nights,
                    ] : null,
                ];

                return [
                    'id'                      => $pkg->id,
                    'name'                    => $pkg->package_title ?? $pkg->package_name ?? $pkg->name ?? 'Package',
                    'title'                   => $pkg->package_title ?? $pkg->name,
                    'type'                    => ucfirst($pkg->package_type ?? 'hajj'),
                    'package_code'            => $pkg->package_code ?? $pkg->code ?? null,
                    'is_price_on_request'     => (bool) $pkg->is_price_on_request,
                    'price_on_request'        => (bool) $pkg->is_price_on_request,
                    'price_on_request_note'   => $pkg->is_price_on_request ? 'Best Available Price: On Request. Final price depends on travel dates, hotel category, and flight availability.' : null,
                    'is_group_package'        => (bool) $pkg->is_group_package,
                    'visa_status'             => $pkg->visa_status ?? 'included',
                    'is_dual_country'         => (bool) $pkg->is_dual_country,
                    'is_multi_city'           => (bool) $pkg->is_multi_city,
                    'departure_city'          => $pkg->departure_city,
                    'destination_city'        => $pkg->destination_city,
                    'destination_country'     => $pkg->destination_country,
                    'sightseeing_summary'     => $pkg->sightseeing_summary,
                    'price'                   => $startingPrice,
                    'starting_price'          => $startingPrice,
                    'price_per_person'        => (float) ($pkg->price_per_person ?? 0),
                    'room_rates'              => [
                        'sharing'    => (float) ($pkg->price_sharing ?? 0),
                        'quad'       => (float) ($pkg->price_quad ?? 0),
                        'triple'     => (float) ($pkg->price_triple ?? 0),
                        'double'     => (float) ($pkg->price_double ?? 0),
                        'per_person' => (float) ($pkg->price_per_person ?? 0),
                    ],
                    'currency'                => $pkg->currency ?? 'PKR',
                    'duration_days'           => $pkg->duration_days ?? $pkg->days ?? ($pkg->stay_nights ? $pkg->stay_nights + 1 : null),
                    'stay_nights'             => $pkg->stay_nights,
                    'duration_nights'         => $pkg->duration_nights ?? $pkg->stay_nights,
                    'valid_from'              => optional($pkg->valid_from)->format('Y-m-d') ?? $pkg->start_date ?? null,
                    'valid_until'             => optional($pkg->valid_until)->format('Y-m-d') ?? $pkg->end_date ?? null,
                    'hotels_summary'          => $hotelSummary,
                    'flight_overview'         => $flightOverview,
                    'ground_transfer_fleet'   => $pkg->ground_transfer_fleet,
                    'key_features'            => $pkg->key_features,
                    'feature_icons'           => $pkg->feature_icons ?? [],
                    'included_points'         => $pkg->package_included_points ?? [],
                    'company'                 => $pkg->company ? [
                        'id'   => $pkg->company->id,
                        'name' => $pkg->company->name ?? null,
                        'logo' => !empty($pkg->company->logo) ? asset($pkg->company->logo) : null,
                    ] : null,
                    'package_image'           => $pkg->package_image_url ?? (!empty($pkg->image ?? $pkg->banner_image) ? asset($pkg->image ?? $pkg->banner_image) : null),
                    'banner_image'            => $pkg->banner_image_url ?? null,
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

        $startingPrice = 0.0;
        if (!empty($package->price_per_person) && $package->price_per_person > 0) {
            $startingPrice = (float) $package->price_per_person;
        } elseif (!empty($package->price_sharing) && $package->price_sharing > 0) {
            $startingPrice = (float) $package->price_sharing;
        } elseif (!empty($package->price_quad) && $package->price_quad > 0) {
            $startingPrice = (float) $package->price_quad;
        } elseif (!empty($package->price_triple) && $package->price_triple > 0) {
            $startingPrice = (float) $package->price_triple;
        } elseif (!empty($package->price_double) && $package->price_double > 0) {
            $startingPrice = (float) $package->price_double;
        } elseif (!empty($package->adult_pkr) && $package->adult_pkr > 0) {
            $startingPrice = (float) $package->adult_pkr;
        } elseif (!empty($package->price) && $package->price > 0) {
            $startingPrice = (float) $package->price;
        }

        // Parse repeatable inclusions array
        $parsedInclusions = [];
        if (!empty($package->inclusions)) {
            $decoded = json_decode($package->inclusions, true);
            if (is_array($decoded)) {
                $parsedInclusions = $decoded;
            } else {
                $parsedInclusions = array_values(array_filter(array_map('trim', explode("\n", $package->inclusions))));
            }
        }

        // Parse exclusions list: from package_excludes_list (or fallback to exclusions text)
        $parsedExcludes = [];
        if (!empty($package->package_excludes_list)) {
            $parsedExcludes = is_array($package->package_excludes_list)
                ? $package->package_excludes_list
                : (json_decode($package->package_excludes_list, true) ?? []);
        } elseif (!empty($package->exclusions)) {
            $lines = array_filter(array_map('trim', explode("\n", $package->exclusions)));
            foreach ($lines as $line) {
                $parsedExcludes[] = [
                    'title'       => $line,
                    'description' => '',
                ];
            }
        }

        // Parse structured itinerary days
        $itineraryDays = [];
        if (!empty($package->itinerary_days)) {
            $itineraryDays = is_array($package->itinerary_days)
                ? $package->itinerary_days
                : (json_decode($package->itinerary_days, true) ?? []);
        }

        // Parse hotel amenities array
        $hotelAmenities = is_array($package->hotel_amenities) ? $package->hotel_amenities : [];

        // Format flight legs with full departure/arrival
        $flightLegs = $package->transportFlights->map(function ($fl) {
            return [
                'id'              => $fl->id,
                'airline'         => $fl->airline,
                'flight_no'       => $fl->flight_no,
                'flight_class'    => $fl->flight_class,
                'origin'          => $fl->origin,
                'destination'     => $fl->destination,
                'departure_date'  => $fl->departure_date,
                'departure_time'  => $fl->departure_time,
                'arrival_date'    => $fl->arrival_date,
                'arrival_time'    => $fl->arrival_time,
                'is_preferred'    => (bool) $fl->is_preferred,
                'is_outbound'     => (bool) $fl->is_preferred,
                'is_inbound'      => !(bool) $fl->is_preferred,
            ];
        });

        // Transport fleet overview
        $transportsOverview = $package->transports->map(function ($tr) {
            return [
                'id'      => $tr->id,
                'route'   => $tr->route,
                'vehicle' => $tr->vehicle,
                'type'    => $tr->type,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Package details retrieved successfully.',
            'data'    => [
                'id'                       => $package->id,
                'name'                     => $package->package_title ?? $package->package_name ?? $package->name ?? 'Package',
                'title'                    => $package->package_title ?? $package->name,
                'subtitle'                 => $package->subtitle,
                'type'                     => ucfirst($package->package_type ?? 'hajj'),
                'package_code'             => $package->package_code ?? $package->code ?? null,
                'is_price_on_request'      => (bool) $package->is_price_on_request,
                'price_on_request'         => (bool) $package->is_price_on_request,
                'price_on_request_note'    => $package->is_price_on_request ? 'Best Available Price: On Request. Final price depends on travel dates, hotel category, and flight availability.' : null,
                'is_group_package'         => (bool) $package->is_group_package,
                'visa_status'              => $package->visa_status ?? 'included',
                'is_dual_country'          => (bool) $package->is_dual_country,
                'is_multi_city'            => (bool) $package->is_multi_city,
                'departure_city'           => $package->departure_city,
                'destination_city'         => $package->destination_city,
                'destination_country'      => $package->destination_country,
                'sightseeing_summary'      => $package->sightseeing_summary,
                'price'                    => $startingPrice,
                'starting_price'           => $startingPrice,
                'price_per_person'         => (float) ($package->price_per_person ?? 0),
                'room_rates'               => [
                    'sharing'    => (float) ($package->price_sharing ?? 0),
                    'quad'       => (float) ($package->price_quad ?? 0),
                    'triple'     => (float) ($package->price_triple ?? 0),
                    'double'     => (float) ($package->price_double ?? 0),
                    'per_person' => (float) ($package->price_per_person ?? 0),
                ],
                'currency'                 => $package->currency ?? 'PKR',
                'duration_days'            => $package->duration_days ?? $package->days ?? ($package->stay_nights ? $package->stay_nights + 1 : null),
                'stay_nights'              => $package->stay_nights,
                'duration_nights'          => $package->duration_nights ?? $package->stay_nights,
                'valid_from'               => optional($package->valid_from)->format('Y-m-d') ?? $package->start_date ?? null,
                'valid_until'              => optional($package->valid_until)->format('Y-m-d') ?? $package->end_date ?? null,

                // Content & Matrices
                'duration_pricing_options' => $package->duration_pricing_options ?? [],
                'key_features'             => $package->key_features,
                'inclusions'               => $package->inclusions,
                'parsed_inclusions'        => $parsedInclusions,
                'hotel_amenities'          => $hotelAmenities,
                'exclusions'               => $package->exclusions,
                'excludes_list'            => $parsedExcludes,
                'package_excludes_list'    => $parsedExcludes,
                'itinerary_days'           => $itineraryDays,
                'requirements_text'        => $package->requirements_text,
                'description_notes'        => $package->description_notes ?? $package->notes,
                'notes'                    => $package->description_notes ?? $package->notes,
                'terms_and_conditions'     => $package->terms ? $package->terms->content : null,

                // Lodging & Ground Logistics
                'primary_hotel'            => $package->primary_hotel,
                'hotel_star_rating'        => $package->hotel_star_rating,
                'meal_plan'                => $package->meal_plan,
                'ground_transfer_fleet'    => $package->ground_transfer_fleet,

                // Casted JSON Arrays for Hajj packages compatibility
                'makkah_a'                 => $package->makkah_a,
                'makkah_b'                 => $package->makkah_b,
                'madinah_a'                => $package->madinah_a,
                'madinah_b'                => $package->madinah_b,
                'feature_icons'            => $package->feature_icons ?? [],
                'included_points'          => $package->package_included_points ?? [],
                'instructions'             => $package->instructions_points ?? [],

                // Relational Sections
                'accommodations'           => $package->accommodations,
                'itinerary'                => $package->itinerary,
                'maktab_address'           => $package->maktabAddress,
                'transports'               => $transportsOverview,
                'flights'                  => $flightLegs,
                'trains'                   => $package->transportTrains,
                'package_image'            => $package->package_image_url ?? (!empty($package->image ?? $package->banner_image) ? asset($package->image ?? $package->banner_image) : null),
                'banner_image'             => $package->banner_image_url ?? null,
                'giveaways'                => $package->giveaways->map(function ($item) {
                    return [
                        'id'    => $item->id,
                        'name'  => $item->name ?? $item->title ?? null,
                        'image' => !empty($item->image) ? asset($item->image) : null,
                    ];
                }),
                'company'                  => $package->company ? [
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
            'email'                => 'nullable|email|max:150',
            'passport_number'      => 'nullable|string|max:50',
            'passport'             => 'nullable|string|max:50',
            'cnic_number'          => 'nullable|string|max:30',
            'cnic'                 => 'nullable|string|max:30',
            'pax'                  => 'nullable|integer|min:1',
            'duration_days'        => 'nullable|string|max:50',
            'room_type'            => 'nullable|in:sharing,quad,triple,double',
            'special_request'      => 'nullable|string|max:2000',
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

        // Handle file uploads to storage/inquiries
        $passportPath = $request->hasFile('passport_document')
            ? $request->file('passport_document')->store('inquiries/passports', 'public')
            : null;

        $nicVisaPath = $request->hasFile('nic_or_visa_document')
            ? $request->file('nic_or_visa_document')->store('inquiries/documents', 'public')
            : null;

        $passportNum = $request->passport_number ?? $request->passport;
        $cnicNum = $request->cnic_number ?? $request->cnic;

        $inquiry = PackageInquiry::create([
            'package_id'            => $request->package_id,
            'full_name'             => $request->full_name,
            'contact'               => $request->contact,
            'email'                 => $request->email,
            'passport_number'       => $passportNum,
            'cnic_number'           => $cnicNum,
            'pax'                   => $request->pax ?? 1,
            'duration_days'         => $request->duration_days,
            'room_type'             => $request->room_type ?? 'sharing',
            'special_request'       => $request->special_request,
            'passport_document'     => $passportPath,
            'nic_or_visa_document'  => $nicVisaPath,
            'status'                => 'pending',
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

    public function getGalleries(Request $request): JsonResponse
    {
        $category = $request->query('category');
        $search   = $request->query('search');

        $query = Gallery::with('images')->latest();

        if (!empty($category) && in_array($category, ['makkah', 'madina', 'other'])) {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $galleries = $query->get()->map(function ($item) {
            return [
                'id'           => $item->id,
                'category'     => $item->category,
                'title'        => $item->title,
                'cover_image'  => $item->image_url,
                'total_images' => $item->images->count(),
                'description'  => $item->description,
                'created_at'   => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $galleries,
        ]);
    }

    public function getGalleryDetail($id): JsonResponse
    {
        $item = Gallery::with('images')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Ziarat item not found.',
            ], 404);
        }

        $images = $item->images->map(function ($img) {
            return [
                'id'         => $img->id,
                'image_url'  => $img->image_url,
                'is_primary' => (bool)$img->is_primary,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $item->id,
                'category'    => $item->category,
                'title'       => $item->title,
                'description' => $item->description,
                'cover_image' => $item->image_url,
                'images'      => $images,
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
    public function getHajjBooks(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = HajjBook::latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $books = $query->get()->map(function ($book) {
            return [
                'id'            => $book->id,
                'title'         => $book->title,
                'thumbnail_url' => $book->thumbnail_url,
                'file_url'      => $book->file_url,
                'file_type'     => $book->file_type,
                'file_size'     => $book->file_size,
                'created_at'    => $book->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $books,
        ]);
    }

    public function getHajjBookDetail($id): JsonResponse
    {
        $book = HajjBook::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $book->id,
                'title'         => $book->title,
                'thumbnail_url' => $book->thumbnail_url,
                'file_url'      => $book->file_url,
                'file_type'     => $book->file_type,
                'file_size'     => $book->file_size,
                'created_at'    => $book->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function getHujjajGuidelines(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = HujjajGuideline::where('is_active', true)->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . trim($search) . '%')
                    ->orWhere('sub_heading', 'like', '%' . trim($search) . '%');
            });
        }

        $guidelines = $query->get()->map(function ($item) {
            return [
                'id'          => $item->id,
                'title'       => $item->title,
                'sub_heading' => $item->sub_heading,
                'content'     => $item->content,
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $guidelines,
        ]);
    }

    public function getHajjVideos(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = TutorialVideo::where('is_active', true)->latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . trim($search) . '%');
        }

        $videos = $query->get()->map(function ($v) {
            return [
                'id'            => $v->id,
                'title'         => $v->title,
                'description'   => $v->description,
                'thumbnail_url' => $v->thumbnail_url,
                'video_type'    => $v->video_type,
                'play_url'      => $v->play_url,
                'created_at'    => $v->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $videos,
        ]);
    }

    public function getHujjajGuidelineDetail($id): JsonResponse
    {
        $item = HujjajGuideline::where('is_active', true)->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Guideline not found or inactive.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $item->id,
                'title'       => $item->title,
                'sub_heading' => $item->sub_heading,
                'content'     => $item->content,
                'created_at'  => $item->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function getHajjVideoDetail($id): JsonResponse
    {
        $video = TutorialVideo::where('is_active', true)->find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'            => $video->id,
                'title'         => $video->title,
                'description'   => $video->description,
                'thumbnail_url' => $video->thumbnail_url,
                'video_type'    => $video->video_type,
                'play_url'      => $video->play_url,
                'created_at'    => $video->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
    public function getVisaCountries(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = \App\Models\VisaCountry::where('is_active', true)
            ->with('visaTypes')
            ->latest();

        if (!empty($search)) {
            $query->where('country_name', 'like', '%' . trim($search) . '%')
                ->orWhere('country_code', 'like', '%' . trim($search) . '%');
        }

        $countries = $query->get()->map(function ($c) {
            return [
                'id'                   => $c->id,
                'country_name'         => $c->country_name,
                'country_code'         => $c->country_code,
                'monument_picture_url' => $c->monument_picture_url,
                'country_flag_url'     => $c->country_flag_url,
                'total_visa_types'     => $c->visaTypes->count(),
                'starting_price_pkr'   => $c->visaTypes->min('b2b_rate_pkr') ?? 0,
                'starting_fee_usd'     => $c->visaTypes->min('visa_fee_usd') ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $countries,
        ]);
    }

    public function getVisaCountryDetail($id): JsonResponse
    {
        $country = VisaCountry::where('is_active', true)
            ->with('visaTypes')
            ->find($id);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Visa Country not found or inactive.',
            ], 404);
        }

        $types = $country->visaTypes->map(function ($t) {
            return [
                'id'           => $t->id,
                'visa_name'    => $t->visa_name,
                'b2b_rate_pkr' => $t->b2b_rate_pkr,
                'visa_fee_usd' => $t->visa_fee_usd,
                'process_time' => $t->process_time,
                'notes'        => $t->notes,
                'requirements' => $t->requirements ?? [],
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                   => $country->id,
                'country_name'         => $country->country_name,
                'country_code'         => $country->country_code,
                'monument_picture_url' => $country->monument_picture_url,
                'country_flag_url'     => $country->country_flag_url,
                'visa_types'           => $types,
            ],
        ]);
    }
    public function submitVisaInquiry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'visa_country_id'          => 'required|exists:visa_countries,id',
            'visa_type_id'             => 'nullable|exists:visa_types,id',
            'full_name'                => 'required|string|max:150',
            'phone_number'             => 'required|string|max:30',
            'email'                    => 'nullable|email|max:150',
            'cnic'                     => 'required|string|max:30',
            'requirements'             => 'nullable|array',
            'requirements.*.title'     => 'required_with:requirements|string|max:200',
            'requirements.*.file'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Store dynamic files uploaded against each requirement
        $uploadedRequirements = [];
        if ($request->has('requirements') && is_array($request->requirements)) {
            foreach ($request->requirements as $index => $item) {
                $filePath = null;
                if ($request->hasFile("requirements.{$index}.file")) {
                    $filePath = $request->file("requirements.{$index}.file")->store('visa/inquiries/documents', 'public');
                }

                if ($filePath || !empty($item['title'])) {
                    $uploadedRequirements[] = [
                        'title'     => $item['title'],
                        'file_path' => $filePath,
                    ];
                }
            }
        }

        $inquiry = VisaInquiry::create([
            'visa_country_id' => $request->visa_country_id,
            'visa_type_id'    => $request->visa_type_id,
            'full_name'       => $request->full_name,
            'phone_number'    => $request->phone_number,
            'email'           => $request->email,
            'cnic'            => $request->cnic,
            'documents'       => $uploadedRequirements, // stored in JSON column
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully. Our team will contact you shortly.',
            'data'    => [
                'id'         => $inquiry->id,
                'status'     => $inquiry->status,
                'created_at' => $inquiry->created_at->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }
}
