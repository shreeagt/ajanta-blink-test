<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Monitor and manage doctors' campaigns with real-time data and comprehensive analytics">
    <meta name="CreativeLayers" content="ATFN">

    <!-- Title -->
    <title>Blink Test Dashboard</title>
    <meta property="og:title" content="Blink Test">
    <meta property="og:url" content="">
    {{--
    <meta property="og:image" content="{{asset('new/assets/images/preservelogo.png')}}"> --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" />


    <!-- css file -->
    <link rel="stylesheet" href="{{asset('theme/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="{{asset('theme/css/style.css')}}">
    <!-- Responsive stylesheet -->
    <link rel="stylesheet" href="{{asset('theme/css/responsive.css')}}">

    <!-- Favicon -->
    <link href="{{ asset('assets/images/brand_logo.png') }}" rel="shortcut icon" type="image/png" />


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="text-center">

    <main class="form-signin">

        @yield('content')

    </main>

    <!-- Wrapper End -->
    <script src="{{asset('theme/js/jquery-3.6.0.js')}}"></script>

    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>


    <script src="{{asset('theme/js/jquery-migrate-3.0.0.min.js')}}"></script>
    <script src="{{asset('theme/js/popper.min.js')}}"></script>
    <script src="{{asset('theme/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('theme/js/bootstrap-select.min.js')}}"></script>
    <script src="{{asset('theme/js/jquery.mmenu.all.js')}}"></script>
    <script src="{{asset('theme/js/ace-responsive-menu.js')}}"></script>
    <script src="{{asset('theme/js/snackbar.min.js')}}"></script>
    <script src="{{asset('theme/js/simplebar.js')}}"></script>
    <script src="{{asset('theme/js/parallax.js')}}"></script>
    <script src="{{('theme/js/scrollto.js')}}"></script>
    <script src="{{asset('theme/js/jquery-scrolltofixed-min.js')}}"></script>
    <script src="{{asset('theme/js/jquery.counterup.js')}}"></script>
    <script src="{{asset('theme/js/wow.min.js')}}"></script>
    <script src="{{asset('theme/js/progressbar.js')}}"></script>
    <script src="{{asset('theme/js/slider.js')}}"></script>
    <script src="{{asset('theme/js/timepicker.js')}}"></script>
    <script src="{{asset('theme/js/scrollbalance.js')}}"></script>
    <!-- Custom script for all pages -->
    <script src="{{asset('theme/js/script.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>
</body>

</html>