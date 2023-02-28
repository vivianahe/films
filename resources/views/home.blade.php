@extends('layouts.app')

@section('content')
    <div>
        <div id="app"></div>
    </div>
@endsection

@section('js')
    <script src="{{mix('js/panel.js')}}"></script>
@stop
