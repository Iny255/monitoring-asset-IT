<!DOCTYPE html>
<html lang="en">

<head>
    @include('_partials.header')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Sembilan | {{ $title ?? 'Default Title' }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


    <style>
        .menu-item.active>.menu-link {
            background-color: blue;
            color: white !important;
            /* Untuk memastikan teks tetap terlihat */
        }

        .bg-blue {
            background-color: #cfdff0;
            /* Warna biru */
        }

        .bg-line {
            position: relative;
            display: inline-block;
            padding: 0 5px;
            /* Adjust padding as needed */
        }

        .bg-line::before {
            content: '';
            position: absolute;
            bottom: 0;
            /* Adjust position as needed */
            left: 0;
            width: 50%;
            height: 1px;
            /* Adjust height as needed */
            background-color: #000;
            /* Adjust color as needed */
            transform: scaleX(0);
            transform-origin: bottom right;
            transition: transform 0.3s ease-out;
        }

        .bg-line:hover::before {
            transform: scaleX(1);
            transform-origin: bottom left;
        }
    </style>



</head>

<body>
    @include('_partials.nav')
    @yield('header')
    <main>
        @yield('content')
    </main>
    @include('_partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>
