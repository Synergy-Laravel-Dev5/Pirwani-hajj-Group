<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PersonReportController extends Controller
{
    private function getFilteredPersons(Request $request)
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
                        $sub->where(function ($q) {
                            $q->whereNotNull('camp')->where('camp', '!=', '')
                              ->where(function ($c) {
                                  $c->where('camp', 'LIKE', '%Maktab A%')
                                    ->orWhere('camp', 'LIKE', '%Zone 1%')
                                    ->orWhere('camp', 'LIKE', '%Zone 2%');
                              })
                              ->where('camp', 'NOT LIKE', '%Maktab C%')
                              ->where('camp', 'NOT LIKE', '%Zone 5%');
                        })->orWhere(function ($q) {
                            $q->where(function ($empty) {
                                $empty->whereNull('camp')->orWhere('camp', '');
                            })->whereHas('package', function ($pq) {
                                $pq->where(function ($pSub) {
                                    $pSub->where('camp_category', '=', 'A')
                                         ->orWhere('camp_category', 'LIKE', 'A (%')
                                         ->orWhere('camp_zone', 'LIKE', '%ZONE 1%')
                                         ->orWhere('camp_zone', 'LIKE', '%ZONE 2%');
                                })->where('camp_category', 'NOT LIKE', '%C%');
                            });
                        });
                    });
                } elseif ($maktab === 'maktab_c') {
                    $bq->where(function ($sub) {
                        $sub->where(function ($q) {
                            $q->whereNotNull('camp')->where('camp', '!=', '')
                              ->where(function ($c) {
                                  $c->where('camp', 'LIKE', '%Maktab C%')
                                    ->orWhere('camp', 'LIKE', '%Zone 5%');
                              })
                              ->where('camp', 'NOT LIKE', '%Maktab A%')
                              ->where('camp', 'NOT LIKE', '%Zone 1%')
                              ->where('camp', 'NOT LIKE', '%Zone 2%');
                        })->orWhere(function ($q) {
                            $q->where(function ($empty) {
                                $empty->whereNull('camp')->orWhere('camp', '');
                            })->whereHas('package', function ($pq) {
                                $pq->where(function ($pSub) {
                                    $pSub->where('camp_category', '=', 'C')
                                         ->orWhere('camp_category', 'LIKE', 'C (%')
                                         ->orWhere('camp_zone', 'LIKE', '%ZONE 5%');
                                })->where('camp_category', 'NOT LIKE', '%A%');
                            });
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
                    $bq->where('camp', 'LIKE', $term);
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

        return compact(
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
        );
    }

    public function index(Request $request)
    {
        $data = $this->getFilteredPersons($request);

        return view('reports.person', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredPersons($request);

        $visibleCols = null;
        if ($request->has('columns') && !empty($request->columns)) {
            $visibleCols = is_array($request->columns) ? $request->columns : explode(',', $request->columns);
            $visibleCols = array_map('trim', $visibleCols);
        }
        $data['visibleColumns'] = $visibleCols;

        $pdf = Pdf::loadView('reports.person_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $maktabPart = (!empty($data['maktab']) && $data['maktab'] !== 'all') ? '_' . strtoupper(str_replace(' ', '_', $data['maktab'])) : '';
        $genderPart = (!empty($data['gender']) && $data['gender'] !== 'all') ? '_' . strtoupper($data['gender']) : '';
        $fileName = 'Official_Person_Manifest' . $maktabPart . $genderPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data = $this->getFilteredPersons($request);
        $persons = $data['persons'];

        $maktabPart = (!empty($data['maktab']) && $data['maktab'] !== 'all') ? '_' . strtoupper(str_replace(' ', '_', $data['maktab'])) : '';
        $genderPart = (!empty($data['gender']) && $data['gender'] !== 'all') ? '_' . strtoupper($data['gender']) : '';
        $fileName = 'Official_Person_Manifest' . $maktabPart . $genderPart . '_' . date('Ymd_His') . '.xlsx';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Maktab: ' . strtoupper($data['maktab']) . ' | Gender: ' . strtoupper($data['gender']) . ' | Total: ' . $data['totalPersons'];

        // Live server safe fallback to stream HTML .xls
        $xlsFileName = str_replace('.xlsx', '.xls', $fileName);
        return response()->streamDownload(function () use ($persons, $filterSummary) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Pilgrims Manifest</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #1E293B; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center; vertical-align: middle; height: 32px; font-size: 11px; }
                td { border: 1px solid #CBD5E1; vertical-align: middle; padding: 6px; font-size: 11px; }
                .center { text-align: center; }
                .gender-male { color: #1E3A8A; font-weight: bold; }
                .gender-female { color: #9D174D; font-weight: bold; }
            </style></head><body>';
            echo '<table>';
            echo '<tr><th colspan="16" style="font-size: 16px; height: 40px; background-color: #F8FAFC; color: #1E293B; border: none;">PIRWANI HAJJ GROUP - OFFICIAL BOOKING PERSON & PILGRIM MANIFEST</th></tr>';
            echo '<tr><td colspan="16" style="text-align: center; font-style: italic; color: #64748B; border: none; height: 25px;">' . htmlspecialchars($filterSummary) . '</td></tr>';
            echo '<tr>
                <th>SR</th>
                <th>HAJJ ID</th>
                <th>HB NUMBER</th>
                <th>FULL NAME</th>
                <th>GIVEN NAME</th>
                <th>SURNAME</th>
                <th>HUSBAND / FATHER NAME</th>
                <th>GENDER</th>
                <th>PASSPORT NO</th>
                <th>DATE OF ISSUE</th>
                <th>PASSPORT EXPIRY DATE</th>
                <th>CNIC</th>
                <th>PHONE / CONTACT</th>
                <th>VOUCHER / BOOKING REF</th>
                <th>PACKAGE</th>
                <th>MAKTAB / CAMP</th>
            </tr>';

            $sr = 1;
            foreach ($persons as $p) {
                $booking = $p->booking;
                $campName = $booking ? ($booking->camp ?: ($booking->package->camp_category ? 'Maktab ' . $booking->package->camp_category : 'Standard Camp')) : '—';
                $pkgTitle = $booking && $booking->package ? ($booking->package->package_title ?: $booking->package->name) : '—';
                $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';
                $pGender = strtoupper($p->gender ?? 'MALE');
                $genderClass = ($pGender === 'FEMALE') ? 'gender-female' : 'gender-male';

                $rawDoi = $p->date_of_issue ?: $p->passport_issue_date;
                $doiStr = !empty($rawDoi) ? date('d M Y', strtotime($rawDoi)) : '—';
                $expStr = !empty($p->passport_expiry_date) ? date('d M Y', strtotime($p->passport_expiry_date)) : '—';

                echo '<tr>';
                echo '<td class="center">' . ($sr++) . '</td>';
                echo '<td class="center"><strong>' . htmlspecialchars($p->hajj_id ?? '—') . '</strong></td>';
                echo '<td class="center">' . htmlspecialchars($p->hb_number ?? '—') . '</td>';
                echo '<td><strong>' . htmlspecialchars($p->full_name ?? ($p->given_name . ' ' . $p->surname)) . '</strong></td>';
                echo '<td>' . htmlspecialchars($p->given_name ?? '—') . '</td>';
                echo '<td>' . htmlspecialchars($p->surname ?? '—') . '</td>';
                echo '<td>' . htmlspecialchars($p->father_name ?? '—') . '</td>';
                echo '<td class="center ' . $genderClass . '">' . htmlspecialchars($pGender) . '</td>';
                echo '<td class="center" style="font-weight: bold;">' . htmlspecialchars($p->passport_number ?? '—') . '</td>';
                echo '<td class="center">' . htmlspecialchars($doiStr) . '</td>';
                echo '<td class="center">' . htmlspecialchars($expStr) . '</td>';
                echo '<td class="center">' . htmlspecialchars($p->cnic ?? '—') . '</td>';
                echo '<td class="center">' . htmlspecialchars($p->phone ?? '—') . '</td>';
                echo '<td class="center">' . htmlspecialchars($voucherNo) . '</td>';
                echo '<td>' . htmlspecialchars($pkgTitle) . '</td>';
                echo '<td class="center"><strong>' . htmlspecialchars($campName) . '</strong></td>';
                echo '</tr>';
            }
            echo '</table></body></html>';
        }, $xlsFileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
