@extends("layouts.master")

@section("titulo", "Restablecer contraseña")
@section("content")
<form method="POST" action="{{ route("auth.password.reset")}}">    
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
        <img src="{{ asset("assets/eah/img/logo.png")}}" class="img-logo-login" width="150"/>
      </div>
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-group has-feedback">
        <input type="email" name="email"  value="{{ $email or old('email') }}" class="form-control" placeholder="@lang("validation.attributes.email")"/>
               <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">                    
        <input type="password" name="password" class="form-control" placeholder="@lang("validation.attributes.password")"/>
               <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">                    
        <input type="password" name="password_confirmation" class="form-control" placeholder="@lang("validation.attributes.password_confirmation")"/>
               <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-sm-12">                        
          <button type="submit" class="btn btn-default btn-block btn-flat">@lang("passwords.reset_password")</button>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
