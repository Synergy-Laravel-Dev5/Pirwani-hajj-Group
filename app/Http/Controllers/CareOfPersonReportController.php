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

        // Dropdown filter data (cached for high performance)
        $distinctCareOfList = \Illuminate\Support\Facades\Cache::remember('distinct_care_of_list_v2', 120, function () {
            return Booking::whereNotNull('care_of')
                ->where('care_of', '!=', '')
                ->whereNull('deleted_at')
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->distinct()
                ->orderBy('care_of', 'asc')
                ->pluck('care_of');
        });

        $packages = \Illuminate\Support\Facades\Cache::remember('packages_list_v2', 300, function () {
            return Package::latest()->get();
        });

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
     * Helper to return Excel column letters (1 => 'A', 2 => 'B', ..., 27 => 'AA')
     */
    private function colLetter(int $colIndex): string
    {
        $letters = '';
        while ($colIndex > 0) {
            $mod = ($colIndex - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $colIndex = (int)(($colIndex - $mod) / 26);
        }
        return $letters;
    }


    /**
     * Export Care of Persons Report as Excel (Native .xlsx with embedded drawings + resilient styled MHTML fallback)
     * Zero external PhpOffice dependencies, zero 500 errors, with 100% design and embedded photos!
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
            'sr'          => ['label' => '#',                     'width' => 6,   'html_width' => '40px',  'align' => 'center'],
            'picture'     => ['label' => 'PHOTO',                 'width' => 10,  'html_width' => '65px',  'align' => 'center'],
            'hajj-id'     => ['label' => 'HAJJ ID',               'width' => 14,  'html_width' => '90px',  'align' => 'center'],
            'hb'          => ['label' => 'HB NUMBER',             'width' => 13,  'html_width' => '85px',  'align' => 'center'],
            'name'        => ['label' => 'PILGRIM FULL NAME',     'width' => 32,  'html_width' => '220px', 'align' => 'left'],
            'gender'      => ['label' => 'GENDER',                'width' => 11,  'html_width' => '75px',  'align' => 'center'],
            'passport'    => ['label' => 'PASSPORT NO',           'width' => 16,  'html_width' => '110px', 'align' => 'center'],
            'doi'         => ['label' => 'DOI',                   'width' => 13,  'html_width' => '90px',  'align' => 'center'],
            'expiry'      => ['label' => 'EXPIRY',                'width' => 13,  'html_width' => '90px',  'align' => 'center'],
            'cnic'        => ['label' => 'CNIC',                  'width' => 18,  'html_width' => '120px', 'align' => 'center'],
            'phone'       => ['label' => 'PHONE',                 'width' => 16,  'html_width' => '110px', 'align' => 'center'],
            'booking-ref' => ['label' => 'BOOKING REF / CLIENT',  'width' => 26,  'html_width' => '180px', 'align' => 'left'],
            'package'     => ['label' => 'PACKAGE',               'width' => 22,  'html_width' => '150px', 'align' => 'left'],
            'room'        => ['label' => 'ROOM ALLOCATION',       'width' => 16,  'html_width' => '110px', 'align' => 'center'],
            'hotel'       => ['label' => 'HOTEL',                 'width' => 22,  'html_width' => '150px', 'align' => 'left'],
            'camp'        => ['label' => 'MAKTAB / CAMP',         'width' => 16,  'html_width' => '110px', 'align' => 'left'],
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

        $storageDir = storage_path('app');
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        $tempDir = (is_dir($storageDir) && is_writable($storageDir)) ? $storageDir : sys_get_temp_dir();
        $thumbDir = $tempDir . '/temp_thumbnails';
        if (!is_dir($thumbDir)) {
            @mkdir($thumbDir, 0777, true);
        }

        // =========================================================================
        // ENGINE 1: Native OpenXML (.xlsx with embedded photo drawings)
        // Uses project storage path or system temp to guarantee zero open_basedir issues
        // =========================================================================
        if (class_exists(\ZipArchive::class)) {
            try {
                $colKeys = array_keys($activeCols);
                $colCount = count($activeCols);
                $firstCol = 'A';
                $lastCol = $this->colLetter($colCount);

                $tempZipPath = $tempDir . '/care_of_' . uniqid() . '.xlsx';
                @unlink($tempZipPath);

                $zip = new \ZipArchive();
                if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                    $xmlEscape = function ($v) {
                        return htmlspecialchars((string)$v, ENT_XML1, 'UTF-8');
                    };

                    // 1. [Content_Types].xml
                    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Default Extension="jpg" ContentType="image/jpeg"/>
  <Default Extension="jpeg" ContentType="image/jpeg"/>
  <Default Extension="png" ContentType="image/png"/>
  <Default Extension="webp" ContentType="image/webp"/>
  <Default Extension="gif" ContentType="image/gif"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>
</Types>';
                    $zip->addFromString('[Content_Types].xml', $contentTypes);

                    // 2. _rels/.rels
                    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
                    $zip->addFromString('_rels/.rels', $rels);

                    // 3. xl/_rels/workbook.xml.rels
                    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
                    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

                    // 4. xl/workbook.xml
                    $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Care Of Report" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
                    $zip->addFromString('xl/workbook.xml', $wb);

                    // 5. xl/worksheets/_rels/sheet1.xml.rels
                    $wsRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/>
</Relationships>';
                    $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', $wsRels);

                    // 6. xl/styles.xml (Colors, borders, fonts, alignments identical to PDF)
                    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="9">
    <font><name val="Arial"/><sz val="10"/></font>
    <font><name val="Arial"/><sz val="13"/><b/><color rgb="FFF3DC9B"/></font>
    <font><name val="Arial"/><sz val="9.5"/><i/><color rgb="FF475569"/></font>
    <font><name val="Arial"/><sz val="8.5"/><b/><color rgb="FF64748B"/></font>
    <font><name val="Arial"/><sz val="11"/><b/><color rgb="FF0F172A"/></font>
    <font><name val="Arial"/><sz val="10"/><b/><color rgb="FFFFFFFF"/></font>
    <font><name val="Arial"/><sz val="10"/><b/><color rgb="FF1E3A8A"/></font>
    <font><name val="Arial"/><sz val="10"/><b/><color rgb="FF9D174D"/></font>
    <font><name val="Arial"/><sz val="11"/><b/><color rgb="FFF3DC9B"/></font>
  </fonts>
  <fills count="7">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF071527"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF1E293B"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFFFFFFF"/><bgColor indexed="64"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border>
      <left style="thin"><color rgb="FFCBD5E1"/></left>
      <right style="thin"><color rgb="FFCBD5E1"/></right>
      <top style="thin"><color rgb="FFCBD5E1"/></top>
      <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="20">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="2" fillId="3" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="3" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="4" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="8" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
    <xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="4" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="4" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="7" fillId="6" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <!-- Zebra styles (fillId=3) -->
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="6" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="7" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  </cellXfs>
</styleSheet>';
                    $zip->addFromString('xl/styles.xml', $styles);

                    $mergeCells = [];
                    $drawingsXmlArr = [];
                    $drawingRelsArr = [];
                    $mediaCount = 0;

                    $rowsXml = '';

                    // Row 1: Title Header (Dark Navy & Gold)
                    $rowsXml .= '<row r="1" ht="34" customHeight="1"><c r="A1" s="1" t="inlineStr"><is><t>' . $xmlEscape('PIRWANI HAJJ GROUP (PVT) LTD. - CARE OF PERSONS REPORT & BOOKING DETAILS') . '</t></is></c></row>';
                    $mergeCells[] = "A1:{$lastCol}1";

                    // Row 2: Subtitle & Filters
                    $rowsXml .= '<row r="2" ht="20" customHeight="1"><c r="A2" s="2" t="inlineStr"><is><t>' . $xmlEscape($filterSummary) . '</t></is></c></row>';
                    $mergeCells[] = "A2:{$lastCol}2";

                    // Row 3: Spacer
                    $rowsXml .= '<row r="3" ht="8" customHeight="1"/>';

                    // Rows 4-5: KPI Summary Cards Block (identical to PDF 6-box header summary)
                    $kpis = [
                        ['label' => 'Care Of Groups', 'val' => number_format($data['totalCareOfGroups'])],
                        ['label' => 'Total Bookings',  'val' => number_format($data['totalBookings'])],
                        ['label' => 'Total Pilgrims',  'val' => number_format($data['totalPersons'])],
                        ['label' => 'Male / Female',   'val' => $data['maleCount'] . ' M / ' . $data['femaleCount'] . ' F'],
                        ['label' => 'Total Received',  'val' => 'PKR ' . number_format($data['totalReceived'])],
                        ['label' => 'Total Balance',   'val' => 'PKR ' . number_format($data['totalBalance'])],
                    ];
                    $kpiCount = count($kpis);
                    $step = $colCount / $kpiCount;
                    $row4Cells = '';
                    $row5Cells = '';
                    for ($k = 0; $k < $kpiCount; $k++) {
                        $sIdx = (int)floor($k * $step) + 1;
                        $eIdx = (int)floor(($k + 1) * $step);
                        if ($k === $kpiCount - 1) {
                            $eIdx = $colCount;
                        }
                        $sLetter = $this->colLetter($sIdx);
                        $eLetter = $this->colLetter($eIdx);
                        if ($sLetter !== $eLetter) {
                            $mergeCells[] = "{$sLetter}4:{$eLetter}4";
                            $mergeCells[] = "{$sLetter}5:{$eLetter}5";
                        }
                        $row4Cells .= '<c r="' . $sLetter . '4" s="3" t="inlineStr"><is><t>' . $xmlEscape($kpis[$k]['label']) . '</t></is></c>';
                        $row5Cells .= '<c r="' . $sLetter . '5" s="4" t="inlineStr"><is><t>' . $xmlEscape($kpis[$k]['val']) . '</t></is></c>';
                    }
                    $rowsXml .= '<row r="' . $row4Cells ? 4 : 4 . '" ht="18" customHeight="1">' . $row4Cells . '</row>';
                    $rowsXml .= '<row r="' . $row5Cells ? 5 : 5 . '" ht="22" customHeight="1">' . $row5Cells . '</row>';

                    // Row 6: Spacer
                    $rowsXml .= '<row r="6" ht="10" customHeight="1"/>';

                    $curRow = 7;
                    $picColIdx = array_search('picture', $colKeys);

                    foreach ($grouped as $careOfName => $bookings) {
                        $allGroupPersons = $bookings->flatMap(fn($b) => $b->persons->map(function ($p) use ($b) {
                            $p->parent_booking = $b;
                            return $p;
                        }));

                        $groupPax = $allGroupPersons->count();
                        $groupAmount = $bookings->sum('total_amount');
                        $groupReceived = $bookings->sum('total_received');
                        $groupBalance = $bookings->sum('balance');

                        // 1. Single Care Of Title Bar (Merged across all active columns)
                        $groupBarText = 'CARE OF: ' . strtoupper($careOfName)
                            . '  |  Bookings: ' . $bookings->count()
                            . '  |  Pilgrims: ' . $groupPax
                            . '  |  Total Amount: PKR ' . number_format($groupAmount)
                            . '  |  Received: PKR ' . number_format($groupReceived)
                            . '  |  Balance: PKR ' . number_format($groupBalance);

                        $rowsXml .= '<row r="' . $curRow . '" ht="26" customHeight="1"><c r="A' . $curRow . '" s="5" t="inlineStr"><is><t>' . $xmlEscape($groupBarText) . '</t></is></c></row>';
                        $mergeCells[] = "A{$curRow}:{$lastCol}{$curRow}";
                        $curRow++;

                        // 2. Manifest Table Headers (once per Care Of group, identical to PDF)
                        $thCells = '';
                        foreach ($colKeys as $ci => $ck) {
                            $cLetter = $this->colLetter($ci + 1);
                            $sId = ($activeCols[$ck]['align'] === 'left') ? 7 : 6;
                            $thCells .= '<c r="' . $cLetter . $curRow . '" s="' . $sId . '" t="inlineStr"><is><t>' . $xmlEscape($activeCols[$ck]['label']) . '</t></is></c>';
                        }
                        $rowsXml .= '<row r="' . $curRow . '" ht="26" customHeight="1">' . $thCells . '</row>';
                        $curRow++;

                        // 3. Pilgrims listing
                        if ($allGroupPersons->isEmpty()) {
                            $rowsXml .= '<row r="' . $curRow . '" ht="22" customHeight="1"><c r="A' . $curRow . '" s="8" t="inlineStr"><is><t>No pilgrims found for this Care of person.</t></is></c></row>';
                            $mergeCells[] = "A{$curRow}:{$lastCol}{$curRow}";
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

                                $isZebra = ($pIndex % 2 === 0);
                                $zOffset = $isZebra ? 6 : 0;

                                // Resolve thumbnail/photo
                                $photoPath = $this->getFastThumbnail($person->photo, $thumbDir, 120, 140);
                                if ($picColIdx !== false && $photoPath && file_exists($photoPath)) {
                                    $mediaCount++;
                                    $ext = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION)) ?: 'jpg';
                                    $mediaName = "image{$mediaCount}.{$ext}";
                                    $zip->addFile($photoPath, "xl/media/{$mediaName}");
                                    $drawingRelsArr[] = '<Relationship Id="rId' . $mediaCount . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/' . $mediaName . '"/>';

                                    $r0 = $curRow - 1; // 0-based row
                                    $c0 = $picColIdx;  // 0-based col
                                    // Robust oneCellAnchor: 38px (361950 EMUs) x 46px (438150 EMUs), centered with 8px left and 4px top offset
                                    $drawingsXmlArr[] = '  <xdr:oneCellAnchor>
    <xdr:from>
      <xdr:col>' . $c0 . '</xdr:col>
      <xdr:colOff>76200</xdr:colOff>
      <xdr:row>' . $r0 . '</xdr:row>
      <xdr:rowOff>38100</xdr:rowOff>
    </xdr:from>
    <xdr:ext cx="361950" cy="438150"/>
    <xdr:pic>
      <xdr:nvPicPr>
        <xdr:cNvPr id="' . $mediaCount . '" name="Photo ' . $mediaCount . '"/>
        <xdr:cNvPicPr><a:picLocks noChangeAspect="1"/></xdr:cNvPicPr>
      </xdr:nvPicPr>
      <xdr:blipFill>
        <a:blip r:embed="rId' . $mediaCount . '"/>
        <a:stretch><a:fillRect/></a:stretch>
      </xdr:blipFill>
      <xdr:spPr>
        <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
      </xdr:spPr>
    </xdr:pic>
    <xdr:clientData/>
  </xdr:oneCellAnchor>';
                                }

                                $pRowCells = '';
                                foreach ($colKeys as $ci => $ck) {
                                    $cLetter = $this->colLetter($ci + 1);
                                    switch ($ck) {
                                        case 'sr':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '" t="inlineStr"><is><t>' . $pIndex . '</t></is></c>';
                                            break;
                                        case 'picture':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '"/>';
                                            break;
                                        case 'hajj-id':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (10 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($person->hajj_id ?? '—') . '</t></is></c>';
                                            break;
                                        case 'hb':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (10 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($hb ?: '—') . '</t></is></c>';
                                            break;
                                        case 'name':
                                            $pName = $person->full_name ?: ($person->given_name . ' ' . $person->surname);
                                            if (!empty($person->father_name)) {
                                                $rel = $isFemale ? 'D/O, W/O' : 'S/O';
                                                $pName .= "\n" . $rel . ': ' . $person->father_name;
                                            }
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (11 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($pName) . '</t></is></c>';
                                            break;
                                        case 'gender':
                                            $gStyle = $isFemale ? (13 + $zOffset) : (12 + $zOffset);
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . $gStyle . '" t="inlineStr"><is><t>' . $xmlEscape($gender) . '</t></is></c>';
                                            break;
                                        case 'passport':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (10 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($person->passport_number ?? '—') . '</t></is></c>';
                                            break;
                                        case 'doi':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($doi) . '</t></is></c>';
                                            break;
                                        case 'expiry':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($person->passport_expiry_date ?? '—') . '</t></is></c>';
                                            break;
                                        case 'cnic':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($person->cnic ?? '—') . '</t></is></c>';
                                            break;
                                        case 'phone':
                                            $pPhone = $person->phone ?: ($booking->phone ?? '—');
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($pPhone) . '</t></is></c>';
                                            break;
                                        case 'booking-ref':
                                            $bText = $voucherNo . "\n" . $clientName;
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (9 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($bText) . '</t></is></c>';
                                            break;
                                        case 'package':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (9 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($pkg) . '</t></is></c>';
                                            break;
                                        case 'room':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (8 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($roomInfo ?: '—') . '</t></is></c>';
                                            break;
                                        case 'hotel':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (9 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($person->hotel_name ?? '—') . '</t></is></c>';
                                            break;
                                        case 'camp':
                                            $pRowCells .= '<c r="' . $cLetter . $curRow . '" s="' . (9 + $zOffset) . '" t="inlineStr"><is><t>' . $xmlEscape($campName) . '</t></is></c>';
                                            break;
                                    }
                                }
                                $rowsXml .= '<row r="' . $curRow . '" ht="48" customHeight="1">' . $pRowCells . '</row>';
                                $curRow++;
                                $pIndex++;
                            }
                        }
                        // Spacer row between groups
                        $rowsXml .= '<row r="' . $curRow . '" ht="10" customHeight="1"/>';
                        $curRow++;
                    }

                    // Drawings files
                    if ($mediaCount > 0) {
                        $drawRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
' . implode("\n", $drawingRelsArr) . '
</Relationships>';
                        $zip->addFromString('xl/drawings/_rels/drawing1.xml.rels', $drawRelsXml);

                        $drawingXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
' . implode("\n", $drawingsXmlArr) . '
</xdr:wsDr>';
                        $zip->addFromString('xl/drawings/drawing1.xml', $drawingXml);
                    }

                    // Columns definition XML
                    $colsXml = '<cols>';
                    foreach ($colKeys as $ci => $ck) {
                        $cIdx = $ci + 1;
                        $colsXml .= '<col min="' . $cIdx . '" max="' . $cIdx . '" width="' . $activeCols[$ck]['width'] . '" customWidth="1"/>';
                    }
                    $colsXml .= '</cols>';

                    // Merge cells XML
                    $mergeCellsXml = '';
                    if (!empty($mergeCells)) {
                        $mergeCellsXml = '<mergeCells count="' . count($mergeCells) . '">';
                        foreach ($mergeCells as $mc) {
                            $mergeCellsXml .= '<mergeCell ref="' . $mc . '"/>';
                        }
                        $mergeCellsXml .= '</mergeCells>';
                    }

                    $drawingTag = ($mediaCount > 0) ? '<drawing r:id="rId1"/>' : '';

                    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetViews>
    <sheetView tabSelected="1" workbookViewId="0">
      <pane ySplit="6" topLeftCell="A7" activePane="bottomLeft" state="frozen"/>
    </sheetView>
  </sheetViews>
  ' . $colsXml . '
  <sheetData>
    ' . $rowsXml . '
  </sheetData>
  ' . $mergeCellsXml . '
  ' . $drawingTag . '
</worksheet>';
                    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

                    $zip->close();

                    if (file_exists($tempZipPath) && filesize($tempZipPath) > 500) {
                        $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.xlsx';
                        return response()->download($tempZipPath, $fileName, [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                            'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Type',
                            'Cache-Control' => 'max-age=0, must-revalidate',
                        ])->deleteFileAfterSend(true);
                    }
                }
            } catch (\Throwable $xlsxEx) {
                \Illuminate\Support\Facades\Log::warning('CareOfPersonReportController ZipArchive failed, falling back to MHTML Excel: ' . $xlsxEx->getMessage());
            }
        }

        // =========================================================================
        // ENGINE 2: Resilient MHTML Multipart Excel (.xls)
        // 100% styled (Dark Navy #071527, Gold #F3DC9B, KPI summary cards, dark table headers)
        // With all photos EMBEDDED as Base64 MIME parts (NO broken [x], NO 15-min freeze)
        // =========================================================================
        $totalColsCount = count($activeCols);
        $fileName = 'Care_Of_Persons_Report' . $careOfSuffix . '_' . date('Ymd_His') . '.xls';
        $boundary = "----=_Part_CareOf_" . md5(time());

        return response()->streamDownload(function () use ($grouped, $filterSummary, $activeCols, $totalColsCount, $data, $thumbDir, $boundary) {
            $imagesToEmbed = [];
            $imgCounter = 0;

            ob_start();
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Care Of Report</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>
                br { mso-data-placement: same-cell; }
                body { font-family: Arial, Calibri, sans-serif; font-size: 11px; margin: 0; padding: 0; }
                table { border-collapse: collapse; table-layout: fixed; width: 100%; }
                th { background-color: #1E293B; color: #FFFFFF; font-weight: bold; border: 1px solid #000000; text-align: center; vertical-align: middle; height: 28px; font-size: 11px; padding: 4px; }
                td { border: 1px solid #CBD5E1; vertical-align: middle; padding: 5px; font-size: 11px; }
                .center { text-align: center; }
                .fw-bold { font-weight: bold; }
                .text-cell { mso-number-format: "\@"; }
                .title-header { background-color: #071527; color: #F3DC9B; font-size: 14px; font-weight: bold; text-align: center; height: 38px; border: 1px solid #000000; }
                .subtitle-header { background-color: #F8FAFC; color: #475569; font-size: 10px; font-style: italic; text-align: center; height: 22px; border: 1px solid #CBD5E1; }
                .kpi-th { background-color: #F1F5F9; color: #64748B; font-size: 10px; font-weight: bold; border: 1px solid #CBD5E1; text-align: center; height: 22px; }
                .kpi-td { background-color: #FFFFFF; color: #0F172A; font-size: 12px; font-weight: bold; border: 1px solid #CBD5E1; text-align: center; height: 26px; }
                .group-header { background-color: #071527; color: #F3DC9B; font-size: 11px; font-weight: bold; height: 30px; padding-left: 8px; border: 1px solid #000000; text-align: left; }
                .row-zebra { background-color: #F8FAFC; }
                .gender-male { color: #1E3A8A; font-weight: bold; }
                .gender-female { color: #9D174D; font-weight: bold; }
            </style></head><body>';

            echo '<table>';

            // Column colgroup with widths
            echo '<colgroup>';
            foreach ($activeCols as $cdef) {
                echo '<col style="width: ' . ($cdef['html_width'] ?? '100px') . ';">';
            }
            echo '</colgroup>';

            // Row 1: Title Header (Dark Navy & Gold)
            echo '<tr><td colspan="' . $totalColsCount . '" class="title-header">'
                . 'PIRWANI HAJJ GROUP (PVT) LTD. - CARE OF PERSONS REPORT & BOOKING DETAILS'
                . '</td></tr>';

            // Row 2: Subtitle & Filters
            echo '<tr><td colspan="' . $totalColsCount . '" class="subtitle-header">'
                . htmlspecialchars($filterSummary)
                . '</td></tr>';

            // Row 3: Spacer
            echo '<tr><td colspan="' . $totalColsCount . '" style="height: 8px; border: none; background: #FFFFFF;"></td></tr>';

            // Rows 4-5: KPI Summary Cards Block (identical to PDF 6 metric boxes)
            $kpis = [
                ['label' => 'Care Of Groups', 'val' => number_format($data['totalCareOfGroups'])],
                ['label' => 'Total Bookings',  'val' => number_format($data['totalBookings'])],
                ['label' => 'Total Pilgrims',  'val' => number_format($data['totalPersons'])],
                ['label' => 'Male / Female',   'val' => $data['maleCount'] . ' M / ' . $data['femaleCount'] . ' F'],
                ['label' => 'Total Received',  'val' => 'PKR ' . number_format($data['totalReceived'])],
                ['label' => 'Total Balance',   'val' => 'PKR ' . number_format($data['totalBalance'])],
            ];
            $kpiColsSpan = (int)floor($totalColsCount / 6);
            $remSpan = $totalColsCount - ($kpiColsSpan * 5);

            echo '<tr>';
            for ($k = 0; $k < 6; $k++) {
                $span = ($k === 5) ? $remSpan : $kpiColsSpan;
                echo '<th colspan="' . $span . '" class="kpi-th">' . htmlspecialchars($kpis[$k]['label']) . '</th>';
            }
            echo '</tr>';

            echo '<tr>';
            for ($k = 0; $k < 6; $k++) {
                $span = ($k === 5) ? $remSpan : $kpiColsSpan;
                echo '<td colspan="' . $span . '" class="kpi-td">' . htmlspecialchars($kpis[$k]['val']) . '</td>';
            }
            echo '</tr>';

            echo '<tr><td colspan="' . $totalColsCount . '" style="height: 12px; border: none; background: #FFFFFF;"></td></tr>';

            // Manifest Groups
            foreach ($grouped as $careOfName => $bookings) {
                $allGroupPersons = $bookings->flatMap(fn($b) => $b->persons->map(function ($p) use ($b) {
                    $p->parent_booking = $b;
                    return $p;
                }));

                $groupPax = $allGroupPersons->count();
                $groupAmount = $bookings->sum('total_amount');
                $groupReceived = $bookings->sum('total_received');
                $groupBalance = $bookings->sum('balance');

                // 1. Single Care Of Title Bar (Merged across all active columns)
                $barText = 'CARE OF: ' . strtoupper($careOfName)
                    . ' &nbsp;|&nbsp; Bookings: ' . $bookings->count()
                    . ' &nbsp;|&nbsp; Pilgrims: ' . $groupPax
                    . ' &nbsp;|&nbsp; Total Amount: PKR ' . number_format($groupAmount)
                    . ' &nbsp;|&nbsp; Received: PKR ' . number_format($groupReceived)
                    . ' &nbsp;|&nbsp; Balance: PKR ' . number_format($groupBalance);

                echo '<tr><td colspan="' . $totalColsCount . '" class="group-header">' . $barText . '</td></tr>';

                // 2. Manifest Table Headers (once per Care Of group, identical to PDF)
                echo '<tr>';
                foreach ($activeCols as $cdef) {
                    $alignStyle = ($cdef['align'] === 'left') ? 'text-align: left; padding-left: 6px;' : 'text-align: center;';
                    echo '<th style="' . $alignStyle . '">' . htmlspecialchars($cdef['label']) . '</th>';
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

                        echo '<tr style="height: 52px;" ' . $zebraClass . '>';

                        foreach (array_keys($activeCols) as $ck) {
                            switch ($ck) {
                                case 'sr':
                                    echo '<td class="center">' . $pIndex . '</td>';
                                    break;

                                case 'picture':
                                    $photoFile = $this->getFastThumbnail($person->photo, $thumbDir, 120, 140);
                                    if ($photoFile && file_exists($photoFile)) {
                                        $imgCounter++;
                                        $cid = "photo_{$imgCounter}";
                                        $ext = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION)) ?: 'jpg';
                                        $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                                        $imgRaw = @file_get_contents($photoFile);
                                        if ($imgRaw) {
                                            $imgItem = [
                                                'cid' => $cid,
                                                'filename' => "photo_{$imgCounter}.{$ext}",
                                                'mime' => $mime,
                                                'data' => base64_encode($imgRaw),
                                            ];
                                            $imagesToEmbed[] = $imgItem;
                                            echo '<td class="center" style="padding: 2px; height: 52px; width: 65px; text-align: center; vertical-align: middle;">'
                                                . '<img src="http://report.local/' . $imgItem['filename'] . '" width="38" height="46" style="width: 38px; height: 46px; object-fit: cover; border-radius: 2px; display: block; margin: 0 auto;" alt="Photo">'
                                                . '</td>';
                                        } else {
                                            echo '<td class="center" style="color: #94A3B8;">—</td>';
                                        }
                                    } else {
                                        echo '<td class="center" style="color: #94A3B8;">—</td>';
                                    }
                                    break;

                                case 'hajj-id':
                                    echo '<td class="center fw-bold text-cell">' . htmlspecialchars($person->hajj_id ?? '—') . '</td>';
                                    break;

                                case 'hb':
                                    echo '<td class="center fw-bold text-cell">' . htmlspecialchars($hb ?: '—') . '</td>';
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
                                    echo '<td class="center fw-bold text-cell">' . htmlspecialchars($person->passport_number ?? '—') . '</td>';
                                    break;

                                case 'doi':
                                    echo '<td class="center text-cell">' . htmlspecialchars($doi) . '</td>';
                                    break;

                                case 'expiry':
                                    echo '<td class="center text-cell">' . htmlspecialchars($person->passport_expiry_date ?? '—') . '</td>';
                                    break;

                                case 'cnic':
                                    echo '<td class="center text-cell">' . htmlspecialchars($person->cnic ?? '—') . '</td>';
                                    break;

                                case 'phone':
                                    echo '<td class="center text-cell">' . htmlspecialchars($person->phone ?: ($booking->phone ?? '—')) . '</td>';
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
                        $pIndex++;
                    }
                }
                echo '<tr><td colspan="' . $totalColsCount . '" style="height: 12px; background-color: #FFFFFF; border: none;"></td></tr>';
            }

            echo '</table></body></html>';
            $htmlBody = ob_get_clean();

            // Stream MHTML Multipart format
            echo "MIME-Version: 1.0\r\n";
            echo "Content-Type: multipart/related; boundary=\"{$boundary}\"; type=\"text/html\"\r\n\r\n";

            // Part 1: Main HTML Document
            echo "--{$boundary}\r\n";
            echo "Content-Type: text/html; charset=\"utf-8\"\r\n";
            echo "Content-Transfer-Encoding: 8bit\r\n";
            echo "Content-Location: http://report.local/index.html\r\n\r\n";
            echo $htmlBody . "\r\n\r\n";

            // Part 2+: Base64 Image attachments mapped to CIDs and Locations
            foreach ($imagesToEmbed as $img) {
                echo "--{$boundary}\r\n";
                echo "Content-Type: {$img['mime']}\r\n";
                echo "Content-Transfer-Encoding: base64\r\n";
                echo "Content-Location: http://report.local/{$img['filename']}\r\n";
                echo "Content-ID: <{$img['cid']}>\r\n\r\n";
                echo chunk_split($img['data']) . "\r\n";
            }

            echo "--{$boundary}--\r\n";
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Type',
            'Cache-Control' => 'max-age=0, must-revalidate',
        ]);
    }
}
