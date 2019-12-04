@extends("layouts.master")
@section("titulo", "Pagos a profesores")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/docente/pago/principal.js")}}"></script>    
@endsection

@section("breadcrumb")
<li class="active">Pagos a profesores</li>
@endsection

@section("content")
@include("docente.pago.lista")     
@include("docente.pago.formulario")
@endsection