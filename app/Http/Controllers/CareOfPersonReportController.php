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
                $groupPax = $bookings->sum(function ($b) {
                    return $b->persons->count();
                });
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
