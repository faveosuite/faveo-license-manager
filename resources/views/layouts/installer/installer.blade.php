
<html lang="en-US">
    <head>
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         @if(file_exists(app_path('/Whitelabel/WhitelabelServiceProvider.php')))
          <!--<link href="{{assetLink('image','whitefavicon')}}"  rel="shortcut icon" />-->
          <title>HELPDESK</title>
          @else
          <title>LICENSE MANAGER</title>
          <!--<link href="{{assetLink('image','favicon')}}"  rel="shortcut icon" />-->
          @endif
        
        
        <link href='themes/default/css/jquery.ui.latest.css' rel="stylesheet" type="text/css"/>
        <link href='themes/default/css/loadstyles.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/css.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/admin.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/setup.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/activation.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/style.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/font-awesome.min.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/ggpopover.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/prism.css' rel="stylesheet" type="text/css" />
        <link href='themes/default/css/chosen.css' rel="stylesheet" type="text/css" />
        <script src='themes/default/plugins/jquery/jquery-3.5.1.min.js' type="text/javascript"></script>
    </head>
    <body class="setup wp-core-ui">
        <center><a href="http://www.faveohelpdesk.com" tabIndex="-1">
         @if(file_exists(app_path('/Whitelabel/WhitelabelServiceProvider.php')))
         <!--<img src="{{ assetLink('image','whitelabel') }}" alt="faveo" width="200px" height="130px">-->
        @else

        <!--<img src="{{assetLink('image','faveo')}}" alt="faveo" width="200px" height="130px" style="margin-bottom: 20px;">-->
        @endif



            </a></center>
    <ol class="setup-steps" style="margin-left: 70px;">
        <li class="done">Server Requirements</li>
        <li class="@yield('database')">Database Setup</li>
        <li class="@yield('locale')">Getting Started</li>
        <li class="@yield('ready')">Final</li>
    </ol>
    <div class="setup-content">
        @yield('content')
    </div>



   
    @if(file_exists(app_path('/Whitelabel/WhitelabelServiceProvider.php')))
        <p style="text-align: center;"> Copyright &copy; 2015 - <?php echo date('Y')?> . 
     {!! Lang::get('lang.all_rights_reserved') !!}

    @else
    <p style="text-align: center;">
    Copyright &copy; 2015 - <?php echo date('Y')?> .
    Ladybird Web Solution Pvt Ltd.
    {!! Lang::get('lang.all_rights_reserved') !!}. {!! Lang::get('lang.powered_by') !!}
    <a target="_blank" href="http://www.faveohelpdesk.com">Faveo </a>
</p>    
    @endif

    <script src='themes/default/js/chosen.jquery.js' type="text/javascript"></script>
    <script src='themes/default/js/jquery.ui.latest.js' type="text/javascript"></script>
    <script src='themes/default/js/prism.js' type="text/javascript"></script>
    <script src='themes/default/js/min/bootstrap4.min.js' type="text/javascript"></script>
    <script src='themes/default/js/ggpopover.js' type="text/javascript"></script>
    <script type="text/javascript">
        $('[data-toggle="popover"]').ggpopover();
    </script>


    <script type="text/javascript">
        localStorage.setItem('LANGUAGE', '{{Lang::getLocale()}}');
        var config = {
            '.chosen-select': {},
            '.chosen-select-deselect': {
                allow_single_deselect: true
            },
            '.chosen-select-no-single': {
                disable_search_threshold: 10
            },
            '.chosen-select-no-results': {
                no_results_text: 'Oops, nothing found!'
            },
            '.chosen-select-width': {
                width: "95%"
            }
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
    </script>

</body>
</html>