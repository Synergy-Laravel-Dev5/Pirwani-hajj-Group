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
     * Rooming List & Bed Allocation Report (Matching Official Manifest Format)
     */
    public function index(Request $request)
    {
        $data = $this->getFilteredRoomingData($request);

        // Dropdown Data
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

        $pdf = Pdf::loadView('reports.rooming_list_pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $hotelString = is_array($data['hotelFilter'] ?? null) ? implode('_', $data['hotelFilter']) : ($data['hotelFilter'] ?? '');
        $hotelPart = !empty($hotelString) ? '_' . preg_replace('/[^A-Za-z0-9]/', '_', $hotelString) : '';
        $locPart = (!empty($data['locationFilter']) && $data['locationFilter'] !== 'all') ? '_' . strtoupper($data['locationFilter']) : '';
        $fileName = 'Official_Rooming_List_Manifest' . $locPart . $hotelPart . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Official Rooming List & Manifest as Excel (CSV with UTF-8 BOM for MS Excel)
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

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rooming Manifest');
        $sheet->setShowGridLines(true);

        // Title Block
        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', 'PIRWANI HAJJ GROUP - OFFICIAL ROOMING LIST & MANIFEST REPORT');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

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
        $sheet->mergeCells('A2:U2');
        $sheet->setCellValue('A2', $filterSummary);
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Column Headers
        $headers = [
            'A' => ['title' => 'SR', 'width' => 8],
            'B' => ['title' => 'PICTURE', 'width' => 14],
            'C' => ['title' => 'HAJJ ID', 'width' => 14],
            'D' => ['title' => 'HB NUMBER', 'width' => 14],
            'E' => ['title' => 'PASSPORT NO', 'width' => 16],
            'F' => ['title' => 'PILGRIM FULL NAME', 'width' => 28],
            'G' => ['title' => 'GENDER', 'width' => 12],
            'H' => ['title' => 'LOCATION', 'width' => 14],
            'I' => ['title' => 'HOTEL / BUILDING', 'width' => 26],
            'J' => ['title' => 'ROOM TYPE', 'width' => 15],
            'K' => ['title' => 'ROOM NUMBER', 'width' => 16],
            'L' => ['title' => 'TOTAL BEDS', 'width' => 12],
            'M' => ['title' => 'OCCUPIED', 'width' => 12],
            'N' => ['title' => 'AVAILABLE', 'width' => 12],
            'O' => ['title' => 'ROOM GENDER', 'width' => 14],
            'P' => ['title' => 'BOOKING REF', 'width' => 16],
            'Q' => ['title' => 'CLIENT / AGENT', 'width' => 24],
            'R' => ['title' => 'PHONE', 'width' => 16],
            'S' => ['title' => 'CHECK IN', 'width' => 14],
            'T' => ['title' => 'CHECK OUT', 'width' => 14],
            'U' => ['title' => 'ROOM NOTES', 'width' => 22],
        ];

        $headerRow = 4;
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        foreach ($headers as $col => $colInfo) {
            $sheet->setCellValue($col . $headerRow, $colInfo['title']);
            $sheet->getColumnDimension($col)->setWidth($colInfo['width']);
        }

        // Header Styling
        $headerRange = 'A' . $headerRow . ':U' . $headerRow;
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

        $currentRow = 5;
        $sr = 1;

        $cacheDir = storage_path('app/temp_photos');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        foreach ($groupedRooms as $room) {
            $rNum = trim($room['room_number']);
            $isUnassigned = ($rNum === 'PENDING' || $rNum === 'UNASSIGNED' || empty($rNum));
            $roomDisplayNo = $isUnassigned ? 'UNASSIGNED' : (str_starts_with(strtoupper($rNum), 'R') ? strtoupper($rNum) : ('R' . $rNum));
            $locLabel = strtoupper($room['location'] ?? 'MAKKAH');

            if (empty($room['occupants'])) {
                // Empty room row
                $sheet->getRowDimension($currentRow)->setRowHeight(26);
                $sheet->setCellValue('A' . $currentRow, $sr++);
                $sheet->setCellValue('B' . $currentRow, '—');
                $sheet->setCellValue('C' . $currentRow, '—');
                $sheet->setCellValue('D' . $currentRow, '—');
                $sheet->setCellValue('E' . $currentRow, '—');
                $sheet->setCellValue('F' . $currentRow, '(Empty Room)');
                $sheet->setCellValue('G' . $currentRow, '—');
                $sheet->setCellValue('H' . $currentRow, $locLabel);
                $sheet->setCellValue('I' . $currentRow, $room['hotel_name']);
                $sheet->setCellValue('J' . $currentRow, $room['room_type']);
                $sheet->setCellValue('K' . $currentRow, $roomDisplayNo);
                $sheet->setCellValue('L' . $currentRow, $room['total_capacity']);
                $sheet->setCellValue('M' . $currentRow, 0);
                $sheet->setCellValue('N' . $currentRow, $room['total_capacity']);
                $sheet->setCellValue('O' . $currentRow, $room['room_gender'] ?? 'Any');
                $sheet->setCellValue('P' . $currentRow, '—');
                $sheet->setCellValue('Q' . $currentRow, '—');
                $sheet->setCellValue('R' . $currentRow, '—');
                $sheet->setCellValue('S' . $currentRow, $room['check_in_min'] ? date('d M Y', strtotime($room['check_in_min'])) : '—');
                $sheet->setCellValue('T' . $currentRow, $room['check_out_max'] ? date('d M Y', strtotime($room['check_out_max'])) : '—');
                $sheet->setCellValue('U' . $currentRow, $room['notes'] ?? '');

                $rowRange = 'A' . $currentRow . ':U' . $currentRow;
                $sheet->getStyle($rowRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $currentRow . ':E' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $currentRow . ':O' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('S' . $currentRow . ':T' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('K' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
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
                            } else {
                                $remoteUrl = 'https://pirwanitravels.com.pk/' . $rawClean;
                                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                                $imgData = @file_get_contents($remoteUrl, false, $ctx);
                                if ($imgData && strlen($imgData) > 100) {
                                    @file_put_contents($cachedFile, $imgData);
                                    if (@getimagesize($cachedFile)) {
                                        $photoFile = $cachedFile;
                                    }
                                }
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
                    $sheet->setCellValue('F' . $currentRow, $occ['name'] ?? '—');

                    $occGender = strtoupper($occ['gender'] ?? 'MALE');
                    $sheet->setCellValue('G' . $currentRow, $occGender);

                    $sheet->setCellValue('H' . $currentRow, $locLabel);
                    $sheet->setCellValue('I' . $currentRow, $room['hotel_name']);
                    $sheet->setCellValue('J' . $currentRow, $room['room_type']);
                    $sheet->setCellValue('K' . $currentRow, $roomDisplayNo);
                    $sheet->setCellValue('L' . $currentRow, $room['total_capacity']);
                    $sheet->setCellValue('M' . $currentRow, $room['occupied_beds']);
                    $sheet->setCellValue('N' . $currentRow, $room['available_beds']);
                    $sheet->setCellValue('O' . $currentRow, $room['room_gender'] ?? 'Any');
                    $sheet->setCellValue('P' . $currentRow, $occ['booking_number'] ?? '—');
                    $sheet->setCellValue('Q' . $currentRow, $occ['client_name'] ?? '—');
                    $sheet->setCellValue('R' . $currentRow, $occ['phone'] ?? '—');
                    $sheet->setCellValue('S' . $currentRow, $occ['check_in'] ?? '—');
                    $sheet->setCellValue('T' . $currentRow, $occ['check_out'] ?? '—');
                    $sheet->setCellValue('U' . $currentRow, $room['notes'] ?? '');

                    // Alignments
                    $rowRange = 'A' . $currentRow . ':U' . $currentRow;
                    $sheet->getStyle($rowRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B' . $currentRow . ':E' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('G' . $currentRow . ':O' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('S' . $currentRow . ':T' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    // Gender Styling
                    if ($occGender === 'FEMALE') {
                        $sheet->getStyle('G' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('9D174D'));
                    } else {
                        $sheet->getStyle('G' . $currentRow)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E3A8A'));
                    }

                    // Room No Styling
                    $sheet->getStyle('K' . $currentRow)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
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
     * Bulk Assign Selected Pilgrims (via Checkboxes) to a Room
     */
    public function assignRoom(Request $request)
    {
        $request->validate([
            'person_ids'   => 'nullable|array',
            'haji_ids'     => 'nullable|string',
            'hotel_name'   => 'required|string|max:191',
            'room_number'  => 'nullable|string|max:100',
            'room_type'    => 'nullable|string|max:100',
            'location'     => 'nullable|string|max:100',
            'gender'       => 'nullable|string|max:50',
        ]);

        $personIds  = $request->person_ids ? (array)$request->person_ids : [];

        // If haji_ids provided (single or comma/space-separated)
        if (!empty($request->haji_ids)) {
            $rawHaji = preg_split('/[\s,]+/', trim($request->haji_ids), -1, PREG_SPLIT_NO_EMPTY);
            if (!empty($rawHaji)) {
                $matchedByHaji = BookingPerson::where(function ($q) use ($rawHaji) {
                    $q->whereIn('hajj_id', $rawHaji)
                      ->orWhereIn('hb_number', $rawHaji)
                      ->orWhereIn('passport_number', $rawHaji)
                      ->orWhereIn('id', $rawHaji);
                    foreach ($rawHaji as $h) {
                        $q->orWhere('hajj_id', 'LIKE', '%' . $h . '%')
                          ->orWhere('hb_number', 'LIKE', '%' . $h . '%');
                    }
                })->pluck('id')->toArray();

                $personIds = array_unique(array_merge($personIds, $matchedByHaji));
            }
        }

        if (empty($personIds)) {
            $errMsg = "Please select at least one pilgrim or provide valid Haji ID(s) to assign.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }
            return back()->with('error', $errMsg);
        }

        $hotelName  = trim($request->hotel_name);
        $roomNumber = !empty($request->room_number) ? trim($request->room_number) : null;
        $roomType   = $request->room_type ? trim($request->room_type) : 'Quad';
        $location   = $request->location ? trim($request->location) : 'makkah';
        $gender     = $request->gender ? trim($request->gender) : 'Any';

        $updatedCount = 0;
        foreach ($personIds as $pid) {
            if (is_numeric($pid)) {
                $person = BookingPerson::find($pid);
                if ($person) {
                    // Update or create allocation for this person at this specific location & hotel stage
                    $allocation = BookingPersonRoomAllocation::where('booking_person_id', $person->id)
                        ->where(function ($q) use ($location, $hotelName) {
                            $q->where(function ($sub) use ($location, $hotelName) {
                                $sub->where('location', $location)->where('hotel_name', $hotelName);
                            })->orWhere(function ($sub) use ($location) {
                                $sub->where('location', $location)
                                    ->where(function ($h) {
                                        $h->whereNull('hotel_name')
                                          ->orWhere('hotel_name', '')
                                          ->orWhere('hotel_name', 'Pending Room Allocation')
                                          ->orWhere('hotel_name', 'Unspecified Hotel');
                                    });
                            });
                        })->first();

                    if (!$allocation) {
                        $allocation = new BookingPersonRoomAllocation();
                        $allocation->booking_id = $person->booking_id;
                        $allocation->booking_person_id = $person->id;
                    }

                    $allocation->location    = $location;
                    $allocation->hotel_name  = $hotelName;
                    $allocation->room_number = $roomNumber;
                    $allocation->room_type   = $roomType;
                    $allocation->gender      = $gender;
                    $allocation->save();

                    // Also sync to person model as primary/latest room for backwards compatibility
                    $person->room_number = $roomNumber;
                    $person->room_type   = $roomType;
                    $person->hotel_name  = $hotelName;
                    $person->location    = $location;
                    $person->room_gender = $gender;
                    $person->save();

                    $updatedCount++;

                    // Sync/Ensure BookingHotel on this booking
                    $bHotel = BookingHotel::where('booking_id', $person->booking_id)
                        ->where(function ($q) use ($location, $hotelName) {
                            $q->where('location', $location)->orWhere('hotel_name', $hotelName);
                        })->first();

                    if ($bHotel) {
                        if (!empty($roomNumber) || empty($bHotel->room_number)) {
                            $bHotel->room_number = $roomNumber;
                            $bHotel->room_type = strtolower($roomType);
                            $bHotel->save();
                        }
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
                        $p->room_number = $otherAlloc->room_number;
                        $p->room_type   = $otherAlloc->room_type;
                        $p->hotel_name  = $otherAlloc->hotel_name;
                        $p->location    = $otherAlloc->location;
                        $p->room_gender = $otherAlloc->gender;
                    } else {
                        $p->room_number = null;
                        $p->room_type   = null;
                        $p->hotel_name  = null;
                        $p->room_gender = null;
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
                        $p->room_number = $otherAlloc->room_number;
                        $p->room_type   = $otherAlloc->room_type;
                        $p->hotel_name  = $otherAlloc->hotel_name;
                        $p->location    = $otherAlloc->location;
                        $p->room_gender = $otherAlloc->gender;
                    } else {
                        $p->room_number = null;
                        $p->room_type   = null;
                        $p->hotel_name  = null;
                        $p->room_gender = null;
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
                        $person->room_number = null;
                        $person->room_type   = null;
                        $person->hotel_name  = null;
                        $person->room_gender = null;
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
        } elseif (!empty($rawHotelFilter)) {
            $hotelFilter = [trim($rawHotelFilter)];
        } else {
            $hotelFilter = [];
        }
        $locationFilter = $request->get('location');
        $roomTypeFilter = $request->get('room_type');
        $statusFilter   = $request->get('status'); // all, available, full, overbooked, unassigned
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

        $allBookings = $bookingQuery->latest()->get();

        $groupedRooms = [];
        $unassignedOccupants = [];

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

            // Determine Booked Room Type & default hotel from booking
            $bookedRoomType = 'Quad';
            $bHotelFirst = $b->hotels ? $b->hotels->first() : null;
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

                // Get all room allocations for this person
                $allocations = $p->roomAllocations ?? collect();

                // If person has no allocations table records yet, fall back to person model columns
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
                    // Totally unassigned pilgrim
                    $occData = [
                        'allocation_id'     => null,
                        'person_id'         => $p->id,
                        'real_person_id'    => is_numeric($p->id) ? (int)$p->id : null,
                        'hajj_id'           => $hajjId,
                        'hb_number'         => $hbNumber,
                        'name'              => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                        'father_name'       => $p->father_name ?? null,
                        'dob'               => $p->dob ? date('d M Y', strtotime($p->dob)) : null,
                        'passport'          => $p->passport_number ?: '—',
                        'gender'            => $gender,
                        'photo'             => $p->photo,
                        'cnic'              => $p->cnic,
                        'phone'             => $p->phone ?: $b->phone,
                        'booking_id'        => $b->id,
                        'booking_number'    => $b->booking_number ?? ('#BK-' . $b->id),
                        'client_name'       => $b->client->name ?? ($b->company->company_name ?? ($b->company->name ?? 'N/A')),
                        'check_in'          => $bHotelFirst?->check_in ? date('d M Y', strtotime($bHotelFirst->check_in)) : '—',
                        'check_out'         => $bHotelFirst?->check_out ? date('d M Y', strtotime($bHotelFirst->check_out)) : '—',
                        'room_type'         => $bHotelFirst?->room_type ? ucfirst($bHotelFirst->room_type) : $bookedRoomType,
                        'booked_room_type'  => $bookedRoomType,
                        'booked_hotel_name' => $bHotelFirst ? $bHotelFirst->hotel_name : 'Pending Allocation',
                        'booking_pax'       => $b->no_of_pax ?: 1,
                        'room_number'       => null,
                        'hotel_name'        => $bHotelFirst?->hotel_name,
                        'location'          => $bHotelFirst?->location ?: 'makkah',
                    ];
                    $unassignedOccupants[] = $occData;
                } else {
                    foreach ($allocations as $alloc) {
                        $rNum = !empty($alloc->room_number) ? trim($alloc->room_number) : null;
                        if (in_array(strtoupper((string)$rNum), ['PENDING', 'UNASSIGNED', '—', '-'])) {
                            $rNum = null;
                        }
                        $hName = !empty($alloc->hotel_name) ? trim($alloc->hotel_name) : null;
                        $loc = !empty($alloc->location) ? trim($alloc->location) : 'makkah';
                        $rType = !empty($alloc->room_type) ? trim($alloc->room_type) : null;
                        $rGender = !empty($alloc->gender) ? trim($alloc->gender) : 'Any';

                        $matchedHotel = null;
                        if ($b->hotels && $b->hotels->count() > 0) {
                            $matchedHotel = $b->hotels->first(function ($bh) use ($loc, $hName) {
                                return (!empty($hName) && stripos($bh->hotel_name, $hName) !== false) ||
                                       (strtolower($bh->location) === strtolower($loc));
                            });
                            if (!$matchedHotel) {
                                $matchedHotel = $b->hotels->first();
                            }
                        }

                        $cIn = $alloc->check_in ?? ($matchedHotel?->check_in);
                        $cOut = $alloc->check_out ?? ($matchedHotel?->check_out);
                        $rType = $rType ?: ($matchedHotel?->room_type ? ucfirst($matchedHotel->room_type) : $bookedRoomType);
                        $hName = $hName ?: ($matchedHotel?->hotel_name ?: 'Unspecified Hotel');

                        $occData = [
                            'allocation_id'     => $alloc->id ?? null,
                            'person_id'         => $p->id,
                            'real_person_id'    => is_numeric($p->id) ? (int)$p->id : null,
                            'hajj_id'           => $hajjId,
                            'hb_number'         => $hbNumber,
                            'name'              => $p->full_name ?: trim(($p->given_name ?? '') . ' ' . ($p->surname ?? 'Pilgrim')),
                            'father_name'       => $p->father_name ?? null,
                            'dob'               => $p->dob ? date('d M Y', strtotime($p->dob)) : null,
                            'passport'          => $p->passport_number ?: '—',
                            'gender'            => $gender,
                            'photo'             => $p->photo,
                            'cnic'              => $p->cnic,
                            'phone'             => $p->phone ?: $b->phone,
                            'booking_id'        => $b->id,
                            'booking_number'    => $b->booking_number ?? ('#BK-' . $b->id),
                            'client_name'       => $b->client->name ?? ($b->company->company_name ?? ($b->company->name ?? 'N/A')),
                            'check_in'          => $cIn ? date('d M Y', strtotime($cIn)) : '—',
                            'check_out'         => $cOut ? date('d M Y', strtotime($cOut)) : '—',
                            'room_type'         => $rType,
                            'booked_room_type'  => $bookedRoomType,
                            'booked_hotel_name' => $hName,
                            'booking_pax'       => $b->no_of_pax ?: 1,
                            'room_number'       => $rNum,
                            'hotel_name'        => $hName,
                            'location'          => $loc,
                        ];

                        $hasExplicitHotel = !empty($hName) && !in_array(strtolower($hName), ['pending room allocation', 'unassigned', '—', '-']);
                        $hasExplicitRoom = !empty($rNum) && !in_array(strtoupper((string)$rNum), ['PENDING', 'UNASSIGNED', '—', '-']);

                        if (!$hasExplicitHotel && !$hasExplicitRoom) {
                            $unassignedOccupants[] = $occData;
                        } else {
                            $hName = $hasExplicitHotel ? $hName : 'Unspecified Hotel';
                            $rType = ucfirst($rType ?: 'Quad');
                            $loc = $loc ?: 'makkah';
                            $displayRoomNo = $hasExplicitRoom ? $rNum : 'PENDING';
                            $occData['room_number'] = $displayRoomNo;

                            $groupKey = strtolower($loc . '___' . $hName . '___' . $displayRoomNo);

                            if (!isset($groupedRooms[$groupKey])) {
                                $baseCap = HotelRoomCapacity::resolveCapacity($hName, $displayRoomNo, $rType, $loc);
                                $customCap = HotelRoomCapacity::where(function ($q) use ($displayRoomNo) {
                                    foreach (HotelRoomCapacity::normalizeRoomNumber($displayRoomNo) as $v) {
                                        $q->orWhereRaw('LOWER(TRIM(room_number)) = ?', [$v]);
                                    }
                                })->first();

                                $extraBeds = $customCap ? (int) $customCap->extra_beds : 0;
                                $effectiveCap = max(1, $baseCap);
                                $roomGender = !empty($customCap->gender) && $customCap->gender !== 'Any' ? $customCap->gender : $rGender;

                                $groupedRooms[$groupKey] = [
                                    'hotel_name'      => $hName,
                                    'room_number'     => $displayRoomNo,
                                    'room_type'       => $rType,
                                    'room_gender'     => $roomGender,
                                    'location'        => $loc,
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
                            if (!in_array($b->id, $groupedRooms[$groupKey]['booking_ids'])) {
                                $groupedRooms[$groupKey]['booking_ids'][] = $b->id;
                            }
                            $groupedRooms[$groupKey]['occupants'][] = $occData;

                            $pActivity = $p->updated_at ? $p->updated_at->timestamp : ($p->created_at ? $p->created_at->timestamp : 0);
                            if ($pActivity > ($groupedRooms[$groupKey]['latest_activity'] ?? 0)) {
                                $groupedRooms[$groupKey]['latest_activity'] = $pActivity;
                            }
                        }
                    }
                }
            }
        }

        // Apply filters to assigned rooms
        if (!empty($hotelFilter)) {
            $groupedRooms = array_filter($groupedRooms, function ($r) use ($hotelFilter) {
                $rHotel = $r['hotel_name'] ?? '';
                foreach ($hotelFilter as $hf) {
                    if (stripos($rHotel, $hf) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $groupedRooms = array_filter($groupedRooms, fn($r) => strtolower($r['location']) === strtolower($locationFilter));
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

        // Filter by Status if requested
        if (!empty($statusFilter) && $statusFilter !== 'all') {
            if ($statusFilter === 'unassigned') {
                $groupedRooms = [];
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

        // Sort assigned rooms so the most recently assigned room is at the TOP
        uasort($groupedRooms, function ($a, $b) {
            $actA = $a['latest_activity'] ?? 0;
            $actB = $b['latest_activity'] ?? 0;
            if ($actA !== $actB) {
                return $actB <=> $actA;
            }
            return strcmp($a['room_number'] ?? '', $b['room_number'] ?? '');
        });

        // Apply filters to unassigned occupants so they strictly match selected criteria
        if (!empty($hotelFilter)) {
            $unassignedOccupants = array_values(array_filter($unassignedOccupants, function ($occ) use ($hotelFilter) {
                $occHotel = $occ['hotel_name'] ?? ($occ['booked_hotel_name'] ?? '');
                foreach ($hotelFilter as $hf) {
                    if (stripos($occHotel, $hf) !== false) {
                        return true;
                    }
                }
                return false;
            }));
        }
        if (!empty($locationFilter) && $locationFilter !== 'all') {
            $unassignedOccupants = array_values(array_filter($unassignedOccupants, function ($occ) use ($locationFilter) {
                return strtolower($occ['location'] ?? '') === strtolower($locationFilter);
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

        $unassignedPilgrimsCount = count($unassignedOccupants);

        // If there are unassigned pilgrims, append them after assigned rooms so assigned rooms stay on TOP
        if ($unassignedPilgrimsCount > 0 && (empty($statusFilter) || $statusFilter === 'all' || $statusFilter === 'unassigned')) {
            $groupedRooms['__pending_allocation__'] = [
                'hotel_name'      => 'Pending Room Allocation',
                'room_number'     => 'PENDING',
                'room_type'       => 'Pending Allocation',
                'room_gender'     => 'Any',
                'location'        => 'unassigned',
                'capacity_id'     => null,
                'base_capacity'   => $unassignedPilgrimsCount,
                'extra_beds'      => 0,
                'total_capacity'  => $unassignedPilgrimsCount,
                'occupied_beds'   => $unassignedPilgrimsCount,
                'available_beds'  => 0,
                'is_full'         => false,
                'is_overbooked'   => false,
                'notes'           => 'Pilgrims awaiting room assignment',
                'occupants'       => $unassignedOccupants,
                'check_in_min'    => null,
                'check_out_max'   => null,
                'booking_ids'     => array_unique(array_column($unassignedOccupants, 'booking_id')),
                'latest_activity' => 0,
            ];
            $totalBedsOccupied += $unassignedPilgrimsCount;
        }

        return compact(
            'groupedRooms',
            'unassignedOccupants',
            'unassignedPilgrimsCount',
            'totalRoomsInUse',
            'totalBedsCapacity',
            'totalBedsOccupied',
            'totalBedsAvailable',
            'fullRoomsCount',
            'partialRoomsCount',
            'overbookedRoomsCount',
            'hotelFilter',
            'locationFilter',
            'roomTypeFilter',
            'statusFilter',
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
