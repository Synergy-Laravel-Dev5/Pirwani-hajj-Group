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

class CheckInReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getFilteredCheckInData($request);

        return view('reports.check_in', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredCheckInData($request);

        $pdf = Pdf::loadView('reports.check_in_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $hotelPart    = !empty($data['hotelFilter']) ? '_' . strtoupper(str_replace(' ', '_', $data['hotelFilter'])) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName     = 'Official_Hotel_Check_In_Manifest' . $hotelPart . $locationPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data       = $this->getFilteredCheckInData($request);
        $hotelStays = $data['hotelStays'];

        $hotelPart    = !empty($data['hotelFilter']) ? '_' . strtoupper(str_replace(' ', '_', $data['hotelFilter'])) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName     = 'Official_Hotel_Check_In_Manifest' . $hotelPart . $locationPart . '_' . date('Ymd_His') . '.xlsx';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Hotel: ' . ($data['hotelFilter'] ?: 'ALL') . ' | Location: ' . strtoupper($data['locationFilter'] ?: 'ALL');

        // Check if PhpSpreadsheet is available
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $xlsFileName = str_replace('.xlsx', '.xls', $fileName);
            return response()->streamDownload(function () use ($hotelStays, $filterSummary) {
                echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                echo '<head><meta charset="UTF-8"><style>table{border-collapse:collapse;width:100%;} th,td{border:1px solid #000;padding:6px;font-family:Arial,sans-serif;font-size:11px;} th{background-color:#1e293b;color:#fff;font-weight:bold;text-align:center;} .text-center{text-align:center;} .fw-bold{font-weight:bold;}</style></head><body>';
                echo '<h2>PIRWANI HAJJ GROUP - HOTEL CHECK-IN REPORT & MANIFEST</h2>';
                echo '<p>' . htmlspecialchars($filterSummary) . '</p>';
                echo '<table><thead><tr>';
                echo '<th>SR</th><th>HAJJ ID</th><th>HB #</th><th>PILGRIM FULL NAME</th><th>GIVEN NAME</th><th>SURNAME</th><th>HUSBAND / FATHER NAME</th><th>GENDER</th><th>PASSPORT NO</th><th>DOI</th><th>EXPIRY</th><th>HOTEL NAME</th><th>LOCATION</th><th>ROOM DETAILS</th><th>ROOM NO</th><th>CHECK-IN</th><th>CHECK-OUT</th><th>NIGHTS</th><th>MAKTAB / CAMP</th><th>PACKAGE</th><th>VOUCHER #</th><th>CLIENT / GROUP</th><th>STATUS</th>';
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

                    $checkInStr = $stay->check_in ? date('d/m/Y', strtotime($stay->check_in)) : '—';
                    $checkOutStr = $stay->check_out ? date('d/m/Y', strtotime($stay->check_out)) : '—';
                    $nights = ($stay->check_in && $stay->check_out) ? \Carbon\Carbon::parse($stay->check_in)->diffInDays(\Carbon\Carbon::parse($stay->check_out)) : '—';

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
                        echo '<td class="text-center">' . htmlspecialchars($b->camp ?? ($b->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '')) . '</td>';
                        echo '<td>' . htmlspecialchars($b->package->package_title ?? ($b->package->name ?? '')) . '</td>';
                        echo '<td class="text-center fw-bold">' . htmlspecialchars($b->voucher_number ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($b->client->name ?? ($b->company->company_name ?? '')) . '</td>';
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
        $sheet->setTitle('Check-In Manifest');

        // Main Title
        $sheet->mergeCells('A1:W1');
        $sheet->setCellValue('A1', 'PIRWANI HAJJ GROUP - HOTEL CHECK-IN REPORT & MANIFEST');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKBLUE));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Subtitle
        $sheet->mergeCells('A2:W2');
        $sheet->setCellValue('A2', $filterSummary);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = [
            'SR', 'HAJJ ID', 'HB #', 'PILGRIM FULL NAME', 'GIVEN NAME', 'SURNAME', 'HUSBAND / FATHER NAME',
            'GENDER', 'PASSPORT NO', 'DOI', 'EXPIRY', 'HOTEL NAME', 'LOCATION', 'ROOM DETAILS', 'ROOM NO',
            'CHECK-IN', 'CHECK-OUT', 'NIGHTS', 'MAKTAB / CAMP', 'PACKAGE', 'VOUCHER #', 'CLIENT / GROUP', 'STATUS'
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

            $checkInStr = $stay->check_in ? date('d/m/Y', strtotime($stay->check_in)) : '—';
            $checkOutStr = $stay->check_out ? date('d/m/Y', strtotime($stay->check_out)) : '—';
            $nights = ($stay->check_in && $stay->check_out) ? \Carbon\Carbon::parse($stay->check_in)->diffInDays(\Carbon\Carbon::parse($stay->check_out)) : '—';

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
                $sheet->setCellValue('L' . $rowIdx, $stay->hotel_name ?? '');
                $sheet->setCellValue('M' . $rowIdx, strtoupper($stay->location ?? ''));
                $sheet->setCellValue('N' . $rowIdx, strtoupper($stay->room_type ?? ''));
                $sheet->setCellValue('O' . $rowIdx, $stay->room_number ?? '—');
                $sheet->setCellValue('P' . $rowIdx, $checkInStr);
                $sheet->setCellValue('Q' . $rowIdx, $checkOutStr);
                $sheet->setCellValue('R' . $rowIdx, $nights);
                $sheet->setCellValue('S' . $rowIdx, $b->camp ?? ($b->package?->camp_category ? 'Maktab ' . $b->package->camp_category : ''));
                $sheet->setCellValue('T' . $rowIdx, $b->package->package_title ?? ($b->package->name ?? ''));
                $sheet->setCellValue('U' . $rowIdx, $b->voucher_number ?? '');
                $sheet->setCellValue('V' . $rowIdx, $b->client->name ?? ($b->company->company_name ?? ''));
                $sheet->setCellValue('W' . $rowIdx, 'Active');

                $rowIdx++;
            }
        }

        $lastRow = $rowIdx - 1;
        if ($lastRow >= 5) {
            $sheet->getStyle('A5:W' . $lastRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B5:C' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H5:K' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('M5:R' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('U5:U' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('W5:W' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
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

    private function getFilteredCheckInData(Request $request): array
    {
        $rawHotel = $request->get('hotel_name');
        $hotelFilter = [];
        if (is_array($rawHotel)) {
            $hotelFilter = array_values(array_filter(array_map('trim', $rawHotel)));
        } elseif (!empty($rawHotel) && $rawHotel !== 'all') {
            $hotelFilter = [trim($rawHotel)];
        }

        $locationFilter = $request->get('location');
        $aziziaPhase    = $request->get('azizia_phase', 'both'); // 'both', 'first', 'second'

        // Map location shortcuts
        if ($locationFilter === 'azizia_first') {
            $locationFilter = 'azizia';
            $aziziaPhase = 'first';
        } elseif ($locationFilter === 'azizia_second') {
            $locationFilter = 'azizia';
            $aziziaPhase = 'second';
        }

        $viewMode = $request->get('view_mode', ($locationFilter === 'all' || empty($locationFilter)) ? 'consolidated' : 'stays');

        $rawPkg = $request->get('package_id');
        $packageFilter = [];
        if (is_array($rawPkg)) {
            $packageFilter = array_values(array_filter(array_map('trim', $rawPkg)));
        } elseif (!empty($rawPkg) && $rawPkg !== 'all') {
            $packageFilter = [trim($rawPkg)];
        }

        $maktabFilter   = $request->get('maktab');
        $search         = $request->get('search');
        $quickFilter    = $request->get('quick_filter', 'all');
        $fromDate       = $request->get('from_date');
        $toDate         = $request->get('to_date');

        // Apply quick filter presets if from_date/to_date not manually set
        $today = Carbon::today()->format('Y-m-d');
        if ($quickFilter === 'today' && empty($fromDate) && empty($toDate)) {
            $fromDate = $today;
            $toDate   = $today;
        } elseif ($quickFilter === 'tomorrow' && empty($fromDate) && empty($toDate)) {
            $fromDate = Carbon::tomorrow()->format('Y-m-d');
            $toDate   = Carbon::tomorrow()->format('Y-m-d');
        } elseif ($quickFilter === 'this_week' && empty($fromDate) && empty($toDate)) {
            $fromDate = $today;
            $toDate   = Carbon::today()->addDays(7)->format('Y-m-d');
        } elseif ($quickFilter === 'this_month' && empty($fromDate) && empty($toDate)) {
            $fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $toDate   = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($quickFilter === 'upcoming' && empty($fromDate) && empty($toDate)) {
            $fromDate = $today;
        }

        // Query BookingHotel records with active bookings
        $query = BookingHotel::with(['booking.client', 'booking.company', 'booking.package', 'booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // Hotel name filter (Multi-Select or Single)
        if (!empty($hotelFilter)) {
            $query->where(function ($q) use ($hotelFilter) {
                foreach ($hotelFilter as $hf) {
                    $q->orWhere('hotel_name', 'LIKE', '%' . $hf . '%');
                }
            });
        }

        // Location filter
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $query->where('location', $locationFilter);
        }

        // Check-in date range
        if (!empty($fromDate)) {
            $query->whereDate('check_in', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('check_in', '<=', $toDate);
        }

        // Package filter (Multi-Select or Single)
        if (!empty($packageFilter)) {
            $query->whereHas('booking', function ($q) use ($packageFilter) {
                $q->whereIn('package_id', $packageFilter);
            });
        }

        // Maktab / Camp filter (Maktab A, Maktab C, Combo)
        if (!empty($maktabFilter) && $maktabFilter !== 'all') {
            $query->whereHas('booking', function ($bq) use ($maktabFilter) {
                if ($maktabFilter === 'maktab_a') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Maktab A%')
                            ->orWhere('camp', 'LIKE', '%Zone 1%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%A%')
                                   ->orWhere('maktab', 'LIKE', '%A%');
                            });
                    });
                } elseif ($maktabFilter === 'maktab_c') {
                    $bq->where(function ($sub) {
                        $sub->where('camp', 'LIKE', '%Maktab C%')
                            ->orWhere('camp', 'LIKE', '%Zone 5%')
                            ->orWhereHas('package', function ($pq) {
                                $pq->where('camp_category', 'LIKE', '%C%')
                                   ->orWhere('maktab', 'LIKE', '%C%');
                            });
                    });
                } elseif ($maktabFilter === 'combo') {
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
                    $term = '%' . $maktabFilter . '%';
                    $bq->where('camp', 'LIKE', $term)
                       ->orWhereHas('package', function ($pq) use ($term) {
                           $pq->where('camp_category', 'LIKE', $term)
                              ->orWhere('maktab', 'LIKE', $term);
                       });
                }
            });
        }

        // Search filter (pilgrim name, passport, voucher, client name, haji_id, hb_number, phone, cnic)
        if (!empty($search)) {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('hotel_name', 'LIKE', $term)
                  ->orWhereHas('booking', function ($bq) use ($term) {
                      $bq->where('voucher_number', 'LIKE', $term)
                         ->orWhere('care_of', 'LIKE', $term)
                         ->orWhere('camp', 'LIKE', $term)
                         ->orWhere('given_name', 'LIKE', $term)
                         ->orWhere('sur_name', 'LIKE', $term)
                         ->orWhere('passport_number', 'LIKE', $term)
                         ->orWhere('cnic', 'LIKE', $term)
                         ->orWhere('phone', 'LIKE', $term)
                         ->orWhereHas('client', function ($cq) use ($term) {
                             $cq->where('name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term)
                                ->orWhere('phone', 'LIKE', $term);
                         })
                         ->orWhereHas('company', function ($comQ) use ($term) {
                             $comQ->where('company_name', 'LIKE', $term)
                                  ->orWhere('company_code', 'LIKE', $term);
                         })
                         ->orWhereHas('persons', function ($pq) use ($term) {
                             $pq->where('full_name', 'LIKE', $term)
                                ->orWhere('surname', 'LIKE', $term)
                                ->orWhere('given_name', 'LIKE', $term)
                                ->orWhere('passport_number', 'LIKE', $term)
                                ->orWhere('cnic', 'LIKE', $term)
                                ->orWhere('phone', 'LIKE', $term)
                                ->orWhere('hajj_id', 'LIKE', $term)
                                ->orWhere('hb_number', 'LIKE', $term);
                         });
                  });
            });
        }

        // Order by check_in date ascending
        $hotelStays = $query->orderBy('check_in', 'asc')->get();

        // ══════════════════════════════════════════════════════════════════
        // Azizia Check-In Phase Filtering (1st vs 2nd vs Both Check-Ins)
        // ══════════════════════════════════════════════════════════════════
        if ($locationFilter === 'azizia' && in_array($aziziaPhase, ['first', 'second'])) {
            $filteredStays = collect();
            $groupedByBooking = $hotelStays->groupBy('booking_id');
            foreach ($groupedByBooking as $bId => $bStays) {
                $sortedStays = $bStays->values();
                if ($aziziaPhase === 'first' && isset($sortedStays[0])) {
                    $filteredStays->push($sortedStays[0]);
                } elseif ($aziziaPhase === 'second' && isset($sortedStays[1])) {
                    $filteredStays->push($sortedStays[1]);
                }
            }
            $hotelStays = $filteredStays->sortBy('check_in')->values();
        }

        // Tag every Azizia stay with its phase label (1st Check-In vs 2nd Check-In)
        $allAziziaBookingGroups = BookingHotel::where('location', 'azizia')
            ->whereIn('booking_id', $hotelStays->pluck('booking_id')->unique())
            ->orderBy('check_in', 'asc')
            ->get()
            ->groupBy('booking_id');

        foreach ($hotelStays as $stay) {
            if (strtolower($stay->location) === 'azizia') {
                $bAzStays = $allAziziaBookingGroups->get($stay->booking_id, collect());
                $idx = $bAzStays->search(fn($s) => $s->id == $stay->id);
                if ($idx === 0) {
                    $stay->azizia_phase_label = '1st Check-In (Pre-Hajj)';
                    $stay->azizia_phase_badge = 'bg-warning text-dark';
                } elseif ($idx === 1) {
                    $stay->azizia_phase_label = '2nd Check-In (Post-Hajj)';
                    $stay->azizia_phase_badge = 'bg-info text-dark';
                } else {
                    $stay->azizia_phase_label = 'Azizia Check-In';
                    $stay->azizia_phase_badge = 'bg-secondary text-white';
                }
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // Consolidated Pilgrim View (1 Row Per Haji - All Locations in One)
        // ══════════════════════════════════════════════════════════════════
        $matchingBookingIds = $hotelStays->pluck('booking_id')->unique();
        $consolidatedPilgrims = collect();

        if ($matchingBookingIds->isNotEmpty()) {
            $bookingsForConsolidated = Booking::with([
                'client',
                'company',
                'package',
                'persons',
                'hotels' => function ($hq) {
                    $hq->orderBy('check_in', 'asc');
                }
            ])
            ->whereIn('id', $matchingBookingIds)
            ->get();

            foreach ($bookingsForConsolidated as $b) {
                $allHotels = $b->hotels;
                $makkahStay = $allHotels->first(fn($h) => strtolower($h->location) === 'makkah');
                $madinahStay = $allHotels->first(fn($h) => strtolower($h->location) === 'madinah');
                $minaStay = $allHotels->first(fn($h) => strtolower($h->location) === 'mina');
                $azStays = $allHotels->filter(fn($h) => strtolower($h->location) === 'azizia')->sortBy('check_in')->values();
                $azizia1Stay = $azStays->get(0);
                $azizia2Stay = $azStays->get(1);

                $persons = $b->persons;
                if ($persons->isEmpty()) {
                    $consolidatedPilgrims->push((object)[
                        'is_dummy'      => true,
                        'person'        => null,
                        'booking'       => $b,
                        'client_name'   => $b->client?->name ?? ($b->company?->company_name ?? 'Direct Pilgrim'),
                        'hajj_id'       => '—',
                        'hb_number'     => '—',
                        'gender'        => '—',
                        'passport'      => $b->passport_number ?: '—',
                        'makkah'        => $makkahStay,
                        'azizia1'       => $azizia1Stay,
                        'mina'          => $minaStay,
                        'azizia2'       => $azizia2Stay,
                        'madinah'       => $madinahStay,
                    ]);
                } else {
                    foreach ($persons as $person) {
                        $consolidatedPilgrims->push((object)[
                            'is_dummy'      => false,
                            'person'        => $person,
                            'booking'       => $b,
                            'client_name'   => $person->full_name,
                            'hajj_id'       => $person->hajj_id ?: '—',
                            'hb_number'     => $person->hb_number ?: '—',
                            'gender'        => $person->gender ?: '—',
                            'passport'      => $person->passport_number ?: '—',
                            'makkah'        => $makkahStay,
                            'azizia1'       => $azizia1Stay,
                            'mina'          => $minaStay,
                            'azizia2'       => $azizia2Stay,
                            'madinah'       => $madinahStay,
                        ]);
                    }
                }
            }
        }

        // Calculate KPI Statistics
        $totalStays = $hotelStays->count();
        $totalRooms = $hotelStays->sum('no_of_rooms');
        
        $allBookingIds = $hotelStays->pluck('booking_id')->unique();
        $totalPilgrims = BookingPerson::whereIn('booking_id', $allBookingIds)->count();
        if ($totalPilgrims === 0 && $totalStays > 0) {
            $totalPilgrims = Booking::whereIn('id', $allBookingIds)->sum('no_of_pax');
        }

        $todayCheckins = BookingHotel::whereDate('check_in', $today)->count();
        $upcoming7Days = BookingHotel::whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', Carbon::today()->addDays(7)->format('Y-m-d'))
            ->count();
        $activeInHouse = BookingHotel::whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->count();

        // Dropdown Data
        $registeredHotels = Hotel::orderBy('name')->get();
        $distinctHotelNames = BookingHotel::select('hotel_name')->distinct()->whereNotNull('hotel_name')->pluck('hotel_name');
        $allHotelOptions = $registeredHotels->pluck('name')->merge($distinctHotelNames)->unique()->filter()->sort()->values();
        $packages = Package::latest()->get();

        return compact(
            'hotelStays',
            'consolidatedPilgrims',
            'totalStays',
            'totalRooms',
            'totalPilgrims',
            'todayCheckins',
            'upcoming7Days',
            'activeInHouse',
            'registeredHotels',
            'distinctHotelNames',
            'allHotelOptions',
            'packages',
            'hotelFilter',
            'locationFilter',
            'aziziaPhase',
            'viewMode',
            'packageFilter',
            'maktabFilter',
            'search',
            'quickFilter',
            'fromDate',
            'toDate'
        );
    }
}
