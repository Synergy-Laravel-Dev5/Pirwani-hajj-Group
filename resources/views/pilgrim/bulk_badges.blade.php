<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hajji ID Badges Sheet — Booking #{{ $booking->voucher_number ?? $booking->id }} ({{ $booking->persons->count() }} Pilgrims) | Pirwani Hajj Group</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Cinzel:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    @php
        $pkgType = strtolower($booking->package_type ?? 'hajj');
        $appName = 'PIRWANI HAJJ GROUP';
        $agencyPhone = $booking->emergency_phone ?? ($booking->phone ?? '+92 300 0000000');
        $saudiHelpline = '911 / 1966';
        $packageYear = $booking->package_year ?? date('Y');
    @endphp

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background-color: #0b1320;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 24px 16px;
            color: #0f172a;
        }

        /* ── Top Bar (Screen Only) ── */
        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 14px 24px;
            border-radius: 14px;
            border: 1px solid #d4af37;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            flex-wrap: wrap;
            gap: 12px;
        }

        .bar-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .bar-title .icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #091322 0%, #162a4a 100%);
            color: #d4af37;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            border: 1.5px solid #d4af37;
        }

        .bar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            background: linear-gradient(135deg, #d4af37 0%, #aa8010 100%);
            color: #091322;
            border: 1px solid #fef08a;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
            transition: all 0.2s;
        }

        .btn-action:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            box-shadow: none;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff;
            border: 1px solid #f87171;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);
        }

        .btn-pdf:hover {
            filter: brightness(1.15);
            transform: translateY(-1px);
            color: #ffffff;
        }

        /* ── Sheet Container (A4 2-Column Grid) ── */
        .badges-grid {
            max-width: 190mm;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, 86mm);
            gap: 10mm 12mm;
            justify-content: center;
        }

        /* ── Standard Lanyard Badge Dimensions (86mm x 126mm) ── */
        .badge-card {
            width: 86mm;
            height: 126mm;
            background: #ffffff;
            border-radius: 12px;
            border: 2px solid #c9a84c;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        /* Watermark Background */
        .badge-watermark {
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at center, rgba(201, 168, 76, 0.05) 0%, transparent 70%),
                repeating-linear-gradient(45deg, rgba(15, 30, 54, 0.02) 0px, rgba(15, 30, 54, 0.02) 1px, transparent 1px, transparent 8px);
            pointer-events: none;
            z-index: 1;
        }

        /* Lanyard Hole Punch Slot Guide */
        .lanyard-hole-guide {
            position: absolute;
            top: 2.2mm;
            left: 50%;
            transform: translateX(-50%);
            width: 14mm;
            height: 3.5mm;
            border: 1px dashed rgba(255, 255, 255, 0.7);
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.3);
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lanyard-hole-guide::after {
            content: "";
            width: 4px;
            height: 1px;
            background: rgba(255, 255, 255, 0.9);
        }

        /* ── Top Official Government Strip ── */
        .badge-top-strip {
            background: #064e3b;
            color: #d1fae5;
            font-size: 6.8px;
            font-weight: 800;
            text-align: center;
            padding: 1.5px 0 1px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            border-bottom: 1px solid #10b981;
            position: relative;
            z-index: 5;
        }

        /* ── Header ── */
        .badge-header-wrapper {
            position: relative;
            z-index: 5;
            background: #ffffff;
        }

        .badge-brand-plate {
            background: #ffffff;
            padding: 3.5mm 3.2mm 2.6mm;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-logo-img {
            height: 36px;
            width: auto;
            max-width: 44px;
            object-fit: contain;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.15));
        }

        .badge-brand-text {
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .badge-brand-title {
            font-family: 'Cinzel', serif;
            font-size: 11.5px;
            font-weight: 900;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #0c1a2e;
            line-height: 1.15;
        }

        .badge-brand-sub {
            font-size: 6.8px;
            font-weight: 800;
            color: #92400e;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .badge-vip-ribbon {
            background: linear-gradient(135deg, #070e19 0%, #0c1a2e 50%, #152740 100%);
            padding: 2.2px 3.5mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #d4af37;
            position: relative;
        }

        .badge-vip-ribbon::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #b8860b, #fef08a, #d4af37, #fef08a, #b8860b);
        }

        .vip-ribbon-title {
            font-size: 7.4px;
            font-weight: 800;
            color: #fde047;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .vip-ribbon-badge {
            background: linear-gradient(135deg, #d4af37, #aa8010);
            color: #091322;
            font-size: 6.5px;
            font-weight: 900;
            padding: 0.8px 5px;
            border-radius: 3px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ── Body Section ── */
        .badge-body {
            padding: 2.5mm 3.2mm;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 5;
        }

        /* Profile Row */
        .profile-row {
            display: flex;
            gap: 2.8mm;
            align-items: center;
            margin-bottom: 1.5mm;
        }

        .photo-container {
            position: relative;
            flex-shrink: 0;
        }

        .photo-box {
            width: 26mm;
            height: 31mm;
            border-radius: 7px;
            border: 2px solid #d4af37;
            object-fit: cover;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 900;
            color: #0c1a2e;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }

        .verified-stamp {
            position: absolute;
            bottom: -3px;
            right: -3px;
            background: #10b981;
            color: #ffffff;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            border: 1.5px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .profile-meta {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .id-tags-row {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            margin-bottom: 2.5px;
        }

        .haji-pill {
            background: linear-gradient(135deg, #091322 0%, #152740 100%);
            color: #fde047;
            border: 1px solid #d4af37;
            font-size: 8px;
            font-weight: 900;
            padding: 1.2px 5.5px;
            border-radius: 3px;
            display: inline-block;
            letter-spacing: 0.4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .hb-pill {
            background: #1e3a8a;
            color: #ffffff;
            border: 1px solid #3b82f6;
            font-size: 8px;
            font-weight: 900;
            padding: 1.2px 5.5px;
            border-radius: 3px;
            display: inline-block;
        }

        .camp-pill {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #f59e0b;
            font-size: 7.2px;
            font-weight: 900;
            padding: 1.2px 5px;
            border-radius: 3px;
            display: inline-block;
        }

        .pax-name {
            font-size: 11.8px;
            font-weight: 900;
            color: #091322;
            line-height: 1.15;
            margin-bottom: 2px;
            text-transform: uppercase;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            letter-spacing: -0.2px;
        }

        .pax-kv {
            font-size: 8px;
            color: #334155;
            line-height: 1.35;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pax-kv strong {
            color: #091322;
        }

        /* ── Hotel Box ── */
        .hotel-summary-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3px solid #d4af37;
            border-radius: 6px;
            padding: 1.8mm 2.2mm;
            margin-bottom: 1.5mm;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .hotel-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 7.8px;
            padding: 1.5px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .hotel-row:last-child {
            border-bottom: none;
        }

        .hotel-loc-tag {
            font-weight: 900;
            font-size: 7.4px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 2.5px;
        }

        .hotel-name-trunc {
            flex: 1;
            margin: 0 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 700;
            color: #0f172a;
            font-size: 7.4px;
        }

        .room-badge {
            background: linear-gradient(135deg, #091322 0%, #152740 100%);
            color: #fde047;
            border: 1px solid #d4af37;
            font-weight: 900;
            padding: 1px 4.5px;
            border-radius: 3px;
            font-size: 7.2px;
            white-space: nowrap;
        }

        /* ── QR + Emergency Row ── */
        .qr-emergency-row {
            display: flex;
            gap: 2mm;
            align-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 1.8mm 2.2mm;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .qr-img {
            width: 15.5mm;
            height: 15.5mm;
            border-radius: 4px;
            border: 1px solid #94a3b8;
            flex-shrink: 0;
        }

        .qr-text-col {
            flex: 1;
            font-size: 7.2px;
            color: #475569;
            line-height: 1.25;
        }

        .qr-text-col strong {
            font-size: 8px;
            color: #091322;
            display: block;
            margin-bottom: 1px;
            text-transform: uppercase;
            font-weight: 900;
        }

        .verified-pill {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-weight: 800;
            padding: 1px 4.5px;
            border-radius: 3px;
            display: inline-block;
            margin-top: 1.5px;
            font-size: 7px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        /* ── Footer ── */
        .badge-footer {
            background: #091322;
            color: #cbd5e1;
            text-align: center;
            padding: 1.6mm 3mm;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 0.3px;
            border-top: 1.5px solid #d4af37;
            position: relative;
            z-index: 5;
        }

        .badge-footer strong {
            color: #fde047;
        }

        /* ── Print Styling ── */
        @media print {
            body {
                background: none !important;
                padding: 4mm 2mm;
                margin: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .badges-grid {
                max-width: 100%;
                gap: 6mm 10mm;
            }
            .badge-card {
                box-shadow: none !important;
                border: 1.5px solid #b8860b;
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 6mm;
            }
        }
    </style>
</head>
<body>

    <!-- ── Screen Control Bar ── -->
    <div class="no-print-bar">
        <div class="bar-title">
            <div class="icon-circle">
                <i class="mdi mdi-badge-account"></i>
            </div>
            <div>
                <strong style="font-size: 15px; color: #091322; display: flex; align-items: center; gap: 8px;">
                    Hajji ID Badges Sheet
                    <span style="font-size: 11px; font-weight: 800; background: #091322; color: #fde047; border: 1px solid #d4af37; padding: 2px 8px; border-radius: 12px;">
                        {{ $booking->persons->count() }} Badges
                    </span>
                </strong>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                    Voucher: <strong>#{{ $booking->voucher_number ?? $booking->id }}</strong> · {{ $booking->client->name ?? 'Client' }} · Ready for 2×2 A4 Card Print
                </div>
            </div>
        </div>
        <div class="bar-actions">
            <button type="button" class="btn-action btn-pdf" id="btnDownloadPdf" onclick="downloadBadgesPdf()">
                <i class="mdi mdi-file-pdf-box"></i> Download PDF
            </button>
            <button class="btn-action" onclick="window.print()">
                <i class="mdi mdi-printer"></i> Print All Badges (A4 Sheet)
            </button>
            <a href="{{ route('booking.show', $booking->id) }}" class="btn-action btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Back to Booking
            </a>
        </div>
    </div>

    <!-- ── 2x2 Printable Badges Grid (A4 Layout) ── -->
    <div class="badges-grid" id="printableBadgesGrid">
        @foreach($booking->persons as $person)
            @php
                $hajjId = \App\Models\BookingPerson::formatHajjId($person->hajj_id) ?? ('PW27' . str_pad($person->id, 3, '0', STR_PAD_LEFT));
                $hbNumber = \App\Models\BookingPerson::formatHbNumber($person->hb_number) ?? 'HB0001';
                $epassUrl = url('/pilgrim/e-pass/' . $person->id);
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($epassUrl);
                
                $pilgrimPhoto = null;
                if (!empty($person->photo) && file_exists(public_path($person->photo))) {
                    $pilgrimPhoto = asset($person->photo);
                } elseif (!empty($person->passport_image) && file_exists(public_path($person->passport_image))) {
                    $pilgrimPhoto = asset($person->passport_image);
                }
            @endphp
            <div class="badge-card">
                <!-- Watermark Background -->
                <div class="badge-watermark"></div>

                <!-- Lanyard Punch Slot Guide -->
                <div class="lanyard-hole-guide"></div>

                <!-- Top Official Ministry Strip -->
                <div class="badge-top-strip">
                    PAKISTAN HAJJ MISSION · MINISTRY OF RELIGIOUS AFFAIRS
                </div>

                <!-- Header -->
                <div class="badge-header-wrapper">
                    <div class="badge-brand-plate">
                        <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group" class="badge-logo-img" onerror="this.src='{{ asset('assets/images/logo.png') }}'">
                        <div class="badge-brand-text">
                            <div class="badge-brand-title">{{ $appName }}</div>
                            <div class="badge-brand-sub">VIP HAJJ MANIFEST PORTAL</div>
                        </div>
                    </div>
                    <div class="badge-vip-ribbon">
                        <span class="vip-ribbon-title">
                            <i class="mdi mdi-kaaba text-warning"></i> VIP HAJJ PASS · {{ $packageYear }} / 1447H
                        </span>
                        <span class="vip-ribbon-badge">OFFICIAL</span>
                    </div>
                </div>

                <!-- Body -->
                <div class="badge-body">
                    <!-- Profile Row -->
                    <div class="profile-row">
                        <div class="photo-container">
                            @if($pilgrimPhoto)
                                <img src="{{ $pilgrimPhoto }}" alt="{{ $person->full_name }}" class="photo-box">
                            @else
                                <div class="photo-box">
                                    {{ strtoupper(substr($person->full_name, 0, 1) ?: 'P') }}
                                </div>
                            @endif
                            <div class="verified-stamp" title="Verified Pilgrim">
                                <i class="mdi mdi-check"></i>
                            </div>
                        </div>

                        <div class="profile-meta">
                            <div class="id-tags-row">
                                <span class="haji-pill">{{ $hajjId }}</span>
                                <span class="hb-pill">{{ $hbNumber }}</span>
                                @if($booking->camp)
                                    <span class="camp-pill">{{ $booking->camp }}</span>
                                @endif
                            </div>
                            <div class="pax-name" title="{{ $person->full_name }}">{{ $person->full_name }}</div>
                            @if($person->father_name)
                                <div class="pax-kv">S/O: <strong>{{ $person->father_name }}</strong></div>
                            @endif
                            <div class="pax-kv">Pass: <strong style="color:#091322;">{{ $person->passport_number ?? ($booking->passport_number ?? '—') }}</strong></div>
                            <div class="pax-kv">Blood: <strong style="color:#dc2626;">{{ $person->blood_group ?? '—' }}</strong> · {{ ucfirst($person->gender ?? 'Male') }}</div>
                            <div class="pax-kv">Origin: <strong>{{ $person->city ?? 'Pakistan' }}</strong></div>
                        </div>
                    </div>

                    <!-- Accommodation Box -->
                    <div class="hotel-summary-box">
                        @if($makkahHotel)
                        <div class="hotel-row">
                            <span class="hotel-loc-tag" style="color:#92400e;"><i class="mdi mdi-kaaba"></i> Makkah:</span>
                            <span class="hotel-name-trunc">{{ $makkahHotel->hotel_name ?? 'Designated Hotel' }}</span>
                            <span class="room-badge">Rm: {{ $makkahHotel->room_number ?? ($person->room_number ?? '—') }}</span>
                        </div>
                        @endif

                        @if($aziziaHotel)
                        <div class="hotel-row">
                            <span class="hotel-loc-tag" style="color:#0284c7;"><i class="mdi mdi-home-city"></i> Azizia:</span>
                            <span class="hotel-name-trunc">{{ $aziziaHotel->hotel_name ?? 'Azizia Stay' }}</span>
                            <span class="room-badge">Rm: {{ $aziziaHotel->room_number ?? ($person->room_number ?? '—') }}</span>
                        </div>
                        @endif

                        @if($madinahHotel)
                        <div class="hotel-row">
                            <span class="hotel-loc-tag" style="color:#166534;"><i class="mdi mdi-mosque"></i> Madinah:</span>
                            <span class="hotel-name-trunc">{{ $madinahHotel->hotel_name ?? 'Designated Hotel' }}</span>
                            <span class="room-badge">Rm: {{ $madinahHotel->room_number ?? ($person->room_number ?? '—') }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- QR Code & Emergency Help -->
                    <div class="qr-emergency-row">
                        <img src="{{ $qrCodeUrl }}" alt="Scan Live Pass" class="qr-img">
                        <div class="qr-text-col">
                            <strong>Scan for Live E-Pass</strong>
                            <div>GPS Navigation & Live Flight Status</div>
                            <div class="verified-pill">
                                <i class="mdi mdi-shield-check"></i> Official Pilgrim E-Pass
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="badge-footer">
                    VOUCHER <strong>#{{ $booking->voucher_number ?? $booking->id }}</strong> · KSA EMERGENCY: <strong>{{ $saudiHelpline }}</strong>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function downloadBadgesPdf() {
            const btn = document.getElementById('btnDownloadPdf');
            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Preparing PDF...';
            btn.disabled = true;

            const grid = document.getElementById('printableBadgesGrid');
            const opt = {
                margin:       [4, 4, 4, 4],
                filename:     'Hajji_ID_Badges_Booking_{{ $booking->voucher_number ?? $booking->id }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2.2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(grid).save().then(() => {
                btn.innerHTML = origHtml;
                btn.disabled = false;
            }).catch(err => {
                console.error('PDF generation error, fallback to print:', err);
                btn.innerHTML = origHtml;
                btn.disabled = false;
                window.print();
            });
        }
    </script>
</body>
</html>
