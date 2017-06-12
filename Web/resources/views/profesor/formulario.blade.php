<div class="row">
  <div class="col-sm-12">
    <div id="wiz-registro-profesor" class="box box-info wizard" data-initialize="wizard">
      <div class="steps-container">
        <ul class="steps">
          <li data-step="1" class="active">
            <span class="badge">1</span>Datos personales
            <span class="chevron"></span>
          </li>
          <li data-step="2">
            <span class="badge">2</span>Datos de dirección
            <span class="chevron"></span>
          </li>
          <li data-step="3">
            <span class="badge">3</span>Datos de experiencia laboral
            <span class="chevron"></span>
          </li>
          <li data-step="4">
            <span class="badge">4</span>Datos de cursos asignados
            <span class="chevron"></span>
          </li>
        </ul>
      </div>
      <div class="step-content box-body">
        <div id="sec-wiz-profesor-1" class="step-pane active sample-pane alert" data-step="1">
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
            {{ Form::label("telefono", "Teléfono: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("telefono", null, ["class" => "form-control", "maxlength" =>"30"]) }}
            </div>
          </div>                 
          <div class="form-group">
            {{ Form::label("fecha-nacimiento", "Fecha nacimiento: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                          
                {{ Form::text("fechaNacimiento", (isset($profesor->fechaNacimiento) ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $profesor->fechaNacimiento)->format("d/m/Y") : null), ["id" => "fecha-nacimiento", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
            {{ Form::label("sexo", "Sexo: ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-2">
              {{ Form::select("sexo", App\Helpers\Enum\SexosEntidad::listar(), null, ["class" => "form-control"]) }}
            </div>
          </div>            
          <div class="form-group">
            {{ Form::label("numeroDocumento", "Doc. de identidad: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3" style="display:none">
              {{ Form::select("idTipoDocumento", App\Models\TipoDocumento::listarSimple(), null, ["class" => "form-control"]) }}
            </div>
            <div class="col-sm-3">
              {{ Form::number("numeroDocumento", null, ["class" => "form-control", "minlength" =>"8", "maxlength" =>"20"]) }}
            </div>                    
          </div> 
          <div class="form-group">
            {{ Form::label("correoElectronico", "Correo electrónico (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::email("correoElectronico", null, ["class" => "form-control", "maxlength" =>"245"]) }}
            </div>
          </div> 
          <div class="form-group">
            {{ Form::label("imagenPerfil", "Imagen de perfil: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-4">
              {{ Form::file("imagenPerfil", null) }}
            </div>
            @if (isset($profesor->imagenPerfil) && !empty($profesor->imagenPerfil))
            <div class="col-sm-3">
              <a href="{{ route("archivos", ["nombre" => $profesor->imagenPerfil]) }}" target="_blank">
                <img src="{{ route("archivos", ["nombre" => $profesor->imagenPerfil]) }}" width="40"/>
              </a>
            </div>
            @endif
          </div>          
        </div>
        <div id="sec-wiz-profesor-2" class="step-pane sample-pane alert" data-step="2">
          @include("util.ubigeo")  
          <div class="form-group">    
            {{ Form::label("direccion", "Dirección (*): ", ["class" => "col-sm-2 control-label"]) }}                
            <div class="col-sm-10">
              {{ Form::text("direccion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label("numeroDepartamento", "Depto./Int: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("numeroDepartamento", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>  
          <div class="form-group">
            {{ Form::label("referenciaDireccion", "Referencia: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("referenciaDireccion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                
          <div class="form-group">
            {{ Form::label("geoLocalizacion", "Ubicación mapa: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10 sec-mapa">
              @include("util.ubicacionMapa")              
            </div>
            <div class="col-sm-10 col-sm-offset-2">
              <b>Sugerimos seleccionar la ubicación exacta en el mapa de la dirección del profesor.</b>
            </div>
            {{ Form::hidden("geoLatitud", null) }} 
            {{ Form::hidden("geoLongitud", null) }} 
          </div>
        </div>               
        <div id="sec-wiz-postulante-3" class="step-pane sample-pane alert" data-step="3">  
          <div class="form-group">
            {{ Form::label("ultimosTrabajos", "Últimos dos trabajos como profesor: ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("ultimosTrabajos", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>               
          </div> 
          <div class="form-group">
            {{ Form::label("experienciaOtrosIdiomas", "Experiencia como profesor de otros idiomas: ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("experienciaOtrosIdiomas", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>               
          </div>
          <div class="form-group">
            {{ Form::label("descripcionPropia", "Descripción propia como profesor: ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("descripcionPropia", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>               
          </div>
          <div class="form-group">
            {{ Form::label("ensayo", "Ensayo: ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("ensayo", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>              
          </div>
          @include("util.documentosPersonalesDocente", ["docente" => (isset($profesor) ? $profesor : null)]) 
        </div>               
        <div id="sec-wiz-profesor-4" class="step-pane sample-pane alert" data-step="4">                    
          <div class="form-group">
            <h4>Cursos asignados:</h4>
          </div>
          <div class="form-group">
            {{ Form::label("curso-interes", "Cursos (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-5">
              {{ Form::select("idCursos[]", $cursos, null, ["id" => "curso-interes", "class" => "form-control", "multiple" => "multiple", "style" => "width: 100%"]) }}
            </div>    
            {{ Form::hidden("cursos", (isset($profesor) ? $profesor->cursos : null)) }}                
          </div>
          <div class="form-group">
            <h4>Horario disponible (*):</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              <div id="sec-men-alerta-horario"></div>
              @include("util.horario")  
            </div>                                        
          </div>
          <div class="form-group">
            <h4>Audio:</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              {{ Form::file("audio", null) }}
            </div>  
          </div>
          @if (isset($profesor) && isset($profesor->audio) && !empty($profesor->audio))
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              <audio controls>
                <source src="{{ route("archivos", ["nombre" => ($profesor->audio), "audio" => 1]) }}">
                Tu explorador no soporta este elemento de audio
              </audio>
            </div>            
          </div>
          @endif
        </div>
        <div class="box-footer">   
          <div class="form-group">
            <div class="col-sm-6">
              <span>(*) Campos obligatorios</span>
            </div>
            <div class="col-sm-6">   
              @if(!(isset($modo) && $modo == "registrar"))
              <button id="btn-guardar-secundario" type="submit" class="btn btn-primary pull-right">Guardar datos</button>  
              @endif
              <button id="btn-guardar" type="button" class="btn btn-primary btn-next pull-right" data-last="{{ ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") }} datos">
                Siguiente
              </button>
              <button type="button" class="btn btn-default btn-prev pull-right">
                Anterior
              </button>
            </div>
          </div>
        </div>                
        {{ Form::hidden("modoEditarRegistrar", 1) }} 
        {{ Form::hidden("modoEditar", ((isset($modo) && $modo == "registrar") ? 0: 1)) }} 
      </div>
    </div>       
  </div>
</div>