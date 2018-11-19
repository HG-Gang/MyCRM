{{ $id }}
@extends('user.layout.main_right')
<div onclick="parentIframe()">{{ $id }}</div>
@section('custom-resources')
<script>
	function parentIframe() {
		//window.parent.createTable();
	}
</script>
@endsection