<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\Package;
use Illuminate\Http\Request;

class PersonReportController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $gender     = $request->get('gender', 'all');
        $maktab     = $request->get('maktab', 'all');
        $packageId  = $request->get('package_id');

        // Query BookingPerson with active bookings
        $query = BookingPerson::with(['booking.client', 'booking.company', 'booking.package'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // Search Filter
        if (!empty($search)) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'LIKE', $term)
                  ->orWhere('given_name', 'LIKE', $term)
                  ->orWhere('surname', 'LIKE', $term)
                  ->orWhere('father_name', 'LIKE', $term)
                  ->orWhere('passport_number', 'LIKE', $term)
                  ->orWhere('cnic', 'LIKE', $term)
                  ->orWhere('phone', 'LIKE', $term)
                  ->orWhere('hajj_id', 'LIKE', $term)
                  ->orWhere('hb_number', 'LIKE', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('voucher_number', 'LIKE', $term)
                         ->orWhere('camp', 'LIKE', $term)
                         ->orWhereHas('client', function ($cq) use ($term) {
                             $cq->where('name', 'LIKE', $term)->orWhere('phone', 'LIKE', $term);
                         })
                         ->orWhereHas('company', function ($comQ) use ($term) {
                             $comQ->where('company_name', 'LIKE', $term);
                         });
                  });
            });
        }

        // Gender Filter
        if (!empty($gender) && $gender !== 'all') {
            $query->whereRaw('LOWER(gender) = ?', [strtolower($gender)]);
        }

        // Maktab / Camp Filter
        if (!empty($maktab) && $maktab !== 'all') {
            $query->whereHas('booking', function ($bq) use ($maktab) {
                if ($maktab === 'maktab_a') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Maktab A%')
                            ->orWhere('camp', 'LIKE', '%Zone 1%')
                            ->orWhere('camp', 'LIKE', '%Zone 2%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%A%')->orWhere('maktab', 'LIKE', '%A%');
                            });
                    });
                } elseif ($maktab === 'maktab_c') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Maktab C%')
                            ->orWhere('camp', 'LIKE', '%Zone 5%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%C%')->orWhere('maktab', 'LIKE', '%C%');
                            });
                    });
                } elseif ($maktab === 'combo') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Combo%')
                            ->orWhere('camp', 'LIKE', '%A & C%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%Combo%')
                                   ->orWhere('camp_category', 'LIKE', '%A & C%')
                                   ->orWhere('camp_category', 'LIKE', '%C /A%');
                            });
                    });
                } else {
                    $term = '%' . $maktab . '%';
                    $bq->where('camp', 'LIKE', $term)
                       ->orWhereHas('package', function ($pq) use ($term) {
                           $pq->where('camp_category', 'LIKE', $term)->orWhere('maktab', 'LIKE', $term);
                       });
                }
            });
        }

        // Package Filter
        if (!empty($packageId) && $packageId !== 'all') {
            $query->whereHas('booking', function ($bq) use ($packageId) {
                $bq->where('package_id', $packageId);
            });
        }

        $persons = $query->orderBy('booking_id', 'desc')->orderBy('id', 'asc')->get();

        // KPI Statistics
        $totalPersons   = $persons->count();
        $maleCount      = $persons->filter(fn($p) => strtolower($p->gender ?? '') === 'male')->count();
        $femaleCount    = $persons->filter(fn($p) => strtolower($p->gender ?? '') === 'female')->count();
        $passportCount  = $persons->filter(fn($p) => !empty($p->passport_number) && trim($p->passport_number) !== '-')->count();
        $cnicCount      = $persons->filter(fn($p) => !empty($p->cnic) && trim($p->cnic) !== '-')->count();
        $photoCount     = $persons->filter(fn($p) => !empty($p->photo))->count();

        // Dropdown Data
        $packages = Package::latest()->get();
        $distinctCamps = Booking::whereNotNull('camp')->where('camp', '!=', '')->distinct()->pluck('camp')->toArray();

        return view('reports.person', compact(
            'persons',
            'totalPersons',
            'maleCount',
            'femaleCount',
            'passportCount',
            'cnicCount',
            'photoCount',
            'packages',
            'distinctCamps',
            'search',
            'gender',
            'maktab',
            'packageId'
        ));
    }
}
