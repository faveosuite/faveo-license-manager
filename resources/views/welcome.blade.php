<!DOCTYPE html>

<html>

  <head>

    <meta charset="UTF-8">

    <base href="{{url('/')}}">

    <title> Faveo Helpdesk </title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <meta name="_token" content="{!! csrf_token() !!}"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="api-base-url" content="{{ url('/') }}" />

    <link href="https://support.faveohelpdesk.com/themes/default/common/images/favicon.ico" rel="shortcut icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link href="{{assetLink('css','adminlte-3')}}" rel='stylesheet' type='text/css'/>

    <link href="{{assetLink('css','font-awesome-5')}}" rel="stylesheet" type="text/css" />

    <link href="{{assetLink('css','ionicons')}}" rel="stylesheet"  type="text/css" />

    <link href="{{assetLink('css','select2')}}" rel="stylesheet" type="text/css"  media="none" onload="this.media='all';"/>

    <link rel="stylesheet" href="{{assetLink('css','new-overlay')}}">

{{--    <link rel="stylesheet" href="{{assetLink('css','pagination')}}">--}}

    <link rel="stylesheet" href="{{assetLink('css','glyphicon')}}">

    <link rel="stylesheet" href="{{assetLink('css','icheck')}}">

    <script src="{{assetLink('js','jquery')}}" type="text/javascript" media="none" onload="this.media='all';">

    </script>

    <script src="{{assetLink('js','polyfill')}}"></script>

    <script src="{{assetLink('js','select2')}}" type="text/javascript"></script>

      <style>

          .VuePagination__pagination{
              margin-top: -5px !important;
              margin-right: -15px !important;
              float: right !important;
          }
          .VuePagination{
              margin-top: 10px !important;
          }
          .VuePagination__count {
              display: contents !important;
              margin-top: -10px !important;
          }
          .VuePagination .text-center{
              text-align: left !important;
              width: inherit;
          }

          .VueTables__search{
              float : right;
          }

          .VueTables__limit{
              float : left !important;
          }

          .VueTables__search-field input{
              width : 300px !important;
          }
      </style>

      @vite(['resources/css/app.scss', 'resources/js/app.js'])
  </head>

  <body class="sidebar-mini layout-fixed layout-navbar-fixed text-sm layout-footer-fixed">

    <div id="app">

        <license-manager-renderer></license-manager-renderer>
    </div>

    <script type="text/javascript" src="{{ bundleLink('js/lang' ) }}"></script>

    <script type="text/javascript" src="{{assetLink('js','popper')}}"></script>

    <script src="{{assetLink('js','bootstrap-4')}}" type="text/javascript"></script>

    <script src="{{assetLink('js','adminlte-3')}}" type="text/javascript"></script>

    <script src="{{assetLink('js','new-overlay')}}" type="text/javascript"></script>

  </body>
</html>
