<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{config('app.name')}}</title>
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <!-- Google Font: Source Sans Pro -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}"> --}}
    @yield('css')
    
    <link rel="shortcut icon" type="image/ico" href="{{ asset('dist/img/logo.png')}}">
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">
    {{-- <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css')}}"> --}}
    <link rel="stylesheet" href="{{ asset('css/style.css')}}">
    <style>
        body{font-family: 'Poppins', sans-serif;}
    </style>
    <livewire:styles />
</head>

<body class="hold-transition layout-top-nav text-sm accent-maroon">
    <div class="wrapper">
        @include('layouts.header')

        <div class="content-wrapper">
            @yield('content')
        </div>
        <footer class="main-footer text-sm d-print-none bg-dark">
            Copyright &copy; 2020 <strong><a href="http://fts_portal">FTS Portal</a></strong> All rights reserved.
        </footer>
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
    @yield('scripts')
    <script src="{{ asset('dist/js/adminlte.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            if ($('#dataTableBuilder').length) {
                let table = $('#dataTableBuilder').DataTable();

                $('#filterBtn').click(function () {
                    table.ajax.reload();
                });

                $('#resetBtn').click(function () {
                    $('#date_from').val('');
                    $('#date_to').val('');
                    table.ajax.reload();
                });
            }
        });
    </script>
    <script>
        window.baseUrl = function(path) {
            let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
            if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
            if (path[0] === '/') path = path.slice(1);
            return basePath + '/' + path;
        };
        window.toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
    <script>
        $('a[data-notif-id]').click(function () {
            var notif_id   = $(this).data('notifId');
            var targetHref = $(this).data('href');
            var APP_URL = {!! json_encode(url('/')) !!}
            var token = $('meta[name=token]').attr('content')

            $.post(APP_URL + '/notifications/NotifMarkAsRead', {'_token':token, 'notif_id': notif_id}, function (data) {
                data.success ? (window.location.href = targetHref) : false;
            }, 'json');

            return false;
        });
    </script>
    <livewire:scripts />
</body>
</html>
