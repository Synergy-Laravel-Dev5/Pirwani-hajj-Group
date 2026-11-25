<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $sale->reservation_title ?: 'HAJJ RESERVATION' }} - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm 10mm 7mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 8.8px;
            color: #000000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* 1. Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .company-name-title {
            font-size: 14.5px;
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            line-height: 1.15;
            margin-bottom: 2px;
        }

        .company-reg-line {
            font-size: 8px;
            font-weight: bold;
            color: #111111;
            margin-bottom: 2px;
        }

        .company-contact-line {
            font-size: 7.2px;
            color: #333333;
            line-height: 1.3;
        }

        .header-accreditation {
            text-align: right;
            vertical-align: middle;
        }

        .iata-title {
            font-size: 10.5px;
            font-weight: bold;
            letter-spacing: 0.4px;
            color: #000000;
        }

        .iata-code {
            font-size: 8.5px;
            color: #222222;
            font-weight: bold;
        }

        .hoap-member {
            font-size: 8px;
            color: #444444;
            margin-top: 1px;
            font-weight: bold;
        }

        /* 2. Title & Meta */
        .voucher-title {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #000000;
            margin-top: 4px;
            margin-bottom: 3px;
        }

        .voucher-title-hr {
            border: none;
            border-top: 1.5px solid #000000;
            margin: 0 0 6px 0;
        }

        .meta-table {
            border-collapse: collapse;
            font-size: 9px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 6px;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
            border: none;
        }

        .meta-label {
            width: 55px;
        }

        .meta-sep {
            width: 14px;
            text-align: center;
        }

        .voucher-salutation {
            font-size: 9px;
            color: #000000;
            margin-bottom: 6px;
            line-height: 1.35;
        }

        /* 3. Items Table */
        .res-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 8px;
            font-size: 8px;
            color: #000000;
            page-break-inside: avoid;
        }

        .res-table th {
            border: 1px solid #000000;
            padding: 4px 2px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            background-color: #f8f9fa;
            line-height: 1.2;
            vertical-align: middle;
            font-size: 7.5px;
        }

        .res-table td {
            border: 1px solid #000000;
            padding: 3px 2px;
            vertical-align: middle;
            background-color: #ffffff;
            line-height: 1.2;
        }

        .total-row td {
            font-weight: bold;
            padding: 3px 2px;
        }

        .currency-inner-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .currency-inner-table td {
            border: none;
            padding: 0 2px;
            background: transparent;
        }

        /* 4. Bottom Section */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            border: none;
            page-break-inside: avoid;
        }

        .bottom-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .terms-section {
            font-size: 8px;
            line-height: 1.35;
            color: #000000;
        }

        .balance-box-table {
            border: 1px solid #000000;
            border-collapse: collapse;
            width: 250px;
            font-size: 8.5px;
            font-weight: bold;
            margin-left: auto;
        }

        .balance-box-table td {
            border: 1px solid #000000;
            padding: 5px 6px;
        }

        .bullet-point {
            color: #000000;
            margin-right: 2px;
        }
    </style>
</head>
<body>

    <!-- 1. Header with Logo & Credentials -->
    <table class="header-table">
        <tr>
            <td style="width: 72%;">
                <table style="border: none; border-collapse: collapse;">
                    <tr>
                        @if(!empty($logoBase64))
                            <td style="border: none; padding-right: 10px; vertical-align: middle; width: 68px;">
                                <img src="{{ $logoBase64 }}" alt="Logo" style="height: 65px; width: auto;">
                            </td>
                        @endif
                        <td style="border: none; vertical-align: middle;">
                            <div class="company-name-title">PIRWANI HAJJ GROUP (PVT) LTD.</div>
                            <div class="company-reg-line">Govt. Licence No. 2990 &nbsp;|&nbsp; Hajj Reg. No. 4171 &nbsp;|&nbsp; Munazzam # 2990</div>
                            <div class="company-contact-line">
                                Office # 106, 1st Floor, Balad Trade Center, Alamgir Road, BMCHS, Sharafabad, Karachi.<br>
                                Tel: 021-34133006, 021-34133007 &nbsp;|&nbsp; Email: pirwanihajjgroup@gmail.com
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-accreditation" style="width: 28%;">
                <div class="iata-title">IATA ACCREDITED</div>
                <div class="iata-code">Numeric Code: 27-3 1018 3</div>
                <div class="hoap-member">HOAP Member</div>
            </td>
        </tr>
    </table>

    <!-- 2. Reservation Title & Rule -->
    <div class="voucher-title">
        {{ strtoupper($sale->reservation_title ?: 'HAJJ RESERVATION 1448/2027') }}
    </div>
    <div class="voucher-title-hr"></div>

    <!-- 3. Metadata Section -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Date</td>
            <td class="meta-sep">:</td>
            <td>{{ $sale->invoice_date ? $sale->invoice_date->format('d-m-Y') : date('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">To</td>
            <td class="meta-sep">:</td>
            <td>{{ strtoupper($sale->customer_name ?: ($sale->hotelClient->company_name ?? ($sale->hotelClient->name ?? ($sale->company->company_name ?? ($sale->client->name ?? 'AL MOAZZIN'))))) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Attn.</td>
            <td class="meta-sep">:</td>
            <td>{{ strtoupper($sale->attn ?: ($sale->hotelClient->name ?? 'MUHAMMAD SALEEM')) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Contact</td>
            <td class="meta-sep">:</td>
            <td>{{ $sale->contact ?: ($sale->hotelClient->contact ?? '') }}</td>
        </tr>
    </table>

    <!-- 4. Salutation -->
    <div class="voucher-salutation">
        <div style="font-weight: bold;">Dear Sir :</div>
        <div>We are pleased to confirm the following reservation for {{ strtolower($sale->reservation_title ?: 'hajj 1448/2027') }}</div>
    </div>

    <!-- 5. Reservation Table with Grouped Stays -->
    @php
        $groupedStays = [];
        $prevKey = null;
        $stayIndex = -1;
        foreach ($sale->items as $it) {
            $hKey = ($it->hotel_name ?? '') . '|' . ($it->check_in ? $it->check_in->format('Y-m-d') : '') . '|' . ($it->check_out ? $it->check_out->format('Y-m-d') : '');
            if ($hKey !== $prevKey) {
                $stayIndex++;
                $groupedStays[$stayIndex] = [
                    'hotel_name' => $it->hotel_name ?? ($it->hotel->name ?? 'Hotel Stay'),
                    'items' => [],
                ];
                $prevKey = $hKey;
            }
            $groupedStays[$stayIndex]['items'][] = $it;
        }
    @endphp

    <table class="res-table">
        <thead>
            <tr>
                <th style="width: 17%;">Hotel</th>
                <th style="width: 10%;">ROOM TYP</th>
                <th style="width: 7%;">ROOM QTY</th>
                <th style="width: 8%;">MP / NTS</th>
                <th style="width: 13%;">Check In</th>
                <th style="width: 13%;">Check Out</th>
                <th style="width: 6%;">NIGHTS</th>
                <th style="width: 13%;">RATE PER ROOM / NHT</th>
                <th style="width: 13%;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedStays as $stay)
                @php $rowCount = count($stay['items']); @endphp
                @foreach($stay['items'] as $idx => $item)
                    <tr>
                        @if($idx === 0)
                            <td rowspan="{{ $rowCount }}" style="font-weight: bold; text-align: center; vertical-align: middle;">
                                {{ strtoupper($stay['hotel_name']) }}
                            </td>
                        @endif
                        <td style="text-align: center; font-weight: bold;">
                            {{ strtoupper($item->room_type) }}
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $item->quantity }}
                        </td>
                        <td style="text-align: center;">
                            {{ strtoupper($item->meal_plan ?: 'RO') }}
                        </td>
                        <td style="text-align: center;">
                            @if($item->hijri_check_in)
                                <div style="font-weight: bold; font-size: 8px;">{{ strtoupper($item->hijri_check_in) }}</div>
                            @endif
                            @if($item->check_in)
                                <div style="font-size: 7.5px; color: #333333;">{{ \Carbon\Carbon::parse($item->check_in)->format('d-M-Y') }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($item->hijri_check_out)
                                <div style="font-weight: bold; font-size: 8px;">{{ strtoupper($item->hijri_check_out) }}</div>
                            @endif
                            @if($item->check_out)
                                <div style="font-size: 7.5px; color: #333333;">{{ \Carbon\Carbon::parse($item->check_out)->format('d-M-Y') }}</div>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $item->no_of_nights }}
                        </td>
                        <td style="padding: 2px;">
                            <table class="currency-inner-table">
                                <tr>
                                    <td style="text-align: left; font-size: 7.5px; font-weight: bold; width: 30px;">SAR</td>
                                    <td style="text-align: right; font-weight: bold; font-size: 8px;">
                                        {{ $item->rate_per_unit > 0 ? number_format($item->rate_per_unit, 2) : '—' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding: 2px;">
                            <table class="currency-inner-table">
                                <tr>
                                    <td style="text-align: left; font-size: 7.5px; font-weight: bold; width: 30px;">SAR</td>
                                    <td style="text-align: right; font-weight: bold; font-size: 8px;">
                                        {{ number_format($item->amount, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
            @endforeach

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="6" style="border: none; border-top: 1px solid #000000;"></td>
                <td style="text-align: center; border: 1px solid #000000; font-weight: bold;">TOTAL</td>
                <td style="border: 1px solid #000000; padding: 2px;">
                    <table class="currency-inner-table">
                        <tr>
                            <td style="text-align: left; font-size: 7.5px; font-weight: bold; width: 30px;">SAR</td>
                            <td style="text-align: right;"></td>
                        </tr>
                    </table>
                </td>
                <td style="border: 1px solid #000000; padding: 2px;">
                    <table class="currency-inner-table">
                        <tr>
                            <td style="text-align: left; font-size: 7.5px; font-weight: bold; width: 30px;">SAR</td>
                            <td style="text-align: right; font-weight: bold; font-size: 8.5px;">
                                {{ number_format($sale->total_amount, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 6. Bottom Section: Terms & Signature (Left) | Till & Balance (Right) -->
    <table class="bottom-table">
        <tr>
            <!-- Left: Terms & Conditions & Signatures -->
            <td style="width: 58%; padding-right: 15px;">
                <div class="terms-section">
                    @if($sale->terms_conditions)
                        <div style="white-space: pre-line;">{!! nl2br(e($sale->terms_conditions)) !!}</div>
                    @else
                        <div><span class="bullet-point">&#9670;</span> Rates are Net &amp; Non Commissionable</div>
                        <div><span class="bullet-point">&#9670;</span> Confirmed reservations are non-refundable</div>
                        <div style="color: #c00000; font-weight: bold;"><span class="bullet-point">&#9670;</span> 15 % VAT &amp; 15 % Muncipalty tax include of above rates</div>
                        <div style="color: #c00000; font-weight: bold;"><span class="bullet-point">&#9670;</span> Any other tax if apply will be added your side.</div>
                    @endif

                    <div style="margin-top: 8px;">
                        <div style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">Cancellation policy</div>
                        @if($sale->cancellation_policy)
                            <div style="white-space: pre-line;">{!! nl2br(e($sale->cancellation_policy)) !!}</div>
                        @else
                            <div><span class="bullet-point">&#9670;</span> 100 % charge Applicable once confirmed No Changes / Amendment Permitted.</div>
                        @endif
                    </div>

                    <div style="margin-top: 8px;">
                        <div style="font-weight: bold; margin-bottom: 2px;">Payment Procedure,</div>
                        @if($sale->payment_procedure)
                            <div style="white-space: pre-line;">{!! nl2br(e($sale->payment_procedure)) !!}</div>
                        @else
                            <div>1. 30 % ADVANCE BEFORE 22-OCT-2026</div>
                            <div style="padding-left: 14px;">BALANCE PAYMENT FROM E.HAJJ</div>
                        @endif
                    </div>

                    <div style="margin-top: 22px;">
                        <div style="font-size: 8.5px;">Sincerely Yours,</div>
                        <div style="margin-top: 30px; font-weight: bold; font-size: 10px; letter-spacing: 0.3px;">
                            {{ strtoupper($sale->ceo_name ?: 'MUHAMMAD HANIF PIRWANI') }}
                        </div>
                        <div style="font-weight: bold; font-size: 8.5px;">
                            {{ strtoupper($sale->ceo_title ?: 'CHIEF EXECUTIVE') }}
                        </div>
                    </div>
                </div>
            </td>

            <!-- Right: Balance Table -->
            <td style="width: 42%; text-align: right; vertical-align: top;">
                <table class="balance-box-table">
                    <tr>
                        <td style="text-transform: uppercase;">TILL REC</td>
                        <td style="width: 35px; text-align: left;">SAR</td>
                        <td style="width: 85px; text-align: right;">{{ $sale->till_received > 0 ? number_format($sale->till_received, 2) : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="text-transform: uppercase;">BALANCE REMAINING</td>
                        <td style="width: 35px; text-align: left;">SAR</td>
                        <td style="width: 85px; text-align: right;">{{ number_format($sale->balance_remaining, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
