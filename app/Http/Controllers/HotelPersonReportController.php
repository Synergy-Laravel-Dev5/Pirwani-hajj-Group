<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHotel;
use App\Models\BookingPerson;
use App\Models\Hotel;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelPersonReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getFilteredData($request);

        return view('reports.hotel_person', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);

        $pdf = Pdf::loadView('reports.hotel_person_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $hotelPart    = !empty($data['hotelFilter']) ? '_' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '_', $data['hotelFilter'])) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName     = 'Official_Hotel_Person_Report' . $hotelPart . $locationPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data       = $this->getFilteredData($request);
        $hotelStays = $data['hotelStays'];

        $hotelPart    = !empty($data['hotelFilter']) ? '_' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '_', $data['hotelFilter'])) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName     = 'Official_Hotel_Person_Report' . $hotelPart . $locationPart . '_' . date('Ymd_His') . '.xlsx';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Hotel: ' . ($data['hotelFilter'] ?: 'ALL') . ' | Location: ' . strtoupper($data['locationFilter'] ?: 'ALL');

        // Check if PhpSpreadsheet is available
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $xlsFileName = str_replace('.xlsx', '.xls', $fileName);
            return response()->streamDownload(function () use ($hotelStays, $filterSummary) {
                echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;width:100%;} th,td{border:1px solid #000;padding:6px;font-family:Arial,sans-serif;font-size:11px;} th{background-color:#0f172a;color:#fff;font-weight:bold;text-align:center;} .text-center{text-align:center;} .fw-bold{font-weight:bold;}</style></head><body>';
                echo '<h2>PIRWANI HAJJ GROUP - HOTEL & LOCATION PERSON OCCUPANCY REPORT</h2>';
                echo '<p>' . htmlspecialchars($filterSummary) . '</p>';
                echo '<table><thead><tr>';
                echo '<th>SR</th><th>HAJJ ID</th><th>HB #</th><th>PILGRIM FULL NAME</th><th>GIVEN NAME</th><th>SURNAME</th><th>HUSBAND / FATHER NAME</th><th>GENDER</th><th>PASSPORT NO</th><th>DOI</th><th>EXPIRY</th><th>HOTEL NAME</th><th>LOCATION</th><th>ROOM TYPE</th><th>ROOM NO</th><th>CHECK-IN</th><th>CHECK-OUT</th><th>NIGHTS</th><th>MAKTAB / CAMP</th><th>PACKAGE</th><th>VOUCHER #</th><th>BOOKING #</th><th>STATUS</th>';
                echo '</tr></thead><tbody>';

                $sr = 1;
                foreach ($hotelStays as $stay) {
                    $b = $stay->booking;
                    $persons = $b ? $b->persons : collect([]);
                    if ($persons->isEmpty()) {
                        $persons = collect([(object)[
                            'full_name' => $b?->client?->name ?? 'Guest',
                            'given_name' => '', 'surname' => '', 'father_name' => '',
                            'hajj_id' => '', 'hb_number' => '', 'gender' => '',
                            'passport_number' => $b?->passport_number ?? '',
                            'date_of_issue' => '', 'passport_expiry_date' => '',
                        ]]);
                    }

                    $checkInStr  = $stay->check_in ? date('d/m/Y', strtotime($stay->check_in)) : '—';
                    $checkOutStr = $stay->check_out ? date('d/m/Y', strtotime($stay->check_out)) : '—';
                    $nights      = ($stay->check_in && $stay->check_out) ? \Carbon\Carbon::parse($stay->check_in)->diffInDays(\Carbon\Carbon::parse($stay->check_out)) : '—';
                    $maktabStr   = $b?->camp ?: ($b?->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');

                    foreach ($persons as $p) {
                        echo '<tr>';
                        echo '<td class="text-center">' . $sr++ . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars($p->hajj_id ?? '') . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars($p->hb_number ?? '') . '</td>';
                        echo '<td class="fw-bold">' . htmlspecialchars($p->full_name ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($p->given_name ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($p->surname ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($p->father_name ?? ($p->husband_or_father_name ?? '')) . '</td>';
                        echo '<td class="text-center">' . strtoupper($p->gender ?? '') . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($p->passport_number ?? '') . '</td>';
                        echo '<td class="text-center">' . (!empty($p->date_of_issue) ? date('d/m/Y', strtotime($p->date_of_issue)) : '') . '</td>';
                        echo '<td class="text-center">' . (!empty($p->passport_expiry_date) ? date('d/m/Y', strtotime($p->passport_expiry_date)) : '') . '</td>';
                        echo '<td class="fw-bold">' . htmlspecialchars($stay->hotel_name ?? '') . '</td>';
                        echo '<td class="text-center">' . strtoupper($stay->location ?? '') . '</td>';
                        echo '<td class="text-center">' . strtoupper($stay->room_type ?? '') . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($stay->room_number ?? '—') . '</td>';
                        echo '<td class="text-center">' . $checkInStr . '</td>';
                        echo '<td class="text-center">' . $checkOutStr . '</td>';
                        echo '<td class="text-center">' . $nights . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars($maktabStr) . '</td>';
                        echo '<td>' . htmlspecialchars($b?->package?->package_title ?? ($b?->package?->name ?? '—')) . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b?->voucher_number ?? '') . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars($b?->booking_number ?? $b?->id) . '</td>';
                        echo '<td class="text-center">Active</td>';
                        echo '</tr>';
                    }
                }
                echo '</tbody></table></body></html>';
            }, $xlsFileName, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="' . $xlsFileName . '"',
            ]);
        }

        // PhpSpreadsheet Export
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hotel Person Occupancy');

        // Main Title
        $sheet->mergeCells('A1:W1');
        $sheet->setCellValue('A1', 'PIRWANI HAJJ GROUP - HOTEL & LOCATION PERSON OCCUPANCY REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Subtitle
        $sheet->mergeCells('A2:W2');
        $sheet->setCellValue('A2', $filterSummary);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = [
            'SR', 'HAJJ ID', 'HB #', 'PILGRIM FULL NAME', 'GIVEN NAME', 'SURNAME', 'HUSBAND / FATHER NAME',
            'GENDER', 'PASSPORT NO', 'DOI', 'EXPIRY', 'HOTEL NAME', 'LOCATION', 'ROOM TYPE', 'ROOM NO',
            'CHECK-IN', 'CHECK-OUT', 'NIGHTS', 'MAKTAB / CAMP', 'PACKAGE', 'VOUCHER #', 'BOOKING #', 'STATUS'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $sheet->getStyle($col . '4')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
            $sheet->getStyle($col . '4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
            $sheet->getStyle($col . '4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        $rowNum = 5;
        $sr = 1;
        foreach ($hotelStays as $stay) {
            $b = $stay->booking;
            $persons = $b ? $b->persons : collect([]);
            if ($persons->isEmpty()) {
                $persons = collect([(object)[
                    'full_name' => $b?->client?->name ?? 'Guest',
                    'given_name' => '', 'surname' => '', 'father_name' => '',
                    'hajj_id' => '', 'hb_number' => '', 'gender' => '',
                    'passport_number' => $b?->passport_number ?? '',
                    'date_of_issue' => '', 'passport_expiry_date' => '',
                ]]);
            }

            $checkInStr  = $stay->check_in ? date('d/m/Y', strtotime($stay->check_in)) : '—';
            $checkOutStr = $stay->check_out ? date('d/m/Y', strtotime($stay->check_out)) : '—';
            $nights      = ($stay->check_in && $stay->check_out) ? \Carbon\Carbon::parse($stay->check_in)->diffInDays(\Carbon\Carbon::parse($stay->check_out)) : '—';
            $maktabStr   = $b?->camp ?: ($b?->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');

            foreach ($persons as $p) {
                $sheet->setCellValue('A' . $rowNum, $sr++);
                $sheet->setCellValue('B' . $rowNum, $p->hajj_id ?? '');
                $sheet->setCellValue('C' . $rowNum, $p->hb_number ?? '');
                $sheet->setCellValue('D' . $rowNum, $p->full_name ?? '');
                $sheet->setCellValue('E' . $rowNum, $p->given_name ?? '');
                $sheet->setCellValue('F' . $rowNum, $p->surname ?? '');
                $sheet->setCellValue('G' . $rowNum, $p->father_name ?? ($p->husband_or_father_name ?? ''));
                $sheet->setCellValue('H' . $rowNum, strtoupper($p->gender ?? ''));
                $sheet->setCellValue('I' . $rowNum, $p->passport_number ?? '');
                $sheet->setCellValue('J' . $rowNum, !empty($p->date_of_issue) ? date('d/m/Y', strtotime($p->date_of_issue)) : '');
                $sheet->setCellValue('K' . $rowNum, !empty($p->passport_expiry_date) ? date('d/m/Y', strtotime($p->passport_expiry_date)) : '');
                $sheet->setCellValue('L' . $rowNum, $stay->hotel_name ?? '');
                $sheet->setCellValue('M' . $rowNum, strtoupper($stay->location ?? ''));
                $sheet->setCellValue('N' . $rowNum, strtoupper($stay->room_type ?? ''));
                $sheet->setCellValue('O' . $rowNum, $stay->room_number ?? '—');
                $sheet->setCellValue('P' . $rowNum, $checkInStr);
                $sheet->setCellValue('Q' . $rowNum, $checkOutStr);
                $sheet->setCellValue('R' . $rowNum, $nights);
                $sheet->setCellValue('S' . $rowNum, $maktabStr);
                $sheet->setCellValue('T' . $rowNum, $b?->package?->package_title ?? ($b?->package?->name ?? '—'));
                $sheet->setCellValue('U' . $rowNum, $b?->voucher_number ?? '');
                $sheet->setCellValue('V' . $rowNum, $b?->booking_number ?? $b?->id);
                $sheet->setCellValue('W' . $rowNum, 'Active');

                $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $rowNum . ':K' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('M' . $rowNum . ':S' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('U' . $rowNum . ':W' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $rowNum++;
            }
        }

        foreach (range('A', 'W') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function getFilteredData(Request $request)
    {
        $hotelFilter    = trim($request->get('hotel_name', ''));
        $locationFilter = strtolower(trim($request->get('location', 'all')));
        $search         = trim($request->get('search', ''));
        $packageFilter  = $request->get('package_id');
        $fromDate       = $request->get('from_date');
        $toDate         = $request->get('to_date');

        // Query active booking stays
        $query = BookingHotel::with(['booking.client', 'booking.company', 'booking.package', 'booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // Hotel Name Filter
        if (!empty($hotelFilter) && $hotelFilter !== 'all') {
            $query->where('hotel_name', 'LIKE', '%' . $hotelFilter . '%');
        }

        // Location Filter
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $query->whereRaw('LOWER(location) = ?', [$locationFilter]);
        }

        // Package Filter
        if (!empty($packageFilter) && $packageFilter !== 'all') {
            $query->whereHas('booking', function ($bq) use ($packageFilter) {
                $bq->where('package_id', $packageFilter);
            });
        }

        // Stay Date Filters
        if (!empty($fromDate)) {
            $query->whereDate('check_in', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('check_out', '<=', $toDate);
        }

        // Search Pilgrim / Passport / Voucher / Booking
        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('hotel_name', 'LIKE', $term)
                  ->orWhere('room_number', 'LIKE', $term)
                  ->orWhere('room_type', 'LIKE', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('voucher_number', 'LIKE', $term)
                         ->orWhere('passport_number', 'LIKE', $term)
                         ->orWhere('given_name', 'LIKE', $term)
                         ->orWhere('sur_name', 'LIKE', $term)
                         ->orWhereHas('client', function ($cq) use ($term) {
                             $cq->where('name', 'LIKE', $term)->orWhere('phone', 'LIKE', $term);
                         })
                         ->orWhereHas('persons', function ($pq) use ($term) {
                             $pq->where('full_name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term)
                                ->orWhere('hajj_id', 'LIKE', $term)
                                ->orWhere('hb_number', 'LIKE', $term);
                         });
                  });
            });
        }

        $hotelStays = $query->orderBy('location', 'asc')
                            ->orderBy('hotel_name', 'asc')
                            ->orderByRaw('COALESCE(check_in, "9999-12-31") ASC')
                            ->get();

        // Calculate Totals for Current Filter
        $totalStays = $hotelStays->count();
        $totalRooms = $hotelStays->sum(fn($s) => $s->no_of_rooms ?: 1);
        $totalPersons = $hotelStays->sum(function ($stay) {
            $b = $stay->booking;
            return $b ? ($b->persons->count() ?: ($b->no_of_pax ?: 1)) : 1;
        });

        // ══════════════════════════════════════════════════════════════════
        // Overall Global Aggregations (To Answer: "Anjum mai kitne person, Azizia mai kitne person")
        // ══════════════════════════════════════════════════════════════════
        $allActiveStays = BookingHotel::with(['booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            })
            ->get();

        $locationBreakdown = [];
        $hotelBreakdown    = [];

        foreach ($allActiveStays as $stay) {
            $hName = trim($stay->hotel_name ?: 'Hotel Not Specified');
            $loc   = strtolower(trim($stay->location ?: 'other'));
            $b     = $stay->booking;
            $pax   = $b ? ($b->persons->count() ?: ($b->no_of_pax ?: 1)) : 1;
            $rooms = $stay->no_of_rooms ?: 1;

            // Location tally
            if (!isset($locationBreakdown[$loc])) {
                $locationBreakdown[$loc] = ['persons' => 0, 'rooms' => 0, 'stays' => 0];
            }
            $locationBreakdown[$loc]['persons'] += $pax;
            $locationBreakdown[$loc]['rooms']   += $rooms;
            $locationBreakdown[$loc]['stays']   += 1;

            // Hotel tally
            if (!isset($hotelBreakdown[$hName])) {
                $hotelBreakdown[$hName] = ['persons' => 0, 'rooms' => 0, 'stays' => 0, 'location' => $loc];
            }
            $hotelBreakdown[$hName]['persons'] += $pax;
            $hotelBreakdown[$hName]['rooms']   += $rooms;
            $hotelBreakdown[$hName]['stays']   += 1;
        }

        // Sort hotel breakdown by most persons
        uasort($hotelBreakdown, fn($a, $b) => $b['persons'] <=> $a['persons']);

        // Registered and distinct dropdown options
        $registeredHotels   = Hotel::orderBy('name')->get();
        $distinctHotelNames = BookingHotel::select('hotel_name')->distinct()->whereNotNull('hotel_name')->where('hotel_name', '!=', '')->pluck('hotel_name')->toArray();
        $allHotelOptions    = array_values(array_unique(array_filter(array_merge($registeredHotels->pluck('name')->toArray(), $distinctHotelNames))));
        sort($allHotelOptions);

        $packages = Package::latest()->get();

        return compact(
            'hotelStays',
            'totalStays',
            'totalRooms',
            'totalPersons',
            'locationBreakdown',
            'hotelBreakdown',
            'allHotelOptions',
            'packages',
            'hotelFilter',
            'locationFilter',
            'search',
            'packageFilter',
            'fromDate',
            'toDate'
        );
    }
}
