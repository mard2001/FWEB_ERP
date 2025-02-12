<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('html_title')
    @include('Links.main_stlyles_links')
</head>

<body>
    <div class="wrapper w-100">
        @include('Components.nav')
        <div class="main">
            @yield('title_header')
            @yield('table')
            @yield('modal')
            @include('Components.uploader_modal')
        </div>
</body>

@include('Links.main_js_library_links')
@yield('pagejs')

</html>