<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>帕达控股-客户中心</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="content-type" content="no-cache, must-revalidate" />
	<meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Access-Control-Allow-Origin" content="*">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="shortcut icon" href="{{URL::asset('img/favicon.ico')}}?ver={{ resource_version_number() }}" type="image/vnd.microsoft.icon"/>
	<link rel="stylesheet" href="{{ URL::asset('js/plugins/layui/layadmin/layui/css/layui.css') }}?ver={{ resource_version_number() }}" media="all" />
	<link rel="stylesheet" href="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/themes/default/easyui.css') }}?ver={{ resource_version_number() }}" media="all" />
	<link rel="stylesheet" href="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/themes/icon.css') }}?ver={{ resource_version_number() }}" media="all" />
	<script type="text/javascript" src="{{ URL::asset('js/jquery-1.11.3/jquery-1.11.3.min.js') }}?ver={{ resource_version_number() }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/layui/layadmin/layui/layui.js') }}?ver={{ resource_version_number() }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/jquery.easyui.min.js') }}?ver={{ resource_version_number() }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/formevent/esayui-datagrid-pagination.js') }}?ver={{ resource_version_number() }}"></script>
	<script type="text/javascript" src="{{ URL::asset('js/plugins/jquery-easyui-1.5.1/locale/easyui-lang-zh_CN.js') }}?ver={{ resource_version_number() }}"></script>
	@yield("public-resources")
</head>
<style>
    .panel{
        margin-left: 20px;
    }

	a {
		color: #4FA7ED;
		text-decoration: none;
		font-weight: 900;
	}
    
</style>
<body style="height: 99%; width: 98%;">
	@yield("content")
	@yield("custom-resources")
	<script src="{{ URL::asset('js/formevent/form.core.js') }}?ver={{ resource_version_number() }}"></script>
</body>
</html>