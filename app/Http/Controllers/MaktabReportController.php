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

class MaktabReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getFilteredMaktabStays($request);

        return view('reports.maktab', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredMaktabStays($request);

        $pdf = Pdf::loadView('reports.maktab_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $maktabPart   = (!empty($data['maktabFilter']) && $data['maktabFilter'] !== 'all') ? '_' . strtoupper(str_replace(' ', '_', $data['maktabFilter'])) : '';
        $genderPart   = (!empty($data['genderFilter']) && $data['genderFilter'] !== 'all') ? '_' . strtoupper($data['genderFilter']) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName     = 'Official_Maktab_Report_Manifest' . $maktabPart . $genderPart . $locationPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data           = $this->getFilteredMaktabStays($request);
        $hotelStays     = $data['hotelStays'];
        $genderFilter   = $data['genderFilter'];
        $locationFilter = $data['locationFilter'];
        $isAllLocations = empty($locationFilter) || $locationFilter === 'all';

        $maktabPart   = (!empty($data['maktabFilter']) && $data['maktabFilter'] !== 'all') ? '_' . strtoupper(str_replace(' ', '_', $data['maktabFilter'])) : '';
        $genderPart   = (!empty($data['genderFilter']) && $data['genderFilter'] !== 'all') ? '_' . strtoupper($data['genderFilter']) : '';
        $locationPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName     = 'Official_Maktab_Report_Manifest' . $maktabPart . $genderPart . $locationPart . '_' . date('Ymd_His') . '.xlsx';
        $filterSummary = 'Generated: ' . date('d M Y, h:i A') . ' | Maktab: ' . strtoupper($data['maktabFilter']) . ' | Gender: ' . strtoupper($data['genderFilter']) . ' | Location: ' . strtoupper($locationFilter);

        // Check if PhpSpreadsheet is available (Live server safe fallback)
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $xlsFileName = str_replace('.xlsx', '.xls', $fileName);
            return response()->streamDownload(function () use ($hotelStays, $genderFilter, $filterSummary, $isAllLocations) {
                echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
                echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Maktab Manifest</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
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
                echo '<tr><th colspan="9" style="font-size: 16px; height: 40px; background-color: #F8FAFC; color: #1E293B; border: none;">PIRWANI HAJJ GROUP - OFFICIAL MAKTAB REPORT & MANIFEST</th></tr>';
                echo '<tr><td colspan="9" style="text-align: center; font-style: italic; color: #64748B; border: none; height: 25px;">' . htmlspecialchars($filterSummary) . '</td></tr>';
                echo '<tr>
                    <th>SR</th>
                    <th>PILGRIM FULL NAME</th>
                    <th>GENDER</th>
                    <th>PASSPORT NO</th>
                    <th>HOTEL & LOCATION</th>
                    <th>ROOM DETAILS</th>
                    <th>MAKTAB / CAMP</th>
                    <th>PACKAGE</th>
                    <th>VOUCHER / BOOKING REF</th>
                </tr>';

                $sr = 1;
                foreach ($hotelStays as $stay) {
                    $booking = $stay->booking;
                    $rawPersons = $booking ? $booking->persons : collect([]);
                    if (!empty($genderFilter) && $genderFilter !== 'all') {
                        $gLower = strtolower(trim($genderFilter));
                        $persons = $rawPersons->filter(fn($p) => strtolower(trim($p->gender ?? '')) === $gLower)->values();
                    } else {
                        $persons = $rawPersons;
                    }
                    if ($persons->isEmpty()) continue;

                    $campName = $booking ? ($booking->camp ?: ($booking->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : 'Standard Camp')) : '—';
                    $pkgTitle = $booking && $booking->package ? ($booking->package->package_title ?: $booking->package->name) : '—';
                    $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';

                    $allStays = $stay->all_stays ?? collect([$stay]);
                    if ($isAllLocations) {
                        $hotelLines = [];
                        $roomLines  = [];
                        foreach ($allStays as $st) {
                            $hotelLines[] = strtoupper($st->location ?: 'LOC') . ': ' . strtoupper($st->hotel_name ?: 'Hotel');
                            $roomLines[]  = strtoupper($st->location ?: 'LOC') . ': ' . ucfirst($st->room_type ?: 'Standard') . ' (' . ($st->no_of_rooms ?: 1) . ' R)';
                        }
                        $hotelDisplay = implode('<br>', $hotelLines);
                        $roomDisplay  = implode('<br>', $roomLines);
                    } else {
                        $hotelDisplay = htmlspecialchars(strtoupper($stay->hotel_name ?: 'Hotel') . ' (' . strtoupper($stay->location ?: 'Location') . ')');
                        $roomDisplay  = htmlspecialchars(ucfirst($stay->room_type ?: 'Standard') . ' (' . ($stay->no_of_rooms ?: 1) . ' Room)');
                    }

                    foreach ($persons as $p) {
                        $pGender = strtoupper($p->gender ?? 'MALE');
                        $genderClass = ($pGender === 'FEMALE') ? 'gender-female' : 'gender-male';

                        echo '<tr>';
                        echo '<td class="center">' . ($sr++) . '</td>';
                        echo '<td><strong>' . htmlspecialchars($p->full_name ?? ($p->given_name . ' ' . $p->surname)) . '</strong></td>';
                        echo '<td class="center ' . $genderClass . '">' . htmlspecialchars($pGender) . '</td>';
                        echo '<td class="center" style="font-weight: bold;">' . htmlspecialchars($p->passport_number ?? '—') . '</td>';
                        echo '<td>' . $hotelDisplay . '</td>';
                        echo '<td class="center">' . $roomDisplay . '</td>';
                        echo '<td class="center"><strong>' . htmlspecialchars($campName) . '</strong></td>';
                        echo '<td>' . htmlspecialchars($pkgTitle) . '</td>';
                        echo '<td class="center">' . htmlspecialchars($voucherNo) . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</table></body></html>';
            }, $xlsFileName, [
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Cache-Control' => 'max-age=0',
            ]);
        }

        // PhpSpreadsheet Export (when available)
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Maktab Manifest');
        $sheet->setShowGridLines(true);

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'PIRWANI HAJJ GROUP - OFFICIAL MAKTAB REPORT & MANIFEST');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(15)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', $filterSummary);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $headers = [
            'A' => ['title' => 'SR', 'width' => 8],
            'B' => ['title' => 'PILGRIM FULL NAME', 'width' => 28],
            'C' => ['title' => 'GENDER', 'width' => 12],
            'D' => ['title' => 'PASSPORT NO', 'width' => 16],
            'E' => ['title' => 'HOTEL & LOCATION', 'width' => 32],
            'F' => ['title' => 'ROOM DETAILS', 'width' => 24],
            'G' => ['title' => 'MAKTAB / CAMP', 'width' => 24],
            'H' => ['title' => 'PACKAGE', 'width' => 22],
            'I' => ['title' => 'BOOKING / VOUCHER', 'width' => 18],
        ];

        $headerRow = 4;
        $sheet->getRowDimension($headerRow)->setRowHeight(26);
        foreach ($headers as $col => $colInfo) {
            $sheet->setCellValue($col . $headerRow, $colInfo['title']);
            $sheet->getColumnDimension($col)->setWidth($colInfo['width']);
        }

        $headerRange = 'A' . $headerRow . ':I' . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        $currentRow = 5;
        $sr = 1;

        foreach ($hotelStays as $stay) {
            $booking = $stay->booking;
            $rawPersons = $booking ? $booking->persons : collect([]);
            if (!empty($genderFilter) && $genderFilter !== 'all') {
                $gLower = strtolower(trim($genderFilter));
                $persons = $rawPersons->filter(fn($p) => strtolower(trim($p->gender ?? '')) === $gLower)->values();
            } else {
                $persons = $rawPersons;
            }
            if ($persons->isEmpty()) continue;

            $campName = $booking ? ($booking->camp ?: ($booking->package?->camp_category ? 'Maktab ' . $booking->package->camp_category : 'Standard Camp')) : '—';
            $pkgTitle = $booking && $booking->package ? ($booking->package->package_title ?: $booking->package->name) : '—';
            $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';

            $allStays = $stay->all_stays ?? collect([$stay]);
            if ($isAllLocations) {
                $hotelLines = [];
                $roomLines  = [];
                foreach ($allStays as $st) {
                    $hotelLines[] = strtoupper($st->location ?: 'LOC') . ': ' . strtoupper($st->hotel_name ?: 'Hotel');
                    $roomLines[]  = strtoupper($st->location ?: 'LOC') . ': ' . ucfirst($st->room_type ?: 'Standard') . ' (' . ($st->no_of_rooms ?: 1) . ' R)';
                }
                $hotelText = implode("\n", $hotelLines);
                $roomText  = implode("\n", $roomLines);
            } else {
                $hotelText = strtoupper($stay->hotel_name ?: 'Hotel') . ' (' . strtoupper($stay->location ?: 'Location') . ')';
                $roomText  = ucfirst($stay->room_type ?: 'Standard') . ' (' . ($stay->no_of_rooms ?: 1) . ' Room)';
            }

            foreach ($persons as $p) {
                $pGender = strtoupper($p->gender ?? 'MALE');

                $sheet->setCellValue('A' . $currentRow, $sr++);
                $sheet->setCellValue('B' . $currentRow, $p->full_name ?? ($p->given_name . ' ' . $p->surname));
                $sheet->setCellValue('C' . $currentRow, $pGender);
                $sheet->setCellValue('D' . $currentRow, $p->passport_number ?: '—');
                $sheet->setCellValue('E' . $currentRow, $hotelText);
                $sheet->getStyle('E' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->setCellValue('F' . $currentRow, $roomText);
                $sheet->getStyle('F' . $currentRow)->getAlignment()->setWrapText(true);
                $sheet->setCellValue('G' . $currentRow, $campName);
                $sheet->setCellValue('H' . $currentRow, $pkgTitle);
                $sheet->setCellValue('I' . $currentRow, $voucherNo);

                $rowRange = 'A' . $currentRow . ':I' . $currentRow;
                $sheet->getStyle($rowRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $currentRow . ':D' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $currentRow . ':G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                if ($pGender === 'FEMALE') {
                    $sheet->getStyle('C' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('9D174D'));
                } else {
                    $sheet->getStyle('C' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3A8A'));
                }

                $sheet->getStyle('D' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle('G' . $currentRow)->getFont()->setBold(true);
                $sheet->getStyle($rowRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle($rowRange)->getBorders()->getAllBorders()->getColor()->setRGB('CBD5E1');

                $currentRow++;
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function getFilteredMaktabStays(Request $request)
    {
        $maktabFilter   = $request->get('maktab', 'all');
        $genderFilter   = $request->get('gender', 'all');
        $locationFilter = $request->get('location', 'all');

        // Query BookingHotel records with active bookings
        $query = BookingHotel::with(['booking.client', 'booking.company', 'booking.package', 'booking.persons', 'booking.hotels'])
            ->whereHas('booking', function ($q) {
                $q->whereNull('deleted_at')->whereNotIn('status', ['cancelled', 'rejected']);
            });

        // 1. Maktab / Camp Category filter
        if (!empty($maktabFilter) && $maktabFilter !== 'all') {
            $query->whereHas('booking', function ($bq) use ($maktabFilter) {
                if ($maktabFilter === 'maktab_a') {
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
                } elseif ($maktabFilter === 'maktab_c') {
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
                    $bq->where('camp', 'LIKE', $term);
                }
            });
        }

        // 2. Gender filter (Male / Female / All)
        if (!empty($genderFilter) && $genderFilter !== 'all') {
            $g = strtolower(trim($genderFilter));
            $query->whereHas('booking.persons', function ($pq) use ($g) {
                $pq->whereRaw('LOWER(TRIM(gender)) = ?', [$g]);
            });
        }

        // 3. Location filter handling
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $locLower = strtolower(trim($locationFilter));
            $query->whereRaw('LOWER(TRIM(location)) = ?', [$locLower]);
            $hotelStays = $query->orderBy('check_in', 'asc')->get();
            foreach ($hotelStays as $s) {
                $s->all_stays = collect([$s]);
            }
        } else {
            // All Locations: Each booking / pilgrim appears ONLY ONCE!
            // Group stays by booking_id, take representative stay, and attach all its hotel stays to $primary->all_stays
            $grouped = $query->orderBy('check_in', 'asc')->get()->groupBy('booking_id');
            $hotelStays = $grouped->map(function ($stays) {
                $primary = $stays->first();
                $primary->all_stays = $stays;
                return $primary;
            })->values();
        }

        // Calculate KPI Statistics
        $totalStays = $hotelStays->count();
        $totalRooms = $hotelStays->reduce(function ($carry, $stay) {
            return $carry + ($stay->all_stays ? $stay->all_stays->sum('no_of_rooms') : ($stay->no_of_rooms ?: 1));
        }, 0);

        $allBookingIds = $hotelStays->pluck('booking_id')->unique();

        $pilgrimQuery = BookingPerson::whereIn('booking_id', $allBookingIds);
        if (!empty($genderFilter) && $genderFilter !== 'all') {
            $pilgrimQuery->whereRaw('LOWER(TRIM(gender)) = ?', [strtolower($genderFilter)]);
        }
        $totalPilgrims = $pilgrimQuery->count();
        if ($totalPilgrims === 0 && $totalStays > 0 && ($genderFilter === 'all' || empty($genderFilter))) {
            $totalPilgrims = Booking::whereIn('id', $allBookingIds)->sum('no_of_pax');
        }

        $today = Carbon::today()->format('Y-m-d');
        $todayCheckins = BookingHotel::whereIn('booking_id', $allBookingIds)->whereDate('check_in', $today)->count();
        $upcoming7Days = BookingHotel::whereIn('booking_id', $allBookingIds)->whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', Carbon::today()->addDays(7)->format('Y-m-d'))
            ->count();
        $activeInHouse = BookingHotel::whereIn('booking_id', $allBookingIds)->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>=', $today)
            ->count();

        // Breakdown for badges
        $totalMalePilgrims = BookingPerson::whereIn('booking_id', $allBookingIds)
            ->whereRaw('LOWER(TRIM(gender)) = ?', ['male'])->count();
        $totalFemalePilgrims = BookingPerson::whereIn('booking_id', $allBookingIds)
            ->whereRaw('LOWER(TRIM(gender)) = ?', ['female'])->count();

        // Distinct camps from DB to populate dropdown
        $distinctCamps = Booking::whereNotNull('camp')->where('camp', '!=', '')->distinct()->pluck('camp')->toArray();

        // Distinct locations from DB to populate dropdown
        $distinctLocations = BookingHotel::whereNotNull('location')->where('location', '!=', '')->distinct()->pluck('location')->toArray();

        return compact(
            'hotelStays',
            'totalStays',
            'totalRooms',
            'totalPilgrims',
            'totalMalePilgrims',
            'totalFemalePilgrims',
            'todayCheckins',
            'upcoming7Days',
            'activeInHouse',
            'maktabFilter',
            'genderFilter',
            'locationFilter',
            'distinctCamps',
            'distinctLocations'
        );
    }
}
