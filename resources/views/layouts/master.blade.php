<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1">
        <meta name="description" content="{!! $page_description !!}" />
        <meta name="keywords" content="{!! $page_keywords !!}" />

        <title>{{ trans("content.app.title") }} - {!! $page_title !!}</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="<?= asset('css/bootstrap.min.css'); ?>">

        <!-- RAWCSS -->
        {!! Minify::stylesheet(url('/css/raw-grid/raw-grid-desktop-large.css')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-grid-desktop.css'), array("media" => 'screen and (min-width: 984px) and (max-width: 1272px)')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-grid-tablet.css'), array("media" => 'screen and (min-width: 696px) and (max-width: 984px)')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-grid-mobile.css'), array("media" => 'screen and (max-width: 696px)')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-grid-offset.css')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-grid.css')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-t.css'), array("media" => 'screen and (min-width: 696px) and (max-width: 984px)')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw-m.css'), array("media" => 'screen and (max-width: 732px)')) !!}
        {!! Minify::stylesheet(url('/css/raw-grid/raw.min.css')) !!}

        <!-- App style -->
        {!! Minify::stylesheet(url('/css/main.css')) !!}
        {!! Minify::stylesheet(url('/css/loaders.css')) !!}
        {!! Minify::stylesheet(url('/css/device-desktop.css'), array("media" => 'screen and (min-width: 984px) and (max-width: 1272px)')) !!}
        {!! Minify::stylesheet(url('/css/device-tablet.css'), array("media" => 'screen and (min-width: 696px) and (max-width: 984px)')) !!}
        {!! Minify::stylesheet(url('/css/device-mobile.css'), array("media" => 'screen and (max-width: 696px)')) !!}

        <!-- Card JS -->
        {!! Minify::stylesheet(url('/css/card.css')) !!}

        <!-- Font Awesome -->
        {!! Minify::stylesheet(url('/font-awesome/css/font-awesome.css')) !!}

        <!-- Bootstrap Theme -->
        {!! Minify::stylesheet(url('/themes/bootstrap-cosmo.css')) !!}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        @yield('stylesheets')
    </head>
    <body>

        @include('layouts.loading-overlay')

        @include('account.login-panel')

        <div class="gondolyn-modal">
            <div class="raw-device">
                <div class="raw100 gondolyn-modal-content">
                </div>
            </div>
        </div>

        <div class="gondolyn-notification">
            <div class="gondolyn-notify">
                <p class="gondolyn-notify-comment"></p>
            </div>
            <div class="gondolyn-notify-closer">
                <span class="glyphicon glyphicon-remove gondolyn-notify-closer-icon"></span>
            </div>
        </div>

        @yield("navigation")

        <div class="container-fluid">
            <div class="row">
                @yield("page-content")
            </div>
        </div>

        {!! Minify::javascript(url('/js/jquery.min.js')) !!}
        {!! Minify::javascript(url('/js/bootstrap.min.js')) !!}
        {!! Minify::javascript(url('/js/raw-grid.js')) !!}
        {!! Minify::javascript(url('/js/card.js')) !!}
        {!! Minify::javascript(url('/js/gondolyn.js')) !!}

        <script type="text/javascript">

            $(window).rawGrid();

            @include('commons.notifications')

            @yield('gondolyn_notifications')

        </script>

        @yield("javascript")
    </body>
</html>