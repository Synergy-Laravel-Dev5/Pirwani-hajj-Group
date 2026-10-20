<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->reservation_title }} - {{ $sale->invoice_number }}</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Cinzel:wght@700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Outfit', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 24px 0;
            font-size: 13px;
        }

        .sheet-container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-radius: 4px;
            padding: 36px 42px;
        }

        .header-brand {
            border-bottom: 2px solid #bfa15f;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo img {
            max-height: 75px;
            width: auto;
        }

        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 800;
            color: #001030;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .brand-meta {
            font-size: 11px;
            color: #475569;
        }

        .sheet-title-banner {
            background: #001030;
            color: #fef08a;
            padding: 8px 16px;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 22px;
            border-radius: 3px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .meta-label {
            font-weight: 700;
            color: #001030;
            width: 130px;
            text-transform: uppercase;
        }

        .meta-val {
            font-weight: 600;
            color: #0f172a;
        }

        .salutation {
            font-size: 13px;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .salutation strong {
            color: #001030;
        }

        /* Reservation Table matching the sheet format */
        .res-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .res-table th {
            background-color: #001030;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 6px;
            text-transform: uppercase;
            font-size: 11px;
            border: 1px solid #001030;
            text-align: center;
        }

        .res-table td {
            border: 1px solid #cbd5e1;
            padding: 7px 6px;
            vertical-align: middle;
        }

        .res-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .total-row td {
            font-weight: 800;
            background-color: #f1f5f9;
            border-top: 2px solid #001030;
            font-size: 12.5px;
        }

        /* Section blocks */
        .section-header {
            font-weight: 800;
            color: #001030;
            font-size: 13px;
            margin-top: 18px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }

        .terms-list {
            margin-bottom: 12px;
            font-size: 11.5px;
            color: #1e293b;
            line-height: 1.5;
            white-space: pre-line;
        }

        .payment-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 14px;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .payment-procedure-text {
            font-size: 11.5px;
            line-height: 1.5;
            white-space: pre-line;
            color: #1e293b;
        }

        .payment-amounts-table {
            width: 280px;
            font-size: 12px;
        }

        .payment-amounts-table td {
            padding: 3px 6px;
        }

        .signature-box {
            margin-top: 36px;
            display: inline-block;
        }

        .signature-box .sign-salute {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 24px;
            color: #001030;
        }

        .signature-box .ceo-name {
            font-weight: 800;
            font-size: 14px;
            color: #001030;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: 1px solid #475569;
            padding-top: 4px;
            display: inline-block;
        }

        .signature-box .ceo-title {
            font-size: 12px;
            color: #475569;
            font-weight: 700;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .sheet-container {
                border: none;
                box-shadow: none;
                padding: 15px 20px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 8mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (Hidden in Print) -->
    <div class="container mb-3 no-print" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('hotel-sales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Invoices
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('hotel-sales.edit', $sale->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit Invoice
                </a>
                <button onclick="window.print()" class="btn btn-dark btn-sm px-3">
                    <i class="bi bi-printer me-1"></i> Print / Save as PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Official Sheet Container -->
    <div class="sheet-container">

        <!-- Header Branding -->
        <div class="header-brand">
            <div class="brand-logo">
                <img src="{{ asset('assets/images/PIRWANI PNG FILE.png') }}" alt="Pirwani Hajj Group">
            </div>
            <div class="text-end">
                <div class="brand-title">PIRWANI HAJJ GROUP (PVT) LTD.</div>
                <div class="brand-meta">
                    Govt. Licence No. 2990 | Munazzam # 2990<br>
                    Office # 106, 1st Floor, Balad Trade Center, Bahadurabad, Karachi.<br>
                    Phone: 021-34133006, 021-34133007
                </div>
            </div>
        </div>

        <!-- Banner Title -->
        <div class="sheet-title-banner">
            {{ $sale->reservation_title ?: 'HAJJ RESERVATION 1448/2027' }}
        </div>

        <!-- Metadata Information -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">Date</td>
                <td>: <strong>{{ $sale->invoice_date ? $sale->invoice_date->format('d-m-Y') : date('d-m-Y') }}</strong></td>
                <td class="meta-label text-end">Invoice Ref :</td>
                <td class="meta-val text-end" style="width: 140px;">{{ $sale->invoice_number }}</td>
            </tr>
            <tr>
                <td class="meta-label">CLIENT NAME</td>
                <td>: <strong>{{ $sale->customer_name }}</strong> @if($sale->sale_for === 'company') (Agent) @endif</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="meta-label">Attn.</td>
                <td>: {{ $sale->attn ?: '—' }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="meta-label">Contact</td>
                <td>: {{ $sale->contact ?: '—' }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <!-- Salutation -->
        <div class="salutation">
            <strong>Dear Sir :</strong><br>
            We are pleased to confirm the following reservation for {{ strtolower($sale->reservation_title) }}:
        </div>

        <!-- Reservation Table -->
        <table class="res-table">
            <thead>
                <tr>
                    <th style="width: 22%;">Hotel</th>
                    <th style="width: 11%;">ROOM TYP</th>
                    <th style="width: 9%;">ROOM QTY</th>
                    <th style="width: 13%;">MP / NTS</th>
                    <th style="width: 12%;">Check In</th>
                    <th style="width: 12%;">Check Out</th>
                    <th style="width: 7%;">NIGHTS</th>
                    <th style="width: 14%;">RATE PER {{ $sale->items->contains('allotment_basis', 'bed') ? 'UNIT' : 'ROOM' }}</th>
                    <th style="width: 12%;">AMOUNT ({{ $sale->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $lastHotel = null;
                @endphp
                @foreach($sale->items as $item)
                    <tr>
                        <td class="fw-bold">
                            @if($item->hotel_name !== $lastHotel)
                                {{ $item->hotel_name }}
                                @php $lastHotel = $item->hotel_name; @endphp
                            @endif
                        </td>
                        <td class="text-center fw-semibold">
                            @if($item->allotment_basis === 'bed' || strtoupper($item->room_type) === 'BED')
                                <span class="badge bg-warning text-dark border">BED</span>
                            @else
                                {{ strtoupper($item->room_type) }}
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                        <td class="text-center fs-11">{{ $item->meal_plan ?: 'ROOM ONLY' }}</td>
                        <td class="text-center">
                            @if($item->hijri_check_in)
                                <div class="fw-bold fs-11 text-primary">{{ $item->hijri_check_in }}</div>
                            @endif
                            @if($item->check_in)
                                <small class="text-muted">{{ $item->check_in->format('d-M-Y') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->hijri_check_out)
                                <div class="fw-bold fs-11 text-primary">{{ $item->hijri_check_out }}</div>
                            @endif
                            @if($item->check_out)
                                <small class="text-muted">{{ $item->check_out->format('d-M-Y') }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->no_of_nights }}</td>
                        <td class="text-end">{{ $item->rate_per_unit > 0 ? number_format($item->rate_per_unit, 2) : '—' }}</td>
                        <td class="text-end fw-bold">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="7" class="text-end text-uppercase">TOTAL AMOUNT</td>
                    <td class="text-center">{{ $sale->currency }}</td>
                    <td class="text-end text-primary fs-13">{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Terms & Conditions -->
        <div class="section-header">Terms & Conditions</div>
        <div class="terms-list">
@if($sale->terms_conditions){{ $sale->terms_conditions }}@else
v  Rates are quoted in Saudi Riyal per Bed / Room
v  Rates are Net & Non Commissionable
v  Confirmed reservations are non-refundable
v  15 % VAT & 15 % Muncipalty tax include of above rates
v  Any other tax if apply will be added your side.
@endif
        </div>

        <!-- Cancellation Policy -->
        <div class="section-header">Cancellation policy</div>
        <div class="terms-list">
@if($sale->cancellation_policy){{ $sale->cancellation_policy }}@else
v  100 % charge Applicable once confirmed No Changes / Amendment Permitted.
@endif
        </div>

        <!-- Payment Procedure & Balances -->
        <div class="section-header">Payment Procedure</div>
        <div class="payment-box">
            <div class="payment-procedure-text">
@if($sale->payment_procedure){{ $sale->payment_procedure }}@else
1.  20 % ADVANCE BEFORE DUE DATE
    BALANCE PAYMENT FROM E.HAJJ
@endif
            </div>

            <div class="payment-amounts-table">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="fw-bold text-uppercase">TILL REC :</td>
                        <td class="text-end text-success fw-bold">{{ $sale->currency }} {{ number_format($sale->till_received, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold text-uppercase">BALANCE REMAINING :</td>
                        <td class="text-end text-danger fw-bold fs-13">{{ $sale->currency }} {{ number_format($sale->balance_remaining, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-box">
            <div class="sign-salute">Sincerely Yours,</div>
            <div class="ceo-name">{{ $sale->ceo_name ?: 'MUHAMMAD YOUSUF PIRWANI' }}</div>
            <div class="ceo-title">{{ $sale->ceo_title ?: 'C.E.O,' }}</div>
        </div>

    </div>

</body>
</html>
