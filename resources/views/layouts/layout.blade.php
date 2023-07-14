<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-black dark:text-white">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://use.typekit.net/srz0ovh.css">
        <meta property="og:site_name" content="Bookly" />
        <meta property="og:image" content="{{ Route::is('public') ? asset('i/'.$userSlug.'/'.__('profile.site_slug').'.png?'.time()) : asset('images/bookly-cover.png')}}"/>
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:title" content="@yield('title')"/>
        <meta property="og:url" content="{{ Route::is('public') ? url()->full() : url('/') }}"/>
        <meta property="og:description" content="{!! Route::is('public') ? 'Digital Bookly' : 'Every book you\'ve read. In one simple link.' !!}">
        <meta name="description" content="{!! Route::is('public') ? 'Digital Bookly' : 'Every book you\'ve read. In one simple link.' !!}">

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="@yield('title')" />
        <meta name="twitter:description" content="{!! Route::is('public') ? 'Digital Bookly' : 'Every book you\'ve read. In one simple link.' !!}" />


        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="msapplication-TileColor" content="#ff0000">
        <meta name="theme-color" content="#ffffff">

        @vite('resources/js/app.js')
        @livewireStyles
    </head>
    <body class="antialiased min-h-screen flex flex-col">
        @include('layouts.header')
        @yield('content')
        @include('layouts.footer')
        @livewireScripts
        @stack('scripts')
        <script>
            document.addEventListener('livewire:load', () => {
                window.livewire.on('set-focus', inputname => {
                    document.getElementById("search").focus();
                })
            });
        </script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top',
                showConfirmButton: false,
                showCloseButton: false,
                icon: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            window.addEventListener('alert',({detail:{type,message}})=>{
                Toast.fire({
                    icon:false,
                    title:message
                })
            });
        </script>
        <script defer type="text/javascript" src="https://api.pirsch.io/pirsch.js" id="pirschjs" data-code="arvNIUkDKiD7A44Q6Xh6YcozMlHzv2Zn"></script>
    </body>
</html>
