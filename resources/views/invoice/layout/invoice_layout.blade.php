<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoices</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap/css/bootstrap.min.css') }}">
    <style>
    @media print {
        @page {
        margin-top: 0px;
        margin-bottom: 10px;
        /*padding-top: 40px*/
        }
        body {
            padding-top: 72px;
            padding-bottom: 72px ;
        }
    }
        body {
        font-family: 'Kalpurush', 'Siyam Rupali', 'SolaimanLipi', sans-serif;
        }
        .dashed {
        border-bottom: 1px dashed #000;
        min-height: 24px;
        }
        .label {
        font-weight: bold;
        }
  </style>
    
</head>
<body class="container" onload="printReport()">

    @yield('main_content')
    <script src="{{ asset('js/bootstrap/js/bootstrap.min.js') }}"></script>
    <script>
    function printReport() {
         window.print();
            window.onafterprint = function () {
                window.close();
            };
    }
 </script>
</body>
</html>