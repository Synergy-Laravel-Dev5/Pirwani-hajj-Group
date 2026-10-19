<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FlightReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getFilteredFlightData($request);

        return view('reports.flight', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredFlightData($request);

        $pdf = Pdf::loadView('reports.flight_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $airlinePart = (!empty($data['airline']) && $data['airline'] !== 'all') ? '_' . strtoupper(str_replace(' ', '_', $data['airline'])) : '';
        $typePart    = (!empty($data['flightType']) && $data['flightType'] !== 'all') ? '_' . strtoupper($data['flightType']) : '';
        $fileName    = 'Official_Booking_Flight_Schedule' . $airlinePart . $typePart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data     = $this->getFilteredFlightData($request);
        $bookings = $data['bookings'];

        $airlinePart = (!empty($data['airline']) && $data['airline'] !== 'all') ? '_' . strtoupper(str_replace(' ', '_', $data['airline'])) : '';
        $typePart    = (!empty($data['flightType']) && $data['flightType'] !== 'all') ? '_' . strtoupper($data['flightType']) : '';
        $fileName    = 'Official_Booking_Flight_Schedule' . $airlinePart . $typePart . '_' . date('Ymd_His') . '.xlsx';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Airline: ' . strtoupper($data['airline'] ?: 'ALL') . ' | Flight Type: ' . strtoupper($data['flightType'] ?: 'ALL');

        // Check if PhpSpreadsheet is available
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $xlsFileName = str_replace('.xlsx', '.xls', $fileName);
            return response()->streamDownload(function () use ($bookings, $filterSummary) {
                echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;width:100%;} th,td{border:1px solid #000;padding:6px;font-family:Arial,sans-serif;font-size:11px;} th{background-color:#0f172a;color:#fff;font-weight:bold;text-align:center;} .text-center{text-align:center;} .fw-bold{font-weight:bold;}</style></head><body>';
                echo '<h2>PIRWANI HAJJ GROUP - BOOKING FLIGHT REPORT & SCHEDULE</h2>';
                echo '<p>' . htmlspecialchars($filterSummary) . '</p>';
                echo '<table><thead><tr>';
                echo '<th>SR</th><th>HAJJ ID</th><th>HB #</th><th>PASSENGER FULL NAME</th><th>GIVEN NAME</th><th>SURNAME</th><th>HUSBAND / FATHER NAME</th><th>GENDER</th><th>PASSPORT NO</th><th>DOI</th><th>EXPIRY</th><th>VOUCHER #</th><th>BOOKING #</th><th>DEP AIRLINE</th><th>DEP FLIGHT</th><th>DEP SECTOR</th><th>DEP DATE</th><th>DEP PNR</th><th>RET AIRLINE</th><th>RET FLIGHT</th><th>RET SECTOR</th><th>RET DATE</th><th>RET PNR</th><th>PACKAGE</th><th>MAKTAB</th><th>FLIGHT CHARGES</th><th>STATUS</th>';
                echo '</tr></thead><tbody>';

                $sr = 1;
                foreach ($bookings as $b) {
                    $persons = $b->persons;
                    if ($persons->isEmpty()) {
                        $persons = collect([(object)[
                            'full_name' => $b->client?->name ?? 'Passenger',
                            'given_name' => '', 'surname' => '', 'father_name' => '',
                            'hajj_id' => '', 'hb_number' => '', 'gender' => '',
                            'passport_number' => $b->passport_number ?? '',
                            'date_of_issue' => '', 'passport_expiry_date' => '',
                        ]]);
                    }

                    $depDate = $b->departure_date ? date('d/m/Y', strtotime($b->departure_date)) : '—';
                    $arrDate = $b->arrival_date ? date('d/m/Y', strtotime($b->arrival_date)) : '—';
                    $maktabStr = $b->camp ?? ($b->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');

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
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b->voucher_number ?? '') . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars($b->booking_number ?? $b->id) . '</td>';
                        echo '<td>' . htmlspecialchars($b->departure_airline ?? '—') . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b->departure_flight ?? '—') . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars(($b->departure_from ?? '') . ($b->departure_to ? ' -> ' . $b->departure_to : '')) . '</td>';
                        echo '<td class="text-center">' . $depDate . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b->departure_pnr ?? '—') . '</td>';
                        echo '<td>' . htmlspecialchars($b->arrival_airline ?? '—') . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b->arrival_flight ?? '—') . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars(($b->arrival_from ?? '') . ($b->arrival_to ? ' -> ' . $b->arrival_to : '')) . '</td>';
                        echo '<td class="text-center">' . $arrDate . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b->arrival_pnr ?? '—') . '</td>';
                        echo '<td>' . htmlspecialchars($b->package->package_title ?? ($b->package->name ?? '')) . '</td>';
                        echo '<td class="text-center">' . htmlspecialchars($maktabStr) . '</td>';
                        echo '<td class="text-center">' . number_format($b->flight_charges ?? 0) . '</td>';
                        echo '<td class="text-center">Scheduled</td>';
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
        $sheet->setTitle('Flight Schedule');

        // Main Title
        $sheet->mergeCells('A1:AA1');
        $sheet->setCellValue('A1', 'PIRWANI HAJJ GROUP - BOOKING FLIGHT REPORT & SCHEDULE');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Subtitle
        $sheet->mergeCells('A2:AA2');
        $sheet->setCellValue('A2', $filterSummary);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = [
            'SR', 'HAJJ ID', 'HB #', 'PASSENGER FULL NAME', 'GIVEN NAME', 'SURNAME', 'HUSBAND / FATHER NAME',
            'GENDER', 'PASSPORT NO', 'DOI', 'EXPIRY', 'VOUCHER #', 'BOOKING #', 'DEP AIRLINE', 'DEP FLIGHT',
            'DEP SECTOR', 'DEP DATE', 'DEP PNR', 'RET AIRLINE', 'RET FLIGHT', 'RET SECTOR', 'RET DATE', 'RET PNR',
            'PACKAGE', 'MAKTAB', 'FLIGHT CHARGES', 'STATUS'
        ];

        $col = 1;
        foreach ($headers as $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '4';
            $sheet->setCellValue($cell, $header);
            $col++;
        }

        $headerRange = 'A4:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '4';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '071527']],
        ]);

        $rowIdx = 5;
        $sr = 1;
        foreach ($bookings as $b) {
            $persons = $b->persons;
            if ($persons->isEmpty()) {
                $persons = collect([(object)[
                    'full_name' => $b->client?->name ?? 'Passenger',
                    'given_name' => '', 'surname' => '', 'father_name' => '',
                    'hajj_id' => '', 'hb_number' => '', 'gender' => '',
                    'passport_number' => $b->passport_number ?? '',
                    'date_of_issue' => '', 'passport_expiry_date' => '',
                ]]);
            }

            $depDate = $b->departure_date ? date('d/m/Y', strtotime($b->departure_date)) : '—';
            $arrDate = $b->arrival_date ? date('d/m/Y', strtotime($b->arrival_date)) : '—';
            $maktabStr = $b->camp ?? ($b->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');

            foreach ($persons as $p) {
                $sheet->setCellValue('A' . $rowIdx, $sr++);
                $sheet->setCellValue('B' . $rowIdx, $p->hajj_id ?? '');
                $sheet->setCellValue('C' . $rowIdx, $p->hb_number ?? '');
                $sheet->setCellValue('D' . $rowIdx, $p->full_name ?? '');
                $sheet->setCellValue('E' . $rowIdx, $p->given_name ?? '');
                $sheet->setCellValue('F' . $rowIdx, $p->surname ?? '');
                $sheet->setCellValue('G' . $rowIdx, $p->father_name ?? ($p->husband_or_father_name ?? ''));
                $sheet->setCellValue('H' . $rowIdx, strtoupper($p->gender ?? ''));
                $sheet->setCellValue('I' . $rowIdx, $p->passport_number ?? '');
                $sheet->setCellValue('J' . $rowIdx, !empty($p->date_of_issue) ? date('d/m/Y', strtotime($p->date_of_issue)) : '');
                $sheet->setCellValue('K' . $rowIdx, !empty($p->passport_expiry_date) ? date('d/m/Y', strtotime($p->passport_expiry_date)) : '');
                $sheet->setCellValue('L' . $rowIdx, $b->voucher_number ?? '');
                $sheet->setCellValue('M' . $rowIdx, $b->booking_number ?? $b->id);
                $sheet->setCellValue('N' . $rowIdx, $b->departure_airline ?? '—');
                $sheet->setCellValue('O' . $rowIdx, $b->departure_flight ?? '—');
                $sheet->setCellValue('P' . $rowIdx, ($b->departure_from ?? '') . ($b->departure_to ? ' -> ' . $b->departure_to : ''));
                $sheet->setCellValue('Q' . $rowIdx, $depDate);
                $sheet->setCellValue('R' . $rowIdx, $b->departure_pnr ?? '—');
                $sheet->setCellValue('S' . $rowIdx, $b->arrival_airline ?? '—');
                $sheet->setCellValue('T' . $rowIdx, $b->arrival_flight ?? '—');
                $sheet->setCellValue('U' . $rowIdx, ($b->arrival_from ?? '') . ($b->arrival_to ? ' -> ' . $b->arrival_to : ''));
                $sheet->setCellValue('V' . $rowIdx, $arrDate);
                $sheet->setCellValue('W' . $rowIdx, $b->arrival_pnr ?? '—');
                $sheet->setCellValue('X' . $rowIdx, $b->package->package_title ?? ($b->package->name ?? ''));
                $sheet->setCellValue('Y' . $rowIdx, $maktabStr);
                $sheet->setCellValue('Z' . $rowIdx, $b->flight_charges ?? 0);
                $sheet->setCellValue('AA' . $rowIdx, 'Scheduled');

                $rowIdx++;
            }
        }

        $lastRow = $rowIdx - 1;
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:AA' . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A5:C' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H5:K' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L5:M' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O5:R' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('T5:W' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Z5:AA' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        foreach (range(1, count($headers)) as $cIdx) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx))->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function getFilteredFlightData(Request $request): array
    {
        $search     = $request->get('search');
        $airline    = $request->get('airline', 'all');
        $flightType = $request->get('flight_type', 'all');
        $fromDate   = $request->get('from_date');
        $toDate     = $request->get('to_date');
        $packageId  = $request->get('package_id');

        $query = Booking::with(['client', 'company', 'package', 'persons'])
            ->whereNull('deleted_at')
            ->whereNotIn('status', ['cancelled', 'rejected']);

        $today = Carbon::today()->format('Y-m-d');

        // Search Filter
        if (!empty($search)) {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('voucher_number', 'LIKE', $term)
                  ->orWhere('departure_flight', 'LIKE', $term)
                  ->orWhere('arrival_flight', 'LIKE', $term)
                  ->orWhere('departure_airline', 'LIKE', $term)
                  ->orWhere('arrival_airline', 'LIKE', $term)
                  ->orWhere('departure_pnr', 'LIKE', $term)
                  ->orWhere('arrival_pnr', 'LIKE', $term)
                  ->orWhere('given_name', 'LIKE', $term)
                  ->orWhere('sur_name', 'LIKE', $term)
                  ->orWhere('passport_number', 'LIKE', $term)
                  ->orWhereHas('client', function ($cq) use ($term) {
                      $cq->where('name', 'LIKE', $term)->orWhere('phone', 'LIKE', $term);
                  })
                  ->orWhereHas('company', function ($comQ) use ($term) {
                      $comQ->where('company_name', 'LIKE', $term);
                  })
                  ->orWhereHas('persons', function ($pq) use ($term) {
                      $pq->where('full_name', 'LIKE', $term)
                         ->orWhere('surname', 'LIKE', $term)
                         ->orWhere('given_name', 'LIKE', $term)
                         ->orWhere('father_name', 'LIKE', $term)
                         ->orWhere('passport_number', 'LIKE', $term)
                         ->orWhere('hajj_id', 'LIKE', $term)
                         ->orWhere('hb_number', 'LIKE', $term);
                  });
            });
        }

        // Airline Filter
        if (!empty($airline) && $airline !== 'all') {
            $query->where(function ($q) use ($airline) {
                $q->where('departure_airline', 'LIKE', '%' . $airline . '%')
                  ->orWhere('arrival_airline', 'LIKE', '%' . $airline . '%');
            });
        }

        // Flight Type / Direction Filter
        if ($flightType === 'with_flight') {
            $query->where(function ($q) {
                $q->whereNotNull('departure_flight')->where('departure_flight', '!=', '')
                  ->orWhereNotNull('departure_pnr')->where('departure_pnr', '!=', '')
                  ->orWhereNotNull('arrival_flight')->where('arrival_flight', '!=', '');
            });
        } elseif ($flightType === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('departure_flight')->orWhere('departure_flight', '')
                  ->orWhereNull('departure_pnr')->orWhere('departure_pnr', '');
            });
        } elseif ($flightType === 'upcoming_departure') {
            $query->whereDate('departure_date', '>=', $today);
        } elseif ($flightType === 'upcoming_arrival') {
            $query->whereDate('arrival_date', '>=', $today);
        }

        // Date Range (filters departure or arrival dates)
        if (!empty($fromDate)) {
            $query->where(function ($q) use ($fromDate) {
                $q->whereDate('departure_date', '>=', $fromDate)
                  ->orWhereDate('arrival_date', '>=', $fromDate);
            });
        }
        if (!empty($toDate)) {
            $query->where(function ($q) use ($toDate) {
                $q->whereDate('departure_date', '<=', $toDate)
                  ->orWhereDate('arrival_date', '<=', $toDate);
            });
        }

        // Package Filter
        if (!empty($packageId) && $packageId !== 'all') {
            $query->where('package_id', $packageId);
        }

        // Order by departure date or booking ID
        $bookings = $query->orderByRaw('COALESCE(departure_date, "9999-12-31") ASC')
                          ->orderBy('id', 'desc')
                          ->get();

        // KPI Calculations
        $totalBookings       = $bookings->count();
        $totalPassengers     = $bookings->sum(function ($b) {
            return $b->no_of_pax ?: ($b->persons->count() ?: 1);
        });
        $departuresScheduled = $bookings->filter(fn($b) => !empty($b->departure_date))->count();
        $returnsScheduled    = $bookings->filter(fn($b) => !empty($b->arrival_date))->count();
        $pnrsIssued          = $bookings->filter(fn($b) => !empty($b->departure_pnr) || !empty($b->arrival_pnr))->count();
        $totalFlightCharges  = $bookings->sum('flight_charges');

        // Dropdown Data
        $packages = Package::latest()->get();
        $registeredAirlines = Airline::orderBy('name')->pluck('name')->toArray();
        $bookingAirlines = Booking::whereNotNull('departure_airline')->where('departure_airline', '!=', '')->pluck('departure_airline')
            ->merge(Booking::whereNotNull('arrival_airline')->where('arrival_airline', '!=', '')->pluck('arrival_airline'))
            ->unique()->toArray();
        $allAirlines = array_values(array_unique(array_filter(array_merge($registeredAirlines, $bookingAirlines))));

        return compact(
            'bookings',
            'totalBookings',
            'totalPassengers',
            'departuresScheduled',
            'returnsScheduled',
            'pnrsIssued',
            'totalFlightCharges',
            'packages',
            'allAirlines',
            'search',
            'airline',
            'flightType',
            'fromDate',
            'toDate',
            'packageId'
        );
    }
}
