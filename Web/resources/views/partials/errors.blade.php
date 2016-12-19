@if(count($errors) > 0)
<div class="box-default">
  <div class="box-body contenedor-alerta">
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h4><i class="icon fa fa-ban"></i> @lang("auth.errors_title")</h4>
      <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach  
      </ul>
    </div>
  </div>
</div>
@endif
