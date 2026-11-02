<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHotel;
use App\Models\BookingPerson;
use App\Models\BookingPersonRoomAllocation;
use App\Models\Hotel;
use App\Models\HotelRoomCapacity;
use App\Models\RoomType;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomingListController extends Controller
{
    /**
     */
    public function index(Request $request)
    {
        $data = $this->getFilteredRoomingData($request);

        $registeredHotels = Hotel::orderBy('name')->get();
        $distinctHotelNames = BookingHotel::select('hotel_name')->distinct()->whereNotNull('hotel_name')->pluck('hotel_name');
        $packages = \App\Models\Package::orderBy('name')->orderBy('package_title')->get();
        $roomTypes = RoomType::where('status', 'active')->orderBy('capacity')->get();
        if ($roomTypes->isEmpty()) {
            $roomTypes = collect([
                (object)['name' => 'Single', 'code' => 'single', 'capacity' => 1],
                (object)['name' => 'Double', 'code' => 'double', 'capacity' => 2],
                (object)['name' => 'Triple', 'code' => 'triple', 'capacity' => 3],
                (object)['name' => 'Quad', 'code' => 'quad', 'capacity' => 4],
                (object)['name' => 'Quint', 'code' => 'quint', 'capacity' => 5],
                (object)['name' => 'Six', 'code' => 'six', 'capacity' => 6],
                (object)['name' => 'Sharing', 'code' => 'sharing', 'capacity' => 4],
                (object)['name' => 'Suite', 'code' => 'suite', 'capacity' => 2],
            ]);
        }

        return view('reports.rooming_list', array_merge($data, compact(
            'registeredHotels',
            'distinctHotelNames',
            'packages',
            'roomTypes'
        )));
    }

    /**
     * Export Official Rooming List & Manifest as PDF
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredRoomingData($request);

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

        $paperOrientation = ($visibleCols && count($visibleCols) > 7) ? 'landscape' : 'portrait';
        $data['paperOrientation'] = $paperOrientation;

        $pdf = Pdf::loadView('reports.rooming_list_pdf', $data);
        $pdf->setPaper('A4', $paperOrientation);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $hotelString = is_array($data['hotelFilter'] ?? null) ? implode('_', $data['hotelFilter']) : ($data['hotelFilter'] ?? '');
        $hotelPart = !empty($hotelString) ? '_' . preg_replace('/[^A-Za-z0-9]/', '_', $hotelString) : '';
        $locPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName = 'Official_Rooming_List_Manifest' . $locPart . $hotelPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     */
    public function exportExcel(Request $request)
    {
        @set_time_limit(180);
        @ini_set('memory_limit', '512M');

        $data = $this->getFilteredRoomingData($request);
        $groupedRooms = $data['groupedRooms'];

        $hotelString = is_array($data['hotelFilter'] ?? null) ? implode('_', $data['hotelFilter']) : ($data['hotelFilter'] ?? '');
        $hotelPart = !empty($hotelString) ? '_' . preg_replace('/[^A-Za-z0-9]/', '_', $hotelString) : '';
        $locPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName = 'Official_Rooming_List_Manifest' . $locPart . $hotelPart . '_' . date('Ymd_His') . '.xlsx';

        // Subtitle / Filters summary Block
        $filterSummary = 'Generated: ' . date('d M Y, h:i A');
        if (!empty($data['hotelFilter'])) {
            $filterSummary .= ' | Hotels: ' . (is_array($data['hotelFilter']) ? implode(', ', $data['hotelFilter']) : $data['hotelFilter']);
        }
        if (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') {
            $filterSummary .= ' | Location: ' . strtoupper($data['locationFilter']);
        }
        if (!empty($data['roomTypeFilter']) && $data['roomTypeFilter'] !== 'all') {
            $filterSummary .= ' | Room Type: ' . strtoupper($data['roomTypeFilter']);
        }

        $roomTypeSummary = $data['roomTypeSummary'] ?? [];
        $roomTypeSummaryGrand = $data['roomTypeSummaryGrand'] ?? [];
        $totalAssignedRoomsCount = $data['totalAssignedRoomsCount'] ?? 0;
        $unassignedPilgrimsCount = $data['unassignedPilgrimsCount'] ?? 0;
        $totalBedsOccupied = $data['totalBedsOccupied'] ?? 0;

        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $xlsFileName = str_replace('.xlsx', '.xls', $fileName);
            return response()->streamDownload(function () use ($groupedRooms, $filterSummary, $roomTypeSummary, $roomTypeSummaryGrand, $totalAssignedRoomsCount, $unassignedPilgrimsCount, $totalBedsOccupied) {
                echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
                echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Rooming Manifest</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
                echo '<style>
                    body { font-family: Arial, sans-serif; }
                    table { border-collapse: collapse; width: 100%; }
                    th { background-color: #1E293B; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center; vertical-align: middle; height: 35px; }
                    td { border: 1px solid #CBD5E1; vertical-align: middle; padding: 6px; }
                    .center { text-align: center; }
                    .room-no { font-weight: bold; color: #DC2626; font-size: 14px; text-align: center; }
                    .gender-male { color: #1E3A8A; font-weight: bold; }
                    .gender-female { color: #9D174D; font-weight: bold; }
                    .room-divider { border-top: 2px solid #000000 !important; }
                </style></head><body>';
                echo '<table>';
                echo '<tr><th colspan="23" style="font-size: 16px; height: 40px; background-color: #F8FAFC; color: #1E293B; border: none;">PIRWANI HAJJ GROUP - OFFICIAL ROOMING LIST & MANIFEST REPORT</th></tr>';
                echo '<tr><td colspan="23" style="text-align: center; font-style: italic; color: #64748B; border: none; height: 25px;">' . htmlspecialchars($filterSummary) . '</td></tr>';
                echo '</table>';

                // Room Type Breakdown Summary Table
                if (!empty($roomTypeSummary)) {
                    echo '<br><table style="width: auto; border: 1.5px solid #000; margin-bottom: 15px;">';
                    echo '<tr><th colspan="5" style="background-color: #0F172A; color: #FFFFFF; font-size: 12px; text-align: left; padding: 6px;">ROOM TYPE BREAKDOWN SUMMARY</th></tr>';
                    echo '<tr style="background-color: #1E293B; color: #FFFFFF;">
                        <th style="padding: 5px; text-align: left;">ROOM CATEGORY</th>
                        <th style="padding: 5px; text-align: center;">ALLOCATED ROOMS</th>
                        <th style="padding: 5px; text-align: center;">ALLOCATED PAX</th>
                        <th style="padding: 5px; text-align: center;">PENDING PAX</th>
                        <th style="padding: 5px; text-align: center; background-color: #1E3A8A;">TOTAL PILGRIMS (PAX)</th>
                    </tr>';
                    foreach ($roomTypeSummary as $key => $sum) {
                        if (($sum['total_pax'] ?? 0) > 0 || ($sum['assigned_rooms'] ?? 0) > 0) {
                            echo '<tr>';
                            echo '<td style="font-weight: bold; padding: 4px 6px;">' . htmlspecialchars($sum['name'] ?? $key) . '</td>';
                            echo '<td style="text-align: center; font-weight: bold; color: ' . (($sum['assigned_rooms'] ?? 0) > 0 ? '#059669' : '#64748B') . ';">' . ($sum['assigned_rooms'] ?? 0) . '</td>';
                            echo '<td style="text-align: center; font-weight: bold;">' . ($sum['assigned_pax'] ?? 0) . '</td>';
                            echo '<td style="text-align: center; color: ' . (($sum['pending_pax'] ?? 0) > 0 ? '#D97706' : '#64748B') . ';">' . ($sum['pending_pax'] ?? 0) . '</td>';
                            echo '<td style="text-align: center; font-weight: bold; background-color: #EFF6FF;">' . ($sum['total_pax'] ?? 0) . '</td>';
                            echo '</tr>';
                        }
                    }
                    echo '<tr style="background-color: #E2E8F0; font-weight: bold; border-top: 2px solid #000;">';
                    echo '<td style="padding: 5px 6px;">GRAND TOTAL</td>';
                    echo '<td style="text-align: center; color: #059669;">' . number_format($totalAssignedRoomsCount) . '</td>';
                    echo '<td style="text-align: center;">' . number_format($roomTypeSummaryGrand['assigned_pax'] ?? 0) . '</td>';
                    echo '<td style="text-align: center; color: #D97706;">' . number_format($unassignedPilgrimsCount) . '</td>';
                    echo '<td style="text-align: center; background-color: #DBEAFE; color: #1E3A8A;">' . number_format($totalBedsOccupied) . '</td>';
                    echo '</tr>';
                    echo '</table><br>';
                }

                echo '<table>';
                echo '<tr>
                    <th>SR</th>
                    <th>PICTURE</th>
                    <th>HAJJ ID</th>
                    <th>HB NUMBER</th>
                    <th>PASSPORT NO</th>
                    <th>DATE OF ISSUE</th>
                    <th>PASSPORT EXPIRY</th>
                    <th>PILGRIM FULL NAME</th>
                    <th>GIVEN NAME</th>
                    <th>SURNAME</th>
                    <th>GENDER</th>
                    <th>LOCATION</th>
                    <th>HOTEL / BUILDING</th>
                    <th>ROOM TYPE</th>
                    <th>ROOM NUMBER</th>
                    <th>TOTAL BEDS</th>
                    <th>OCCUPIED</th>
                    <th>AVAILABLE</th>
                    <th>ROOM GENDER</th>
                    <th>BOOKING REF</th>
                    <th>CLIENT / AGENT</th>
                    <th>PHONE</th>
                    <th>CHECK IN</th>
                    <th>CHECK OUT</th>
                    <th>ROOM NOTES</th>
                </tr>';

                $sr = 1;
                foreach ($groupedRooms as $room) {
                    $rNum = trim($room['room_number'] ?? '');
                    $isUnassigned = ($rNum === 'PENDING' || $rNum === 'UNASSIGNED' || empty($rNum) || empty($room['is_assigned']));
                    $roomDisplayNo = $isUnassigned ? '—' : (str_starts_with(strtoupper($rNum), 'R') ? strtoupper($rNum) : ('R' . $rNum));
                    $locLabel = strtoupper($room['location'] ?? 'MAKKAH');

                    if (empty($room['occupants'])) {
                        echo '<tr class="room-divider">';
                        echo '<td class="center">' . ($sr++) . '</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td><em>(Empty Room)</em></td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">' . htmlspecialchars($locLabel) . '</td>';
                        echo '<td>' . htmlspecialchars($room['hotel_name']) . '</td>';
                        $roomTypeExp = (!empty($room['is_assigned']) && !empty($room['room_type'])) ? $room['room_type'] : '—';
                        echo '<td class="center">' . htmlspecialchars($roomTypeExp) . '</td>';
                        echo '<td class="room-no">' . htmlspecialchars($roomDisplayNo) . '</td>';
                        echo '<td class="center">' . $room['total_capacity'] . '</td>';
                        echo '<td class="center">0</td>';
                        echo '<td class="center">' . $room['total_capacity'] . '</td>';
                        echo '<td class="center">' . htmlspecialchars($room['room_gender'] ?? 'Any') . '</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">—</td>';
                        echo '<td class="center">' . ($room['check_in_min'] ? date('d M Y', strtotime($room['check_in_min'])) : '—') . '</td>';
                        echo '<td class="center">' . ($room['check_out_max'] ? date('d M Y', strtotime($room['check_out_max'])) : '—') . '</td>';
                        echo '<td>' . htmlspecialchars($room['notes'] ?? '') . '</td>';
                        echo '</tr>';
                    } else {
                        foreach ($room['occupants'] as $occIdx => $occ) {
                            $isTop = ($occIdx === 0) ? ' class="room-divider"' : '';
                            $occGender = strtoupper($occ['gender'] ?? 'MALE');
                            $genderClass = ($occGender === 'FEMALE') ? 'gender-female' : 'gender-male';

                            $photoUrl = null;
                            if (!empty($occ['photo'])) {
                                $rawPhoto = ltrim($occ['photo'], '/\\');
                                if (file_exists(public_path($rawPhoto))) {
                                    $photoUrl = asset($rawPhoto);
                                } else {
                                    $photoUrl = 'https://pirwanitravels.com.pk/' . $rawPhoto;
                                }
                            }

                            echo '<tr' . $isTop . '>';
                            echo '<td class="center">' . ($sr++) . '</td>';
                            if ($photoUrl) {
                                echo '<td class="center" style="height: 55px;"><img src="' . htmlspecialchars($photoUrl) . '" width="45" height="50" style="object-fit: cover; border-radius: 4px;"></td>';
                            } else {
                                echo '<td class="center">—</td>';
                            }
                            echo '<td class="center">' . htmlspecialchars(!empty($occ['hajj_id']) ? $occ['hajj_id'] : '—') . '</td>';
                            echo '<td class="center" style="font-weight: bold;">' . htmlspecialchars(!empty($occ['hb_number']) ? $occ['hb_number'] : '—') . '</td>';
                            echo '<td class="center" style="font-weight: bold;">' . htmlspecialchars($occ['passport'] ?? '—') . '</td>';
                            echo '<td class="center">' . htmlspecialchars($occ['date_of_issue'] ?? '—') . '</td>';
                            echo '<td class="center">' . htmlspecialchars($occ['passport_expiry_date'] ?? '—') . '</td>';
                            echo '<td><strong>' . htmlspecialchars($occ['name'] ?? '—') . '</strong></td>';
                            echo '<td>' . htmlspecialchars($occ['given_name'] ?? '—') . '</td>';
                            echo '<td>' . htmlspecialchars($occ['surname'] ?? '—') . '</td>';
                            echo '<td class="center ' . $genderClass . '">' . htmlspecialchars($occGender) . '</td>';
                            echo '<td class="center">' . htmlspecialchars($locLabel) . '</td>';
                            echo '<td>' . htmlspecialchars($room['hotel_name']) . '</td>';
                            $roomTypeExp = (!empty($room['is_assigned']) && !empty($room['room_type'])) ? $room['room_type'] : '—';
                        echo '<td class="center">' . htmlspecialchars($roomTypeExp) . '</td>';
                            echo '<td class="room-no">' . htmlspecialchars($roomDisplayNo) . '</td>';
                            echo '<td class="center">' . $room['total_capacity'] . '</td>';
                            echo '<td class="center">' . $room['occupied_beds'] . '</td>';
                            echo '<td class="center">' . $room['available_beds'] . '</td>';
                            echo '<td class="center">' . htmlspecialchars($room['room_gender'] ?? 'Any') . '</td>';
                            echo '<td class="center">' . htmlspecialchars($occ['booking_number'] ?? '—') . '</td>';
                            echo '<td>' . htmlspecialchars($occ['client_name'] ?? '—') . '</td>';
                            echo '<td class="center">' . htmlspecialchars($occ['phone'] ?? '—') . '</td>';
                            echo '<td class="center">' . htmlspecialchars($occ['check_in'] ?? '—') . '</td>';
                            echo '<td class="center">' . htmlspecialchars($occ['check_out'] ?? '—') . '</td>';
                            echo '<td>' . htmlspecialchars($room['notes'] ?? '') . '</td>';
                            echo '</tr>';
                        }
                    }
                }
                echo '</table></body></html>';
            }, $xlsFileName, [
                'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
                'Cache-Control' => 'max-age=0',
            ]);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rooming Manifest');
        $sheet->setShowGridLines(true);

        // Title Block
        $sheet->mergeCells('A1:Y1');
        $sheet->setCellValue('A1', 'PIRWANI HAJJ GROUP - OFFICIAL ROOMING LIST & MANIFEST REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Subtitle / Filters summary Block
        $sheet->mergeCells('A2:Y2');
        $sheet->setCellValue('A2', $filterSummary);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Room Type Breakdown Summary in Spreadsheet
        $curRow = 4;
        $sheet->mergeCells("A{$curRow}:E{$curRow}");
        $sheet->setCellValue("A{$curRow}", "ROOM TYPE BREAKDOWN SUMMARY");
        $sheet->getStyle("A{$curRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($curRow)->setRowHeight(22);
        $curRow++;

        $sumHeaders = ['A' => 'ROOM CATEGORY', 'B' => 'ALLOCATED ROOMS', 'C' => 'ALLOCATED PAX', 'D' => 'PENDING PAX', 'E' => 'TOTAL PILGRIMS (PAX)'];
        foreach ($sumHeaders as $scol => $stitle) {
            $sheet->setCellValue("{$scol}{$curRow}", $stitle);
        }
        $sheet->getStyle("A{$curRow}:E{$curRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getStyle("A{$curRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E{$curRow}")->getFill()->getStartColor()->setRGB('1E3A8A');
        $sheet->getRowDimension($curRow)->setRowHeight(20);
        $curRow++;

        foreach ($roomTypeSummary as $key => $sum) {
            if (($sum['total_pax'] ?? 0) > 0 || ($sum['assigned_rooms'] ?? 0) > 0) {
                $sheet->setCellValue("A{$curRow}", $sum['name'] ?? $key);
                $sheet->setCellValue("B{$curRow}", $sum['assigned_rooms'] ?? 0);
                $sheet->setCellValue("C{$curRow}", $sum['assigned_pax'] ?? 0);
                $sheet->setCellValue("D{$curRow}", $sum['pending_pax'] ?? 0);
                $sheet->setCellValue("E{$curRow}", $sum['total_pax'] ?? 0);

                $sheet->getStyle("A{$curRow}:E{$curRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$curRow}")->getFont()->setBold(true);
                $sheet->getStyle("B{$curRow}:E{$curRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$curRow}")->getFont()->setBold(true);
                $sheet->getStyle("E{$curRow}")->getFill()->applyFromArray([
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EFF6FF'],
                ]);
                $sheet->getRowDimension($curRow)->setRowHeight(18);
                $curRow++;
            }
        }

        // Grand Total Summary Row
        $sheet->setCellValue("A{$curRow}", "GRAND TOTAL");
        $sheet->setCellValue("B{$curRow}", $totalAssignedRoomsCount);
        $sheet->setCellValue("C{$curRow}", $roomTypeSummaryGrand['assigned_pax'] ?? 0);
        $sheet->setCellValue("D{$curRow}", $unassignedPilgrimsCount);
        $sheet->setCellValue("E{$curRow}", $totalBedsOccupied);

        $sheet->getStyle("A{$curRow}:E{$curRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("B{$curRow}:E{$curRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E{$curRow}")->getFill()->getStartColor()->setRGB('DBEAFE');
        $sheet->getRowDimension($curRow)->setRowHeight(20);
        $curRow += 2; // Leave space before main manifest table

        // Column Headers
        $headers = [
            'A' => ['title' => 'SR', 'width' => 8],
            'B' => ['title' => 'PICTURE', 'width' => 14],
            'C' => ['title' => 'HAJJ ID', 'width' => 14],
            'D' => ['title' => 'HB NUMBER', 'width' => 14],
            'E' => ['title' => 'PASSPORT NO', 'width' => 16],
            'F' => ['title' => 'DATE OF ISSUE', 'width' => 16],
            'G' => ['title' => 'PASSPORT EXPIRY', 'width' => 16],
            'H' => ['title' => 'PILGRIM FULL NAME', 'width' => 28],
            'I' => ['title' => 'GIVEN NAME', 'width' => 20],
            'J' => ['title' => 'SURNAME', 'width' => 20],
            'K' => ['title' => 'GENDER', 'width' => 12],
            'L' => ['title' => 'LOCATION', 'width' => 14],
            'M' => ['title' => 'HOTEL / BUILDING', 'width' => 26],
            'N' => ['title' => 'ROOM TYPE', 'width' => 15],
            'O' => ['title' => 'ROOM NUMBER', 'width' => 16],
            'P' => ['title' => 'TOTAL BEDS', 'width' => 12],
            'Q' => ['title' => 'OCCUPIED', 'width' => 12],
            'R' => ['title' => 'AVAILABLE', 'width' => 12],
            'S' => ['title' => 'ROOM GENDER', 'width' => 14],
            'T' => ['title' => 'BOOKING REF', 'width' => 16],
            'U' => ['title' => 'CLIENT / AGENT', 'width' => 24],
            'V' => ['title' => 'PHONE', 'width' => 16],
            'W' => ['title' => 'CHECK IN', 'width' => 14],
            'X' => ['title' => 'CHECK OUT', 'width' => 14],
            'Y' => ['title' => 'ROOM NOTES', 'width' => 22],
        ];

        $headerRow = $curRow;
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        foreach ($headers as $col => $colInfo) {
            $sheet->setCellValue($col . $headerRow, $colInfo['title']);
            $sheet->getColumnDimension($col)->setWidth($colInfo['width']);
        }

        // Header Styling
        $headerRange = 'A' . $headerRow . ':Y' . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $currentRow = $headerRow + 1;
        $sr = 1;

        $cacheDir = storage_path('app/temp_photos');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        foreach ($groupedRooms as $room) {
            $rNum = trim($room['room_number'] ?? '');
            $isUnassigned = ($rNum === 'PENDING' || $rNum === 'UNASSIGNED' || empty($rNum) || empty($room['is_assigned']));
            $roomDisplayNo = $isUnassigned ? '—' : (str_starts_with(strtoupper($rNum), 'R') ? strtoupper($rNum) : ('R' . $rNum));
            $locLabel = strtoupper($room['location'] ?? 'MAKKAH');

            if (empty($room['occupants'])) {
                // Empty room row
                $sheet->getRowDimension($currentRow)->setRowHeight(26);
                $sheet->setCellValue('A' . $currentRow, $sr++);
                $sheet->setCellValue('B' . $currentRow, '—');
                $sheet->setCellValue('C' . $currentRow, '—');
                $sheet->setCellValue('D' . $currentRow, '—');
                $sheet->setCellValue('E' . $currentRow, '—');
                $sheet->setCellValue('F' . $currentRow, '—');
                $sheet->setCellValue('G' . $currentRow, '—');
                $sheet->setCellValue('H' . $currentRow, '(Empty Room)');
                $sheet->setCellValue('I' . $currentRow, '—');
                $sheet->setCellValue('J' . $currentRow, '—');
                $sheet->setCellValue('K' . $currentRow, '—');
                $sheet->setCellValue('L' . $currentRow, $locLabel);
                $sheet->setCellValue('M' . $currentRow, $room['hotel_name']);
                $sheet->setCellValue('N' . $currentRow, (!empty($room['is_assigned']) && !empty($room['room_type'])) ? $room['room_type'] : '—');
                $sheet->setCellValue('O' . $currentRow, $roomDisplayNo);
                $sheet->setCellValue('P' . $currentRow, $room['total_capacity']);
                $sheet->setCellValue('Q' . $currentRow, 0);
                $sheet->setCellValue('R' . $currentRow, $room['total_capacity']);
                $sheet->setCellValue('S' . $currentRow, $room['room_gender'] ?? 'Any');
                $sheet->setCellValue('T' . $currentRow, '—');
                $sheet->setCellValue('U' . $currentRow, '—');
                $sheet->setCellValue('V' . $currentRow, '—');
                $sheet->setCellValue('W' . $currentRow, $room['check_in_min'] ? date('d M Y', strtotime($room['check_in_min'])) : '—');
                $sheet->setCellValue('X' . $currentRow, $room['check_out_max'] ? date('d M Y', strtotime($room['check_out_max'])) : '—');
                $sheet->setCellValue('Y' . $currentRow, $room['notes'] ?? '');

                $rowRange = 'A' . $currentRow . ':Y' . $currentRow;
                $sheet->getStyle($rowRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $currentRow . ':G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $currentRow . ':S' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('W' . $currentRow . ':X' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('O' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
                $sheet->getStyle($rowRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle($rowRange)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

                $currentRow++;
            } else {
                foreach ($room['occupants'] as $occIdx => $occ) {
                    $sheet->getRowDimension($currentRow)->setRowHeight(52);
                    $sheet->setCellValue('A' . $currentRow, $sr++);

                    // Embed Photo in Column B
                    $photoPath = !empty($occ['photo']) ? trim($occ['photo']) : null;
                    $photoFile = null;

                    if ($photoPath) {
                        $rawClean = ltrim($photoPath, '/\\');
                        $localPath = public_path($rawClean);
                        if (file_exists($localPath) && @getimagesize($localPath)) {
                            $photoFile = $localPath;
                        } else {
                            $cachedFile = $cacheDir . '/' . md5($rawClean) . '.jpg';
                            if (file_exists($cachedFile) && filesize($cachedFile) > 0 && @getimagesize($cachedFile)) {
                                $photoFile = $cachedFile;
                            }
                        }
                    }

                    if ($photoFile && file_exists($photoFile)) {
                        try {
                            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                            $drawing->setName('Photo_' . $sr);
                            $drawing->setDescription('Pilgrim Photo');
                            $drawing->setPath($photoFile);
                            $drawing->setHeight(46);
                            $drawing->setCoordinates('B' . $currentRow);
                            $drawing->setOffsetX(10);
                            $drawing->setOffsetY(3);
                            $drawing->setWorksheet($sheet);
                        } catch (\Throwable $e) {
                            $sheet->setCellValue('B' . $currentRow, '—');
                        }
                    } else {
                        $sheet->setCellValue('B' . $currentRow, '—');
                    }

                    $sheet->setCellValue('C' . $currentRow, !empty($occ['hajj_id']) ? $occ['hajj_id'] : '—');
                    $sheet->setCellValue('D' . $currentRow, !empty($occ['hb_number']) ? $occ['hb_number'] : '—');
                    $sheet->setCellValue('E' . $currentRow, $occ['passport'] ?? '—');
                    $sheet->setCellValue('F' . $currentRow, $occ['date_of_issue'] ?? '—');
                    $sheet->setCellValue('G' . $currentRow, $occ['passport_expiry_date'] ?? '—');
                    $sheet->setCellValue('H' . $currentRow, $occ['name'] ?? '—');
                    $sheet->setCellValue('I' . $currentRow, $occ['given_name'] ?? '—');
                    $sheet->setCellValue('J' . $currentRow, $occ['surname'] ?? '—');

                    $occGender = strtoupper($occ['gender'] ?? 'MALE');
                    $sheet->setCellValue('K' . $currentRow, $occGender);

                    $sheet->setCellValue('L' . $currentRow, $locLabel);
                    $sheet->setCellValue('M' . $currentRow, $room['hotel_name']);
                    $sheet->setCellValue('N' . $currentRow, (!empty($room['is_assigned']) && !empty($room['room_type'])) ? $room['room_type'] : '—');
                    $sheet->setCellValue('O' . $currentRow, $roomDisplayNo);
                    $sheet->setCellValue('P' . $currentRow, $room['total_capacity']);
                    $sheet->setCellValue('Q' . $currentRow, $room['occupied_beds']);
                    $sheet->setCellValue('R' . $currentRow, $room['available_beds']);
                    $sheet->setCellValue('S' . $currentRow, $room['room_gender'] ?? 'Any');
                    $sheet->setCellValue('T' . $currentRow, $occ['booking_number'] ?? '—');
                    $sheet->setCellValue('U' . $currentRow, $occ['client_name'] ?? '—');
                    $sheet->setCellValue('V' . $currentRow, $occ['phone'] ?? '—');
                    $sheet->setCellValue('W' . $currentRow, $occ['check_in'] ?? '—');
                    $sheet->setCellValue('X' . $currentRow, $occ['check_out'] ?? '—');
                    $sheet->setCellValue('Y' . $currentRow, $room['notes'] ?? '');

                    // Alignments
                    $rowRange = 'A' . $currentRow . ':Y' . $currentRow;
                    $sheet->getStyle($rowRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $currentRow . ':G' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('K' . $currentRow . ':S' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('W' . $currentRow . ':X' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Gender Styling
                    if ($occGender === 'FEMALE') {
                        $sheet->getStyle('K' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('9D174D'));
                    } else {
                        $sheet->getStyle('K' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3A8A'));
                    }

                    // Room No Styling
                    $sheet->getStyle('O' . $currentRow)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
                    $sheet->getStyle('D' . $currentRow)->getFont()->setBold(true);
                    $sheet->getStyle('E' . $currentRow)->getFont()->setBold(true);

                    // Borders
                    $sheet->getStyle($rowRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $sheet->getStyle($rowRange)->getBorders()->getAllBorders()->getColor()->setRGB('CBD5E1');

                    // If first occupant in room, put thick top border
                    if ($occIdx === 0) {
                        $sheet->getStyle($rowRange)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                        $sheet->getStyle($rowRange)->getBorders()->getTop()->getColor()->setRGB('000000');
                    }

                    $currentRow++;
                }
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

    /**
     * Resolve Pilgrim IDs from any user input string (Haji IDs, HB numbers, Passports, Booking refs, Names)
     */
    private function resolvePilgrimsFromInput(?string $input, array $existingIds = []): array
    {
        $personIds = $existingIds;
        if (empty($input)) {
            return array_values(array_unique(array_filter($personIds)));
        }

        $rawTokens = preg_split('/[,;\/\n\r]+/', trim($input), -1, PREG_SPLIT_NO_EMPTY);
        $allTokens = [];

        foreach ($rawTokens as $rt) {
            $rt = trim($rt);
            if (empty($rt)) continue;

            // Check if token is a range: e.g. 27001-27003 or PW27001-PW27003
            if (preg_match('/^(?:PW)?(?:27)?(\d+)\s*[-–—to]+\s*(?:PW)?(?:27)?(\d+)$/i', $rt, $rm)) {
                $start = min((int)$rm[1], (int)$rm[2]);
                $end = max((int)$rm[1], (int)$rm[2]);
                if (($end - $start) <= 50) {
                    for ($n = $start; $n <= $end; $n++) {
                        $allTokens[] = (string)$n;
                    }
                    continue;
                }
            }

            // Handle space separated "PW 27001" or "HB 0005"
            $spaceParts = preg_split('/\s+/', $rt, -1, PREG_SPLIT_NO_EMPTY);
            if (count($spaceParts) === 2 && (preg_match('/^(?:PW|HB|BK)$/i', $spaceParts[0]) && preg_match('/^\d+$/', $spaceParts[1]))) {
                $allTokens[] = $spaceParts[0] . $spaceParts[1];
            } elseif (count($spaceParts) > 1 && !preg_match('/[a-z]{3,}/i', $rt)) {
                foreach ($spaceParts as $sp) {
                    $allTokens[] = $sp;
                }
            } else {
                $allTokens[] = $rt;
            }
        }

        $allTokens = array_unique(array_filter($allTokens));

        $candidateHajjIds    = [];
        $candidateHbNumbers  = [];
        $candidatePassports  = [];
        $candidatePersonIds  = [];
        $candidateBookingIds = [];
        $nameTerms           = [];

        foreach ($allTokens as $token) {
            $cleanToken   = trim($token);
            $upper        = strtoupper($cleanToken);
            $noPunct      = preg_replace('/[^A-Za-z0-9]/', '', $cleanToken);
            $upperNoPunct = strtoupper($noPunct);

            // 1. HB Number (e.g. HB0005, HB 5, HB-0005)
            if (preg_match('/^HB\s*[-_]?\s*(\d+)$/i', $cleanToken, $m)) {
                $candidateHbNumbers[] = 'HB' . str_pad((int)$m[1], 4, '0', STR_PAD_LEFT);
                continue;
            }

            // 2. Booking Reference (e.g. BK-1, Booking #1)
            if (preg_match('/^(?:BK|BOOKING)\s*[-_#]?\s*(\d+)$/i', $cleanToken, $m)) {
                $candidateBookingIds[] = (int)$m[1];
                continue;
            }

            // 3. Hajj ID format: e.g. PW27001, PW-27001, 27001, or 001
            if (preg_match('/^(?:PW)?\s*[-_]?\s*(?:27)?\s*[-_]?\s*(\d{1,5})$/i', $cleanToken, $m)) {
                $num = (int)$m[1];
                $candidateHajjIds[] = 'PW27' . str_pad($num, 3, '0', STR_PAD_LEFT);
                $candidateHajjIds[] = 'PW' . $num;
                $candidateHajjIds[] = 'PW27' . $num;
                $candidateHajjIds[] = '27' . str_pad($num, 3, '0', STR_PAD_LEFT);
                $candidateHajjIds[] = (string)$num;
                $candidateHajjIds[] = $upperNoPunct;
                $candidateHajjIds[] = $cleanToken;
                // If small number > 100, could also be a direct person ID
                if ($num > 100 && $num < 27000) {
                    $candidatePersonIds[] = $num;
                }
                continue;
            }

            // 4. Passport or direct code
            $candidatePassports[] = $upper;
            $candidatePassports[] = $cleanToken;

            if (is_numeric($cleanToken)) {
                $candidatePersonIds[] = (int)$cleanToken;
            }

            if (strlen($cleanToken) >= 3 && !is_numeric($cleanToken)) {
                $nameTerms[] = $cleanToken;
            }
        }

        $candidateHajjIds    = array_unique(array_filter($candidateHajjIds));
        $candidateHbNumbers  = array_unique(array_filter($candidateHbNumbers));
        $candidatePassports  = array_unique(array_filter($candidatePassports));
        $candidatePersonIds  = array_unique(array_filter($candidatePersonIds));
        $candidateBookingIds = array_unique(array_filter($candidateBookingIds));

        $matchedPersons = BookingPerson::whereHas('booking', function ($bq) {
            $bq->whereNull('deleted_at')
               ->whereNotIn('status', ['cancelled', 'rejected']);
        })->where(function ($q) use (
            $candidateHajjIds,
            $candidateHbNumbers,
            $candidatePassports,
            $candidatePersonIds,
            $candidateBookingIds,
            $nameTerms
        ) {
            $hasWhere = false;
            if (!empty($candidateHajjIds)) {
                $cleanIds = array_values(array_unique(array_map(function($v) {
                    return strtolower(preg_replace('/[^a-z0-9]/i', '', $v));
                }, $candidateHajjIds)));

                $q->where(function($hq) use ($candidateHajjIds, $cleanIds) {
                    $hq->whereIn('hajj_id', $candidateHajjIds)
                       ->orWhereIn(DB::raw("LOWER(REPLACE(REPLACE(REPLACE(COALESCE(hajj_id, ''), ' ', ''), '-', ''), '_', ''))"), $cleanIds);
                });
                $hasWhere = true;
            }
            if (!empty($candidateHbNumbers)) {
                $hasWhere ? $q->orWhereIn('hb_number', $candidateHbNumbers) : $q->whereIn('hb_number', $candidateHbNumbers);
                $hasWhere = true;
            }
            if (!empty($candidatePassports)) {
                $hasWhere ? $q->orWhereIn('passport_number', $candidatePassports) : $q->whereIn('passport_number', $candidatePassports);
                $hasWhere = true;
            }
            if (!empty($candidateBookingIds)) {
                $hasWhere ? $q->orWhereIn('booking_id', $candidateBookingIds) : $q->whereIn('booking_id', $candidateBookingIds);
                $hasWhere = true;
            }
            if (!empty($candidatePersonIds)) {
                $hasWhere ? $q->orWhereIn('id', $candidatePersonIds) : $q->whereIn('id', $candidatePersonIds);
                $hasWhere = true;
            }
            foreach ($nameTerms as $nt) {
                $hasWhere ? $q->orWhere('full_name', 'LIKE', '%' . $nt . '%') : $q->where('full_name', 'LIKE', '%' . $nt . '%');
                $hasWhere = true;
            }
        })->pluck('id')->toArray();

        // Fallback: If no match found through active booking scope, check BookingPerson table directly
        if (empty($matchedPersons) && (!empty($candidateHajjIds) || !empty($candidatePersonIds) || !empty($candidatePassports))) {
            $matchedPersons = BookingPerson::where(function ($q) use ($candidateHajjIds, $candidatePersonIds, $candidatePassports) {
                if (!empty($candidateHajjIds)) {
                    $cleanIds = array_values(array_unique(array_map(function($v) {
                        return strtolower(preg_replace('/[^a-z0-9]/i', '', $v));
                    }, $candidateHajjIds)));

                    $q->where(function($hq) use ($candidateHajjIds, $cleanIds) {
                        $hq->whereIn('hajj_id', $candidateHajjIds)
                           ->orWhereIn(DB::raw("LOWER(REPLACE(REPLACE(REPLACE(COALESCE(hajj_id, ''), ' ', ''), '-', ''), '_', ''))"), $cleanIds);
                    });
                }
                if (!empty($candidatePersonIds)) {
                    $q->orWhereIn('id', $candidatePersonIds);
                }
                if (!empty($candidatePassports)) {
                    $q->orWhereIn('passport_number', $candidatePassports);
                }
            })->pluck('id')->toArray();
        }

        return array_values(array_unique(array_merge($personIds, $matchedPersons)));
    }

    /**
     * AJAX Live Lookup for Pilgrims by Haji ID, HB number, Passport, or Name
     */
    public function lookupPilgrims(Request $request)
    {
        $term = trim($request->get('term', ''));
        if (empty($term)) {
            return response()->json(['success' => true, 'pilgrims' => [], 'count' => 0]);
        }

        $personIds = $this->resolvePilgrimsFromInput($term);
        if (empty($personIds)) {
            return response()->json(['success' => true, 'pilgrims' => [], 'count' => 0]);
        }

        $persons = BookingPerson::with(['roomAllocations', 'booking.hotels'])
            ->whereIn('id', $personIds)
            ->get();

        $list = [];
        foreach ($persons as $p) {
            $latestAlloc = $p->roomAllocations->last();
            $bHotel = $p->booking?->hotels?->first();
            $rNum = $latestAlloc?->room_number ?: ($p->room_number ?: ($bHotel?->room_number ?: null));
            $isPending = empty($rNum) || in_array(strtoupper(trim((string)$rNum)), ['PENDING', 'UNASSIGNED', '—', '-']);

            $list[] = [
                'id'              => $p->id,
                'name'            => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                'hajj_id'         => $p->hajj_id ?: '—',
                'hb_number'       => $p->hb_number ?: ($p->booking ? 'HB' . str_pad($p->booking->id, 4, '0', STR_PAD_LEFT) : '—'),
                'passport_number' => $p->passport_number ?: '—',
                'gender'          => $p->gender ?: 'Male',
                'booking_id'      => $p->booking_id,
                'hotel_name'      => $latestAlloc?->hotel_name ?: ($p->hotel_name ?: ($bHotel?->hotel_name ?: 'Anjum Makkah')),
                'room_number'     => $isPending ? '' : $rNum,
                'room_type'       => $latestAlloc?->room_type ?: ($p->room_type ?: ($bHotel?->room_type ? ucfirst($bHotel->room_type) : 'Quad')),
                'location'        => $latestAlloc?->location ?: ($p->location ?: ($bHotel?->location ?: 'makkah')),
                'is_pending'      => $isPending,
            ];
        }

        return response()->json([
            'success'  => true,
            'pilgrims' => $list,
            'count'    => count($list),
        ]);
    }

    /**
     * Assign Selected Pilgrims (via Checkboxes or Haji ID) to a Room
     */
    public function assignRoom(Request $request)
    {
        $request->validate([
            'person_ids'   => 'nullable|array',
            'haji_ids'     => 'nullable|string',
            'hotel_name'   => 'nullable|string|max:191',
            'room_number'  => 'nullable|string|max:100',
            'room_type'    => 'nullable|string|max:100',
            'location'     => 'nullable|string|max:100',
            'gender'       => 'nullable|string|max:50',
        ]);

        // Resolve pilgrims from both haji_ids and person_ids
        $personIds = [];
        if (!empty($request->haji_ids)) {
            $personIds = $this->resolvePilgrimsFromInput($request->haji_ids);
        }
        if (!empty($request->person_ids)) {
            $cbIds = array_values(array_unique(array_filter((array)$request->person_ids)));
            $personIds = array_values(array_unique(array_merge($personIds, $cbIds)));
        }

        if (empty($personIds)) {
            $errMsg = "Please select at least one pilgrim or provide valid Haji ID(s) to assign.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $hotelName  = trim((string)$request->hotel_name);
        $rawRoomNo  = trim((string)$request->room_number);
        $roomNumber = (!empty($rawRoomNo) && !in_array(strtoupper($rawRoomNo), ['PENDING', 'UNASSIGNED', '—', '-'])) ? $rawRoomNo : 'PENDING';
        $roomType   = $request->room_type ? ucfirst(trim($request->room_type)) : 'Quad';
        $location   = $request->location ? strtolower(trim($request->location)) : 'makkah';
        $gender     = $request->gender ? trim($request->gender) : 'Any';

        if (empty($location) || $location === 'unassigned') {
            $location = 'makkah';
        }

        // If hotelName is empty or placeholder, resolve a valid registered hotel
        $isInvalidHotel = empty($hotelName) || in_array(strtolower($hotelName), [
            'pending room allocation',
            'pending allocation',
            'unassigned',
            'unspecified hotel',
            '—',
            '-'
        ]);

        if ($isInvalidHotel) {
            $firstPerson = BookingPerson::with('booking.hotels')->find($personIds[0] ?? 0);
            $matchedBHotel = $firstPerson?->booking?->hotels?->first(fn($h) => strtolower($h->location) === $location && !empty($h->hotel_name));
            if (!$matchedBHotel) {
                $matchedBHotel = $firstPerson?->booking?->hotels?->first(fn($h) => !empty($h->hotel_name));
            }
            if ($matchedBHotel && !empty($matchedBHotel->hotel_name)) {
                $hotelName = $matchedBHotel->hotel_name;
            } else {
                $firstRegHotel = Hotel::where(function($q) use ($location) {
                    $q->whereRaw('LOWER(TRIM(place)) = ?', [$location])
                      ->orWhereRaw('LOWER(TRIM(city)) = ?', [$location]);
                })->first();
                if (!$firstRegHotel) {
                    $firstRegHotel = Hotel::orderBy('name')->first();
                }
                $hotelName = $firstRegHotel ? $firstRegHotel->name : ($location === 'madinah' ? 'NUSK AL HIJRA' : 'Anjum Makkah');
            }
        }

        $updatedCount = 0;
        $batchRoomGroupId = 'rg_' . ($roomNumber === 'PENDING' ? 'pnd_' : 'rm_') . time() . '_' . mt_rand(100, 999);

        foreach ($personIds as $pid) {
            if (is_numeric($pid)) {
                $person = BookingPerson::find($pid);
                if ($person) {
                    // Update or create allocation for this person at this specific location & hotel stage
                    $allocation = BookingPersonRoomAllocation::where('booking_person_id', $person->id)
                        ->where(function ($q) use ($location, $hotelName) {
                            $q->where('location', $location)
                              ->orWhereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower(trim($hotelName))]);
                        })->first();

                    if (!$allocation) {
                        $allocation = new BookingPersonRoomAllocation();
                        $allocation->booking_id = $person->booking_id;
                        $allocation->booking_person_id = $person->id;
                    }

                    $allocation->location      = $location;
                    $allocation->hotel_name    = $hotelName;
                    $allocation->room_number   = $roomNumber;
                    $allocation->room_group_id = $batchRoomGroupId;
                    $allocation->room_type     = $roomType;
                    $allocation->gender        = $gender;
                    $allocation->save();

                    // Clean up any old duplicate or unassigned allocations for this person at this stage
                    BookingPersonRoomAllocation::where('booking_person_id', $person->id)
                        ->where(function ($q) use ($location) {
                            $q->where('location', $location)
                              ->orWhere('location', 'unassigned')
                              ->orWhereNull('location');
                        })
                        ->where('id', '!=', $allocation->id)
                        ->delete();

                    // Sync to person model
                    $person->room_number   = $roomNumber;
                    $person->room_group_id = $batchRoomGroupId;
                    $person->room_type     = $roomType;
                    $person->hotel_name    = $hotelName;
                    $person->location      = $location;
                    $person->room_gender   = $gender;
                    $person->save();

                    $updatedCount++;

                    // Sync/Ensure BookingHotel on this booking
                    $bHotel = BookingHotel::where('booking_id', $person->booking_id)
                        ->where(function ($q) use ($location, $hotelName) {
                            $q->where('location', $location)
                              ->orWhereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower(trim($hotelName))]);
                        })->first();

                    if ($bHotel) {
                        $bHotel->hotel_name  = $hotelName;
                        if (!empty($roomNumber)) {
                            $bHotel->room_number = $roomNumber;
                        }
                        $bHotel->room_type   = strtolower($roomType);
                        $bHotel->gender      = $gender;
                        $bHotel->save();
                    } else {
                        BookingHotel::create([
                            'booking_id'   => $person->booking_id,
                            'location'     => $location,
                            'hotel_name'   => $hotelName,
                            'room_type'    => strtolower($roomType),
                            'room_number'  => $roomNumber,
                            'gender'       => $gender,
                            'no_of_nights' => 1,
                            'no_of_rooms'  => 1,
                        ]);
                    }
                }
            }
        }

        // Maintain capacity in hotel_room_capacities if room number is provided
        if (!empty($roomNumber)) {
            $capRecord = HotelRoomCapacity::whereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower($hotelName)])
                ->whereRaw('LOWER(TRIM(room_number)) = ?', [strtolower($roomNumber)])
                ->first();

            if (!$capRecord) {
                $baseCap = HotelRoomCapacity::resolveCapacity($hotelName, $roomNumber, $roomType, $location);
                $capRecord = new HotelRoomCapacity();
                $capRecord->hotel_name = $hotelName;
                $capRecord->room_number = $roomNumber;
                $capRecord->room_type = $roomType;
                $capRecord->location = $location;
                $capRecord->gender = $gender;
                $capRecord->bed_capacity = max(count($personIds), $baseCap);
                $capRecord->save();
            } elseif ($capRecord->bed_capacity < count($personIds)) {
                $capRecord->bed_capacity = count($personIds);
                $capRecord->save();
            }
        }

        $successMsg = !empty($roomNumber) 
            ? "{$updatedCount} Pilgrim(s) assigned to Room {$roomNumber} ({$hotelName} - {$roomType}) successfully!"
            : "{$updatedCount} Pilgrim(s) assigned to {$hotelName} ({$roomType} - Room: Pending) successfully!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Bulk Unassign Selected Pilgrims from Rooms (Move back to Pending)
     */
    public function unassignRoom(Request $request)
    {
        $personIds     = $request->person_ids ? (array)$request->person_ids : [];
        $allocationIds = $request->allocation_ids ? (array)$request->allocation_ids : [];
        $hotelName     = $request->hotel_name ? trim($request->hotel_name) : null;
        $roomNumber    = $request->room_number ? trim($request->room_number) : null;
        $location      = $request->location ? trim($request->location) : null;

        $unassignedCount = 0;
        $affectedBookings = [];

        if (!empty($allocationIds)) {
            // Unassign by specific allocation IDs
            $allocs = BookingPersonRoomAllocation::whereIn('id', $allocationIds)->get();
            foreach ($allocs as $al) {
                $bId = $al->booking_id;
                $p = BookingPerson::find($al->booking_person_id);
                if ($p && $p->hotel_name === $al->hotel_name && $p->room_number === $al->room_number) {
                    $otherAlloc = BookingPersonRoomAllocation::where('booking_person_id', $p->id)
                        ->where('id', '!=', $al->id)
                        ->latest()
                        ->first();
                    if ($otherAlloc) {
                        $p->room_number   = $otherAlloc->room_number;
                        $p->room_group_id = $otherAlloc->room_group_id;
                        $p->room_type     = $otherAlloc->room_type;
                        $p->hotel_name    = $otherAlloc->hotel_name;
                        $p->location      = $otherAlloc->location;
                        $p->room_gender   = $otherAlloc->gender;
                    } else {
                        $p->room_number   = null;
                        $p->room_group_id = null;
                        $p->room_type     = null;
                        $p->hotel_name    = null;
                        $p->room_gender   = null;
                    }
                    $p->save();
                }
                $al->delete();
                $unassignedCount++;
                if ($bId) $affectedBookings[$bId] = true;
            }
        } elseif (!empty($hotelName) && !empty($roomNumber) && !empty($personIds)) {
            // Unassign specific room & hotel for given pilgrims
            $allocs = BookingPersonRoomAllocation::whereIn('booking_person_id', $personIds)
                ->where('hotel_name', $hotelName)
                ->where('room_number', $roomNumber)
                ->get();
            foreach ($allocs as $al) {
                $bId = $al->booking_id;
                $p = BookingPerson::find($al->booking_person_id);
                if ($p && $p->hotel_name === $al->hotel_name && $p->room_number === $al->room_number) {
                    $otherAlloc = BookingPersonRoomAllocation::where('booking_person_id', $p->id)
                        ->where('id', '!=', $al->id)
                        ->latest()
                        ->first();
                    if ($otherAlloc) {
                        $p->room_number   = $otherAlloc->room_number;
                        $p->room_group_id = $otherAlloc->room_group_id;
                        $p->room_type     = $otherAlloc->room_type;
                        $p->hotel_name    = $otherAlloc->hotel_name;
                        $p->location      = $otherAlloc->location;
                        $p->room_gender   = $otherAlloc->gender;
                    } else {
                        $p->room_number   = null;
                        $p->room_group_id = null;
                        $p->room_type     = null;
                        $p->hotel_name    = null;
                        $p->room_gender   = null;
                    }
                    $p->save();
                }
                $al->delete();
                $unassignedCount++;
                if ($bId) $affectedBookings[$bId] = true;
            }
        } elseif (!empty($personIds)) {
            // Fallback: unassign pilgrims
            foreach ($personIds as $pid) {
                if (is_numeric($pid)) {
                    $person = BookingPerson::find($pid);
                    if ($person) {
                        $bId = $person->booking_id;
                        BookingPersonRoomAllocation::where('booking_person_id', $person->id)->delete();
                        $person->room_number   = null;
                        $person->room_group_id = null;
                        $person->room_type     = null;
                        $person->hotel_name    = null;
                        $person->room_gender   = null;
                        $person->save();
                        $unassignedCount++;
                        if ($bId) $affectedBookings[$bId] = true;
                    }
                }
            }
        }

        // For affected bookings: if no person in this booking has an assigned room, clear room_number on BookingHotel
        foreach (array_keys($affectedBookings) as $bId) {
            $hasAssignedPersons = BookingPersonRoomAllocation::where('booking_id', $bId)
                ->whereNotNull('room_number')
                ->where('room_number', '!=', '')
                ->where('room_number', '!=', 'PENDING')
                ->where('room_number', '!=', 'UNASSIGNED')
                ->exists();

            if (!$hasAssignedPersons) {
                BookingHotel::where('booking_id', $bId)->update(['room_number' => null]);
            }
        }

        $successMsg = "{$unassignedCount} Pilgrim allocation(s) moved to Pending Room Allocation.";
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return back()->with('success', $successMsg);
    }

    /**
     * Shared filter logic for Web view, PDF export, and Excel export
     * (Includes ALL pilgrims from ALL active bookings)
     */
    private function getFilteredRoomingData(Request $request): array
    {
        $rawHotelFilter = $request->get('hotel_name');
        if (is_array($rawHotelFilter)) {
            $hotelFilter = array_values(array_filter(array_map('trim', $rawHotelFilter)));
        } elseif (!empty($rawHotelFilter) && $rawHotelFilter !== 'all') {
            $hotelFilter = [trim($rawHotelFilter)];
        } else {
            $hotelFilter = [];
        }

        $cleanHotelFilter = [];
        foreach ($hotelFilter as $hf) {
            $cleanHf = trim(preg_replace('/\s*\([^)]*\)/', '', (string)$hf));
            if (!empty($cleanHf)) {
                $cleanHotelFilter[] = $cleanHf;
            }
            $cleanHotelFilter[] = $hf;
        }
        $cleanHotelFilter = array_values(array_unique($cleanHotelFilter));

        $hotelMatchesFilter = function (?string $name) use ($cleanHotelFilter): bool {
            if (empty($cleanHotelFilter)) {
                return true;
            }
            if (empty($name)) {
                return false;
            }
            foreach ($cleanHotelFilter as $hf) {
                if (stripos($name, $hf) !== false) {
                    return true;
                }
            }
            return false;
        };

        $locationFilter = $request->get('location');
        $targetLoc      = (!empty($locationFilter) && $locationFilter !== 'all') ? strtolower(trim($locationFilter)) : null;
        $roomTypeFilter = $request->get('room_type');
        $statusFilter   = $request->get('status'); // all, available, full, overbooked, unassigned
        $ticketStatus   = $request->get('ticket_status', 'all'); // on_ticket, without_ticket
        $search         = $request->get('search');
        $fromDate       = $request->get('from_date');
        $toDate         = $request->get('to_date');
        $viewMode       = $request->get('view', 'manifest');

        // Query active bookings
        $bookingQuery = Booking::with([
            'client',
            'company',
            'persons.roomAllocations',
            'hotels'
        ])->whereNull('deleted_at')
          ->whereNotIn('status', ['cancelled', 'rejected']);

        if (!empty($ticketStatus) && $ticketStatus !== 'all') {
            if ($ticketStatus === 'without_ticket') {
                $bookingQuery->whereHas('persons', function ($pq) {
                    $pq->where('ticket_status', 'without_ticket');
                });
            } elseif ($ticketStatus === 'on_ticket') {
                $bookingQuery->whereHas('persons', function ($pq) {
                    $pq->where(function ($q) {
                        $q->where('ticket_status', 'on_ticket')
                          ->orWhereNull('ticket_status')
                          ->orWhere('ticket_status', '');
                    });
                });
            }
        }

        if (!empty($cleanHotelFilter)) {
            $bookingQuery->where(function ($bq) use ($cleanHotelFilter) {
                $bq->whereHas('hotels', function ($hq) use ($cleanHotelFilter) {
                    $hq->where(function ($q) use ($cleanHotelFilter) {
                        foreach ($cleanHotelFilter as $hf) {
                            $q->orWhere('hotel_name', 'LIKE', '%' . $hf . '%');
                        }
                    });
                })->orWhereHas('persons.roomAllocations', function ($raq) use ($cleanHotelFilter) {
                    $raq->where(function ($q) use ($cleanHotelFilter) {
                        foreach ($cleanHotelFilter as $hf) {
                            $q->orWhere('hotel_name', 'LIKE', '%' . $hf . '%');
                        }
                    });
                });
            });
        }

        if (!empty($targetLoc)) {
            $bookingQuery->where(function ($bq) use ($targetLoc) {
                $bq->whereHas('hotels', function ($hq) use ($targetLoc) {
                    $hq->whereRaw('LOWER(location) = ?', [$targetLoc]);
                })->orWhereHas('persons.roomAllocations', function ($raq) use ($targetLoc) {
                    $raq->whereRaw('LOWER(location) = ?', [$targetLoc]);
                });
            });
        }

        if (!empty($search)) {
            $term = '%' . $search . '%';
            $bookingQuery->where(function ($bq) use ($term) {
                $bq->where('voucher_number', 'LIKE', $term)
                   ->orWhere('passport_number', 'LIKE', $term)
                   ->orWhereHas('client', function ($cq) use ($term) {
                       $cq->where('name', 'LIKE', $term)
                          ->orWhere('passport_number', 'LIKE', $term);
                   })
                   ->orWhereHas('persons', function ($pq) use ($term) {
                       $pq->where('full_name', 'LIKE', $term)
                          ->orWhere('passport_number', 'LIKE', $term)
                          ->orWhere('cnic', 'LIKE', $term)
                          ->orWhere('hajj_id', 'LIKE', $term)
                          ->orWhere('hb_number', 'LIKE', $term)
                          ->orWhere('room_number', 'LIKE', $term)
                          ->orWhereHas('roomAllocations', function ($raq) use ($term) {
                              $raq->where('room_number', 'LIKE', $term)
                                 ->orWhere('hotel_name', 'LIKE', $term);
                          });
                   })
                   ->orWhereHas('hotels', function ($hq) use ($term) {
                       $hq->where('room_number', 'LIKE', $term)
                          ->orWhere('hotel_name', 'LIKE', $term);
                   });
            });
        }

        // Package Filter (Multi-Select or Single)
        $rawPackageFilter = $request->get('package_id');
        if (is_array($rawPackageFilter)) {
            $packageFilter = array_values(array_filter(array_map('trim', $rawPackageFilter)));
        } elseif (!empty($rawPackageFilter) && $rawPackageFilter !== 'all') {
            $packageFilter = [trim($rawPackageFilter)];
        } else {
            $packageFilter = [];
        }

        if (!empty($packageFilter)) {
            $bookingQuery->whereIn('package_id', $packageFilter);
        }

        // Date-wise Filter (Departure/Arrival, Hotel Check-in/out, or Booking Date)
        if (!empty($fromDate)) {
            $bookingQuery->where(function ($q) use ($fromDate) {
                $q->whereDate('departure_date', '>=', $fromDate)
                  ->orWhereDate('created_at', '>=', $fromDate)
                  ->orWhereHas('hotels', function ($hq) use ($fromDate) {
                      $hq->whereDate('check_in', '>=', $fromDate);
                  });
            });
        }
        if (!empty($toDate)) {
            $bookingQuery->where(function ($q) use ($toDate) {
                $q->whereDate('arrival_date', '<=', $toDate)
                  ->orWhereDate('departure_date', '<=', $toDate)
                  ->orWhereDate('created_at', '<=', $toDate)
                  ->orWhereHas('hotels', function ($hq) use ($toDate) {
                      $hq->whereDate('check_out', '<=', $toDate);
                  });
            });
        }

        $allBookings = $bookingQuery->orderBy('id', 'asc')->get();

        $groupedRooms = [];
        $unassignedOccupants = [];
        $alreadyAddedPersonIds = []; // Track persons added to prevent duplicates

        $addGroupedRoomOccupant = function (
            array &$groupedRooms,
            array $occData,
            string $displayRoomNo,
            string $hName,
            string $loc,
            string $rType,
            string $rGender,
            $cIn,
            $cOut,
            $bId,
            $p,
            $roomGroupId = null
        ) {
            $isPendingRoom = (strtoupper(trim($displayRoomNo)) === 'PENDING' || empty($displayRoomNo));
            $cleanDisplayNo = $isPendingRoom ? 'PENDING' : $displayRoomNo;
            $occData['room_number'] = $cleanDisplayNo;
            $occData['hotel_name'] = $hName;
            $occData['location'] = $loc;
            $occData['room_type'] = $rType;
            $occData['is_assigned'] = true;

            if ($isPendingRoom) {
                $groupTag = !empty($roomGroupId) ? $roomGroupId : ('b_' . $bId . '_' . strtolower($rType));
                $groupKey = strtolower($loc . '___' . $hName . '___pending___' . $groupTag);
            } else {
                $groupKey = strtolower($loc . '___' . $hName . '___' . $cleanDisplayNo);
            }

            if (!isset($groupedRooms[$groupKey])) {
                $baseCap = HotelRoomCapacity::resolveCapacity($hName, $cleanDisplayNo, $rType, $loc);
                $customCap = null;
                if (!$isPendingRoom) {
                    $customCap = HotelRoomCapacity::where(function ($q) use ($cleanDisplayNo) {
                        foreach (HotelRoomCapacity::normalizeRoomNumber($cleanDisplayNo) as $v) {
                            $q->orWhereRaw('LOWER(TRIM(room_number)) = ?', [$v]);
                        }
                    })->first();
                }

                $extraBeds = $customCap ? (int) $customCap->extra_beds : 0;
                $effectiveCap = max(1, $baseCap);
                $roomGender = !empty($customCap->gender) && $customCap->gender !== 'Any' ? $customCap->gender : $rGender;

                $groupedRooms[$groupKey] = [
                    'hotel_name'      => $hName,
                    'room_number'     => $cleanDisplayNo,
                    'room_type'       => $rType,
                    'room_gender'     => $roomGender,
                    'location'        => $loc,
                    'is_assigned'     => true,
                    'capacity_id'     => $customCap ? $customCap->id : null,
                    'base_capacity'   => $baseCap,
                    'extra_beds'      => $extraBeds,
                    'total_capacity'  => $effectiveCap,
                    'occupied_beds'   => 0,
                    'available_beds'  => $effectiveCap,
                    'is_full'         => false,
                    'is_overbooked'   => false,
                    'notes'           => $customCap ? $customCap->notes : null,
                    'occupants'       => [],
                    'check_in_min'    => $cIn,
                    'check_out_max'   => $cOut,
                    'booking_ids'     => [],
                    'latest_activity' => 0,
                ];
            }

            $groupedRooms[$groupKey]['occupied_beds']++;
            if (!in_array($bId, $groupedRooms[$groupKey]['booking_ids'])) {
                $groupedRooms[$groupKey]['booking_ids'][] = $bId;
            }
            $groupedRooms[$groupKey]['occupants'][] = $occData;

            $pActivity = $p->updated_at ? $p->updated_at->timestamp : ($p->created_at ? $p->created_at->timestamp : 0);
            if ($pActivity > ($groupedRooms[$groupKey]['latest_activity'] ?? 0)) {
                $groupedRooms[$groupKey]['latest_activity'] = $pActivity;
            }
        };

        $isFilteredByHotelOrLoc = (!empty($cleanHotelFilter) || !empty($targetLoc));

        foreach ($allBookings as $b) {
            $persons = $b->persons;
            
            // If booking has no persons created yet, generate virtual representation
            if (!$persons || $persons->count() === 0) {
                $pax = max(1, (int) ($b->no_of_pax ?: 1));
                $persons = collect();
                for ($i = 1; $i <= $pax; $i++) {
                    $p = new BookingPerson([
                        'booking_id'      => $b->id,
                        'full_name'       => ($b->client->name ?? 'Guest') . ($pax > 1 ? " (#{$i})" : ''),
                        'passport_number' => $b->passport_number ?: '—',
                        'gender'          => 'Male',
                        'hb_number'       => 'HB' . str_pad($b->id, 4, '0', STR_PAD_LEFT),
                    ]);
                    $p->id = 'temp_' . $b->id . '_' . $i;
                    $persons->push($p);
                }
            }

            if (!empty($ticketStatus) && $ticketStatus !== 'all') {
                $persons = $persons->filter(function ($p) use ($ticketStatus) {
                    $pTicket = !empty($p->ticket_status) ? $p->ticket_status : 'on_ticket';
                    return $pTicket === $ticketStatus;
                });
                if ($persons->isEmpty()) {
                    continue;
                }
            }

            $bHotels = $b->hotels ?? collect();
            $bHotelFirst = $bHotels->first();

            // Determine Booked Room Type & default hotel from booking
            $bookedRoomType = 'Quad';
            if ($bHotelFirst && !empty($bHotelFirst->room_type)) {
                $bookedRoomType = ucfirst(trim($bHotelFirst->room_type));
            } elseif (!empty($b->package?->room_type)) {
                $bookedRoomType = ucfirst(trim($b->package->room_type));
            } else {
                $pax = max(1, (int) ($b->no_of_pax ?: 1));
                $bookedRoomType = match($pax) {
                    1 => 'Single',
                    2 => 'Double',
                    3 => 'Triple',
                    4 => 'Quad',
                    5 => 'Quint',
                    6 => 'Six / Sharing',
                    default => 'Sharing (' . $pax . ' Beds)',
                };
            }
            if (in_array(strtolower($bookedRoomType), ['six', 'sharing', '6', 'six-bed', '6-bed'])) {
                $bookedRoomType = 'Six / Sharing';
            }

            if ($isFilteredByHotelOrLoc) {
                // Find relevant hotel stage for this booking matching filter
                $stageHotel = null;
                if (!empty($cleanHotelFilter)) {
                    $stageHotel = $bHotels->first(function ($bh) use ($hotelMatchesFilter, $targetLoc) {
                        if (!$hotelMatchesFilter($bh->hotel_name)) return false;
                        if (!empty($targetLoc) && strtolower(trim($bh->location ?? '')) !== $targetLoc) return false;
                        return true;
                    });
                } elseif (!empty($targetLoc)) {
                    $stageHotel = $bHotels->first(function ($bh) use ($targetLoc) {
                        $bhLoc = strtolower(trim($bh->location ?? ''));
                        if ($bhLoc === $targetLoc) return true;
                        if ($targetLoc === 'madinah' && (str_contains($bhLoc, 'madina') || stripos($bh->hotel_name ?? '', 'madina') !== false || stripos($bh->hotel_name ?? '', 'hijra') !== false)) {
                            return true;
                        }
                        return false;
                    });
                }

                $uHotelName = $stageHotel?->hotel_name ?: (!empty($cleanHotelFilter) ? $cleanHotelFilter[0] : 'Unspecified Hotel');
                $uLoc = $stageHotel?->location ?: (!empty($targetLoc) ? $targetLoc : 'makkah');
                $uCheckIn = $stageHotel?->check_in;
                $uCheckOut = $stageHotel?->check_out;
                $uRoomType = $stageHotel?->room_type ? ucfirst($stageHotel->room_type) : $bookedRoomType;

                foreach ($persons as $p) {
                    $hajjId = !empty($p->hajj_id) ? trim($p->hajj_id) : '';
                    $hbNumber = !empty($p->hb_number) ? BookingPerson::formatHbNumber($p->hb_number) : '';
                    $gender = !empty($p->gender) ? ucfirst(strtolower($p->gender)) : 'Male';

                    if (empty($p->gender) || $p->gender === 'Male') {
                        $fCheck = strtolower($p->full_name . ' ' . $p->given_name . ' ' . $p->surname);
                        if (preg_match('/\b(bibi|khatoon|fatima|ayesha|aisha|maryam|begum|bano|naz|uzma|zahra|zainab|amna|rabia|samina|sumaira|hira|sana|madiha|tooba|javeria|rubab|shagufta|roshan|safia)\b/i', $fCheck)) {
                            $gender = 'Female';
                        }
                    }

                    $allocations = $p->roomAllocations ?? collect();
                    if ($allocations->isEmpty() && (!empty($p->room_number) || !empty($p->hotel_name))) {
                        $allocations = collect([(object)[
                            'id'          => null,
                            'hotel_name'  => $p->hotel_name,
                            'room_number' => $p->room_number,
                            'room_type'   => $p->room_type,
                            'location'    => $p->location ?: 'makkah',
                            'gender'      => $p->room_gender ?: 'Any',
                            'check_in'    => null,
                            'check_out'   => null,
                        ]]);
                    }

                    // Collect all active stage assignments for this person
                    $stageAssignments = [];
                    foreach ($allocations as $ra) {
                        $locK = strtolower(trim($ra->location ?? 'makkah'));
                        if (!empty($ra->hotel_name) && !in_array(strtolower(trim($ra->hotel_name)), ['pending room allocation', 'unassigned', 'unspecified hotel', '—', '-'])) {
                            $stageAssignments[$locK] = [
                                'location'    => $ra->location ?: $locK,
                                'hotel_name'  => $ra->hotel_name,
                                'room_number' => $ra->room_number ?: 'PENDING',
                                'room_type'   => $ra->room_type ?: '',
                            ];
                        }
                    }

                    $matchedAlloc = $allocations->first(function ($ra) use ($targetLoc, $cleanHotelFilter, $hotelMatchesFilter) {
                        if (!empty($targetLoc)) {
                            $raLoc = strtolower(trim($ra->location ?? ''));
                            if ($raLoc !== $targetLoc) return false;
                        }
                        if (!empty($cleanHotelFilter)) {
                            if (!$hotelMatchesFilter($ra->hotel_name ?? '')) return false;
                        }
                        return true;
                    });

                    $hasValidAlloc = $matchedAlloc && !empty($matchedAlloc->hotel_name)
                        && !in_array(strtolower(trim($matchedAlloc->hotel_name)), ['pending room allocation', 'unassigned', 'unspecified hotel', '—', '-'])
                        && strtoupper(trim((string)$matchedAlloc->room_number)) !== 'UNASSIGNED';

                    $rawRoomNo = !empty($matchedAlloc?->room_number) ? trim($matchedAlloc->room_number) : null;
                    $rNum = (!empty($rawRoomNo) && !in_array(strtoupper($rawRoomNo), ['PENDING', 'UNASSIGNED', '—', '-'])) ? $rawRoomNo : 'PENDING';

                    $hName = $matchedAlloc?->hotel_name ?: $uHotelName;
                    $rType = ucfirst($matchedAlloc?->room_type ?: $uRoomType);
                    $loc = $matchedAlloc?->location ?: $uLoc;
                    $rGender = !empty($matchedAlloc?->gender) ? trim($matchedAlloc->gender) : 'Any';
                    $cIn = $matchedAlloc?->check_in ?? $uCheckIn;
                    $cOut = $matchedAlloc?->check_out ?? $uCheckOut;
                    $roomGroupId = $matchedAlloc?->room_group_id ?? ($p->room_group_id ?? null);

                    $occData = [
                        'allocation_id'     => $matchedAlloc?->id ?? null,
                        'person_id'         => $p->id,
                        'real_person_id'    => is_numeric($p->id) ? (int)$p->id : null,
                        'hajj_id'           => $hajjId,
                        'hb_number'         => $hbNumber,
                        'name'              => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                        'given_name'        => $p->given_name ?? null,
                        'surname'           => $p->surname ?? null,
                        'father_name'       => $p->father_name ?? null,
                        'dob'               => $p->dob ? date('d M Y', strtotime($p->dob)) : null,
                        'passport'          => $p->passport_number ?: '—',
                        'date_of_issue'     => ($p->date_of_issue ?: $p->passport_issue_date) ? date('d M Y', strtotime($p->date_of_issue ?: $p->passport_issue_date)) : null,
                        'passport_expiry_date' => $p->passport_expiry_date ? date('d M Y', strtotime($p->passport_expiry_date)) : null,
                        'gender'            => $gender,
                        'photo'             => $p->photo,
                        'cnic'              => $p->cnic,
                        'phone'             => $p->phone ?: $b->phone,
                        'booking_id'        => $b->id,
                        'booking_number'    => $b->booking_number ?? ('#BK-' . $b->id),
                        'client_name'       => $b->client->name ?? ($b->company->company_name ?? ($b->company->name ?? 'N/A')),
                        'check_in'          => $cIn ? date('d M Y', strtotime($cIn)) : '—',
                        'check_out'         => $cOut ? date('d M Y', strtotime($cOut)) : '—',
                        'room_type'         => $hasValidAlloc ? $rType : '',
                        'booked_room_type'  => $uRoomType,
                        'booked_hotel_name' => $uHotelName,
                        'booking_pax'       => $b->no_of_pax ?: 1,
                        'room_number'       => $hasValidAlloc ? $rNum : null,
                        'hotel_name'        => $hasValidAlloc ? $hName : ($stageHotel?->hotel_name ?: $uHotelName),
                        'location'          => $loc,
                        'ticket_status'     => !empty($p->ticket_status) ? $p->ticket_status : 'on_ticket',
                        'is_assigned'       => $hasValidAlloc,
                        'stage_assignments' => $stageAssignments,
                    ];

                    if (!$hasValidAlloc) {
                        // Prevent duplicate unassigned entries for same person across allocations
                        $personKey = (int)$p->id;
                        if (!in_array($personKey, $alreadyAddedPersonIds)) {
                            $alreadyAddedPersonIds[] = $personKey;
                            $unassignedOccupants[] = $occData;
                        }
                    } else {
                        $addGroupedRoomOccupant($groupedRooms, $occData, $rNum, $hName, $loc, $rType, $rGender, $cIn, $cOut, $b->id, $p, $roomGroupId);
                    }
                }
            } else {
                // All Hotels & All Locations
                foreach ($persons as $p) {
                    $hajjId = !empty($p->hajj_id) ? trim($p->hajj_id) : '';
                    $hbNumber = !empty($p->hb_number) ? BookingPerson::formatHbNumber($p->hb_number) : '';
                    $gender = !empty($p->gender) ? ucfirst(strtolower($p->gender)) : 'Male';

                    if (empty($p->gender) || $p->gender === 'Male') {
                        $fCheck = strtolower($p->full_name . ' ' . $p->given_name . ' ' . $p->surname);
                        if (preg_match('/\b(bibi|khatoon|fatima|ayesha|aisha|maryam|begum|bano|naz|uzma|zahra|zainab|amna|rabia|samina|sumaira|hira|sana|madiha|tooba|javeria|rubab|shagufta|roshan|safia)\b/i', $fCheck)) {
                            $gender = 'Female';
                        }
                    }

                    $allocations = $p->roomAllocations ?? collect();
                    if ($allocations->isEmpty() && (!empty($p->room_number) || !empty($p->hotel_name))) {
                        $allocations = collect([(object)[
                            'id'          => null,
                            'hotel_name'  => $p->hotel_name,
                            'room_number' => $p->room_number,
                            'room_type'   => $p->room_type,
                            'location'    => $p->location ?: 'makkah',
                            'gender'      => $p->room_gender ?: 'Any',
                            'check_in'    => null,
                            'check_out'   => null,
                        ]]);
                    }

                    if ($allocations->isEmpty()) {
                        $uLoc = $bHotelFirst?->location ?: 'makkah';
                        $uHotelName = $bHotelFirst?->hotel_name ?: 'Anjum Makkah';
                        $uRoomType = $bHotelFirst?->room_type ? ucfirst($bHotelFirst->room_type) : $bookedRoomType;

                        $occData = [
                            'allocation_id'     => null,
                            'person_id'         => $p->id,
                            'real_person_id'    => is_numeric($p->id) ? (int)$p->id : null,
                            'hajj_id'           => $hajjId,
                            'hb_number'         => $hbNumber,
                            'name'              => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                            'given_name'        => $p->given_name ?? null,
                            'surname'           => $p->surname ?? null,
                            'father_name'       => $p->father_name ?? null,
                            'dob'               => $p->dob ? date('d M Y', strtotime($p->dob)) : null,
                            'passport'          => $p->passport_number ?: '—',
                            'date_of_issue'     => ($p->date_of_issue ?: $p->passport_issue_date) ? date('d M Y', strtotime($p->date_of_issue ?: $p->passport_issue_date)) : null,
                            'passport_expiry_date' => $p->passport_expiry_date ? date('d M Y', strtotime($p->passport_expiry_date)) : null,
                            'gender'            => $gender,
                            'photo'             => $p->photo,
                            'cnic'              => $p->cnic,
                            'phone'             => $p->phone ?: $b->phone,
                            'booking_id'        => $b->id,
                            'booking_number'    => $b->booking_number ?? ('#BK-' . $b->id),
                            'client_name'       => $b->client->name ?? ($b->company->company_name ?? ($b->company->name ?? 'N/A')),
                            'check_in'          => $bHotelFirst?->check_in ? date('d M Y', strtotime($bHotelFirst->check_in)) : '—',
                            'check_out'         => $bHotelFirst?->check_out ? date('d M Y', strtotime($bHotelFirst->check_out)) : '—',
                            'room_type'         => '',
                            'booked_room_type'  => $uRoomType,
                            'booked_hotel_name' => $uHotelName,
                            'booking_pax'       => $b->no_of_pax ?: 1,
                            'room_number'       => null,
                            'hotel_name'        => $uHotelName,
                            'location'          => $uLoc,
                            'ticket_status'     => !empty($p->ticket_status) ? $p->ticket_status : 'on_ticket',
                            'is_assigned'       => false,
                        ];
                        $personKey = (int)$p->id;
                        if (!in_array($personKey, $alreadyAddedPersonIds)) {
                            $alreadyAddedPersonIds[] = $personKey;
                            $unassignedOccupants[] = $occData;
                        }
                    } else {
                        // Collect all active stage assignments for this person
                        $stageAssignments = [];
                        foreach ($allocations as $ra) {
                            $locK = strtolower(trim($ra->location ?? 'makkah'));
                            if (!empty($ra->hotel_name) && !in_array(strtolower(trim($ra->hotel_name)), ['pending room allocation', 'unassigned', 'unspecified hotel', '—', '-'])) {
                                $stageAssignments[$locK] = [
                                    'location'    => $ra->location ?: $locK,
                                    'hotel_name'  => $ra->hotel_name,
                                    'room_number' => $ra->room_number ?: 'PENDING',
                                    'room_type'   => $ra->room_type ?: '',
                                ];
                            }
                        }

                        // For 'all locations' view: pick the primary stage allocation to display so each person appears only ONCE
                        $alloc = $allocations->first(fn($a) => strtolower(trim($a->location ?? '')) === 'makkah')
                            ?: $allocations->first(fn($a) => strtolower(trim($a->location ?? '')) === 'azizia')
                            ?: $allocations->first(fn($a) => strtolower(trim($a->location ?? '')) === 'madinah')
                            ?: $allocations->first();

                        $hasValidAlloc = $alloc && !empty($alloc->hotel_name)
                            && !in_array(strtolower(trim($alloc->hotel_name)), ['pending room allocation', 'unassigned', 'unspecified hotel', '—', '-'])
                            && strtoupper(trim((string)$alloc->room_number)) !== 'UNASSIGNED';

                        $rawRoomNo = !empty($alloc?->room_number) ? trim($alloc->room_number) : null;
                        $rNum = (!empty($rawRoomNo) && !in_array(strtoupper($rawRoomNo), ['PENDING', 'UNASSIGNED', '—', '-'])) ? $rawRoomNo : 'PENDING';

                        $hName = !empty($alloc?->hotel_name) ? trim($alloc->hotel_name) : null;
                        $loc = !empty($alloc?->location) ? trim($alloc->location) : 'makkah';
                        $rType = !empty($alloc?->room_type) ? trim($alloc->room_type) : null;
                        $rGender = !empty($alloc?->gender) ? trim($alloc->gender) : 'Any';
                        $roomGroupId = $alloc?->room_group_id ?? ($p->room_group_id ?? null);

                        $matchedHotel = null;
                        if ($bHotels->count() > 0) {
                            $matchedHotel = $bHotels->first(function ($bh) use ($loc, $hName) {
                                return (!empty($hName) && stripos($bh->hotel_name, $hName) !== false) ||
                                       (strtolower($bh->location) === strtolower($loc));
                            });
                            if (!$matchedHotel) {
                                $matchedHotel = $bHotelFirst;
                            }
                        }

                        $cIn = $alloc?->check_in ?? ($matchedHotel?->check_in);
                        $cOut = $alloc?->check_out ?? ($matchedHotel?->check_out);
                        $rType = $rType ?: ($matchedHotel?->room_type ? ucfirst($matchedHotel->room_type) : $bookedRoomType);
                        $hName = $hName ?: ($matchedHotel?->hotel_name ?: 'Unspecified Hotel');

                        $occData = [
                            'allocation_id'     => $alloc?->id ?? null,
                            'person_id'         => $p->id,
                            'real_person_id'    => is_numeric($p->id) ? (int)$p->id : null,
                            'hajj_id'           => $hajjId,
                            'hb_number'         => $hbNumber,
                            'name'              => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                            'given_name'        => $p->given_name ?? null,
                            'surname'           => $p->surname ?? null,
                            'father_name'       => $p->father_name ?? null,
                            'dob'               => $p->dob ? date('d M Y', strtotime($p->dob)) : null,
                            'passport'          => $p->passport_number ?: '—',
                            'date_of_issue'     => ($p->date_of_issue ?: $p->passport_issue_date) ? date('d M Y', strtotime($p->date_of_issue ?: $p->passport_issue_date)) : null,
                            'passport_expiry_date' => $p->passport_expiry_date ? date('d M Y', strtotime($p->passport_expiry_date)) : null,
                            'gender'            => $gender,
                            'photo'             => $p->photo,
                            'cnic'              => $p->cnic,
                            'phone'             => $p->phone ?: $b->phone,
                            'booking_id'        => $b->id,
                            'booking_number'    => $b->booking_number ?? ('#BK-' . $b->id),
                            'client_name'       => $b->client->name ?? ($b->company->company_name ?? ($b->company->name ?? 'N/A')),
                            'check_in'          => $cIn ? date('d M Y', strtotime($cIn)) : '—',
                            'check_out'         => $cOut ? date('d M Y', strtotime($cOut)) : '—',
                            'room_type'         => $hasValidAlloc ? $rType : '',
                            'booked_room_type'  => $rType,
                            'booked_hotel_name' => $hName,
                            'booking_pax'       => $b->no_of_pax ?: 1,
                            'room_number'       => $hasValidAlloc ? $rNum : null,
                            'hotel_name'        => $hName,
                            'location'          => $loc,
                            'ticket_status'     => !empty($p->ticket_status) ? $p->ticket_status : 'on_ticket',
                            'is_assigned'       => $hasValidAlloc,
                            'stage_assignments' => $stageAssignments,
                        ];

                        $personKey = (int)$p->id;
                        if (!$hasValidAlloc) {
                            // Prevent duplicate unassigned entries for same person
                            if (!in_array($personKey, $alreadyAddedPersonIds)) {
                                $alreadyAddedPersonIds[] = $personKey;
                                $unassignedOccupants[] = $occData;
                            }
                        } else {
                            if (!in_array($personKey, $alreadyAddedPersonIds)) {
                                $alreadyAddedPersonIds[] = $personKey;
                                $addGroupedRoomOccupant($groupedRooms, $occData, $rNum, $hName, $loc, $rType, $rGender, $cIn, $cOut, $b->id, $p, $roomGroupId);
                            }
                        }
                    }
                }
            }
        }

        // Apply filters to assigned rooms
        if (!empty($cleanHotelFilter)) {
            $groupedRooms = array_filter($groupedRooms, function ($r) use ($hotelMatchesFilter) {
                return $hotelMatchesFilter($r['hotel_name'] ?? '');
            });
        }
        if (!empty($targetLoc)) {
            $groupedRooms = array_filter($groupedRooms, fn($r) => strtolower($r['location']) === $targetLoc);
        }
        if (!empty($roomTypeFilter) && $roomTypeFilter !== 'all') {
            $cleanType = strtolower(trim($roomTypeFilter));
            $groupedRooms = array_filter($groupedRooms, function ($r) use ($cleanType) {
                $rt = strtolower(trim($r['room_type']));
                if ($cleanType === 'six') return in_array($rt, ['six', 'sharing']);
                return $rt === $cleanType;
            });
        }

        // Finalize occupancy flags & stats for assigned rooms
        $totalRoomsInUse = 0;
        $totalBedsCapacity = 0;
        $totalBedsOccupied = 0;
        $totalBedsAvailable = 0;
        $fullRoomsCount = 0;
        $partialRoomsCount = 0;
        $overbookedRoomsCount = 0;

        foreach ($groupedRooms as $key => &$room) {
            $cap = $room['total_capacity'];
            $occ = $room['occupied_beds'];

            $room['available_beds'] = max(0, $cap - $occ);
            $room['is_full'] = ($occ >= $cap && $cap > 0);
            $room['is_overbooked'] = ($occ > $cap);

            $totalRoomsInUse++;
            $totalBedsCapacity += $cap;
            $totalBedsOccupied += $occ;
            $totalBedsAvailable += $room['available_beds'];

            if ($room['is_overbooked']) {
                $overbookedRoomsCount++;
            } elseif ($room['is_full']) {
                $fullRoomsCount++;
            } else {
                $partialRoomsCount++;
            }
        }
        unset($room);

        $assignedRoomsOnly = array_filter($groupedRooms, function ($room) {
            return !empty($room['is_assigned']);
        });
        $totalAssignedRoomsCount = count($assignedRoomsOnly);

        // Filter by Status if requested
        if (!empty($statusFilter) && $statusFilter !== 'all') {
            if ($statusFilter === 'unassigned') {
                $groupedRooms = [];
            } elseif ($statusFilter === 'assigned') {
                $groupedRooms = $assignedRoomsOnly;
            } else {
                $groupedRooms = array_filter($groupedRooms, function ($room) use ($statusFilter) {
                    if ($statusFilter === 'available') {
                        return !$room['is_full'] && !$room['is_overbooked'];
                    } elseif ($statusFilter === 'full') {
                        return $room['is_full'] && !$room['is_overbooked'];
                    } elseif ($statusFilter === 'overbooked') {
                        return $room['is_overbooked'];
                    }
                    return true;
                });
            }
        }

        // Sort assigned rooms so the earliest created booking (e.g. HB0005) is at the TOP, matching Booking Index
        uasort($groupedRooms, function ($a, $b) {
            $minBkA = !empty($a['booking_ids']) ? min($a['booking_ids']) : PHP_INT_MAX;
            $minBkB = !empty($b['booking_ids']) ? min($b['booking_ids']) : PHP_INT_MAX;
            if ($minBkA !== $minBkB) {
                return $minBkA <=> $minBkB;
            }
            return strnatcasecmp($a['room_number'] ?? '', $b['room_number'] ?? '');
        });

        // Ensure occupants inside each assigned room are sorted by booking ID ascending
        foreach ($groupedRooms as &$gRoom) {
            if (!empty($gRoom['occupants'])) {
                usort($gRoom['occupants'], function ($oa, $ob) {
                    $bA = (int) ($oa['booking_id'] ?? 0);
                    $bB = (int) ($ob['booking_id'] ?? 0);
                    if ($bA !== $bB) {
                        return $bA <=> $bB;
                    }
                    return (int)($oa['person_id'] ?? 0) <=> (int)($ob['person_id'] ?? 0);
                });
            }
        }
        unset($gRoom);

        // Apply filters to unassigned occupants so they strictly match selected criteria
        if (!empty($cleanHotelFilter)) {
            $unassignedOccupants = array_values(array_filter($unassignedOccupants, function ($occ) use ($hotelMatchesFilter) {
                $occHotel = $occ['hotel_name'] ?? ($occ['booked_hotel_name'] ?? '');
                return $hotelMatchesFilter($occHotel);
            }));
        }
        if (!empty($targetLoc)) {
            $unassignedOccupants = array_values(array_filter($unassignedOccupants, function ($occ) use ($targetLoc) {
                return strtolower($occ['location'] ?? '') === $targetLoc;
            }));
        }
        if (!empty($roomTypeFilter) && $roomTypeFilter !== 'all') {
            $cleanType = strtolower(trim($roomTypeFilter));
            $unassignedOccupants = array_values(array_filter($unassignedOccupants, function ($occ) use ($cleanType) {
                $rt = strtolower(trim($occ['room_type'] ?? ($occ['booked_room_type'] ?? '')));
                if ($cleanType === 'six') return in_array($rt, ['six', 'sharing']);
                return $rt === $cleanType;
            }));
        }

        // Sort unassigned occupants by booking ID / person ID ascending so earliest booking (HB0005) is at top
        usort($unassignedOccupants, function ($oa, $ob) {
            $bA = (int) ($oa['booking_id'] ?? 0);
            $bB = (int) ($ob['booking_id'] ?? 0);
            if ($bA !== $bB) {
                return $bA <=> $bB;
            }
            return (int)($oa['person_id'] ?? 0) <=> (int)($ob['person_id'] ?? 0);
        });

        $unassignedPilgrimsCount = count($unassignedOccupants);

        // If there are unassigned pilgrims, append them after assigned rooms grouped by booking + room type
        if ($unassignedPilgrimsCount > 0 && (empty($statusFilter) || $statusFilter === 'all' || $statusFilter === 'unassigned')) {
            $pendingByBooking = [];
            foreach ($unassignedOccupants as $uOcc) {
                $bId = $uOcc['booking_id'] ?? 0;
                $uType = $uOcc['booked_room_type'] ?? ($uOcc['room_type'] ?? 'Quad');
                $uLoc = $uOcc['location'] ?? 'makkah';
                $uHotel = $uOcc['booked_hotel_name'] ?? ($uOcc['hotel_name'] ?? 'Pending Room Allocation');
                $subKey = '__pending_b_' . $bId;
                if (!isset($pendingByBooking[$subKey])) {
                    $pendingByBooking[$subKey] = [
                        'hotel_name'      => $uHotel,
                        'room_number'     => 'PENDING',
                        'room_type'       => '', // BLANK until user explicitly allocates room
                        'is_assigned'     => false,
                        'room_gender'     => 'Any',
                        'location'        => $uLoc,
                        'capacity_id'     => null,
                        'base_capacity'   => 0,
                        'extra_beds'      => 0,
                        'total_capacity'  => 0,
                        'occupied_beds'   => 0,
                        'available_beds'  => 0,
                        'is_full'         => false,
                        'is_overbooked'   => false,
                        'notes'           => 'Awaiting room number assignment',
                        'occupants'       => [],
                        'check_in_min'    => $uOcc['check_in'] ?? null,
                        'check_out_max'   => $uOcc['check_out'] ?? null,
                        'booking_ids'     => [$bId],
                        'latest_activity' => 0,
                    ];
                }
                $pendingByBooking[$subKey]['occupants'][] = $uOcc;
                $pendingByBooking[$subKey]['occupied_beds']++;
                $pendingByBooking[$subKey]['total_capacity']++;
                $pendingByBooking[$subKey]['base_capacity']++;
            }

            foreach ($pendingByBooking as $pKey => $pGroup) {
                $groupedRooms[$pKey] = $pGroup;
            }
            $totalBedsOccupied += $unassignedPilgrimsCount;
        }

        // Room Type Breakdown Summary calculation (Double, Triple, Quad, Quint, Six / Sharing, Single)
        $classifyRoomType = function (?string $rawType): string {
            $t = strtolower(trim((string)$rawType));
            if (str_contains($t, 'double') || $t === '2' || str_contains($t, '2 bed') || str_contains($t, '2-bed')) {
                return 'Double';
            }
            if (str_contains($t, 'triple') || $t === '3' || str_contains($t, '3 bed') || str_contains($t, '3-bed')) {
                return 'Triple';
            }
            if (str_contains($t, 'quad') || $t === '4' || str_contains($t, '4 bed') || str_contains($t, '4-bed')) {
                return 'Quad';
            }
            if (str_contains($t, 'quint') || $t === '5' || str_contains($t, '5 bed') || str_contains($t, '5-bed')) {
                return 'Quint';
            }
            if (str_contains($t, 'six') || str_contains($t, 'sharing') || $t === '6' || str_contains($t, '6 bed') || str_contains($t, '6-bed')) {
                return 'Six / Sharing';
            }
            if (str_contains($t, 'single') || $t === '1' || str_contains($t, '1 bed') || str_contains($t, '1-bed')) {
                return 'Single';
            }
            if (!empty($t) && !in_array($t, ['—', '-', 'pending', 'unassigned'])) {
                return ucfirst($t);
            }
            return 'Other';
        };

        $roomTypeSummary = [
            'Double' => [
                'name'           => 'Double (2 Beds)',
                'assigned_rooms' => 0,
                'assigned_pax'   => 0,
                'pending_pax'    => 0,
                'total_pax'      => 0,
            ],
            'Triple' => [
                'name'           => 'Triple (3 Beds)',
                'assigned_rooms' => 0,
                'assigned_pax'   => 0,
                'pending_pax'    => 0,
                'total_pax'      => 0,
            ],
            'Quad' => [
                'name'           => 'Quad (4 Beds)',
                'assigned_rooms' => 0,
                'assigned_pax'   => 0,
                'pending_pax'    => 0,
                'total_pax'      => 0,
            ],
            'Quint' => [
                'name'           => 'Quint (5 Beds)',
                'assigned_rooms' => 0,
                'assigned_pax'   => 0,
                'pending_pax'    => 0,
                'total_pax'      => 0,
            ],
            'Six / Sharing' => [
                'name'           => 'Six / Sharing (6 Beds)',
                'assigned_rooms' => 0,
                'assigned_pax'   => 0,
                'pending_pax'    => 0,
                'total_pax'      => 0,
            ],
            'Single' => [
                'name'           => 'Single (1 Bed)',
                'assigned_rooms' => 0,
                'assigned_pax'   => 0,
                'pending_pax'    => 0,
                'total_pax'      => 0,
            ],
        ];

        foreach ($groupedRooms as $room) {
            $isAssigned = !empty($room['is_assigned']);
            if ($isAssigned) {
                $cType = $classifyRoomType($room['room_type'] ?? '');
                if (!isset($roomTypeSummary[$cType])) {
                    $roomTypeSummary[$cType] = [
                        'name'           => $cType,
                        'assigned_rooms' => 0,
                        'assigned_pax'   => 0,
                        'pending_pax'    => 0,
                        'total_pax'      => 0,
                    ];
                }
                $roomTypeSummary[$cType]['assigned_rooms']++;
                $pCount = count($room['occupants'] ?? []);
                $roomTypeSummary[$cType]['assigned_pax'] += $pCount;
                $roomTypeSummary[$cType]['total_pax'] += $pCount;
            } else {
                foreach ($room['occupants'] ?? [] as $uOcc) {
                    $bType = $classifyRoomType($uOcc['booked_room_type'] ?? ($uOcc['room_type'] ?? 'Quad'));
                    if (!isset($roomTypeSummary[$bType])) {
                        $roomTypeSummary[$bType] = [
                            'name'           => $bType,
                            'assigned_rooms' => 0,
                            'assigned_pax'   => 0,
                            'pending_pax'    => 0,
                            'total_pax'      => 0,
                        ];
                    }
                    $roomTypeSummary[$bType]['pending_pax']++;
                    $roomTypeSummary[$bType]['total_pax']++;
                }
            }
        }

        $roomTypeSummaryGrand = [
            'assigned_rooms' => array_sum(array_column($roomTypeSummary, 'assigned_rooms')),
            'assigned_pax'   => array_sum(array_column($roomTypeSummary, 'assigned_pax')),
            'pending_pax'    => array_sum(array_column($roomTypeSummary, 'pending_pax')),
            'total_pax'      => array_sum(array_column($roomTypeSummary, 'total_pax')),
        ];

        return compact(
            'groupedRooms',
            'unassignedOccupants',
            'unassignedPilgrimsCount',
            'totalAssignedRoomsCount',
            'totalRoomsInUse',
            'totalBedsCapacity',
            'totalBedsOccupied',
            'totalBedsAvailable',
            'fullRoomsCount',
            'partialRoomsCount',
            'overbookedRoomsCount',
            'roomTypeSummary',
            'roomTypeSummaryGrand',
            'hotelFilter',
            'locationFilter',
            'roomTypeFilter',
            'statusFilter',
            'ticketStatus',
            'packageFilter',
            'search',
            'fromDate',
            'toDate',
            'viewMode'
        );
    }

    /**
     * Adjust Bed Capacity for a Room (Increase beds, add extra beds)
     */
    public function adjustBed(Request $request)
    {
        $request->validate([
            'hotel_name'   => 'required|string|max:191',
            'room_number'  => 'required|string|max:100',
            'bed_capacity' => 'required|integer|min:1|max:50',
            'extra_beds'   => 'nullable|integer|min:0|max:20',
            'notes'        => 'nullable|string|max:500',
        ]);

        $hotelName = trim($request->hotel_name);
        $roomNumber = trim($request->room_number);
        $bedCapacity = (int) $request->bed_capacity;
        $extraBeds = (int) ($request->extra_beds ?? 0);
        $roomType = $request->room_type ? trim($request->room_type) : null;
        $location = $request->location ? trim($request->location) : null;
        $gender = $request->gender ? trim($request->gender) : 'Any';

        $record = HotelRoomCapacity::whereRaw('LOWER(TRIM(hotel_name)) = ?', [strtolower($hotelName)])
            ->whereRaw('LOWER(TRIM(room_number)) = ?', [strtolower($roomNumber)])
            ->first();

        if (!$record) {
            $record = new HotelRoomCapacity();
            $record->hotel_name = $hotelName;
            $record->room_number = $roomNumber;
        }

        $record->room_type = $roomType;
        $record->location = $location;
        $record->gender = $gender;
        $record->bed_capacity = $bedCapacity;
        $record->extra_beds = $extraBeds;
        $record->notes = $request->notes;
        $record->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Bed capacity for Room {$roomNumber} ({$hotelName}) updated to {$bedCapacity} beds successfully!",
                'data'    => $record,
            ]);
        }

        return back()->with('success', "Bed capacity for Room {$roomNumber} ({$hotelName}) updated to {$bedCapacity} beds successfully!");
    }

    /**
     * Real-time Live Capacity Check API for Booking Create & Edit
     */
    public function checkCapacityApi(Request $request)
    {
        $hotelName = $request->get('hotel_name');
        $roomNumber = $request->get('room_number');
        $roomType = $request->get('room_type');
        $checkIn = $request->get('check_in');
        $checkOut = $request->get('check_out');
        $excludeBookingId = $request->get('booking_id');
        $location = $request->get('location');

        if (empty($roomNumber) || (empty($hotelName) && empty($location))) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide hotel name and room number.',
            ]);
        }

        $occupancy = HotelRoomCapacity::getRoomOccupancy(
            $hotelName,
            $roomNumber,
            $roomType,
            $checkIn,
            $checkOut,
            $excludeBookingId,
            $location
        );

        $cap = $occupancy['capacity'];
        $occ = $occupancy['occupied_beds'];
        $avail = $occupancy['available_beds'];
        $isFull = $occupancy['is_full'];
        $isOver = $occupancy['is_overbooked'];

        $occupantsNames = array_column($occupancy['occupants'], 'name');
        $summaryNames = implode(', ', array_slice($occupantsNames, 0, 3));
        if (count($occupantsNames) > 3) {
            $summaryNames .= ' +' . (count($occupantsNames) - 3) . ' more';
        }

        $message = '';
        if ($isOver) {
            $message = "⚠️ OVERBOOKED: Room {$roomNumber} has {$occ}/{$cap} beds allocated ({$summaryNames}). No more pilgrims can be assigned to this room.";
        } elseif ($isFull) {
            $message = "🔴 ROOM IS FULL: Room {$roomNumber} is at maximum capacity ({$occ}/{$cap} beds occupied by {$summaryNames}). You cannot add more pilgrims to this room unless bed capacity is increased in the Rooming List.";
        } elseif ($occ > 0) {
            $message = "🟡 Room {$roomNumber}: {$occ}/{$cap} beds occupied ({$avail} beds available). Current occupants: {$summaryNames}.";
        } else {
            // Check if current booking itself occupies this room (Edit Mode clarity)
            if ($excludeBookingId) {
                $selfOcc = HotelRoomCapacity::getRoomOccupancy($hotelName, $roomNumber, $roomType, $checkIn, $checkOut, null, $location);
                if ($selfOcc['occupied_beds'] > 0) {
                    $selfNames = implode(', ', array_slice(array_column($selfOcc['occupants'], 'name'), 0, 3));
                    $message = "🔵 Room {$roomNumber}: {$selfOcc['occupied_beds']}/{$cap} beds assigned to this current booking ({$selfNames}).";
                } else {
                    $message = "🟢 Room {$roomNumber}: Fully available ({$cap} total beds).";
                }
            } else {
                $message = "🟢 Room {$roomNumber}: Fully available ({$cap} total beds).";
            }
        }

        return response()->json([
            'success'           => true,
            'room_number'       => $roomNumber,
            'hotel_name'        => $hotelName,
            'location'          => $location,
            'room_type'         => $roomType,
            'capacity'          => $cap,
            'occupied'          => $occ,
            'available'         => $avail,
            'is_full'           => $isFull,
            'is_overbooked'     => $isOver,
            'occupants_count'   => $occupancy['occupants_count'],
            'occupants_summary' => $summaryNames,
            'message'           => $message,
        ]);
    }
}
