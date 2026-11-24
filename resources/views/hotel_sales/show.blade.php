<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->reservation_title ?: 'HAJJ RESERVATION' }} - {{ $sale->invoice_number }}</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            margin: 0;
            padding: 24px 0;
            font-size: 11.5px;
            line-height: 1.4;
        }

        .sheet-container {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d1d5db;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 32px 38px;
        }

        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .voucher-header-logo {
            height: 82px;
            width: auto;
            object-fit: contain;
        }

        .company-name-title {
            font-size: 19px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.15;
            margin-bottom: 2px;
        }

        .company-reg-line {
            font-size: 11px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 2px;
        }

        .company-contact-line {
            font-size: 10px;
            color: #333333;
            line-height: 1.35;
        }

        .header-accreditation {
            text-align: right;
            font-size: 11px;
            font-weight: 700;
            color: #000000;
        }

        .voucher-title {
            font-size: 14.5px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            color: #000000;
            margin-bottom: 4px;
        }

        .voucher-title-hr {
            border: none;
            border-top: 1.5px solid #000000;
            margin: 0 0 14px 0;
            opacity: 1;
        }

        .voucher-meta-table {
            border-collapse: collapse;
            font-size: 12px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 14px;
        }

        .voucher-meta-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 65px;
        }

        .meta-sep {
            width: 16px;
            text-align: center;
        }

        .meta-val {
            padding-left: 2px;
        }

        .voucher-salutation {
            font-size: 12px;
            color: #000000;
            margin-bottom: 14px;
            line-height: 1.45;
        }

        /* Official Contract Table */
        .res-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-bottom: 14px;
            font-size: 10.5px;
            color: #000000;
        }

        .res-table th {
            border: 1px solid #000000;
            padding: 5px 3px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            background: #ffffff;
            line-height: 1.25;
            vertical-align: middle;
        }

        .res-table td {
            border: 1px solid #000000;
            padding: 5px 4px;
            vertical-align: middle;
            background: #ffffff;
            line-height: 1.25;
        }

        .total-row td {
            font-weight: 700;
            padding: 5px 4px;
        }

        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 16px;
            font-size: 11px;
            line-height: 1.45;
            color: #000000;
        }

        .balance-box-table {
            border: 1px solid #000000;
            border-collapse: collapse;
            width: 100%;
            max-width: 290px;
            font-size: 11px;
            font-weight: 700;
        }

        .balance-box-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .sheet-container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden during Print) -->
    <div class="container mb-3 no-print" style="max-width: 860px;">
        <div class="d-flex justify-content-between align-items-center bg-white p-2 px-3 border rounded shadow-sm">
            <a href="{{ route('hotel-sales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Invoices
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('hotel-sales.edit', $sale->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit Invoice
                </a>
                <button onclick="window.print()" class="btn btn-dark btn-sm px-3 fw-bold">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Official Contract Sheet Container -->
    <div class="sheet-container">

        <!-- 1. Official Company Logo (from Login Screen) & Header Credentials -->
        <div class="voucher-header">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group (Pvt.) Ltd." class="voucher-header-logo">
                <div>
                    <div class="company-name-title">PIRWANI HAJJ GROUP (PVT) LTD.</div>
                    <div class="company-reg-line">Govt. Licence No. 2990 &nbsp;|&nbsp; Hajj Reg. No. 4171 &nbsp;|&nbsp; Munazzam # 2990</div>
                    <div class="company-contact-line">
                        Office # 106, 1st Floor, Balad Trade Center, Alamgir Road, BMCHS, Sharafabad, Karachi.<br>
                        Tel: 021-34133006, 021-34133007 &nbsp;|&nbsp; Email: pirwanihajjgroup@gmail.com
                    </div>
                </div>
            </div>
            <div class="header-accreditation">
                <div style="font-size: 13px; font-weight: 800; letter-spacing: 0.5px;">IATA ACCREDITED</div>
                <div style="font-size: 11px; color: #222222;">Numeric Code: 27-3 1018 3</div>
                <div style="font-size: 10.5px; color: #444444; margin-top: 2px;">HOAP Member</div>
            </div>
        </div>

        <!-- 2. Reservation Title & Horizontal Rule -->
        <div class="voucher-title">
            {{ strtoupper($sale->reservation_title ?: 'HAJJ RESERVATION 1448/2027') }}
        </div>
        <hr class="voucher-title-hr">

        <!-- 3. Metadata Section -->
        <table class="voucher-meta-table">
            <tr>
                <td class="meta-label">Date</td>
                <td class="meta-sep">:</td>
                <td class="meta-val">{{ $sale->invoice_date ? $sale->invoice_date->format('d-m-Y') : date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td class="meta-label">To</td>
                <td class="meta-sep">:</td>
                <td class="meta-val">{{ strtoupper($sale->customer_name ?: ($sale->hotelClient->company_name ?? ($sale->hotelClient->name ?? ($sale->company->company_name ?? ($sale->client->name ?? 'AL MOAZZIN'))))) }}</td>
            </tr>
            <tr>
                <td class="meta-label">Attn.</td>
                <td class="meta-sep">:</td>
                <td class="meta-val">{{ strtoupper($sale->attn ?: ($sale->hotelClient->name ?? 'MUHAMMAD SALEEM')) }}</td>
            </tr>
            <tr>
                <td class="meta-label">Contact</td>
                <td class="meta-sep">:</td>
                <td class="meta-val">{{ $sale->contact ?: ($sale->hotelClient->contact ?? '') }}</td>
            </tr>
        </table>

        <!-- 4. Salutation -->
        <div class="voucher-salutation">
            <div class="fw-bold">Dear Sir :</div>
            <div>We are pleased to confirm the following reservation for {{ strtolower($sale->reservation_title ?: 'hajj 1448/2027') }}</div>
        </div>

        <!-- 5. Reservation Table with Dynamic Rowspan Grouping -->
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
                    <th style="width: 18%;">Hotel</th>
                    <th style="width: 10%;">ROOM TYP</th>
                    <th style="width: 8%;">ROOM QTY</th>
                    <th style="width: 8%;">MP / NTS</th>
                    <th style="width: 13%;">Check In</th>
                    <th style="width: 13%;">Check Out</th>
                    <th style="width: 6%;">NIGHTS</th>
                    <th style="width: 12%;">RATE PER ROOM / NHT</th>
                    <th style="width: 12%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedStays as $stay)
                    @php $rowCount = count($stay['items']); @endphp
                    @foreach($stay['items'] as $idx => $item)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ $rowCount }}" style="font-weight: 700; text-align: center; vertical-align: middle;">
                                    {{ strtoupper($stay['hotel_name']) }}
                                </td>
                            @endif
                            <td style="text-align: center; font-weight: 600;">
                                {{ strtoupper($item->room_type) }}
                            </td>
                            <td style="text-align: center; font-weight: 600;">
                                {{ $item->quantity }}
                            </td>
                            <td style="text-align: center; font-weight: 500;">
                                {{ strtoupper($item->meal_plan ?: 'RO') }}
                            </td>
                            <td style="text-align: center;">
                                @if($item->hijri_check_in)
                                    <div style="font-weight: 700;">{{ strtoupper($item->hijri_check_in) }}</div>
                                @endif
                                @if($item->check_in)
                                    <div style="font-size: 10px;">{{ \Carbon\Carbon::parse($item->check_in)->format('d-M-Y') }}</div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($item->hijri_check_out)
                                    <div style="font-weight: 700;">{{ strtoupper($item->hijri_check_out) }}</div>
                                @endif
                                @if($item->check_out)
                                    <div style="font-size: 10px;">{{ \Carbon\Carbon::parse($item->check_out)->format('d-M-Y') }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 600;">
                                {{ $item->no_of_nights }}
                            </td>
                            <td style="padding: 4px 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                    <span style="font-weight: 600;">SAR</span>
                                    <span style="font-weight: 600;">{{ $item->rate_per_unit > 0 ? number_format($item->rate_per_unit, 2) : '—' }}</span>
                                </div>
                            </td>
                            <td style="padding: 4px 6px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                    <span style="font-weight: 700;">SAR</span>
                                    <span style="font-weight: 700;">{{ number_format($item->amount, 2) }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforeach

                <!-- Total Row directly below Rate/Amount -->
                <tr class="total-row">
                    <td colspan="6" style="border: none; border-top: 1px solid #000000;"></td>
                    <td style="text-align: center; border: 1px solid #000000;">TOTAL</td>
                    <td style="border: 1px solid #000000; padding: 4px 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <span>SAR</span>
                            <span></span>
                        </div>
                    </td>
                    <td style="border: 1px solid #000000; padding: 4px 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <span>SAR</span>
                            <span style="font-weight: 800;">{{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 6. Bottom Section: Left Terms & Policies | Right Balances Box -->
        <div class="bottom-section">
            
            <!-- Left Column: Terms, Cancellation, Payment Procedure & Signatures -->
            <div style="width: 58%;">
                @if($sale->terms_conditions)
                    <div style="white-space: pre-line;">{!! nl2br(e($sale->terms_conditions)) !!}</div>
                @else
                    <div>❖ Rates are Net &amp; Non Commissionable</div>
                    <div>❖ Confirmed reservations are non-refundable</div>
                    <div style="color: #ff0000; font-weight: bold;">❖ 15 % VAT &amp; 15 % Muncipalty tax include of above rates</div>
                    <div style="color: #ff0000; font-weight: bold;">❖ Any other tax if apply will be added your side.</div>
                @endif

                <div style="margin-top: 14px;">
                    <div style="text-decoration: underline; font-weight: bold; margin-bottom: 2px;">Cancellation policy</div>
                    @if($sale->cancellation_policy)
                        <div style="white-space: pre-line;">{!! nl2br(e($sale->cancellation_policy)) !!}</div>
                    @else
                        <div>❖ 100 % charge Applicable once confirmed No Changes / Amendment Permitted.</div>
                    @endif
                </div>

                <div style="margin-top: 14px;">
                    <div style="font-weight: bold; margin-bottom: 2px;">Payment Procedure,</div>
                    @if($sale->payment_procedure)
                        <div style="white-space: pre-line;">{!! nl2br(e($sale->payment_procedure)) !!}</div>
                    @else
                        <div>1. 30 % ADVANCE BEFORE 22-OCT-2026</div>
                        <div style="padding-left: 15px;">BALANCE PAYMENT FROM E.HAJJ</div>
                    @endif
                </div>

                <div style="margin-top: 36px;">
                    <div>Sincerely Yours,</div>
                    <div style="margin-top: 45px; font-weight: 800; font-size: 12.5px; letter-spacing: 0.3px;">
                        {{ strtoupper($sale->ceo_name ?: 'MUHAMMAD HANIF PIRWANI') }}
                    </div>
                    <div style="font-weight: 700; font-size: 11px;">
                        {{ strtoupper($sale->ceo_title ?: 'CHIEF EXECUTIVE') }}
                    </div>
                </div>
            </div>

            <!-- Right Column: Till Rec & Balance Remaining Box -->
            <div style="width: 38%; display: flex; justify-content: flex-end;">
                <table class="balance-box-table">
                    <tr>
                        <td style="text-transform: uppercase;">TILL REC</td>
                        <td style="width: 45px; text-align: left;">SAR</td>
                        <td style="width: 105px; text-align: right;">{{ $sale->till_received > 0 ? number_format($sale->till_received, 2) : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="text-transform: uppercase;">BALANCE REMAINING</td>
                        <td style="width: 45px; text-align: left;">SAR</td>
                        <td style="width: 105px; text-align: right;">{{ number_format($sale->balance_remaining, 2) }}</td>
                    </tr>
                </table>
            </div>

        </div>

    </div>

</body>
</html>
