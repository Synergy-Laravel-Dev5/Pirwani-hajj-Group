<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Official Hajj E-Pass | {{ $person->full_name ?? ($booking->client->name ?? 'Pilgrim') }} | Pirwani Hajj Group</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Cinzel:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.2.96/css/materialdesignicons.min.css">
    
    @php
        $pkgType = strtolower($booking->package_type ?? 'hajj');
        $appName = 'PIRWANI HAJJ GROUP';
        $agencyPhone = $booking->emergency_phone ?? ($booking->phone ?? '+92 300 0000000');
        $pilgrimName = $person->full_name ?? ($booking->client->name ?? 'Hajj Pilgrim');
        $hajjId = \App\Models\BookingPerson::formatHajjId($person->hajj_id) ?? ('PW27' . str_pad($person->id ?? 1, 3, '0', STR_PAD_LEFT));
        $hbNumber = \App\Models\BookingPerson::formatHbNumber($person->hb_number) ?? 'HB0001';
        $passport = $person->passport_number ?? ($booking->passport_number ?? '—');
        $roomNo = $person->room_number ?? ($booking->room_number ?? '—');
        
        $pilgrimPhoto = null;
        if (!empty($person->photo) && file_exists(public_path($person->photo))) {
            $pilgrimPhoto = asset($person->photo);
        } elseif (!empty($person->passport_image) && file_exists(public_path($person->passport_image))) {
            $pilgrimPhoto = asset($person->passport_image);
        }
    @endphp

    <style>
        :root {
            --primary: #0f1e36;
            --primary-dark: #091322;
            --primary-light: #e2e8f0;
            --gold: #d4af37;
            --gold-light: #fef9e7;
            --bg-page: #f1f5f9;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            font-size: 13.5px;
            line-height: 1.5;
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .pass-wrapper {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            min-height: 100vh;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
        }

        /* Top Bar */
        .pass-topbar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 70%, #1a365d 100%);
            padding: 16px 20px 24px;
            color: #ffffff;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid var(--gold);
        }

        .pass-topbar::after {
            content: "";
            position: absolute;
            right: -20px;
            top: -20px;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(212,175,55,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #ffffff;
        }

        .badge-live {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pulse {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse 1.6s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Pilgrim Profile Banner */
        .pilgrim-card {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .pilgrim-avatar {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: #ffffff;
            border: 3px solid var(--gold);
            object-fit: cover;
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--primary);
            font-weight: 800;
        }

        .pilgrim-meta h2 {
            font-size: 17px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
            color: #ffffff;
        }

        .id-pills-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 4px;
        }

        .haji-id-tag {
            display: inline-block;
            background: var(--gold);
            color: #000000;
            font-weight: 800;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .hb-tag {
            display: inline-block;
            background: #1e3a8a;
            color: #bfdbfe;
            border: 1px solid #3b82f6;
            font-weight: 800;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .pkg-tag {
            font-size: 11.5px;
            opacity: 0.85;
            color: #cbd5e1;
        }

        /* Content Sections */
        .pass-content {
            padding: 16px 18px;
        }

        /* Quick Action Buttons */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: -12px;
            margin-bottom: 20px;
        }

        .action-btn {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            box-shadow: 0 3px 8px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            border-color: var(--primary);
            color: var(--primary);
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .icon-call { background: #dcfce7; color: #16a34a; }
        .icon-wa { background: #dcfce7; color: #25d366; }
        .icon-print { background: #e0f2fe; color: #0284c7; }

        /* Card Section */
        .section-box {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 14px;
        }

        .info-cell {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }

        .info-val {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-dark);
            word-break: break-word;
        }

        /* Hotel Card */
        .hotel-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 10px;
            position: relative;
        }

        .hotel-item:last-child {
            margin-bottom: 0;
        }

        .hotel-tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .tag-makkah { background: #fef3c7; color: #92400e; }
        .tag-azizia { background: #e0f2fe; color: #0284c7; }
        .tag-madinah { background: #dcfce7; color: #166534; }

        .btn-maps {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 8px;
            color: #0284c7;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            margin-top: 10px;
            transition: all 0.15s;
        }

        .btn-maps:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
        }

        /* Flight Box */
        .flight-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 8px;
        }

        .emergency-banner {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            border-radius: 14px;
            padding: 14px;
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .emergency-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ef4444;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .pass-footer {
            text-align: center;
            padding: 20px;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
            margin-top: 20px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .pass-wrapper { box-shadow: none; max-width: 100%; }
            .action-grid, .btn-maps, .pass-footer button { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="pass-wrapper">
        <!-- Top Banner Header -->
        <div class="pass-topbar">
            <div class="brand-header">
                <div class="brand-title">
                    <i class="mdi mdi-kaaba" style="color: var(--gold); font-size: 18px;"></i>
                    <span>{{ $appName }}</span>
                </div>
                <div class="badge-live">
                    <span class="badge-pulse"></span>
                    Verified E-Pass
                </div>
            </div>

            <div class="pilgrim-card">
                @if($pilgrimPhoto)
                    <img src="{{ $pilgrimPhoto }}" alt="{{ $pilgrimName }}" class="pilgrim-avatar">
                @else
                    <div class="pilgrim-avatar">
                        {{ strtoupper(substr($pilgrimName, 0, 1) ?: 'P') }}
                    </div>
                @endif
                <div class="pilgrim-meta">
                    <div class="id-pills-row">
                        <span class="haji-id-tag"><i class="mdi mdi-identifier"></i> {{ $hajjId }}</span>
                        <span class="hb-tag">{{ $hbNumber }}</span>
                    </div>
                    <h2>{{ $pilgrimName }}</h2>
                    <div class="pkg-tag">
                        <i class="mdi mdi-mosque"></i> Official Hajj {{ $booking->package_year ?? date('Y') }}
                        @if($booking->camp) · {{ $booking->camp }} @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="pass-content">
            <!-- Quick Action Grid -->
            <div class="action-grid">
                <a href="tel:{{ $agencyPhone }}" class="action-btn">
                    <div class="action-icon icon-call"><i class="mdi mdi-phone-in-talk"></i></div>
                    <span>Agency Hotline</span>
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agencyPhone) }}?text={{ urlencode('Assalam-o-Alaikum, I am Pilgrim '.$pilgrimName.' (Hajj ID: '.$hajjId.'). I need assistance in Saudi Arabia.') }}" target="_blank" class="action-btn">
                    <div class="action-icon icon-wa"><i class="mdi mdi-whatsapp"></i></div>
                    <span>WhatsApp Help</span>
                </a>
                <a href="javascript:window.print()" class="action-btn">
                    <div class="action-icon icon-print"><i class="mdi mdi-printer"></i></div>
                    <span>Print E-Pass</span>
                </a>
            </div>

            <!-- 1. Pilgrim Identity & Details -->
            <div class="section-box">
                <div class="section-header">
                    <span class="section-title"><i class="mdi mdi-account-details"></i> Pilgrim Identity</span>
                    <span class="badge" style="background:#e0f2fe;color:#0369a1;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:700;">
                        Active Pilgrim
                    </span>
                </div>
                <div class="info-grid">
                    <div class="info-cell">
                        <span class="info-label">Full Name</span>
                        <span class="info-val">{{ $pilgrimName }}</span>
                    </div>
                    @if($person && $person->father_name)
                    <div class="info-cell">
                        <span class="info-label">Father / Husband</span>
                        <span class="info-val">{{ $person->father_name }}</span>
                    </div>
                    @endif
                    <div class="info-cell">
                        <span class="info-label">Passport Number</span>
                        <span class="info-val" style="color:var(--primary); font-size:13.5px;">{{ $passport }}</span>
                    </div>
                    <div class="info-cell">
                        <span class="info-label">CNIC / ID Card</span>
                        <span class="info-val">{{ $person->cnic ?? ($booking->cnic ?? '—') }}</span>
                    </div>
                    <div class="info-cell">
                        <span class="info-label">Blood Group</span>
                        <span class="info-val" style="color:#dc2626;">{{ $person->blood_group ?? '—' }}</span>
                    </div>
                    <div class="info-cell">
                        <span class="info-label">Gender</span>
                        <span class="info-val">{{ ucfirst($person->gender ?? 'Male') }}</span>
                    </div>
                    <div class="info-cell">
                        <span class="info-label">Mobile Contact</span>
                        <span class="info-val">
                            @if($person && $person->phone)
                                <a href="tel:{{ $person->phone }}" style="color:var(--text-dark);text-decoration:none;">{{ $person->phone }}</a>
                            @else
                                {{ $booking->phone ?? '—' }}
                            @endif
                        </span>
                    </div>
                    <div class="info-cell">
                        <span class="info-label">Camp / Maktab</span>
                        <span class="info-val" style="color:#92400e; font-weight:800;">{{ $booking->camp ?? 'Designated Camp' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Hotel Accommodations & Google Maps Navigation -->
            <div class="section-box">
                <div class="section-header">
                    <span class="section-title"><i class="mdi mdi-office-building-marker"></i> Hotel Stays & Directions</span>
                    <span style="font-size:11px;color:var(--text-muted);">{{ $booking->hotels->count() }} Location(s)</span>
                </div>

                @if($booking->hotels && $booking->hotels->count() > 0)
                    @foreach($booking->hotels as $hotel)
                        @php
                            $loc = strtolower($hotel->location ?? '');
                            $hName = strtolower($hotel->hotel_name ?? '');
                            $isMakkah = str_contains($loc, 'makkah') || str_contains($hName, 'makkah');
                            $isAzizia = str_contains($loc, 'azizia') || str_contains($loc, 'aziziya') || str_contains($hName, 'azizia') || str_contains($hName, 'aziziya');
                            $isMadinah = str_contains($loc, 'madinah') || str_contains($loc, 'medina') || str_contains($hName, 'madinah') || str_contains($hName, 'medina');
                            
                            $tagClass = $isMakkah ? 'tag-makkah' : ($isAzizia ? 'tag-azizia' : ($isMadinah ? 'tag-madinah' : 'tag-makkah'));
                            $tagIcon = $isMakkah ? 'mdi-kaaba' : ($isAzizia ? 'mdi-home-city' : ($isMadinah ? 'mdi-mosque' : 'mdi-hotel'));
                            $locTitle = $isMakkah ? 'Makkah Mukarramah' : ($isAzizia ? 'Azizia Building' : ($isMadinah ? 'Madinah Munawwarah' : ucfirst($hotel->location ?: 'Hotel')));
                            
                            $mapQuery = urlencode(($hotel->hotel_name ?? '') . ' ' . $locTitle . ' Saudi Arabia');
                        @endphp
                        <div class="hotel-item">
                            <span class="hotel-tag {{ $tagClass }}">
                                <i class="mdi {{ $tagIcon }}"></i>
                                {{ $locTitle }}
                            </span>
                            
                            <h4 style="font-size: 14.5px; font-weight: 800; margin-bottom: 4px; color: var(--text-dark);">
                                {{ $hotel->hotel_name }}
                            </h4>

                            <div class="info-grid mt-2">
                                <div class="info-cell">
                                    <span class="info-label">Check-in</span>
                                    <span class="info-val fs-12">{{ $hotel->check_in ? date('d M Y', strtotime($hotel->check_in)) : '—' }}</span>
                                </div>
                                <div class="info-cell">
                                    <span class="info-label">Check-out</span>
                                    <span class="info-val fs-12">{{ $hotel->check_out ? date('d M Y', strtotime($hotel->check_out)) : '—' }}</span>
                                </div>
                                <div class="info-cell">
                                    <span class="info-label">Nights & Room Type</span>
                                    <span class="info-val fs-12">{{ $hotel->no_of_nights ?? 1 }} Nights · {{ ucfirst($hotel->room_type ?? 'Sharing') }}</span>
                                </div>
                                <div class="info-cell">
                                    <span class="info-label">Room #</span>
                                    <span class="info-val fs-12" style="color:var(--primary);font-weight:800;">
                                        {{ $hotel->room_number ?? $roomNo }}
                                    </span>
                                </div>
                            </div>

                            <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" target="_blank" class="btn-maps">
                                <i class="mdi mdi-map-marker-radius"></i> Open in Google Maps
                            </a>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted fs-12 mb-0">No hotel accommodation details uploaded.</p>
                @endif
            </div>

            <!-- 3. Flight Itinerary -->
            @if($booking->departure_date || $booking->arrival_date)
            <div class="section-box">
                <div class="section-header">
                    <span class="section-title"><i class="mdi mdi-airplane"></i> Flight Schedule</span>
                </div>

                @if($booking->departure_date)
                <!-- Departure -->
                <div class="flight-item">
                    <div style="font-size:11px;font-weight:700;color:var(--primary);margin-bottom:4px;text-transform:uppercase;">
                        <i class="mdi mdi-airplane-takeoff"></i> Departure Flight
                    </div>
                    <div class="info-grid">
                        <div class="info-cell">
                            <span class="info-label">Airline & Flight #</span>
                            <span class="info-val">{{ $booking->departure_airline ?? '—' }} {{ $booking->departure_flight ?? '' }}</span>
                        </div>
                        <div class="info-cell">
                            <span class="info-label">PNR / Ticket</span>
                            <span class="info-val" style="font-weight:800;color:var(--primary);">{{ $booking->departure_pnr ?? '—' }}</span>
                        </div>
                        <div class="info-cell">
                            <span class="info-label">Departure Date & Time</span>
                            <span class="info-val">{{ $booking->departure_date ? date('d M Y', strtotime($booking->departure_date)) : '—' }} {{ $booking->departure_time ? '('.date('h:i A', strtotime($booking->departure_time)).')' : '' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($booking->arrival_date)
                <!-- Return / Arrival -->
                <div class="flight-item">
                    <div style="font-size:11px;font-weight:700;color:#0284c7;margin-bottom:4px;text-transform:uppercase;">
                        <i class="mdi mdi-airplane-landing"></i> Return Flight
                    </div>
                    <div class="info-grid">
                        <div class="info-cell">
                            <span class="info-label">Airline & Flight #</span>
                            <span class="info-val">{{ $booking->arrival_airline ?? '—' }} {{ $booking->arrival_flight ?? '' }}</span>
                        </div>
                        <div class="info-cell">
                            <span class="info-label">PNR / Ticket</span>
                            <span class="info-val" style="font-weight:800;color:#0284c7;">{{ $booking->arrival_pnr ?? '—' }}</span>
                        </div>
                        <div class="info-cell">
                            <span class="info-label">Return Date & Time</span>
                            <span class="info-val">{{ $booking->arrival_date ? date('d M Y', strtotime($booking->arrival_date)) : '—' }} {{ $booking->arrival_time ? '('.date('h:i A', strtotime($booking->arrival_time)).')' : '' }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- 4. Transport Transfers -->
            @if($booking->transports && $booking->transports->count() > 0)
            <div class="section-box">
                <div class="section-header">
                    <span class="section-title"><i class="mdi mdi-bus"></i> Transport & Transfers</span>
                </div>
                @foreach($booking->transports as $tr)
                    <div style="background:#f8fafc;padding:10px 12px;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:6px;font-size:12.5px;">
                        <strong><i class="mdi mdi-routes me-1 text-primary"></i> {{ $tr->route }}</strong>
                        @if($tr->transport_type)<br><small class="text-muted">Vehicle: {{ ucfirst(str_replace('_', ' ', $tr->transport_type)) }}</small>@endif
                        @if($tr->notes)<br><small class="text-secondary"><i class="mdi mdi-information-outline me-1"></i>{{ $tr->notes }}</small>@endif
                    </div>
                @endforeach
            </div>
            @endif

            <!-- 5. Emergency Contacts & Nominee -->
            <div class="emergency-banner">
                <div class="emergency-icon">
                    <i class="mdi mdi-phone-alert"></i>
                </div>
                <div>
                    <strong style="color: #991b1b; font-size: 13px;">Emergency Helpline & Support:</strong>
                    <div style="font-size: 12px; color: #7f1d1d; margin-top: 2px;">
                        Agency 24/7 Helpline: <a href="tel:{{ $agencyPhone }}" style="color:#991b1b;font-weight:800;">{{ $agencyPhone }}</a><br>
                        Saudi Police / Emergency: <strong>911</strong> · Ministry of Hajj: <strong>1966</strong>
                    </div>
                </div>
            </div>

            <!-- Footer Agency Disclaimer -->
            <div class="pass-footer">
                <p class="mb-1"><strong>{{ $appName }}</strong> · Official Hajj Pilgrim Pass System</p>
                <p class="mb-0 text-muted">Ministry of Religious Affairs & Interfaith Harmony Government of Pakistan Enrolment</p>
            </div>
        </div>
    </div>

</body>
</html>
