@if(count($errors) > 0)
<div class="box-default">
  <div class="box-body contenedor-alerta">
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h4>
        @if(Auth::guest())
        <i class="icon fa fa-ban"></i> Please fix the following errors
        @else
        <i class="icon fa fa-ban"></i> Por favor corregir los siguientes errores
        @endif
      </h4>
      <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach  
      </ul>
    </div>
  </div>
</div>
@endif
