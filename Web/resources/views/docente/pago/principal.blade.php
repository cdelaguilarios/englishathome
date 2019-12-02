@extends("layouts.master")
@section("titulo", "Profesores - Pagos")

@section("section_script")
<script src="{{ asset("assets/eah/js/modulos/docente/pago/principal.js")}}"></script>    
@endsection

@section("breadcrumb")
<li class="active">Profesores - Pagos</li>
@endsection

@section("content")
@include("docente.pago.lista")     
@include("docente.pago.formulario")
@endsection