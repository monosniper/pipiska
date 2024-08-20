<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kirano Translate | {{ config('app.name') }}</title>
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('vendor/laravel-translate/css/translate.css') }}">
    <meta name="csrf_token" content="{{ csrf_token() }}"/>
    @include('vendor.laravel-translate.inc.sprite')
</head>
<body>
    {{ $slot }}

    @livewireScripts
</body>
</html>
