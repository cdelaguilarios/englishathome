<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Datos</h3>
        <button class="btn btn-default btn-clean pull-right" onclick="limpiarCampos();" type="button">Limpiar campos</button>
      </div>
      <div class="box-body">
        <div class="form-group">
          {{ Form::label("nombre", "Nombres (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("nombre", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label("apellido", "Apellidos (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("apellido", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label("telefono", "Teléfono (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("telefono", null, ["class" => "form-control", "maxlength" =>"30"]) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label("correoElectronico", "Correo electrónico (*): ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::email("correoElectronico", null, ["class" => "form-control", "maxlength" =>"245"]) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label("consulta", "Consulta: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::text("consulta", null, ["class" => "form-control", "maxlength" =>"255"]) }}
          </div>
        </div>
        <div class="form-group">
          {{ Form::label("idCurso", "Curso de interes: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            {{ Form::select("idCurso", $cursos, (isset($interesado) ? $interesado->idCurso : NULL), ["class" => "form-control"]) }}
          </div>        
          <div class="col-sm-3">
            <span>{{ (isset($interesado) && $interesado->cursoInteres != "" ? "(" . $interesado->cursoInteres . ")" : "") }}</span>
          </div>
        </div> 
        @include("util.ubigeo")     
        <div class="form-group">
          {{ Form::label("estado", "Estado: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-3">
            @if(!isset($interesado) || (isset($interesado) && array_key_exists($interesado->estado, App\Helpers\Enum\EstadosInteresado::listarCambio())))
            {{ Form::select("estado", App\Helpers\Enum\EstadosInteresado::listarCambio(),
            (isset($interesado) ? $interesado->estado : App\Helpers\Enum\EstadosInteresado::PendienteInformacion)
            , ["class" => "form-control"]) }}
            @else
            {!! $estadosInteresado[$interesado->estado][0] !!}
            @endif
          </div>
        </div>
        <div class="form-group">  
          {{ Form::label("costo-hora-clase", "Costo por hora de clase(*): ", ["class" => "col-sm-2 control-label"]) }}   
          <div class="col-sm-3">
            <div class="input-group">
              <span class="input-group-addon">
                <b>S/.</b>
              </span>
              {{ Form::text("costoHoraClase", (isset($interesado->costoHoraClase) ? number_format($interesado->costoHoraClase, 2, ".", ",") : NULL), ["id" => "costo-hora-clase", "class" => "form-control", "maxlength" =>"19"]) }}
            </div>
          </div> 
        </div>
        <div class="form-group">
          {{ Form::label("comentarioAdicional", "Comentarios adicionales: ", ["class" => "col-sm-2 control-label"]) }}
          <div class="col-sm-10">
            {{ Form::textarea("comentarioAdicional", NULL, ["class" => "form-control", "rows" => "2", "maxlength" =>"255"]) }}
          </div>                                        
        </div>
        {{ Form::hidden("registrarComoAlumno", NULL) }}
      </div>
      <div class="box-footer">    
        <div class="form-group">
          <div class="col-sm-6">
            <span>(*) Campos obligatorios</span>
          </div>
          <div class="col-sm-6">            
            <button id="btn-guardar" type="button" class="btn btn-success pull-right">
              {{ ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
