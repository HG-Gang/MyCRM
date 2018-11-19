<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>帕达控股</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}?ver={{ resource_version_number() }}" media="all" />
    @yield("css")
</head>
    <body class="childrenBody" style="padding: 10px;">
    @yield("content")
    <script src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}?ver={{ resource_version_number() }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.all.js') }}?ver={{ resource_version_number() }}"></script>
    @yield("js")
    </body>
</html>