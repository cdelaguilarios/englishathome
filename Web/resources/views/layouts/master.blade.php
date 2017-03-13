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
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/select2.min.css") }}" />    
    <link rel="stylesheet" href="{{ asset("assets/dist/css/AdminLTE.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/dist/css/skins/_all-skins.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datepicker/datepicker3.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/jQueryUploadFileMaster/css/uploadfile.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datetimepicker/bootstrap-datetimepicker.min.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/eah/css/mystyles.css") }}" />
    @yield("section_style")   
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn"t work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->    
  </head>  
  <body class="hold-transition fuelux {{ (Auth::guest() || (isset($vistaExterna) && $vistaExterna)) ? "login-page" : "skin-blue sidebar-collapse sidebar-mini" }}">
    @if (Auth::guest() && !(isset($vistaExterna) && $vistaExterna))
    @yield("content")
    <script src="{{ asset("assets/plugins/jquery/jquery.min.js") }}"></script>
    <script src="{{ asset("assets/bootstrap/js/bootstrap.min.js") }}"></script>      
    @else
    @if (isset($vistaExterna) && $vistaExterna)  
    <div class="register-box">
      <div class="register-box-body">
        <div class="register-logo">
          <div class="row">
            <div class="col-sm-6 vcenter">
              <b>Ficha del alumno</b>
            </div><!--
            --><div class="col-sm-6 vcenter">
              <a href="http://englishathomeperu.com/" target="_blank">
                <img src="{{ asset("assets/eah/img/logo.png")}}" class="img-logo-login"/>
              </a>
            </div>
          </div>        
        </div>        
        @yield("content")
      </div>
    </div> 
    @elseif(!(Auth::guest())) 
    <div class="wrapper" style="display: none;">
      <header class="main-header">
        <a href="{{ route("/")}}" class="logo">
          <span class="logo-mini"><b>EAH</b></span>
          <span class="logo-lg"><b>Administrador</b> EAH</span>
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
                  @if ($usuarioActual->imagenPerfil == "NULL" || empty($usuarioActual->imagenPerfil))
                  <img src="{{ asset("assets/eah/img/perfil-imagen.png")}}" class="user-image" />
                  @else
                  <img src="{{ route("archivos", ["nombre" => $usuarioActual->imagenPerfil]) }}" class="user-image"/>
                  @endif
                  <span class="hidden-xs">{!! ucwords(mb_strtolower($usuarioActual->nombre)) !!} {!! ucwords(mb_strtolower($usuarioActual->apellido)) !!}</span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    @if ($usuarioActual->imagenPerfil == "NULL" || empty($usuarioActual->imagenPerfil))
                    <img src="{{ asset("assets/eah/img/perfil-imagen.png") }}" class="img-circle" />
                    @else
                    <img src="{{ route("archivos", ["nombre" => $usuarioActual->imagenPerfil]) }}" class="img-circle"/>
                    @endif
                    <p>
                      {!! ucwords(mb_strtolower($usuarioActual->nombre)) !!} {!! ucwords(mb_strtolower($usuarioActual->apellido)) !!}
                      <small>{!! $rolesUsuarios[$usuarioActual->rol] !!}</small>
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
            <li class="{{ ((isset($seccion) && $seccion == "interesados") ? "active" : "") }}">
              <a href="{{ route("interesados")}}"><i class="fa flaticon-questioning"></i> <span>Interesados</span></a>
            </li>
            <li class="{{ ((isset($seccion) && $seccion == "alumnos") ? "active" : "") }}">
              <a href="{{ route("alumnos")}}"><i class="fa fa-mortar-board"></i> <span>Alumnos</span></a>
            </li> 
            <li class="{{ ((isset($seccion) && $seccion == "postulantes") ? "active" : "") }}">
              <a href="{{ route("postulantes")}}">CV&nbsp;&nbsp;&nbsp;<span>Postulantes</span></a>
            </li>
            <li class="{{ ((isset($seccion) && $seccion == "profesores") ? "active" : "") }}">
              <a href="{{ route("profesores")}}"><i class="fa flaticon-teach"></i> <span>Profesores</span></a>
            </li> 
            <li class="{{ ((isset($seccion) && $seccion == "clases") ? "active" : "") }}">
              <a href="{{ route("clases")}}"><i class="fa flaticon-student-in-front-of-a-stack-of-books"></i> <span>Clases</span></a>
            </li> 
            <li class="{{ ((isset($seccion) && $seccion == "cursos") ? "active" : "") }}">
              <a href="{{ route("cursos")}}"><i class="fa fa-book"></i> <span>Cursos</span></a>
            </li>
            <li class="{{ ((isset($seccion) && $seccion == "reportes") ? "active" : "") }} treeview">
              <a href="javascript:void(0);">
                <i class="fa fa-bar-chart"></i> <span>Reportes</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li class="{{ ((isset($subSeccion) && $subSeccion == "clases") ? "active" : "") }}">
                  <a href="{{ route("reporte.clases")}}"><i class="fa fa-signal"></i> Reporte de clases</a>
                </li>
                <li class="{{ ((isset($subSeccion) && $subSeccion == "pagos") ? "active" : "") }}">
                  <a href="{{ route("reporte.pagos")}}"><i class="fa fa-line-chart"></i> Reporte de pagos</a>
                </li>
                <li class="{{ ((isset($subSeccion) && $subSeccion == "docentes-disponibles") ? "active" : "") }}">
                  <a href="{{ route("reporte.docentes.disponibles")}}"><i class="fa flaticon-teach"></i> Docentes disponibles</a>
                </li>
              </ul>
            </li>
            <li class="{{ ((isset($seccion) && $seccion == "usuarios") ? "active" : "") }}">
              <a href="{{ route("usuarios")}}"><i class="fa fa-users"></i> <span>Usuarios del sistema</span></a>
            </li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
        <section class="content-header">
          @if (isset($seccion) && $seccion != "inicio")
          <ol class="breadcrumb">
            <li><a href="{{ route("/") }}"><i class="fa fa-home"></i> Inicio</a></li>
            @yield("breadcrumb")
          </ol>
          @endif
        </section>
        <section class="content">                
          @include("partials/mensajes")
          @yield("content")
        </section>
      </div>
      <footer class="main-footer">
        <strong>Copyright &copy; 2014-{!! date("Y") !!} <a href="http://englishathomeperu.com/" target="_blank">English at home</a>.</strong> Todos los derechos reservados.
      </footer>
    </div>
    <div id="secCargandoPrincipal" class="box cargando">
      <div class="overlay">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
    </div>  
    <div id="sec-not-mobile"></div>
    @endif      
    <script src="{{ asset("assets/plugins/jquery/jquery.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/jquery-ui.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/jquery-migrate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery/globalize.js") }}"></script>
    <script src="{{ asset("assets/bootstrap/js/bootstrap.min.js") }}"></script>      
    <script src="{{ asset("assets/plugins/select2/select2.full.min.js") }}"></script>      
    <script src="{{ asset("assets/fuelux/3.13.0/js/fuelux.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>         
    <script src="{{ asset("assets/plugins/fastclick/fastclick.js") }}"></script>          
    <script src="{{ asset("assets/plugins/basic-schedule/src/index.js") }}"></script> 
    <script src="{{ asset("assets/dist/js/app.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/ckeditor/ckeditor.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/jquery.dataTables.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/dataTables.bootstrap.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/blockui/jquery.blockUI.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery_validate/jquery.validate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/jquery_validate/jquery.validate.messages_es.js") }}"></script>        
    <script src="{{ asset("assets/plugins/datepicker/bootstrap-datepicker.js") }}"></script>   
    <script src="{{ asset("assets/plugins/datepicker/locales/bootstrap-datepicker.es.js") }}"></script>         
    <script src="{{ asset("assets/plugins/datetimepicker/bootstrap-datetimepicker.min.js") }}"></script>   
    <script src="{{ asset("assets/plugins/datetimepicker/locales/bootstrap-datetimepicker.es.js") }}"></script>
    <script src="{{ asset("assets/plugins/jQueryUploadFileMaster/js/jquery.uploadfile.min.js") }}"></script>
    <script src="//www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
var urlBase = "{{ url('/') }}";
var urlBaseImagen = "{{ route('archivos', ['nombre' => '[RUTA_IMAGEN]']) }}";
var urlImagenes = "{{ route('archivos', ['nombre' => '0']) }}";
var urlRegistrarArchivo = "{{ route('archivos.reqistrar') }}";
var urlEliminarArchivo = "{{ route('archivos.eliminar') }}";
var minHorasClase = "{{ $minHorasClase }}";
var maxHorasClase = "{{ $maxHorasClase }}";
var minHorario = "{{ $minHorario }}";
var maxHorario = "{{ $maxHorario}}";
var urlPerfilProfesor = "{{ route('profesores.perfil', ['id' => 0]) }}";
var urlPerfilAlumno = "{{ route('alumnos.perfil', ['id' => 0]) }}";
var estadosPago = {!!  json_encode(App\Helpers\Enum\EstadosPago::listar()) !!};
    </script>
    <script src="{{ asset("assets/eah/js/util.js") }}"></script>
    <script src="{{ asset("assets/eah/js/mensajes.js") }}"></script>   
    @yield("section_script") 
    @endif
  </body> 
</html>