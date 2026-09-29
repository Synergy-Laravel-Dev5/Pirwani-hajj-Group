<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted Successfully</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            padding: 40px 15px;
        }

        .success-card {
            max-width: 650px;
            margin: 0 auto;
            background: #fff;
            padding: 35px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 3.5rem;
            color: #28a745;
            margin-bottom: 15px;
        }

        .btn-home {
            background: #0e1726;
            color: #fff;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }

        .btn-home:hover {
            background: #000;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h3 class="fw-bold text-success mb-2">Application Submitted Successfully!</h3>
        <p class="text-muted">Thank you, <b>{{ $application->given_name }} {{ $application->surname }}</b>. Your Hajj Application &amp; Contract has been recorded with reference ID <b>#APP-{{ sprintf('%04d', $application->id) }}</b>.</p>

        <div class="bg-light p-3 rounded text-start my-3" style="font-size: 0.85rem;">
            <div><b>Package:</b> {{ $application->package_name ?? ($application->package->package_title ?? 'Hajj Package') }}</div>
            <div><b>Applicant Name:</b> {{ $application->given_name }} {{ $application->surname }}</div>
            <div><b>CNIC No:</b> {{ $application->cnic_no }}</div>
            <div><b>Mobile No:</b> {{ $application->mobile_no }}</div>
            <div><b>Status:</b> <span class="badge bg-warning text-dark text-uppercase">{{ $application->status }}</span></div>
        </div>

        <p class="small text-muted">Our team will review your application and contact you soon.</p>

        <a href="{{ route('hajj-application.form') }}" class="btn-home">Submit Another Application</a>
    </div>
</body>

</html>
