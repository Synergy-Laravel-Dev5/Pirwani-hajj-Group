<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CareOfPersonReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Common query and statistics builder for Care of Persons Report
     */
    private function getCareOfReportData(Request $request)
    {
        $selectedCareOf = $request->get('care_of');
        $packageId      = $request->get('package_id');
        $gender         = $request->get('gender', 'all');
        $search         = $request->get('search');

        // Query bookings that have a care_of value, active (not deleted, not cancelled/rejected)
        $bookingsQuery = Booking::with([
            'client',
            'company',
            'package',
            'persons' => function ($q) {
                $q->orderBy('id', 'asc');
            }
        ])
        ->whereNotNull('care_of')
        ->where('care_of', '!=', '')
        ->whereNull('deleted_at')
        ->whereNotIn('status', ['cancelled', 'rejected']);

        // Filter by selected Care Of
        if (!empty($selectedCareOf) && $selectedCareOf !== 'all') {
            $bookingsQuery->where('care_of', $selectedCareOf);
        }

        // Filter by package
        if (!empty($packageId) && $packageId !== 'all') {
            $bookingsQuery->where('package_id', $packageId);
        }

        // Filter by search term
        if (!empty($search)) {
            $term = '%' . trim($search) . '%';
            $bookingsQuery->where(function ($bq) use ($term) {
                $bq->where('care_of', 'LIKE', $term)
                    ->orWhere('voucher_number', 'LIKE', $term)
                    ->orWhere('camp', 'LIKE', $term)
                    ->orWhere('phone', 'LIKE', $term)
                    ->orWhereHas('client', function ($cq) use ($term) {
                        $cq->where('name', 'LIKE', $term)->orWhere('phone', 'LIKE', $term);
                    })
                    ->orWhereHas('company', function ($comQ) use ($term) {
                        $comQ->where('company_name', 'LIKE', $term);
                    })
                    ->orWhereHas('persons', function ($pq) use ($term) {
                        $pq->where('full_name', 'LIKE', $term)
                            ->orWhere('given_name', 'LIKE', $term)
                            ->orWhere('surname', 'LIKE', $term)
                            ->orWhere('passport_number', 'LIKE', $term)
                            ->orWhere('cnic', 'LIKE', $term)
                            ->orWhere('hajj_id', 'LIKE', $term)
                            ->orWhere('hb_number', 'LIKE', $term);
                    });
            });
        }

        $allBookings = $bookingsQuery->orderBy('care_of', 'asc')->orderBy('id', 'asc')->get();

        // If gender filter is active, filter persons inside each booking
        if (!empty($gender) && $gender !== 'all') {
            $genderLower = strtolower($gender);
            foreach ($allBookings as $booking) {
                $booking->setRelation(
                    'persons',
                    $booking->persons->filter(function ($person) use ($genderLower) {
                        return strtolower($person->gender ?? '') === $genderLower;
                    })->values()
                );
            }
            // If filtering by gender, keep bookings that have matching persons (or all bookings if viewing totals)
            $allBookings = $allBookings->filter(function ($booking) {
                return $booking->persons->count() > 0;
            })->values();
        }

        // Group by Care Of person / agent
        $groupedByCareOf = $allBookings->groupBy('care_of');

        // Calculate KPI Statistics
        $totalCareOfGroups = $groupedByCareOf->count();
        $totalBookings     = $allBookings->count();

        $allPersonsCollection = $allBookings->flatMap(function ($b) {
            return $b->persons;
        });

        $totalPersons  = $allPersonsCollection->count();
        $maleCount     = $allPersonsCollection->filter(fn($p) => strtolower($p->gender ?? '') === 'male')->count();
        $femaleCount   = $allPersonsCollection->filter(fn($p) => strtolower($p->gender ?? '') === 'female')->count();
        $passportCount = $allPersonsCollection->filter(fn($p) => !empty($p->passport_number) && trim($p->passport_number) !== '-')->count();
        $cnicCount     = $allPersonsCollection->filter(fn($p) => !empty($p->cnic) && trim($p->cnic) !== '-')->count();
        $photoCount    = $allPersonsCollection->filter(fn($p) => !empty($p->photo))->count();

        $totalAmount   = $allBookings->sum('total_amount');
        $totalReceived = $allBookings->sum('total_received');
        $totalBalance  = $allBookings->sum('balance');

        // Dropdown filter data
        $distinctCareOfList = Booking::whereNotNull('care_of')
            ->where('care_of', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->distinct()
            ->orderBy('care_of', 'asc')
            ->pluck('care_of');

        $packages = Package::latest()->get();

        return compact(
            'groupedByCareOf',
            'allBookings',
            'totalCareOfGroups',
            'totalBookings',
            'totalPersons',
            'maleCount',
            'femaleCount',
            'passportCount',
            'cnicCount',
            'photoCount',
            'totalAmount',
            'totalReceived',
            'totalBalance',
            'distinctCareOfList',
            'packages',
            'selectedCareOf',
            'packageId',
            'gender',
            'search'
        );
    }

    /**
     * Display Care of Persons Report screen
     */
    public function index(Request $request)
    {
        $this->authorize('care_of_report_view');
        $data = $this->getCareOfReportData($request);

        return view('reports.care_of_person', $data);
    }

    /**
     * Sanitize string for HTTP download filename
     */
    private function sanitizeFilename(string $string): string
    {
        // Replace slashes, backslashes and illegal filename characters with underscores
        $cleaned = preg_replace('~[\\\\/:*?"<>|]+~', '_', $string);
        // Normalize whitespace and underscores
        $cleaned = preg_replace('~[\s_]+~', '_', trim($cleaned));
        return trim($cleaned, '_');
    }

    /**
     * Export Care of Persons Report as PDF
     */
    public function exportPdf(Request $request)
    {
        $this->authorize('care_of_report_view');
        $data = $this->getCareOfReportData($request);

        $visibleCols = null;
        if ($request->has('columns')) {
            $raw = trim($request->get('columns'));
            if ($raw === 'none' || $raw === '') {
                $visibleCols = [];
            } else {
                $visibleCols = array_values(array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== ''));
            }
        }
        $data['visibleColumns'] = $visibleCols;

        $pdf = Pdf::loadView('reports.care_of_person_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $careOfSuffix = '';
        if (!empty($data['selectedCareOf']) && $data['selectedCareOf'] !== 'all') {
            $careOfSuffix = '_' . strtoupper($this->sanitizeFilename($data['selectedCareOf']));
        }
        $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Care of Persons Report as Excel (.xls stream)
     */
    public function exportExcel(Request $request)
    {
        $this->authorize('care_of_report_view');
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data = $this->getCareOfReportData($request);
        $grouped = $data['groupedByCareOf'];

        $careOfSuffix = '';
        if (!empty($data['selectedCareOf']) && $data['selectedCareOf'] !== 'all') {
            $careOfSuffix = '_' . strtoupper($this->sanitizeFilename($data['selectedCareOf']));
        }
        $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.xls';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Care Of: ' . ($data['selectedCareOf'] ?: 'ALL') . ' | Groups: ' . $data['totalCareOfGroups'] . ' | Total Bookings: ' . $data['totalBookings'] . ' | Total Persons: ' . $data['totalPersons'];

        return response()->streamDownload(function () use ($grouped, $filterSummary) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Care Of Report</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #1E293B; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center; vertical-align: middle; height: 30px; font-size: 11px; }
                td { border: 1px solid #CBD5E1; vertical-align: middle; padding: 6px; font-size: 11px; }
                .careof-header { background-color: #0F172A; color: #F8FAFC; font-size: 13px; font-weight: bold; height: 32px; padding-left: 10px; }
                .booking-header { background-color: #E2E8F0; color: #0F172A; font-weight: bold; font-size: 11px; height: 26px; }
                .center { text-align: center; }
                .gender-male { color: #1E3A8A; font-weight: bold; }
                .gender-female { color: #9D174D; font-weight: bold; }
            </style></head><body>';
            echo '<table>';
            echo '<tr><th colspan="14" style="font-size: 16px; height: 38px; background-color: #0F172A; color: #D4AF37; border: none; text-align: center;">PIRWANI HAJJ GROUP - CARE OF PERSONS REPORT & BOOKING DETAILS</th></tr>';
            echo '<tr><td colspan="14" style="text-align: center; font-style: italic; color: #475569; border: none; height: 24px;">' . htmlspecialchars($filterSummary) . '</td></tr>';

            foreach ($grouped as $careOfName => $bookings) {
                $groupPax = $bookings->sum(function($b) { return $b->persons->count(); });
                $groupAmount = $bookings->sum('total_amount');
                $groupReceived = $bookings->sum('total_received');
                $groupBalance = $bookings->sum('balance');

                echo '<tr><td colspan="14" class="careof-header">'
                    . 'CARE OF: ' . htmlspecialchars($careOfName) 
                    . ' &nbsp;|&nbsp; Bookings: ' . $bookings->count()
                    . ' &nbsp;|&nbsp; Total Pilgrims: ' . $groupPax
                    . ' &nbsp;|&nbsp; Total Amount: PKR ' . number_format($groupAmount)
                    . ' &nbsp;|&nbsp; Received: PKR ' . number_format($groupReceived)
                    . ' &nbsp;|&nbsp; Balance: PKR ' . number_format($groupBalance)
                    . '</td></tr>';

                foreach ($bookings as $bIndex => $booking) {
                    $clientName = $booking->booking_for === 'company' ? ($booking->company->company_name ?? 'N/A') : ($booking->client->name ?? 'N/A');
                    $voucher = $booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT));
                    $pkgName = $booking->package->package_title ?? $booking->package->name ?? $booking->package_name ?? ucfirst($booking->package_type ?? 'N/A');
                    $campName = $booking->camp ?: 'â€”';

                    echo '<tr><td colspan="14" class="booking-header">'
                        . 'Booking #' . ($bIndex + 1) . ' | Ref: ' . htmlspecialchars($voucher)
                        . ' | Client: ' . htmlspecialchars($clientName)
                        . ' | Package: ' . htmlspecialchars($pkgName)
                        . ' | Camp: ' . htmlspecialchars($campName)
                        . ' | Pax: ' . $booking->persons->count()
                        . ' | Amount: ' . number_format($booking->total_amount)
                        . ' | Recv: ' . number_format($booking->total_received)
                        . ' | Bal: ' . number_format($booking->balance)
                        . '</td></tr>';

                    echo '<tr>
                        <th>#</th>
                        <th>HAJJ ID</th>
                        <th>HB NUMBER</th>
                        <th>FULL NAME</th>
                        <th>SURNAME / GIVEN</th>
                        <th>GENDER</th>
                        <th>PASSPORT NO</th>
                        <th>DOI</th>
                        <th>EXPIRY</th>
                        <th>CNIC</th>
                        <th>PHONE</th>
                        <th>ROOM ALLOCATION</th>
                        <th>HOTEL</th>
                        <th>MAKTAB / CAMP</th>
                    </tr>';

                    $pIndex = 1;
                    foreach ($booking->persons as $person) {
                        $gender = strtoupper($person->gender ?? 'MALE');
                        $genderClass = ($gender === 'FEMALE') ? 'gender-female' : 'gender-male';
                        $roomInfo = ($person->room_number ? 'Room ' . $person->room_number : '') . ($person->room_type ? ' (' . $person->room_type . ')' : '');

                        echo '<tr>';
                        echo '<td class="center">' . $pIndex++ . '</td>';
                        echo '<td class="center"><strong>' . htmlspecialchars($person->hajj_id ?? 'â€”') . '</strong></td>';
                        echo '<td class="center">' . htmlspecialchars(\App\Models\BookingPerson::formatHbNumber($person->hb_number) ?: 'â€”') . '</td>';
                        echo '<td><strong>' . htmlspecialchars($person->full_name ?? 'â€”') . '</strong></td>';
                        echo '<td>' . htmlspecialchars(trim(($person->surname ?? '') . ' ' . ($person->given_name ?? '')) ?: 'â€”') . '</td>';
                        echo '<td class="center ' . $genderClass . '">' . htmlspecialchars($person->gender ?? 'â€”') . '</td>';
                        echo '<td class="center"><strong>' . htmlspecialchars($person->passport_number ?? 'â€”') . '</strong></td>';
                        echo '<td class="center">' . htmlspecialchars($person->date_of_issue ?: ($person->passport_issue_date ?: 'â€”')) . '</td>';
                        echo '<td class="center">' . htmlspecialchars($person->passport_expiry_date ?: 'â€”') . '</td>';
                        echo '<td class="center">' . htmlspecialchars($person->cnic ?: 'â€”') . '</td>';
                        echo '<td class="center">' . htmlspecialchars($person->phone ?: ($booking->phone ?: 'â€”')) . '</td>';
                        echo '<td class="center">' . htmlspecialchars($roomInfo ?: 'â€”') . '</td>';
                        echo '<td>' . htmlspecialchars($person->hotel_name ?? 'â€”') . '</td>';
                        echo '<td>' . htmlspecialchars($campName) . '</td>';
                        echo '</tr>';
                    }
                }
                echo '<tr><td colspan="14" style="height: 12px; background-color: #FFFFFF; border: none;"></td></tr>';
            }

            echo '</table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
