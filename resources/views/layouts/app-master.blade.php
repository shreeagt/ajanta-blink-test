<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Monitor and manage doctors' campaigns with real-time data and comprehensive analytics">
  <meta name="generator" content="Hugo 0.87.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- CSRF Token -->
  <title>Spreading Smiles – Panel</title>
  <!-- css file -->
  <link rel="stylesheet" href="{{asset('theme/css/bootstrap.min.css')}}">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" />



  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">


  <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet' type='text/css'>


  <link rel="stylesheet" href="{{asset('theme/css/style.css')}}">
  <link rel="stylesheet" href="{{asset('theme/css/dashbord_navitaion.css')}}">
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

  <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
</head>

<style>
  .modal.fade .modal-dialog {
    -webkit-transform: translate(0);
    -ms-transform: translate(0);
    -o-transform: translate(0);
    transform: translate(0);
  }

  .top-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    height: 20px;
    /* small height just to show scrollbar */
  }

  .scroll-sync {
    height: 1px;
  }


  .logo img {
    max-width: 100px;
  }

  .header__container {
    border-bottom: 3px solid #2a4714;
  }

  thead th {
    white-space: nowrap;
    padding: 10px 20px !important;
  }

  table.dataTable thead th,
  table.dataTable thead td {
    font-size: 13px;
    text-align: center;
    align-items: center;
    vertical-align: middle;
  }

  .table {
    width: 100%;
  }

  body,
  html {
    overflow-x: hidden;
  }

  label.form-label {
    font-size: 18px;
    font-weight: 500;
    margin-top: 20px;
    color: #102a83;
    font-weight: 500;
  }

  span.log-text {
    display: inline-block;
    font-size: 16px;
    margin-left: 10px;
  }

  span.ico_height {
    display: inline-block;
    font-size: 15px;
    height: 15px;
    vertical-align: middle;
  }

  span.auth_text {
    color: #102a83;
    font-weight: 600;
  }
</style>

<body>

  <div class="wrapper">
    <div class="preloader"></div>
    <header class="dashboard_header d-block d-lg-none">
      <div class="header__container pt20 pb20 pl30 pr30">
        <div class="row justify-between items-center align-items-center">
          <div class="col-sm-4 col-xl-2">
            <div class="text-center text-lg-start d-flex align-items-center mb15-520">
              <div class="fz20 me-4">
                <a href="#" class="dashboard_sidebar_toggle_icon text-thm1 vam"><i
                    class="fa-sharp fa-solid fa-bars-staggered"></i></a>
              </div>
              <div class="dashboard_header_logo">
                <a href="/" class="logo">
                  {{-- <h3>Inyx</h3> --}}
                  {{-- <img src="{{asset('assets/images/inyx-logo.png')}}" alt=""> --}}
                </a>
              </div>
            </div>
          </div>
          @auth
            <div class="col-sm-8 col-xl-10 d-none d-md-block">
              <div class="text-center text-lg-end header_right_widgets">
                <ul class="mb0 d-flex justify-content-center justify-content-sm-end">
                  @if(Auth::user()->role_id == 1)
                    <li class=""><span class="d-inline-block">Hi {{auth()->user()->name}}</span><a
                        class="text-center d-inline-block mr-2" style="text-align: right;"
                        href="{{ route('admin.logout.perform') }}">Logout <span class="flaticon-exit"></span></a></li>
                  @elseif(Auth::user()->role_id == 2 && Auth::user()->role_id != 1)
                    <li class=""><span class="d-inline-block">Hi {{auth()->user()->name}}</span><a
                        class="text-center d-inline-block mr-2" style="text-align: right;"
                        href="{{ route('supervisor.logout') }}">Logout <span class="flaticon-exit"></span></a></li>
                  @else
                    <li class=""><span class="d-inline-block">Hi {{auth()->user()->name}}</span><a
                        class="text-center d-inline-block mr-2" style="text-align: right;"
                        href="{{ route('so.logout') }}">Logout <span class="flaticon-exit"></span></a></li>
                  @endif
                </ul>
              </div>
            </div>
          @endauth
          <div class="col-sm-3 col-xl-3 d-none d-md-block">
          </div>
          <style>
            .top-right {
              position: absolute;
              top: 0;
              right: 0;
            }
          </style>
        </div>
      </div>
    </header>
    @include('layouts.partials.navbar')
    <div class="dashboard__main pl0-md">
      @yield('content')
    </div>




    @section("scripts")

    @show
    <a class="scrollToHome" href="#"><i class="fas fa-angle-up"></i></a>
  </div>
  <!-- Wrapper End -->
  <script src="{{asset('theme/js/jquery-3.6.0.js')}}"></script>
  <!-- Script -->
  {{--
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
  <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' type='text/javascript'></script>

  <!-- Font Awesome JS -->
  <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js"
    integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ"
    crossorigin="anonymous"> </script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js"
    integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY"
    crossorigin="anonymous"> </script>
  <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
  <script src="{{asset('theme/js/jquery-migrate-3.0.0.min.js')}}"></script>
  <script src="{{asset('theme/js/popper.min.js')}}"></script>
  <script src="{{asset('theme/js/bootstrap.min.js')}}"></script>
  <script src="{{asset('theme/js/bootstrap-select.min.js')}}"></script>
  <script src="{{asset('theme/js/chart.min.js')}}"></script>
  <script src="{{asset('theme/js/chart-custome.js')}}"></script>
  <script src="{{asset('theme/js/jquery.mmenu.all.js')}}"></script>
  <script src="{{asset('theme/js/ace-responsive-menu.js')}}"></script>
  <script src="{{asset('theme/js/snackbar.min.js')}}"></script>
  <script src="{{asset('theme/js/simplebar.js')}}"></script>
  <script src="{{asset('theme/js/parallax.js')}}"></script>
  <script src="{{asset('theme/js/scrollto.js')}}"></script>
  <script src="{{asset('theme/js/jquery-scrolltofixed-min.js')}}"></script>
  <script src="{{asset('theme/js/jquery.counterup.js')}}"></script>
  <script src="{{asset('theme/js/wow.min.js')}}"></script>
  <script src="{{asset('theme/js/progressbar.js')}}"></script>
  <script src="{{asset('theme/js/slider.js')}}"></script>
  <script src="{{asset('theme/js/timepicker.js')}}"></script>
  <script src="{{asset('theme/js/dashboard-script.js')}}"></script>

  <!-- Custom script for all pages -->
  <script src="{{asset('theme/js/script.js')}}"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Pie chart
      new Chart(document.getElementById("chartjs-dashboard-pie"), {
        type: "pie",
        data: {
          labels: ["Direct", "Affiliate", "E-mail", "Other"],
          datasets: [{
            data: [2602, 1253, 541, 1465],
            backgroundColor: [
              window.theme.primary,
              window.theme.warning,
              window.theme.danger,
              "#E8EAED"
            ],
            borderWidth: 5,
            borderColor: window.theme.white
          }]
        },
        options: {
          responsive: !window.MSInputMethodContext,
          maintainAspectRatio: false,
          cutoutPercentage: 70,
          legend: {
            display: false
          }
        }
      });
    });
  </script>

  <script>
    $(document).ready(function () {
      let table = $('.table').DataTable({
        dom: 'Bfrtip',
        scrollX: true,
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        pageLength: 200
      });

      setTimeout(function () {
        let scrollBody = $('.dataTables_scrollBody');
        let actualTable = scrollBody.find('table');

        // Create top scroll container
        let topScrollWrapper = $('<div class="top-scroll"><div class="scroll-sync"></div></div>');
        scrollBody.before(topScrollWrapper);

        // Set the width of .scroll-sync to match the actual table width
        let tableWidth = actualTable.outerWidth();
        topScrollWrapper.find('.scroll-sync').width(tableWidth);

        // Sync both scrolls
        topScrollWrapper.on('scroll', function () {
          scrollBody.scrollLeft($(this).scrollLeft());
        });
        scrollBody.on('scroll', function () {
          topScrollWrapper.scrollLeft($(this).scrollLeft());
        });
      }, 100);
    });
  </script>


  <!-- <script type="text/javascript">
    $(document).ready(function() {
      $('.table').DataTable({
        dom: 'Bfrtip',
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        pageLength:200 // Set default number of results to 200
      });
    });
  </script> -->

</body>

</html>