@extends("layouts.master")
@section("titulo", "Interesados")

@section("section_script")
<script>
  var urlDatosCurso = "{{ route('cursos.datos', ['id' => 0]) }}";
  var urlCotizar = "{{ route('interesados.cotizar', ['id' => 0]) }}";
  var urlBuscar = "{{ route('interesados.buscar') }}";
  var idInteresado = "{{ $interesado->id}}";
  var nombreCompletoInteresado = "{{ $interesado->nombre . " " .  $interesado->apellido }}";
</script>
<script src="{{ asset("assets/eah/js/modulos/interesado/cotizacion.js") }}"></script>
<script src="{{ asset("assets/eah/js/modulos/interesado/busqueda.js") }}"></script>
@endsection

@section("breadcrumb")
<li><a href="{{ route("interesados") }}">Interesados</a></li>
<li class="active">Envio de cotización</li>
@endsection

@section("content") 
@include("partials/errors")
<div class="row">
  <div class="col-sm-12">
    <div class="box box-primary">        
      <div class="box-body">
        <div class="form-group">
          <div class="col-sm-7">
            <a href="{{ route("interesados.crear")}}" class="btn btn-primary btn-clean">Nuevo interesado</a>
            <a href="{{ route("interesados.editar", ["id" => $interesado->idEntidad]) }}" type="button" class="btn btn-primary" ><i class="fa flaticon-questioning"></i> Ver datos del interesado</a>             
            <button class="btn btn-primary" onclick="return busquedaInteresado.copiarEnlaceFichaInscripcion('{{ route("alumnos.crear.externo", ["codigoVerificacion" => Crypt::encrypt($interesado->id)]) }}')">Enlace ficha de inscripción</button>
          </div>         
          <div class="col-sm-2">
            @if(isset($interesado->idInteresadoSiguiente))
            <a href="{{ route("interesados.cotizar", ["id" => $interesado->idInteresadoSiguiente]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-right"></span></a>
            @endif
            @if(isset($interesado->idInteresadoAnterior))
            <a href="{{ route("interesados.cotizar", ["id" => $interesado->idInteresadoAnterior]) }}" class="btn btn-default pull-right"><span class="glyphicon glyphicon-arrow-left"></span></a>
            @endif
          </div>      
          <div class="col-sm-3">
            {{ Form::select("", [], null, ["id"=>"sel-interesado", "class" => "form-control", "data-seccion" => "cotizar", "style" => "width: 100%"]) }}
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="box-header">
      <h2 class="box-title">Datos de la persona interesada</h2>   
    </div>  
    <div class="col-sm-3">
      <div class="box-body sec-datos">
        <strong><i class="fa  fa-check-square margin-r-5"></i> Nombre</strong>
        <p class="text-muted">{{ $interesado->nombre . " " . $interesado->apellido }}</p>
        <strong><i class="fa  fa-check-square margin-r-5"></i> Correo electrónico</strong>
        <p class="text-muted">{{ $interesado->correoElectronico }}</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="box-body sec-datos">
        <strong><i class="fa  fa-check-square margin-r-5"></i> Teléfono</strong>
        <p class="text-muted">{{ $interesado->telefono }}</p>
        <strong><i class="fa  fa-check-square margin-r-5"></i> Estado</strong>
        <p class="text-muted">{{ App\Helpers\Enum\EstadosInteresado::listar()[$interesado->estado][0] }}</p> 
      </div> 
    </div>
    @if(isset($interesado->consulta) && $interesado->consulta !== "")
    <div class="col-sm-6">
      <div class="box-body sec-datos">
        <strong><i class="fa  fa-check-square margin-r-5"></i> Consulta</strong>
        <p class="text-muted">{{ $interesado->consulta }}</p>
      </div> 
    </div>
    @endif
  </div>
</div>
{{ Form::open(["url" => route("interesados.enviar.cotizacion", ["id" => $interesado->idEntidad]), "id" => "formulario-interesado-cotizacion", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
<div class="row">
  <div class="col-sm-12">
    <div class="box-header">
      <h2 class="box-title">Datos de cotización</h2>   
    </div>  
    <div>
      <div class="form-group">
        {{ Form::label("id-curso", "Curso de interes: ", ["class" => "col-sm-2 control-label"]) }}
        <div class="col-sm-4">
          {{ Form::select("idCurso", App\Models\Curso::listarSimple(), $interesado->idCurso, ["id" => "id-curso", "class" => "form-control"]) }}
        </div>  
      </div>
      <div class="form-group">
        {{ Form::label("contenido-correo", "Contenido correo (*): ", ["class" => "col-sm-2 control-label"]) }}
        <div class="col-sm-10">
          {{ Form::textarea("contenidoCorreo", null, ["id" => "contenido-correo", "class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
        </div>                                        
      </div>  
      @include("util.archivosAdjuntos", ["adjuntos" => [(object)["idCampo" => "Adjuntos", "idHtml" => "adjuntos", "titulo" => "Adjuntos"]]]) 
      <div class="form-group">  
        {{ Form::label("costo-hora-clase", "Costo por hora de clase (*): ", ["class" => "col-sm-2 control-label"]) }}   
        <div class="col-sm-3">
          <div class="input-group">
            <span class="input-group-addon">
              <b>S/.</b>
            </span>
            {{ Form::text("costoXHoraClase", (isset($interesado->costoXHoraClase) ? number_format($interesado->costoXHoraClase, 2, ".", ",") : null), ["id" => "costo-hora-clase", "class" => "form-control", "maxlength" =>"19"]) }}
          </div>
        </div>
      </div> 
    </div>
    <div>    
      <div class="form-group">
        <div class="col-sm-6">
          <span>(*) Campos obligatorios</span>
        </div>
        <div class="col-sm-6">   
          <button id="btn-envio-cotización" type="button" class="btn btn-success pull-right">Enviar cotización</button> 
          <button id="btn-envio-cotización-prueba" type="button" class="btn btn-primary pull-right"><i class="fa fa-shield"></i> Enviar cotización de prueba</button> 
        </div>
      </div>
    </div>
  </div>
</div>
<div id="mod-correo-cotizacion-prueba" class="modal" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Envio de cotización de prueba</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          {{ Form::label("correo-cotizacion-prueba", "Correo: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-5">
            {{ Form::text("correoCotizacionPrueba", null, ["id" => "correo-cotizacion-prueba", "class" => "form-control", "maxlength" =>"245", ""]) }}
          </div> 
          <div class="col-sm-5">
            <button type="submit" class="btn btn-primary pull-right">
              <i class="fa fa-shield"></i> Enviar cotización de prueba
            </button> 
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{ Form::close() }}
@endsection