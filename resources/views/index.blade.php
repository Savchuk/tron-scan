<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tron Scan</title>
    <link rel="stylesheet" href="{{asset('cses/uikit.min.css')}}" />
    <link rel="stylesheet" href="https://getuikit.com/css/theme.css" />




    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>

        // Enable pusher logging - don't include this in production

    </script>


</head>
<body>
<div id="app">
    <uikit-header></uikit-header>
    <div class="uk-container">
        <router-view></router-view>
    </div>
</div>
<script src="{{asset('js/uikit.min.js')}}"></script>
<script src="{{asset('js/uikit-icons.min.js')}}"></script>
<script src="./js/app.js?1"></script>
</body>
</html>
