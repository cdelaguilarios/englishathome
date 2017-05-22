@extends("layouts.master")
@section("titulo", "Notificaciones")

@section("breadcrumb")
<li class="active">Notificaciones</li>
@endsection

@section("content") 
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Notificaciones</h3>   
      </div>         
      <div class="box-body">
        @include("util.historial", ["idEntidad" => Auth::id(), "observador" => 1]) 
      </div>
    </div>
  </div>
</div>
@endsection