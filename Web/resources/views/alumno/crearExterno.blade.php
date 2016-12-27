<!DOCTYPE html>
<html lang="es">
  <head>        
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>English at home administrador - @yield("titulo")</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/ico" href="{{ asset("assets/eah/img/favicon.ico") }}" />

    <link rel="stylesheet" href="{{ asset("assets/bootstrap/css/bootstrap.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/fuelux/3.13.0/css/fuelux.min.css") }}" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />  
    <link rel="stylesheet" href="{{ asset("assets/eah/css/iconos-educacion/flaticon.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datatables/dataTables.bootstrap.css") }}" />    
    <link rel="stylesheet" href="{{ asset("assets/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/dist/css/AdminLTE.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/dist/css/skins/_all-skins.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datepicker/datepicker3.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/eah/css/mystyles.css") }}" />
    @yield("section_style")   
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn"t work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->    
  </head>  
  <body class="hold-transition login-page" style="background-color: #e5e8ef;">
    <div class="register-box">
      <div class="register-box-body">
        <div class="register-logo">
          <div class="row">
          <div class="col-sm-6">
            <b>Ficha del alumno</b>
          </div>
          <div class="col-sm-6">
            <a href="http://englishathomeperu.com/" target="_blank">
              <img src="{{ asset("assets/eah/img/logo.png")}}" class="img-logo-login"/>
            </a>
          </div>  
          </div>        
        </div>

        <!--<h4>Datos personales</h4>
        <form action="../../index.html" method="post">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Full name">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Retype password">
            <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
          </div>-->
        @include("partials/errors")
{{ Form::open(["url" => "alumnos", "id" => "formulario-alumno", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
@include("alumno.formularioExterno", ["modo" => "registrar"])
{{ Form::close() }}
        </form>
      </div>
    </div>        
    <script src="{{ asset("assets/plugins/jquery/jquery.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/jquery-migrate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/globalize.js") }}"></script>
    <script src="{{ asset("assets/bootstrap/js/bootstrap.min.js") }}"></script>
    <script src="{{ asset("assets/fuelux/3.13.0/js/fuelux.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>         
    <script src="{{ asset("assets/plugins/fastclick/fastclick.js") }}"></script>          
    <script src="{{ asset("assets/plugins/basic-schedule/src/index.js") }}"></script> 
    <script src="{{ asset("assets/dist/js/app.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/dataTables.bootstrap.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/blockui/jquery.blockUI.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery_validate/jquery.validate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery_validate/jquery.validate.messages_es.js") }}"></script>        
    <script src="{{ asset("assets/plugins/datepicker/bootstrap-datepicker.js") }}"></script>   
    <script src="{{ asset("assets/plugins/datepicker/locales/bootstrap-datepicker.es.js") }}"></script>
    <script type="text/javascript">
var urlBase = "{{ url('/') }}";
var minHorasClase = "{{ $minHorasClase }}";
var maxHorasClase = "{{ $maxHorasClase }}";
var minHorario = "{{ $minHorario }}";
var maxHorario = "{{ $maxHorario}}";
var urlImagenes = "{{ route("imagenes", ["rutaImagen" => "0"]) }}";
var estadosClase = {!!  json_encode($estadosClase) !!};
var estadoClaseRealizada = "{{ $estadoClaseRealizada }}";
var estadoClaseCancelada = "{{ $estadoClaseCancelada }}";
var tipoCancelacionClaseAlumno = "{{ $tipoCancelacionClaseAlumno }}";
var motivosPago = {!!  json_encode($motivosPago) !!};
var urlPerfilProfesor = "{{ route("profesores.perfil", ["id" => 0]) }}";
var urlPerfilAlumno = "{{ route("alumnos.perfil", ["id" => 0]) }}";
var estadosPago = {!!  json_encode($estadosPago) !!};
var estadoPagoRealizado = "{{  $estadoPagoRealizado }}";
    </script>
    <script src="{{ asset("assets/eah/js/util.js") }}"></script>
    <script src="{{ asset("assets/eah/js/mensajes.js") }}"></script>   
    @yield("section_script")
  </body>  
</html>