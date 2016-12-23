@extends("layouts.master")

@section("titulo", "Inicio de sesión")
@section("content")
<form method="POST" action="{{ route("auth.login")}}">    
  <div class="row">
    <div class="col-sm-offset-2 col-sm-8">
      @include("partials/mensajes")
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-8">
      @include("partials/errors")
    </div>
  </div>
  {!! csrf_field() !!}
  <div class="login-box">
    <div class="login-box-body">
      <div class="login-logo">
        <img src="{{ asset("assets/eah/img/logo.png")}}" class="img-logo-login"/>
      </div>
      <div class="form-group has-feedback">
        <input type="email" name="email"  value="{{ old("email") }}" class="form-control" placeholder="@lang("validation.attributes.email")"/>
               <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">                    
        <input type="password" name="password" class="form-control" placeholder="@lang("validation.attributes.password")"/>
               <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-12">                        
          <button type="submit" class="btn btn-default btn-block btn-flat">@lang("auth.login")</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
