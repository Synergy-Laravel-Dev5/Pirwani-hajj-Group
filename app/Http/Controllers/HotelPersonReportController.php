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

        $pdf = Pdf::loadView('reports.hotel_person_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $hotelString  = is_array($data['hotelFilter'] ?? null) ? implode('_', $data['hotelFilter']) : ($data['hotelFilter'] ?? '');
        $hotelPart    = !empty($hotelString) ? '_' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '_', $hotelString)) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $phasePart    = ($data['locationFilter'] === 'azizia' && in_array($data['aziziaPhase'] ?? '', ['first', 'second'])) ? '_' . strtoupper($data['aziziaPhase']) . '_PHASE' : '';
        $fileName     = 'Official_Hotel_Person_Report' . $hotelPart . $locationPart . $phasePart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data       = $this->getFilteredData($request);
        $hotelStays = $data['hotelStays'];

        $hotelString   = is_array($data['hotelFilter'] ?? null) ? implode('_', $data['hotelFilter']) : ($data['hotelFilter'] ?? '');
        $hotelPart     = !empty($hotelString) ? '_' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '_', $hotelString)) : '';
        $locationPart  = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $phasePart     = ($data['locationFilter'] === 'azizia' && in_array($data['aziziaPhase'] ?? '', ['first', 'second'])) ? '_' . strtoupper($data['aziziaPhase']) . '_PHASE' : '';
        $fileName      = 'Official_Hotel_Person_Report' . $hotelPart . $locationPart . $phasePart . '_' . date('Ymd_His') . '.xlsx';
        
        $hotelLabel    = is_array($data['hotelFilter'] ?? null) ? (empty($data['hotelFilter']) ? 'ALL' : implode(', ', $data['hotelFilter'])) : ($data['hotelFilter'] ?: 'ALL');
        $phaseLabel    = ($data['locationFilter'] === 'azizia') ? ' (Azizia Phase: ' . strtoupper($data['aziziaPhase'] ?? 'BOTH') . ')' : '';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Hotel: ' . $hotelLabel . ' | Location: ' . strtoupper($data['locationFilter'] ?: 'ALL') . $phaseLabel;

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
                    $displayHotel = $stay->clean_hotel_name ?? ($stay->hotel_name ?? '');

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
                        echo '<td class="fw-bold">' . htmlspecialchars($displayHotel) . '</td>';
                        echo '<td class="text-center">' . strtoupper($stay->clean_location ?? ($stay->location ?? '')) . '</td>';
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
            $displayHotel = $stay->clean_hotel_name ?? ($stay->hotel_name ?? '');

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
                $sheet->setCellValue('L' . $rowNum, $displayHotel);
                $sheet->setCellValue('M' . $rowNum, strtoupper($stay->clean_location ?? ($stay->location ?? '')));
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
        $rawHotel = $request->get('hotel_name');
        $hotelFilter = [];
        if (is_array($rawHotel)) {
            $hotelFilter = array_values(array_filter(array_map('trim', $rawHotel)));
        } elseif (!empty($rawHotel) && $rawHotel !== 'all') {
            $hotelFilter = [trim($rawHotel)];
        }

        $locationFilter = strtolower(trim($request->get('location', 'all')));
        $aziziaPhase    = $request->get('azizia_phase', 'both');
        if ($locationFilter === 'azizia_first') {
            $locationFilter = 'azizia';
            $aziziaPhase = 'first';
        } elseif ($locationFilter === 'azizia_second') {
            $locationFilter = 'azizia';
            $aziziaPhase = 'second';
        }

        $search         = trim($request->get('search', ''));

        $rawPkg = $request->get('package_id');
        $packageFilter = [];
        if (is_array($rawPkg)) {
            $packageFilter = array_values(array_filter(array_map('trim', $rawPkg)));
        } elseif (!empty($rawPkg) && $rawPkg !== 'all') {
            $packageFilter = [trim($rawPkg)];
        }

        $fromDate       = $request->get('from_date');
        $toDate         = $request->get('to_date');

        // Query active booking stays
        $query = BookingHotel::with(['booking.client', 'booking.company', 'booking.package', 'booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // Hotel Name Filter (Multi-Select or Single)
        if (!empty($hotelFilter)) {
            $query->where(function ($q) use ($hotelFilter) {
                foreach ($hotelFilter as $hf) {
                    $q->orWhere('hotel_name', 'LIKE', '%' . $hf . '%');
                }
            });
        }

        // Canonical Location Filter
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            if ($locationFilter === 'azizia') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(location) = ?', ['azizia'])
                      ->orWhere('hotel_name', 'LIKE', '%AZIZ%')
                      ->orWhere('hotel_name', 'LIKE', '%BUILD%');
                });
            } elseif ($locationFilter === 'mina') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(location) = ?', ['mina'])
                      ->orWhere('hotel_name', 'LIKE', '%MINA%')
                      ->orWhere('hotel_name', 'LIKE', '%CAMP%');
                });
            } elseif ($locationFilter === 'madinah') {
                $query->where(function ($q) {
                    $q->whereRaw('LOWER(location) = ?', ['madinah'])
                      ->orWhere('hotel_name', 'LIKE', '%NUSK%')
                      ->orWhere('hotel_name', 'LIKE', '%MADIN%');
                });
            } elseif ($locationFilter === 'makkah') {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereRaw('LOWER(location) = ?', ['makkah'])
                            ->orWhere('hotel_name', 'LIKE', '%ANJUM%')
                            ->orWhere('hotel_name', 'LIKE', '%PULLMAN%')
                            ->orWhere('hotel_name', 'LIKE', '%ZAMZAM%')
                            ->orWhere('hotel_name', 'LIKE', '%SWISS%');
                    })->where('hotel_name', 'NOT LIKE', '%AZIZ%')
                      ->where('hotel_name', 'NOT LIKE', '%BUILD%');
                });
            } else {
                $query->whereRaw('LOWER(location) = ?', [$locationFilter]);
            }
        }

        // Package Filter (Multi-Select or Single)
        if (!empty($packageFilter)) {
            $query->whereHas('booking', function ($bq) use ($packageFilter) {
                $bq->whereIn('package_id', $packageFilter);
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

        $hotelStays = $query->orderBy('booking_id', 'asc')
                            ->orderByRaw('COALESCE(check_in, "9999-12-31") ASC')
                            ->orderBy('id', 'asc')
                            ->get();

        // Attach clean location and clean hotel name to each stay
        // Also tag Azizia stays with phase index (1st check-in vs 2nd check-in)
        $bookingAziziaStays = [];
        foreach ($hotelStays as $stay) {
            $rawLoc = strtolower(trim($stay->location ?: 'other'));
            $hName  = trim($stay->hotel_name ?: '');

            if ($rawLoc === 'azizia' || preg_match('/(aziz|build)/i', $hName)) {
                $stay->clean_location = 'azizia';
                $stay->clean_hotel_name = 'Azizia Building';
                $bId = $stay->booking_id;
                if (!isset($bookingAziziaStays[$bId])) {
                    $bookingAziziaStays[$bId] = [];
                }
                $stay->azizia_stay_index = count($bookingAziziaStays[$bId]) + 1;
                $stay->azizia_phase_name = ($stay->azizia_stay_index === 1) ? '1st Check-In (Pre-Hajj)' : '2nd Check-In (Post-Hajj)';
                $bookingAziziaStays[$bId][] = $stay;
            } elseif ($rawLoc === 'mina' || preg_match('/(mina|camp)/i', $hName)) {
                $stay->clean_location = 'mina';
                $stay->clean_hotel_name = 'Mina Camp';
            } elseif ($rawLoc === 'madinah' || preg_match('/(nusk|madin)/i', $hName)) {
                $stay->clean_location = 'madinah';
                $stay->clean_hotel_name = !empty($hName) ? $hName : 'Madinah Hotel';
            } elseif ($rawLoc === 'makkah') {
                $stay->clean_location = 'makkah';
                $stay->clean_hotel_name = !empty($hName) ? $hName : 'Makkah Hotel';
            } else {
                $stay->clean_location = $rawLoc;
                $stay->clean_hotel_name = !empty($hName) ? $hName : 'Hotel Not Specified';
            }
        }

        // Azizia Check-In Phase Filtering (1st vs 2nd vs Both Check-Ins)
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
            $hotelStays = $filteredStays->values();
        }

        // Calculate Totals for Current Filter
        $totalStays = $hotelStays->count();
        $totalRooms = $hotelStays->sum(fn($s) => $s->no_of_rooms ?: 1);
        $totalPersons = $hotelStays->sum(function ($stay) {
            $b = $stay->booking;
            return $b ? ($b->persons->count() ?: ($b->no_of_pax ?: 1)) : 1;
        });
        $totalUniquePersons = $hotelStays->pluck('booking')->filter()->unique('id')->sum(function ($b) {
            return $b ? ($b->persons->count() ?: ($b->no_of_pax ?: 1)) : 1;
        });

        // ══════════════════════════════════════════════════════════════════
        // Overall Global Aggregations (Purely Location & Actual Hotel Breakdown)
        // ══════════════════════════════════════════════════════════════════
        $allActiveStays = BookingHotel::with(['booking.persons'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            })
            ->get();

        $locationBreakdown    = [
            'makkah'  => ['persons' => 0, 'rooms' => 0, 'stays' => 0],
            'azizia'  => ['persons' => 0, 'rooms' => 0, 'stays' => 0],
            'madinah' => ['persons' => 0, 'rooms' => 0, 'stays' => 0],
            'mina'    => ['persons' => 0, 'rooms' => 0, 'stays' => 0],
            'other'   => ['persons' => 0, 'rooms' => 0, 'stays' => 0],
        ];
        $locationBookingsSeen = [];
        $hotelBreakdown       = [];

        // Track Azizia 1st vs 2nd check-in stats across all active bookings
        $aziziaBookingsGrouped = [];

        foreach ($allActiveStays as $stay) {
            $rawLoc = strtolower(trim($stay->location ?: 'other'));
            $hName  = trim($stay->hotel_name ?: '');

            // Canonical Location Normalization
            if ($rawLoc === 'azizia' || preg_match('/(aziz|build)/i', $hName)) {
                $loc = 'azizia';
                $aziziaBookingsGrouped[$stay->booking_id][] = $stay;
            } elseif ($rawLoc === 'mina' || preg_match('/(mina|camp)/i', $hName)) {
                $loc = 'mina';
            } elseif ($rawLoc === 'madinah' || preg_match('/(nusk|madin)/i', $hName)) {
                $loc = 'madinah';
            } elseif ($rawLoc === 'makkah' || preg_match('/(anjum|pullman|zamzam|swiss)/i', $hName)) {
                $loc = 'makkah';
            } else {
                $loc = $rawLoc;
            }

            $b     = $stay->booking;
            $bId   = $stay->booking_id;
            $pax   = $b ? ($b->persons->count() ?: ($b->no_of_pax ?: 1)) : 1;
            $rooms = $stay->no_of_rooms ?: 1;

            if (!isset($locationBreakdown[$loc])) {
                $locationBreakdown[$loc] = ['persons' => 0, 'rooms' => 0, 'stays' => 0];
            }
            if (!isset($locationBookingsSeen[$loc])) {
                $locationBookingsSeen[$loc] = [];
            }
            if ($bId && !in_array($bId, $locationBookingsSeen[$loc])) {
                $locationBookingsSeen[$loc][] = $bId;
                $locationBreakdown[$loc]['persons'] += $pax;
            }
            $locationBreakdown[$loc]['rooms']   += $rooms;
            $locationBreakdown[$loc]['stays']   += 1;

            // Actual Hotels breakdown (strictly exclude raw building and camp labels)
            $isBuildingOrCamp = in_array($loc, ['azizia', 'mina', 'arafat']) ||
                preg_match('/^(building|bulding|buildin|azizia|azizya|camp|mina|arafat)\b/i', $hName) ||
                preg_match('/\b(building|bulding|buildin|camp)\b/i', $hName);

            if (!$isBuildingOrCamp && !empty($hName) && !in_array(strtolower($hName), ['hotel not specified', 'unspecified hotel', 'pending'])) {
                if (!isset($hotelBreakdown[$hName])) {
                    $hotelBreakdown[$hName] = ['persons' => 0, 'rooms' => 0, 'stays' => 0, 'location' => $loc];
                }
                $hotelBreakdown[$hName]['persons'] += $pax;
                $hotelBreakdown[$hName]['rooms']   += $rooms;
                $hotelBreakdown[$hName]['stays']   += 1;
            }
        }

        // Calculate Azizia 1st and 2nd Check-In stats
        $aziziaStats = [
            'total'        => $locationBreakdown['azizia']['persons'] ?? 0,
            'first'        => 0,
            'second'       => 0,
            'first_stays'  => 0,
            'second_stays' => 0,
        ];
        foreach ($aziziaBookingsGrouped as $bId => $stays) {
            $b = $stays[0]->booking;
            $pax = $b ? ($b->persons->count() ?: ($b->no_of_pax ?: 1)) : 1;
            if (isset($stays[0])) {
                $aziziaStats['first'] += $pax;
                $aziziaStats['first_stays'] += 1;
            }
            if (isset($stays[1])) {
                $aziziaStats['second'] += $pax;
                $aziziaStats['second_stays'] += 1;
            }
        }

        // Sort hotel breakdown by most persons
        uasort($hotelBreakdown, fn($a, $b) => $b['persons'] <=> $a['persons']);

        // Registered and distinct dropdown options (excluding raw building / camp strings)
        $registeredHotels   = Hotel::orderBy('name')->get();
        $distinctHotelNames = BookingHotel::select('hotel_name')->distinct()->whereNotNull('hotel_name')->where('hotel_name', '!=', '')->pluck('hotel_name')->toArray();
        $allHotelOptions    = array_values(array_unique(array_filter(array_merge($registeredHotels->pluck('name')->toArray(), $distinctHotelNames))));
        $allHotelOptions    = array_values(array_filter($allHotelOptions, function($h) {
            return !preg_match('/^(building|bulding|buildin|camp|azizia|azizya|mina)\b/i', trim($h));
        }));
        sort($allHotelOptions);

        $packages = Package::latest()->get();

        return compact(
            'hotelStays',
            'totalStays',
            'totalRooms',
            'totalPersons',
            'totalUniquePersons',
            'locationBreakdown',
            'aziziaStats',
            'hotelBreakdown',
            'allHotelOptions',
            'packages',
            'hotelFilter',
            'locationFilter',
            'aziziaPhase',
            'search',
            'packageFilter',
            'fromDate',
            'toDate'
        );
    }
}
