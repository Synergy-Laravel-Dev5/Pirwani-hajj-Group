<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Passenger Manifest - {{ $bus->group_name }}</title>
    <style>
        @page {
            margin: 8mm 8mm 8mm 8mm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 13px;
            font-weight: 700;
            color: #b45309;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .meta-box {
            font-size: 10.5px;
            line-height: 1.4;
            text-align: right;
        }
        .info-ribbon {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
        }
        .info-ribbon td {
            padding: 6px 8px;
            font-size: 11px;
            border: 1px solid #cbd5e1;
        }
        .info-ribbon td strong {
            color: #0f172a;
        }
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .manifest-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 6px 4px;
            border: 1px solid #000000;
            text-transform: uppercase;
        }
        .manifest-table td {
            padding: 5px 4px;
            vertical-align: middle;
            font-size: 10.5px;
            border: 1px solid #334155;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .seat-badge {
            background-color: #0f172a;
            color: #ffffff;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 800;
            font-size: 11px;
            display: inline-block;
        }
        .gender-male { color: #1e3a8a; font-weight: bold; }
        .gender-female { color: #9d174d; font-weight: bold; }
        .footer-sign-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .footer-sign-table td {
            padding: 10px 20px;
            vertical-align: top;
            font-size: 11px;
        }
        .sign-line {
            border-top: 1px dashed #000;
            width: 80%;
            margin-top: 30px;
            padding-top: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- Official Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <div class="header-title">PIRWANI HAJJ GROUP</div>
                <div class="header-subtitle">Official Bus Passenger Manifest</div>
                <div style="font-size: 10px; color: #475569; margin-top: 2px;">
                    Licensed Hajj & Umrah Tour Operator &bull; Kingdom of Saudi Arabia & Pakistan
                </div>
            </td>
            <td style="width: 40%; vertical-align: middle;" class="meta-box">
                <div><strong>Sector / Route:</strong> {{ strtoupper($bus->route_name ?? '—') }}</div>
                <div><strong>Printed On:</strong> {{ now()->format('d M Y, h:i A') }}</div>
                <div><strong>Manifest Status:</strong> Confirmed Official Roster</div>
            </td>
        </tr>
    </table>

    {{-- Bus Information Ribbon --}}
    <table class="info-ribbon">
        <tr>
            <td><strong>Bus / Vehicle:</strong> {{ $bus->group_name }} ({{ $bus->vehicle ? $bus->vehicle->brand_name : 'Charter Coach' }})</td>
            <td><strong>Vehicle Plate #:</strong> {{ $bus->plate_number ?: '—' }}</td>
            <td><strong>Driver Name:</strong> {{ $bus->driver_name ?: '—' }}</td>
            <td><strong>Driver Phone:</strong> {{ $bus->driver_phone ?: '—' }}</td>
        </tr>
        <tr>
            <td><strong>Departure Date:</strong> {{ $bus->departure_date ? date('d/m/Y', strtotime($bus->departure_date)) : 'Scheduled' }}</td>
            <td><strong>Departure Time:</strong> {{ $bus->departure_time ? date('h:i A', strtotime($bus->departure_time)) : 'As Scheduled' }}</td>
            <td><strong>Seating Capacity:</strong> {{ $bus->bus_capacity }} Seats</td>
            <td><strong>Assigned Passengers:</strong> <strong>{{ $passengers->count() }}</strong> / {{ $bus->bus_capacity }} (Available: {{ max(0, $bus->bus_capacity - $passengers->count()) }})</td>
        </tr>
    </table>

    {{-- Manifest Table --}}
    <table class="manifest-table">
        <thead>
            <tr>
                <th style="width: 45px;">SEAT #</th>
                <th style="width: 75px;">HAJJ ID</th>
                <th style="width: 70px;">HB #</th>
                <th style="min-width: 170px; text-align: left; padding-left: 6px;">PILGRIM FULL NAME</th>
                <th style="width: 55px;">GENDER</th>
                <th style="width: 85px;">PASSPORT NO</th>
                <th style="width: 95px;">CONTACT NO</th>
                <th style="width: 85px;">VOUCHER #</th>
                <th style="min-width: 120px; text-align: left; padding-left: 6px;">PACKAGE</th>
                <th style="width: 80px;">CAMP / MAKTAB</th>
            </tr>
        </thead>
        <tbody>
            @forelse($passengers as $idx => $gp)
                @php
                    $p = $gp->person;
                    $b = $gp->booking;
                    $maktab = $b?->camp ?: ($b?->package?->camp_category ? 'Maktab ' . $b->package->camp_category : '—');
                @endphp
                <tr>
                    <td class="text-center">
                        <span class="seat-badge">{{ $gp->seat_number ?: ($idx + 1) }}</span>
                    </td>
                    <td class="text-center fw-bold">{{ $p?->hajj_id ?: '—' }}</td>
                    <td class="text-center fw-bold">{{ $p?->hb_number ?: '—' }}</td>
                    <td class="text-left fw-bold" style="padding-left: 6px;">
                        {{ $p?->full_name ?? ($b?->client?->name ?? 'Guest') }}
                    </td>
                    <td class="text-center {{ strtoupper($p?->gender ?? '') === 'FEMALE' ? 'gender-female' : 'gender-male' }}">
                        {{ strtoupper($p?->gender ?? '—') }}
                    </td>
                    <td class="text-center fw-bold">{{ $p?->passport_number ?? ($b?->passport_number ?? '—') }}</td>
                    <td class="text-center">{{ $p?->phone ?? ($b?->client?->phone ?? '—') }}</td>
                    <td class="text-center fw-bold">{{ $b?->voucher_number ?? ('UB' . ($b?->id ?? '')) }}</td>
                    <td class="text-left" style="padding-left: 6px;">{{ $b?->package?->package_title ?? ($b?->package?->name ?? '—') }}</td>
                    <td class="text-center">{{ $maktab }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-3" style="color: #64748b; font-style: italic;">
                        No passengers currently assigned to this bus manifest.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Signature Sign-off Section --}}
    <table class="footer-sign-table">
        <tr>
            <td style="width: 33.3%;">
                <div class="sign-line">Prepared By (Transport Desk)</div>
            </td>
            <td style="width: 33.3%; text-align: center;">
                <div class="sign-line" style="margin: 30px auto 0 auto;">Bus Captain / Driver Signature</div>
            </td>
            <td style="width: 33.3%; text-align: right;">
                <div class="sign-line" style="margin-left: auto;">Group Leader / Hajj Coordinator</div>
            </td>
        </tr>
    </table>

</body>
</html>
