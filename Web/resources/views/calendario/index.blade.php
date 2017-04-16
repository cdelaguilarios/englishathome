@extends("layouts.master")
@section("titulo", "Calendario")

@section("breadcrumb")
<li class="active">Calendario</li>
@endsection

@section("content")
@include("util.calendario", ["incluirListasBusqueda" => 1, "incluirClaseBox" => 1]) 
@endsection
