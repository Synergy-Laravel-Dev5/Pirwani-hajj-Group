<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hajj Application & Contract - {{ $selectedPackage->package_title ?? 'Maxims Group' }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0e1726;
            --dark-grey: #3e3e3e;
            --light-grey: #f2f2f2;
            --border-color: #888888;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #e9ecef;
            color: #111;
            padding: 20px 10px;
        }

        .contract-card {
            max-width: 950px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 25px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            box-sizing: border-box;
        }

        .main-header-banner {
            background: var(--dark-grey);
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 2px;
        }

        .main-header-banner h1 {
            font-weight: 900;
            font-size: 1.5rem;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .section-header-banner {
            background: #666;
            color: #fff;
            font-weight: 800;
            font-size: 0.95rem;
            text-align: center;
            padding: 5px;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-badge {
            border: 1px solid #777;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 5px;
            background: #fdfdfd;
        }

        .policy-text {
            font-size: 0.70rem;
            line-height: 1.4;
            color: #333;
        }

        .policy-text ul {
            padding-left: 18px;
            margin-bottom: 4px;
        }

        .docs-list {
            font-size: 0.72rem;
            font-weight: 600;
            color: #222;
            line-height: 1.45;
        }

        .app-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            margin-top: 5px;
        }

        .app-table td, .app-table th {
            border: 1px solid var(--border-color);
            padding: 5px 8px;
            vertical-align: middle;
        }

        .app-table .lbl {
            font-weight: 700;
            font-size: 0.70rem;
            text-transform: uppercase;
            color: #333;
        }

        .app-input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 2px 0;
        }

        .app-input:focus {
            background: #fff9e6;
        }

        .photo-box {
            width: 120px;
            height: 140px;
            border: 2px dashed #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            background: #fafafa;
            margin: 0 auto;
            cursor: pointer;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .btn-submit-app {
            background: var(--navy);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            padding: 10px 30px;
            border: none;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }

        .btn-submit-app:hover {
            background: #000;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="contract-card">
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger">
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('hajj-application.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Main Header -->
            <div class="main-header-banner">
                <h1>CONTRACT HAJJ {{ $selectedPackage->year ?? '2027 / 1448 AH' }}</h1>
            </div>

            <!-- Top Payment Plan & Package Selection -->
            <div class="row g-2 mb-2">
                <div class="col-md-5">
                    <div class="fw-bold mb-1" style="font-size: 0.85rem;">PAYMENT PLAN</div>
                    <div>
                        <span class="plan-badge">50% at the time of booking</span>
                        <span class="plan-badge">25% by 15 September 2026</span>
                        <span class="plan-badge">25% by 15 December 2026</span>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="d-flex align-items-center mb-1">
                        <label class="fw-bold me-2" style="font-size: 0.85rem; white-space: nowrap;">PACKAGE NAME:</label>
                        <select name="package_id" id="packageSelect" class="form-select form-select-sm fw-bold">
                            @foreach ($packages as $pkg)
                                <option value="{{ $pkg->id }}" {{ (old('package_id', $package_id) == $pkg->id || ($selectedPackage && $selectedPackage->id == $pkg->id)) ? 'selected' : '' }}>
                                    {{ $pkg->package_title ?? ($pkg->name ?? ($pkg->code ?? 'Package #' . $pkg->id)) }} ({{ $pkg->days ?? 14 }} Days)
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="package_name" id="packageNameInput" value="{{ $selectedPackage->package_title ?? ($selectedPackage->name ?? '') }}">
                    </div>
                </div>
            </div>

            <!-- Refund & Substitution Policy -->
            <div class="policy-text mt-2 p-2 bg-light border rounded">
                <b>Refund &amp; Substitution Policy:</b><br>
                • If a Haji needs to cancel due to any emergency before 15th December 2026, a full refund will be issued after deducting US $350 per person as service charges.<br>
                • After 15th December 2026, and before Hajj visa issuance, the case will be treated as a substitution. The Haji may:<br>
                <div class="ms-3">
                    - Nominate another person in their place, or<br>
                    - Allow us to offer the same package to another waiting applicant. (depending on demand)<br>
                </div>
                In either case, US $350 per person will be deducted as service charges.<br>
                <small class="text-muted"><b>Important Note:</b> All policies / Packages / Bookings are subject to the final guidelines issued by the Saudi Government and the Government of Pakistan. Services are subject to payment plan and company will have the right to cancel / substitute in case payment plan not followed.</small>
            </div>

            <!-- Required Documents Section -->
            <div class="section-header-banner">
                REQUIRED DOCUMENTS
            </div>
            <div class="row docs-list px-2 py-1">
                <div class="col-md-6">
                    1) Passenger passport copy (Upto 30 November 2027)<br>
                    2) 2 photographs 4x3cm (white background)<br>
                    3) Passenger ID Card copy (Nadra)
                </div>
                <div class="col-md-6">
                    4) Next of Kin ID Card, contact number &amp; Relation<br>
                    5) Passenger blood group<br>
                    6) Children B-FORM required (Nadra)
                </div>
            </div>

            <!-- Hajj Application Grid Form -->
            <div class="section-header-banner">
                HAJJ APPLICATION
            </div>

            <table class="app-table">
                <tbody>
                    <tr>
                        <td rowspan="5" style="width: 140px; text-align: center; background: #fafafa;">
                            <div class="photo-box" onclick="document.getElementById('photoInput').click()">
                                <img id="photoPreview" src="" style="display: none;">
                                <div id="photoPlaceholder">
                                    <div style="font-size: 1.5rem; color: #888;">📷</div>
                                    <span style="font-size: 0.65rem; color: #666; font-weight: 600;">Upload Photo<br>(White BG)</span>
                                </div>
                            </div>
                            <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;" onchange="previewPhoto(event)">
                        </td>
                        <td colspan="2" class="p-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="lbl me-3">GENDER:</span>
                                <div>
                                    <label class="me-3 fw-bold"><input type="radio" name="gender" value="Male" {{ old('gender', 'Male') == 'Male' ? 'checked' : '' }}> MALE</label>
                                    <label class="fw-bold"><input type="radio" name="gender" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}> FEMALE</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%;">
                            <span class="lbl">SUR NAME:</span>
                            <input type="text" name="surname" class="app-input" value="{{ old('surname') }}" placeholder="e.g. Khan">
                        </td>
                        <td>
                            <span class="lbl">GIVEN NAME: *</span>
                            <input type="text" name="given_name" class="app-input" value="{{ old('given_name') }}" placeholder="e.g. Muhammad Ali" required>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lbl">CNIC / NIC No: *</span>
                            <input type="text" name="cnic_no" class="app-input" value="{{ old('cnic_no') }}" placeholder="e.g. 42101-1234567-1" required>
                        </td>
                        <td>
                            <span class="lbl">DATE OF BIRTH:</span>
                            <input type="date" name="dob" class="app-input" value="{{ old('dob') }}">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lbl">PASSPORT NO:</span>
                            <input type="text" name="passport_no" class="app-input" value="{{ old('passport_no') }}" placeholder="e.g. AB1234567">
                        </td>
                        <td>
                            <span class="lbl">PASSPORT EXPIRY:</span>
                            <input type="date" name="passport_expiry" class="app-input" value="{{ old('passport_expiry') }}">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="lbl">FATHER'S / HUSBAND'S NAME:</span>
                            <input type="text" name="father_or_husband_name" class="app-input" value="{{ old('father_or_husband_name') }}">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <span class="lbl">PRESENT POSTAL ADDRESS:</span>
                            <input type="text" name="postal_address" class="app-input" value="{{ old('postal_address') }}" placeholder="House #, Street, Area, City">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lbl">TEHSIL CODE:</span>
                            <input type="text" name="tehsil_code" class="app-input" value="{{ old('tehsil_code') }}">
                        </td>
                        <td>
                            <span class="lbl">MOBILE NO: *</span>
                            <input type="text" name="mobile_no" class="app-input" value="{{ old('mobile_no') }}" placeholder="0300-1234567" required>
                        </td>
                        <td>
                            <span class="lbl">TELEPHONE NO:</span>
                            <input type="text" name="telephone_no" class="app-input" value="{{ old('telephone_no') }}">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="lbl">ARE YOU MARRIED?</span>
                            <select name="is_married" class="app-input">
                                <option value="Yes" {{ old('is_married') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('is_married') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </td>
                        <td>
                            <span class="lbl">FIQAH:</span>
                            <input type="text" name="fiqah" class="app-input" value="{{ old('fiqah', 'Hanafi') }}" placeholder="e.g. Hanafi, Jafari">
                        </td>
                        <td>
                            <span class="lbl">BLOOD GROUP:</span>
                            <select name="blood_group" class="app-input">
                                <option value="">-- Select --</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Questions Checklist Table -->
            <table class="app-table mt-1">
                <tbody>
                    <tr>
                        <td style="width: 70%;" class="lbl">PERFORMED HAJJ IN LAST 5 YEARS?</td>
                        <td class="text-center">
                            <label class="me-3 fw-bold"><input type="radio" name="performed_hajj_last_5_years" value="YES" {{ old('performed_hajj_last_5_years') == 'YES' ? 'checked' : '' }}> YES</label>
                            <label class="fw-bold"><input type="radio" name="performed_hajj_last_5_years" value="NO" {{ old('performed_hajj_last_5_years', 'NO') == 'NO' ? 'checked' : '' }}> NO</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">WANT TO PERFORM HAJJ-E-BADAL?</td>
                        <td class="text-center">
                            <label class="me-3 fw-bold"><input type="radio" name="hajj_e_badal" value="YES" {{ old('hajj_e_badal') == 'YES' ? 'checked' : '' }}> YES</label>
                            <label class="fw-bold"><input type="radio" name="hajj_e_badal" value="NO" {{ old('hajj_e_badal', 'NO') == 'NO' ? 'checked' : '' }}> NO</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">PERFORM HAJJ AS A GROUP WORKER?</td>
                        <td class="text-center">
                            <label class="me-3 fw-bold"><input type="radio" name="group_worker" value="YES" {{ old('group_worker') == 'YES' ? 'checked' : '' }}> YES</label>
                            <label class="fw-bold"><input type="radio" name="group_worker" value="NO" {{ old('group_worker', 'NO') == 'NO' ? 'checked' : '' }}> NO</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">ARE YOU MEHRAM OF A LADY?</td>
                        <td class="text-center">
                            <label class="me-3 fw-bold"><input type="radio" name="is_mehram_of_lady" value="YES" {{ old('is_mehram_of_lady') == 'YES' ? 'checked' : '' }}> YES</label>
                            <label class="fw-bold"><input type="radio" name="is_mehram_of_lady" value="NO" {{ old('is_mehram_of_lady', 'NO') == 'NO' ? 'checked' : '' }}> NO</label>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Emergency Nominee & Mehram Section -->
            <div class="row g-1 mt-1">
                <div class="col-md-6">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th colspan="2" class="lbl text-center bg-light">NOMINEE IN CASE OF DEATH (MUST BE ADULT / RELATIVE) EMERGENCY CONTACT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 35%;" class="lbl">NAME</td>
                                <td><input type="text" name="nominee_name" class="app-input" value="{{ old('nominee_name') }}"></td>
                            </tr>
                            <tr>
                                <td class="lbl">CONTACT NO</td>
                                <td><input type="text" name="nominee_contact" class="app-input" value="{{ old('nominee_contact') }}"></td>
                            </tr>
                            <tr>
                                <td class="lbl">CNIC NO</td>
                                <td><input type="text" name="nominee_cnic" class="app-input" value="{{ old('nominee_cnic') }}"></td>
                            </tr>
                            <tr>
                                <td class="lbl">RELATIONSHIP</td>
                                <td><input type="text" name="nominee_relation" class="app-input" value="{{ old('nominee_relation') }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th colspan="2" class="lbl text-center bg-light">MEHRAM'S DETAILS (IF APPLICABLE)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="width: 35%;" class="lbl">NAME</td>
                                <td><input type="text" name="mehram_name" class="app-input" value="{{ old('mehram_name') }}"></td>
                            </tr>
                            <tr>
                                <td class="lbl">RELATIONSHIP</td>
                                <td><input type="text" name="mehram_relation" class="app-input" value="{{ old('mehram_relation') }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Declaration and Submit Row -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                <div>
                    <label class="fw-semibold" style="font-size: 0.8rem;">
                        <input type="checkbox" required> I confirm the above information is accurate &amp; agree to terms.
                    </label>
                </div>
                <div>
                    <button type="submit" class="btn-submit-app">Submit Application ➔</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                    document.getElementById('photoPlaceholder').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('packageSelect').addEventListener('change', function() {
            const selectedText = this.options[this.selectedIndex].text;
            document.getElementById('packageNameInput').value = selectedText;
        });
    </script>
</body>

</html>
