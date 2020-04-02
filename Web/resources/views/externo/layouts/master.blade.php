<!DOCTYPE html>
<html lang="es">
  <head>        
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>English at home {{ ((isset($vistaImpresion) && $vistaImpresion) ? "" : "administrador") }} - @yield("titulo")</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/ico" href="{{ asset("assets/eah/img/favicon.ico") }}" />
    <link rel="stylesheet" href="{{ asset("assets/bootstrap/css/bootstrap.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/fuelux/3.13.0/css/fuelux.min.css") }}" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />  
    <link rel="stylesheet" href="{{ asset("assets/eah/css/iconos-educacion/flaticon.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datatables/dataTables.bootstrap.css") }}" />    
    <link rel="stylesheet" href="{{ asset("assets/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css") }}" />    
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/select2.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/fullcalendar/fullcalendar.min.css") }}" /> 
    <link rel="stylesheet" href="{{ asset("assets/plugins/fullcalendar/fullcalendar.print.min.css") }}" media="print" />     
    <link rel="stylesheet" href="{{ asset("assets/dist/css/AdminLTE.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/dist/css/skins/_all-skins.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datepicker/datepicker3.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/jQueryUploadFileMaster/css/uploadfile.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datetimepicker/bootstrap-datetimepicker.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/eah/css/eah.css") }}" />
    @yield("section_style")   
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn"t work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->    
  </head>  
  <body class="hold-transition fuelux skin-blue sidebar-collapse sidebar-mini">
    <div class="wrapper" style="display: none">
      <header class="main-header">
        <a href="{{ route("/")}}" class="logo">
          <span class="logo-mini" style="background-color: #ecf0f5">
            <img src="{{ asset("assets/eah/img/logo.png")}}" class="img-logo-login" width="46" />
          </span>
          <span class="logo-lg">English at Home Perú</span>
        </a>
        <nav class="navbar navbar-static-top">
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>

          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  @if ($usuarioActual->imagenPerfil == "null" || empty($usuarioActual->imagenPerfil))
                  <img src="{{ asset("assets/eah/img/perfil-imagen.png")}}" class="user-image" />
                  @else
                  <img src="{{ route("archivos", ["nombre" => (isset($usuarioActual->imagenPerfil) && $usuarioActual->imagenPerfil != "" ? $usuarioActual->imagenPerfil : "-"), "sexoEntidad" => ($usuarioActual->sexo == "F" ? "f" : "m")]) }}" class="user-image"/> 
                  @endif
                  <span class="hidden-xs">{!! ucwords(mb_strtolower($usuarioActual->nombre)) !!} {!! ucwords(mb_strtolower($usuarioActual->apellido)) !!}</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    @if ($usuarioActual->imagenPerfil == "null" || empty($usuarioActual->imagenPerfil))
                    <img src="{{ asset("assets/eah/img/perfil-imagen.png") }}" class="img-circle" />
                    @else
                    <img src="{{ route("archivos", ["nombre" => (isset($usuarioActual->imagenPerfil) && $usuarioActual->imagenPerfil != "" ? $usuarioActual->imagenPerfil : "-"), "sexoEntidad" => ($usuarioActual->sexo == "F" ? "f" : "m")]) }}" class="img-circle"/>  
                    @endif
                    <p>
                      {!! ucwords(mb_strtolower($usuarioActual->nombre)) !!} {!! ucwords(mb_strtolower($usuarioActual->apellido)) !!}
                      <small>{{ App\Helpers\Enum\RolesUsuario::listarExternos()[$usuarioActual->rol][$usuarioActual->sexo == "F" ? 1 : 0] }}</small>
                    </p>
                  </li>
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="{{ route("usuarios.editar", $usuarioActual->id)}}" class="btn btn-default btn-flat">Mi cuenta</a>
                    </div>
                    <div class="pull-right">
                      <a href="{{ route("auth.logout")}}" class="btn btn-default btn-flat">Cerrar sesión</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <aside class="main-sidebar">
        <section class="sidebar">
          <ul class="sidebar-menu">
            <li class="header">Menú</li>
            @if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Profesor)
            <li class="{{ ((isset($seccion) && $seccion == "docentes") ? "active" : "") }}">
              <a href="{{ route("profesores.mis.alumnos") }}"><i class="fa fa-mortar-board"></i> <span>Mis alumnos</span></a>
            </li> 
            @else
            <li class="{{ ((isset($seccion) && $seccion == "alumnos") ? "active" : "") }}">
              <a href="{{ route("alumnos.mis.clases") }}"><i class="fa flaticon-teach"></i> <span>Mis clases</span></a>
            </li> 
            @endif
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <section class="content">                
          @include("partials/mensajes")
          @yield("content")
        </section>
      </div>
      <footer class="main-footer">
        <strong>Copyright &copy; 2014-{!! date("Y") !!} <a href="http://englishathomeperu.com/" target="_blank">English at Home Perú</a>.</strong> Todos los derechos reservados.
      </footer>
    </div>
    <div id="secCargandoPrincipal" class="box cargando">
      <div class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>  
    <div id="sec-not-mobile"></div>
    <script src="{{ asset("assets/plugins/jquery/jquery.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/jquery-ui.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery_validate/jquery.validate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/jquery-migrate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/globalize.js") }}"></script>
    <script src="{{ asset("assets/bootstrap/js/bootstrap.min.js") }}"></script>      
    <script src="{{ asset("assets/plugins/select2/select2.full.min.js") }}"></script> 
    <script src="{{ asset("assets/plugins/select2/i18n/es.js") }}"></script>     
    <script src="{{ asset("assets/fuelux/3.13.0/js/fuelux.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>         
    <script src="{{ asset("assets/plugins/fastclick/fastclick.js") }}"></script>          
    <script src="{{ asset("assets/plugins/basic-schedule/src/index.js") }}"></script> 
    <script src="{{ asset("assets/dist/js/app.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/ckeditor_4.8/ckeditor.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/dataTables.bootstrap.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/blockui/jquery.blockUI.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery_validate/jquery.validate.messages_es.js") }}"></script> 
    <script src="{{ asset("assets/plugins/datepicker/bootstrap-datepicker.js") }}"></script>   
    <script src="{{ asset("assets/plugins/datepicker/locales/bootstrap-datepicker.es.js") }}"></script>         
    <script src="{{ asset("assets/plugins/datetimepicker/bootstrap-datetimepicker.min.js") }}"></script>   
    <script src="{{ asset("assets/plugins/datetimepicker/locales/bootstrap-datetimepicker.es.js") }}"></script>
    <script src="{{ asset("assets/plugins/jQueryUploadFileMaster/js/jquery.uploadfile.min.js") }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset("assets/plugins/fullcalendar/fullcalendar.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/fullcalendar/locale/es.js") }}"></script>
    <script src="//www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
var urlBase = "{{ url('/') }}";
var usuarioActualEsAlumno = {{ $usuarioActual->tipo == App\Helpers\Enum\TiposEntidad::Alumno ? "true" : "false" }};
var minHorasClase = "{{ $minHorasClase }}";
var maxHorasClase = "{{ $maxHorasClase }}";
var minHorario = "{{ $minHorario }}";
var maxHorario = "{{ $maxHorario}}";
var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::listar()) !!};
    </script>
    <script src="{{ asset("assets/eah/js/util.js") }}"></script>
    <script src="{{ asset("assets/eah/js/mensajes.js") }}"></script>      
    @yield("section_script") 
  </body> 
</html>