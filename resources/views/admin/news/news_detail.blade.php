@extends('user.layout.main_right')

@section('content')

@endsection
    <div>
        <div style="font-size: 16px; text-align: center; font-weight: 600; margin-top: 6px;">{{ $newsInfo['news_title'] }}</div>
        <div style="text-align: center; margin-top: 10px;">时间: {{ $newsInfo['rec_upd_date'] }}</div>
        <div style="text-align: center; margin-top: 20px;">{!! $newsInfo['news_content'] !!}</div>
    </div>
@section('custom-resources')
@endsection