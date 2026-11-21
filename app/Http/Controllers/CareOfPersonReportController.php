<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPerson;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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

        // Filter by selected Care Of (normalized comparison)
        if (!empty($selectedCareOf) && $selectedCareOf !== 'all') {
            $normalizedSelected = trim($selectedCareOf);
            $bookingsQuery->whereRaw('TRIM(care_of) = ?', [$normalizedSelected]);
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

        // Sort by clean normalized care_of first, then booking ID
        $allBookings = $bookingsQuery->orderByRaw('TRIM(care_of) ASC')->orderBy('id', 'asc')->get();

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
            // If filtering by gender, keep bookings that have matching persons
            $allBookings = $allBookings->filter(function ($booking) {
                return $booking->persons->count() > 0;
            })->values();
        }

        // Group by normalized trimmed Care Of person / agent name (case-insensitive)
        $groupedByCareOf = $allBookings->groupBy(function ($booking) {
            $val = trim((string)($booking->care_of ?? ''));
            // Normalize multiple whitespaces into a single space
            $val = preg_replace('/\s+/', ' ', $val);
            return mb_strtoupper($val, 'UTF-8');
        });

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
     * Helper to create or fetch a lightweight cached thumbnail for high-speed rendering in PDF
     */
    private function getFastThumbnail(?string $photoPath, string $thumbDir, int $maxW = 100, int $maxH = 120): ?string
    {
        if (empty($photoPath)) {
            return null;
        }

        $clean = ltrim($photoPath, '/\\');
        $fullPath = null;
        if (file_exists(public_path($clean))) {
            $fullPath = public_path($clean);
        } elseif (file_exists($photoPath)) {
            $fullPath = $photoPath;
        }

        if (!$fullPath || !file_exists($fullPath)) {
            return null;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            return null;
        }

        $mtime = filemtime($fullPath);
        $fileSize = filesize($fullPath);
        $hash = md5($fullPath . '_' . $mtime . "_{$maxW}x{$maxH}");
        $thumbPath = $thumbDir . '/' . $hash . '.jpg';

        if (file_exists($thumbPath) && filesize($thumbPath) > 0) {
            return $thumbPath;
        }

        // If file is already tiny (<= 15KB) and JPEG, use it directly
        if ($fileSize <= 15360 && in_array($ext, ['jpg', 'jpeg'])) {
            return $fullPath;
        }

        $info = @getimagesize($fullPath);
        if (!$info) {
            return null;
        }

        $w = $info[0];
        $h = $info[1];
        if ($w <= 0 || $h <= 0) {
            return null;
        }

        $ratio = min($maxW / $w, $maxH / $h, 1.0);
        $newW = max(1, (int)round($w * $ratio));
        $newH = max(1, (int)round($h * $ratio));

        $srcImg = null;
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $srcImg = @imagecreatefromjpeg($fullPath);
                break;
            case IMAGETYPE_PNG:
                $srcImg = @imagecreatefrompng($fullPath);
                break;
            case IMAGETYPE_WEBP:
                $srcImg = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : null;
                break;
            case IMAGETYPE_GIF:
                $srcImg = @imagecreatefromgif($fullPath);
                break;
            default:
                return null;
        }

        if (!$srcImg) {
            return null;
        }

        $dstImg = imagecreatetruecolor($newW, $newH);
        $white = imagecolorallocate($dstImg, 255, 255, 255);
        imagefilledrectangle($dstImg, 0, 0, $newW, $newH, $white);

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagejpeg($dstImg, $thumbPath, 75);

        imagedestroy($srcImg);
        imagedestroy($dstImg);

        return $thumbPath;
    }

    /**
     * Export Care of Persons Report as PDF
     */
    public function exportPdf(Request $request)
    {
        $this->authorize('care_of_report_view');
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

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

        // Auto-optimize pilgrim photos into cached lightweight thumbnails for fast PDF rendering
        $showPictures = ($visibleCols === null || in_array('picture', $visibleCols));
        if ($showPictures) {
            $thumbDir = storage_path('app/temp_thumbnails');
            if (!is_dir($thumbDir)) {
                @mkdir($thumbDir, 0777, true);
            }

            foreach ($data['groupedByCareOf'] as $bookings) {
                foreach ($bookings as $b) {
                    foreach ($b->persons as $person) {
                        if (!empty($person->photo)) {
                            $person->thumb_photo = $this->getFastThumbnail($person->photo, $thumbDir, 80, 95);
                        }
                    }
                }
            }
        }

        $pdf = Pdf::loadView('reports.care_of_person_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        $pdf->setOption('isFontSubsettingEnabled', true);
        $pdf->setOption('defaultFont', 'Helvetica');
        $pdf->setOption('dpi', 96);

        $careOfSuffix = '';
        if (!empty($data['selectedCareOf']) && $data['selectedCareOf'] !== 'all') {
            $careOfSuffix = '_' . strtoupper($this->sanitizeFilename($data['selectedCareOf']));
        }
        $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Care of Persons Report as Excel (Native .xlsx with embedded drawings + resilient zero-freeze fallback)
     */
    public function exportExcel(Request $request)
    {
        $this->authorize('care_of_report_view');
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

        $data = $this->getCareOfReportData($request);
        $grouped = $data['groupedByCareOf'];

        $visibleCols = null;
        if ($request->has('columns')) {
            $raw = trim($request->get('columns'));
            if ($raw === 'none' || $raw === '') {
                $visibleCols = [];
            } else {
                $visibleCols = array_values(array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== ''));
            }
        }

        $showCol = function ($key) use ($visibleCols) {
            if ($visibleCols === null) {
                return in_array($key, ['sr', 'picture', 'hajj-id', 'hb', 'name', 'gender', 'passport', 'cnic', 'phone', 'booking-ref', 'package', 'room', 'hotel', 'camp']);
            }
            return in_array($key, $visibleCols);
        };

        // All column definitions matching Web and PDF report with Excel widths
        $allCols = [
            'sr'          => ['label' => '#',                     'width' => 6,   'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'picture'     => ['label' => 'PHOTO',                 'width' => 10,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'hajj-id'     => ['label' => 'HAJJ ID',               'width' => 14,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'hb'          => ['label' => 'HB NUMBER',             'width' => 13,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'name'        => ['label' => 'PILGRIM FULL NAME',     'width' => 30,  'align' => Alignment::HORIZONTAL_LEFT,   'html_align' => 'left'],
            'gender'      => ['label' => 'GENDER',                'width' => 11,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'passport'    => ['label' => 'PASSPORT NO',           'width' => 16,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'doi'         => ['label' => 'DOI',                   'width' => 13,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'expiry'      => ['label' => 'EXPIRY',                'width' => 13,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'cnic'        => ['label' => 'CNIC',                  'width' => 18,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'phone'       => ['label' => 'PHONE',                 'width' => 16,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'booking-ref' => ['label' => 'BOOKING REF / CLIENT',  'width' => 24,  'align' => Alignment::HORIZONTAL_LEFT,   'html_align' => 'left'],
            'package'     => ['label' => 'PACKAGE',               'width' => 20,  'align' => Alignment::HORIZONTAL_LEFT,   'html_align' => 'left'],
            'room'        => ['label' => 'ROOM ALLOCATION',       'width' => 16,  'align' => Alignment::HORIZONTAL_CENTER, 'html_align' => 'center'],
            'hotel'       => ['label' => 'HOTEL',                 'width' => 20,  'align' => Alignment::HORIZONTAL_LEFT,   'html_align' => 'left'],
            'camp'        => ['label' => 'MAKTAB / CAMP',         'width' => 16,  'align' => Alignment::HORIZONTAL_LEFT,   'html_align' => 'left'],
        ];

        $activeCols = [];
        foreach ($allCols as $colKey => $colDef) {
            if ($showCol($colKey)) {
                $activeCols[$colKey] = $colDef;
            }
        }
        if (empty($activeCols)) {
            $activeCols = $allCols;
        }

        $careOfSuffix = '';
        if (!empty($data['selectedCareOf']) && $data['selectedCareOf'] !== 'all') {
            $careOfSuffix = '_' . strtoupper($this->sanitizeFilename($data['selectedCareOf']));
        }

        $filterSummary = 'Generated: ' . date('d M Y, h:i A');
        if (!empty($data['selectedCareOf']) && $data['selectedCareOf'] !== 'all') {
            $filterSummary .= ' | Care Of: ' . strtoupper($data['selectedCareOf']);
        }
        if (!empty($data['gender']) && $data['gender'] !== 'all') {
            $filterSummary .= ' | Gender: ' . strtoupper($data['gender']);
        }
        $filterSummary .= ' | Total Groups: ' . $data['totalCareOfGroups'] . ' | Total Bookings: ' . $data['totalBookings'] . ' | Total Pilgrims: ' . $data['totalPersons'];

        // =========================================================================
        // PRIMARY ENGINE: Native PhpSpreadsheet (.xlsx with embedded photo drawings)
        // =========================================================================
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class) && class_exists(\ZipArchive::class)) {
            try {
                $colCount = count($activeCols);
                $colKeys = array_keys($activeCols);
                $colLetterMap = [];
                for ($i = 0; $i < $colCount; $i++) {
                    $colLetterMap[$colKeys[$i]] = Coordinate::stringFromColumnIndex($i + 1);
                }
                $firstCol = 'A';
                $lastCol = Coordinate::stringFromColumnIndex($colCount);

                $thumbDir = storage_path('app/temp_thumbnails');
                if (!is_dir($thumbDir)) {
                    @mkdir($thumbDir, 0777, true);
                }

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Care Of Report');
                $sheet->setShowGridLines(true);

                // Set column widths
                foreach ($activeCols as $ck => $cdef) {
                    $colLetter = $colLetterMap[$ck];
                    $sheet->getColumnDimension($colLetter)->setWidth($cdef['width']);
                }

                // Row 1: Main Title Header
                $sheet->mergeCells("{$firstCol}1:{$lastCol}1");
                $sheet->setCellValue("{$firstCol}1", 'PIRWANI HAJJ GROUP (PVT) LTD. - CARE OF PERSONS REPORT & BOOKING DETAILS');
                $sheet->getStyle("{$firstCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'F3DC9B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '071527']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(34);

                // Row 2: Subtitle & Filters
                $sheet->mergeCells("{$firstCol}2:{$lastCol}2");
                $sheet->setCellValue("{$firstCol}2", $filterSummary);
                $sheet->getStyle("{$firstCol}2")->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9.5, 'color' => ['rgb' => '475569']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Row 3: Spacer
                $sheet->getRowDimension(3)->setRowHeight(8);

                // Rows 4-5: KPI Summary Cards Block (matching PDF)
                $kpis = [
                    ['label' => 'Care Of Groups', 'val' => number_format($data['totalCareOfGroups'])],
                    ['label' => 'Total Bookings',  'val' => number_format($data['totalBookings'])],
                    ['label' => 'Total Pilgrims',  'val' => number_format($data['totalPersons'])],
                    ['label' => 'Male / Female',   'val' => $data['maleCount'] . ' M / ' . $data['femaleCount'] . ' F'],
                    ['label' => 'Total Received',  'val' => 'PKR ' . number_format($data['totalReceived'])],
                    ['label' => 'Total Balance',   'val' => 'PKR ' . number_format($data['totalBalance'])],
                ];

                $kpiCount = count($kpis);
                if ($colCount >= $kpiCount) {
                    $kpiCols = [];
                    $step = $colCount / $kpiCount;
                    for ($k = 0; $k < $kpiCount; $k++) {
                        $startIdx = (int)floor($k * $step) + 1;
                        $endIdx = (int)floor(($k + 1) * $step);
                        if ($k === $kpiCount - 1) {
                            $endIdx = $colCount;
                        }
                        $kpiCols[] = [
                            'start' => Coordinate::stringFromColumnIndex($startIdx),
                            'end'   => Coordinate::stringFromColumnIndex($endIdx),
                        ];
                    }
                    foreach ($kpis as $kIdx => $kpi) {
                        $range = $kpiCols[$kIdx];
                        $cell4 = "{$range['start']}4";
                        $cell5 = "{$range['start']}5";
                        if ($range['start'] !== $range['end']) {
                            $sheet->mergeCells("{$range['start']}4:{$range['end']}4");
                            $sheet->mergeCells("{$range['start']}5:{$range['end']}5");
                        }
                        $sheet->setCellValue($cell4, $kpi['label']);
                        $sheet->setCellValue($cell5, $kpi['val']);

                        $fullRange = "{$range['start']}4:{$range['end']}5";
                        $sheet->getStyle($fullRange)->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                        ]);
                        $sheet->getStyle($cell4)->applyFromArray([
                            'font' => ['size' => 8.5, 'color' => ['rgb' => '64748B']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getStyle($cell5)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0F172A']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                    }
                    $sheet->getRowDimension(4)->setRowHeight(18);
                    $sheet->getRowDimension(5)->setRowHeight(22);
                }

                // Row 6: Spacer
                $sheet->getRowDimension(6)->setRowHeight(12);

                $curRow = 7;

                // Loop Care Of Groups (matching PDF structure)
                foreach ($grouped as $careOfName => $bookings) {
                    $allGroupPersons = $bookings->flatMap(function ($b) {
                        return $b->persons->map(function ($p) use ($b) {
                            $p->parent_booking = $b;
                            return $p;
                        });
                    });

                    $groupPax = $allGroupPersons->count();
                    $groupAmount = $bookings->sum('total_amount');
                    $groupReceived = $bookings->sum('total_received');
                    $groupBalance = $bookings->sum('balance');

                    // 1. Care Of Title Bar (Merged across all active columns)
                    $sheet->mergeCells("{$firstCol}{$curRow}:{$lastCol}{$curRow}");
                    $careOfTitleText = 'CARE OF: ' . strtoupper($careOfName)
                        . '   |   Bookings: ' . $bookings->count()
                        . '   |   Pilgrims: ' . $groupPax
                        . '   |   Total Amount: PKR ' . number_format($groupAmount)
                        . '   |   Received: PKR ' . number_format($groupReceived)
                        . '   |   Balance: PKR ' . number_format($groupBalance);

                    $sheet->setCellValue("{$firstCol}{$curRow}", $careOfTitleText);
                    $sheet->getStyle("{$firstCol}{$curRow}:{$lastCol}{$curRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10.5, 'color' => ['rgb' => 'F3DC9B']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '071527']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($curRow)->setRowHeight(28);
                    $curRow++;

                    // 2. Manifest Table Headers (once per Care Of group, identical to PDF)
                    foreach ($activeCols as $ck => $cdef) {
                        $colLetter = $colLetterMap[$ck];
                        $sheet->setCellValue("{$colLetter}{$curRow}", $cdef['label']);
                        $sheet->getStyle("{$colLetter}{$curRow}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                            'alignment' => ['horizontal' => $cdef['align'], 'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                        ]);
                    }
                    $sheet->getRowDimension($curRow)->setRowHeight(24);
                    $curRow++;

                    // 3. Pilgrims listing
                    if ($allGroupPersons->isEmpty()) {
                        $sheet->mergeCells("{$firstCol}{$curRow}:{$lastCol}{$curRow}");
                        $sheet->setCellValue("{$firstCol}{$curRow}", 'No pilgrims found for this Care of person.');
                        $sheet->getStyle("{$firstCol}{$curRow}:{$lastCol}{$curRow}")->applyFromArray([
                            'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                        ]);
                        $sheet->getRowDimension($curRow)->setRowHeight(24);
                        $curRow++;
                    } else {
                        $pIndex = 1;
                        foreach ($allGroupPersons as $person) {
                            $booking = $person->parent_booking;
                            $gender = strtoupper($person->gender ?? 'MALE');
                            $isFemale = ($gender === 'FEMALE');
                            $hb = BookingPerson::formatHbNumber($person->hb_number);
                            $doi = $person->date_of_issue ?: ($person->passport_issue_date ?: '—');
                            $roomInfo = ($person->room_number ? 'Room ' . $person->room_number : '') . ($person->room_type ? ' (' . $person->room_type . ')' : '');

                            $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';
                            $clientName = $booking ? ($booking->booking_for === 'company' ? ($booking->company->company_name ?? 'N/A') : ($booking->client->name ?? 'N/A')) : '—';
                            $pkg = $booking ? ($booking->package->package_title ?? $booking->package->name ?? $booking->package_name ?? ucfirst($booking->package_type ?? 'N/A')) : '—';
                            $campName = $booking ? ($booking->camp ?: '—') : '—';

                            $sheet->getRowDimension($curRow)->setRowHeight(46);

                            foreach ($activeCols as $colKey => $colDef) {
                                $colLetter = $colLetterMap[$colKey];
                                $cellCoord = "{$colLetter}{$curRow}";

                                switch ($colKey) {
                                    case 'sr':
                                        $sheet->setCellValue($cellCoord, $pIndex++);
                                        break;

                                    case 'picture':
                                        $thumbPath = null;
                                        if (!empty($person->photo)) {
                                            $thumbPath = $this->getFastThumbnail($person->photo, $thumbDir, 70, 85);
                                        }

                                        if ($thumbPath && file_exists($thumbPath)) {
                                            try {
                                                $drawing = new Drawing();
                                                $drawing->setName('Pax_' . $person->id);
                                                $drawing->setDescription('Pilgrim Photo');
                                                $drawing->setPath($thumbPath);
                                                $drawing->setCoordinates($cellCoord);
                                                $drawing->setHeight(38);
                                                $drawing->setOffsetX(8);
                                                $drawing->setOffsetY(4);
                                                $drawing->setWorksheet($sheet);
                                            } catch (\Throwable $drawEx) {
                                                $sheet->setCellValue($cellCoord, '—');
                                                $sheet->getStyle($cellCoord)->getFont()->setColor(new Color('94A3B8'));
                                            }
                                        } else {
                                            $sheet->setCellValue($cellCoord, '—');
                                            $sheet->getStyle($cellCoord)->getFont()->setColor(new Color('94A3B8'));
                                        }
                                        break;

                                    case 'hajj-id':
                                        $sheet->setCellValue($cellCoord, $person->hajj_id ?? '—');
                                        $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                                        break;

                                    case 'hb':
                                        $sheet->setCellValue($cellCoord, $hb ?: '—');
                                        $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                                        break;

                                    case 'name':
                                        $nameText = $person->full_name ?: ($person->given_name . ' ' . $person->surname);
                                        if (!empty($person->father_name)) {
                                            $rel = $isFemale ? 'D/O, W/O' : 'S/O';
                                            $nameText .= "\n" . $rel . ': ' . $person->father_name;
                                        }
                                        $sheet->setCellValue($cellCoord, $nameText);
                                        $sheet->getStyle($cellCoord)->getAlignment()->setWrapText(true);
                                        $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                                        break;

                                    case 'gender':
                                        $sheet->setCellValue($cellCoord, $gender);
                                        $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                                        if ($isFemale) {
                                            $sheet->getStyle($cellCoord)->getFont()->setColor(new Color('9D174D'));
                                        } else {
                                            $sheet->getStyle($cellCoord)->getFont()->setColor(new Color('1E3A8A'));
                                        }
                                        break;

                                    case 'passport':
                                        $sheet->setCellValue($cellCoord, $person->passport_number ?? '—');
                                        $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                                        break;

                                    case 'doi':
                                        $sheet->setCellValue($cellCoord, $doi);
                                        break;

                                    case 'expiry':
                                        $sheet->setCellValue($cellCoord, $person->passport_expiry_date ?? '—');
                                        break;

                                    case 'cnic':
                                        $sheet->setCellValue($cellCoord, $person->cnic ?? '—');
                                        break;

                                    case 'phone':
                                        $sheet->setCellValue($cellCoord, $person->phone ?: ($booking->phone ?? '—'));
                                        break;

                                    case 'booking-ref':
                                        $sheet->setCellValue($cellCoord, $voucherNo . "\n" . $clientName);
                                        $sheet->getStyle($cellCoord)->getAlignment()->setWrapText(true);
                                        break;

                                    case 'package':
                                        $sheet->setCellValue($cellCoord, $pkg);
                                        $sheet->getStyle($cellCoord)->getAlignment()->setWrapText(true);
                                        break;

                                    case 'room':
                                        $sheet->setCellValue($cellCoord, $roomInfo ?: '—');
                                        break;

                                    case 'hotel':
                                        $sheet->setCellValue($cellCoord, $person->hotel_name ?? '—');
                                        $sheet->getStyle($cellCoord)->getAlignment()->setWrapText(true);
                                        break;

                                    case 'camp':
                                        $sheet->setCellValue($cellCoord, $campName);
                                        break;
                                }

                                $sheet->getStyle($cellCoord)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                                $sheet->getStyle($cellCoord)->getAlignment()->setHorizontal($colDef['align']);
                                $sheet->getStyle($cellCoord)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

                                if ($pIndex % 2 === 1) {
                                    $sheet->getStyle($cellCoord)->getFill()->applyFromArray([
                                        'fillType' => Fill::FILL_SOLID,
                                        'startColor' => ['rgb' => 'F8FAFC'],
                                    ]);
                                }
                            }

                            $curRow++;
                        }
                    }

                    $sheet->getRowDimension($curRow)->setRowHeight(12);
                    $curRow++;
                }

                $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.xlsx';
                $writer = new Xlsx($spreadsheet);

                return response()->streamDownload(function () use ($writer) {
                    $writer->save('php://output');
                }, $fileName, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'max-age=0',
                ]);
            } catch (\Throwable $spreadEx) {
                \Illuminate\Support\Facades\Log::warning('CareOfPersonReportController@exportExcel PhpSpreadsheet failed, switching to streaming HTML format: ' . $spreadEx->getMessage());
            }
        }

        // =========================================================================
        // RESILIENT FALLBACK: High-speed Streaming HTML Excel matching PDF design
        // ZERO external HTTP requests (Base64 local photo embedding, NO 15-minute freeze)
        // =========================================================================
        $totalColsCount = count($activeCols);
        $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.xls';

        return response()->streamDownload(function () use ($grouped, $filterSummary, $activeCols, $totalColsCount, $data) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Care Of Report</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                body { font-family: Arial, sans-serif; font-size: 11px; }
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #1E293B; color: #FFFFFF; font-weight: bold; border: 1px solid #000; text-align: center; vertical-align: middle; height: 30px; font-size: 11px; }
                td { border: 1px solid #CBD5E1; vertical-align: middle; padding: 5px; font-size: 11px; }
                .center { text-align: center; }
                .fw-bold { font-weight: bold; }
                .careof-header { background-color: #071527; color: #F3DC9B; font-size: 12px; font-weight: bold; height: 32px; padding-left: 10px; border: 1px solid #000; }
                .kpi-th { background-color: #F1F5F9; color: #64748B; font-size: 10px; font-weight: bold; border: 1px solid #CBD5E1; text-align: center; height: 24px; }
                .kpi-td { background-color: #FFFFFF; color: #0F172A; font-size: 12px; font-weight: bold; border: 1px solid #CBD5E1; text-align: center; height: 28px; }
                .gender-male { color: #1E3A8A; font-weight: bold; }
                .gender-female { color: #9D174D; font-weight: bold; }
                .row-zebra { background-color: #F8FAFC; }
            </style></head><body>';
            echo '<table>';

            // Main Title
            echo '<tr><th colspan="' . $totalColsCount . '" style="font-size: 15px; height: 38px; background-color: #071527; color: #F3DC9B; border: none; text-align: center;">PIRWANI HAJJ GROUP (PVT) LTD. - CARE OF PERSONS REPORT & BOOKING DETAILS</th></tr>';
            echo '<tr><td colspan="' . $totalColsCount . '" style="text-align: center; font-style: italic; color: #475569; background-color: #F8FAFC; border: none; height: 24px;">' . htmlspecialchars($filterSummary) . '</td></tr>';
            echo '<tr><td colspan="' . $totalColsCount . '" style="height: 10px; border: none;"></td></tr>';

            // KPI Summary Cards Block (matching PDF)
            echo '<tr>
                <th colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-th">Care Of Groups</th>
                <th colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-th">Total Bookings</th>
                <th colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-th">Total Pilgrims</th>
                <th colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-th">Male / Female</th>
                <th colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-th">Total Received</th>
                <th colspan="' . max(1, $totalColsCount - 5 * (int)floor($totalColsCount / 6)) . '" class="kpi-th">Total Balance</th>
            </tr>';
            echo '<tr>
                <td colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-td">' . number_format($data['totalCareOfGroups']) . '</td>
                <td colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-td">' . number_format($data['totalBookings']) . '</td>
                <td colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-td">' . number_format($data['totalPersons']) . '</td>
                <td colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-td">' . $data['maleCount'] . ' M / ' . $data['femaleCount'] . ' F</td>
                <td colspan="' . max(1, (int)floor($totalColsCount / 6)) . '" class="kpi-td">PKR ' . number_format($data['totalReceived']) . '</td>
                <td colspan="' . max(1, $totalColsCount - 5 * (int)floor($totalColsCount / 6)) . '" class="kpi-td">PKR ' . number_format($data['totalBalance']) . '</td>
            </tr>';
            echo '<tr><td colspan="' . $totalColsCount . '" style="height: 12px; border: none;"></td></tr>';

            // Loop Care Of Groups (matching PDF structure)
            foreach ($grouped as $careOfName => $bookings) {
                $allGroupPersons = $bookings->flatMap(function ($b) {
                    return $b->persons->map(function ($p) use ($b) {
                        $p->parent_booking = $b;
                        return $p;
                    });
                });

                $groupPax = $allGroupPersons->count();
                $groupAmount = $bookings->sum('total_amount');
                $groupReceived = $bookings->sum('total_received');
                $groupBalance = $bookings->sum('balance');

                // 1. Single Care Of Title Bar (Merged across all active columns)
                echo '<tr><td colspan="' . $totalColsCount . '" class="careof-header">'
                    . 'CARE OF: ' . htmlspecialchars(strtoupper($careOfName))
                    . ' &nbsp;|&nbsp; Bookings: ' . $bookings->count()
                    . ' &nbsp;|&nbsp; Pilgrims: ' . $groupPax
                    . ' &nbsp;|&nbsp; Total Amount: PKR ' . number_format($groupAmount)
                    . ' &nbsp;|&nbsp; Received: PKR ' . number_format($groupReceived)
                    . ' &nbsp;|&nbsp; Balance: PKR ' . number_format($groupBalance)
                    . '</td></tr>';

                // 2. Manifest Table Headers (once per Care Of group, identical to PDF)
                echo '<tr>';
                foreach ($activeCols as $colDef) {
                    $alignStyle = ($colDef['html_align'] === 'left') ? 'text-align: left; padding-left: 6px;' : 'text-align: center;';
                    echo '<th style="' . $alignStyle . '">' . $colDef['label'] . '</th>';
                }
                echo '</tr>';

                // 3. Pilgrims listing
                if ($allGroupPersons->isEmpty()) {
                    echo '<tr><td colspan="' . $totalColsCount . '" class="center" style="color: #64748B; font-style: italic; padding: 10px;">No pilgrims found for this Care of person.</td></tr>';
                } else {
                    $pIndex = 1;
                    foreach ($allGroupPersons as $person) {
                        $booking = $person->parent_booking;
                        $gender = strtoupper($person->gender ?? 'MALE');
                        $isFemale = ($gender === 'FEMALE');
                        $genderClass = $isFemale ? 'gender-female' : 'gender-male';
                        $hb = BookingPerson::formatHbNumber($person->hb_number);
                        $doi = $person->date_of_issue ?: ($person->passport_issue_date ?: '—');
                        $roomInfo = ($person->room_number ? 'Room ' . $person->room_number : '') . ($person->room_type ? ' (' . $person->room_type . ')' : '');

                        $voucherNo = $booking ? ($booking->voucher_number ?: ('UB' . str_pad($booking->id, 6, '0', STR_PAD_LEFT))) : '—';
                        $clientName = $booking ? ($booking->booking_for === 'company' ? ($booking->company->company_name ?? 'N/A') : ($booking->client->name ?? 'N/A')) : '—';
                        $pkg = $booking ? ($booking->package->package_title ?? $booking->package->name ?? $booking->package_name ?? ucfirst($booking->package_type ?? 'N/A')) : '—';
                        $campName = $booking ? ($booking->camp ?: '—') : '—';

                        $zebraClass = ($pIndex % 2 === 0) ? 'class="row-zebra"' : '';

                        echo '<tr style="height: 48px;" ' . $zebraClass . '>';
                        foreach ($activeCols as $colKey => $colDef) {
                            switch ($colKey) {
                                case 'sr':
                                    echo '<td class="center">' . $pIndex++ . '</td>';
                                    break;

                                case 'picture':
                                    // Embed local photo as Base64 data URI to eliminate remote HTTP calls and 15-minute freeze
                                    $base64Img = null;
                                    if (!empty($person->photo)) {
                                        $cleanPhoto = ltrim($person->photo, '/\\');
                                        $localPhoto = null;
                                        if (file_exists(public_path($cleanPhoto))) {
                                            $localPhoto = public_path($cleanPhoto);
                                        } elseif (file_exists($person->photo)) {
                                            $localPhoto = $person->photo;
                                        }

                                        if ($localPhoto && file_exists($localPhoto)) {
                                            $rawContent = @file_get_contents($localPhoto);
                                            if ($rawContent && strlen($rawContent) > 0) {
                                                $base64Img = 'data:image/jpeg;base64,' . base64_encode($rawContent);
                                            }
                                        }
                                    }

                                    if ($base64Img) {
                                        echo '<td class="center" style="height: 48px; width: 55px; text-align: center; vertical-align: middle; padding: 2px;">'
                                            . '<img src="' . $base64Img . '" width="38" height="44" style="width: 38px; height: 44px; object-fit: cover; border-radius: 2px; display: block; margin: 0 auto;" alt="Photo">'
                                            . '</td>';
                                    } else {
                                        echo '<td class="center" style="color: #94A3B8;">—</td>';
                                    }
                                    break;

                                case 'hajj-id':
                                    echo '<td class="center fw-bold">' . htmlspecialchars($person->hajj_id ?? '—') . '</td>';
                                    break;

                                case 'hb':
                                    echo '<td class="center fw-bold">' . htmlspecialchars($hb ?: '—') . '</td>';
                                    break;

                                case 'name':
                                    $pName = htmlspecialchars($person->full_name ?: ($person->given_name . ' ' . $person->surname));
                                    $fNameHtml = '';
                                    if (!empty($person->father_name)) {
                                        $rel = $isFemale ? 'D/O, W/O' : 'S/O';
                                        $fNameHtml = '<br><span style="font-size: 9px; color: #64748B;">' . $rel . ': ' . htmlspecialchars($person->father_name) . '</span>';
                                    }
                                    echo '<td><strong>' . $pName . '</strong>' . $fNameHtml . '</td>';
                                    break;

                                case 'gender':
                                    echo '<td class="center ' . $genderClass . '">' . htmlspecialchars($gender) . '</td>';
                                    break;

                                case 'passport':
                                    echo '<td class="center fw-bold">' . htmlspecialchars($person->passport_number ?? '—') . '</td>';
                                    break;

                                case 'doi':
                                    echo '<td class="center">' . htmlspecialchars($doi) . '</td>';
                                    break;

                                case 'expiry':
                                    echo '<td class="center">' . htmlspecialchars($person->passport_expiry_date ?? '—') . '</td>';
                                    break;

                                case 'cnic':
                                    echo '<td class="center">' . htmlspecialchars($person->cnic ?? '—') . '</td>';
                                    break;

                                case 'phone':
                                    echo '<td class="center">' . htmlspecialchars($person->phone ?: ($booking->phone ?? '—')) . '</td>';
                                    break;

                                case 'booking-ref':
                                    echo '<td>' . htmlspecialchars($voucherNo) . '<br><span style="font-size: 9px; color: #64748B;">' . htmlspecialchars($clientName) . '</span></td>';
                                    break;

                                case 'package':
                                    echo '<td>' . htmlspecialchars($pkg) . '</td>';
                                    break;

                                case 'room':
                                    echo '<td class="center">' . htmlspecialchars($roomInfo ?: '—') . '</td>';
                                    break;

                                case 'hotel':
                                    echo '<td>' . htmlspecialchars($person->hotel_name ?? '—') . '</td>';
                                    break;

                                case 'camp':
                                    echo '<td>' . htmlspecialchars($campName) . '</td>';
                                    break;
                            }
                        }
                        echo '</tr>';
                    }
                }
                echo '<tr><td colspan="' . $totalColsCount . '" style="height: 12px; background-color: #FFFFFF; border: none;"></td></tr>';
            }

            echo '</table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
