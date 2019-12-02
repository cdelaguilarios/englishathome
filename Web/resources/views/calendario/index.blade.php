{{----}}
@extends("layouts.master")
@section("titulo", "Calendario")

@section("breadcrumb")
<li class="active">Calendario</li>
@endsection

@section("content")
@include("util.calendario", ["paginaIndependiente" => 1]) 
@endsection
