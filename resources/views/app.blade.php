<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <title>Accreditation</title>

    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body class="antialiased">
@inertia

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="{{ asset('js/html2canvas.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.js "></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.1.1/jspdf.umd.min.js"></script>
</body>
</html>
