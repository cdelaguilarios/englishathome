<div class="row">
  <div class="col-sm-12">
    <div id="wiz-registro-alumno" class="box box-info wizard" data-initialize="wizard">
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
            <span class="badge">3</span>Nivel de inglés
            <span class="chevron"></span>
          </li>
          <li data-step="4">
            <span class="badge">4</span>Datos del curso
            <span class="chevron"></span>
          </li>
        </ul>
      </div>
      <div class="step-content box-body">
        <div id="sec-wiz-alumno-1" class="step-pane active sample-pane alert" data-step="1">
          <div class="form-group">
            {{ Form::label("nombre", "Nombres (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("nombre", (isset($interesado) ? $interesado->nombre : null), ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label("apellido", "Apellidos (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("apellido", (isset($interesado) ? $interesado->apellido : null), ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>  
          <div class="form-group">
            {{ Form::label("telefono", "Teléfono" . ((Auth::guest()) ? " (*)" : "") . ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::text("telefono", (isset($interesado) ? $interesado->telefono : null), ["class" => "form-control", "maxlength" =>"30"]) }}
            </div>
          </div>                 
          <div class="form-group">
            {{ Form::label("fecha-nacimiento", "Fecha nacimiento" . ((Auth::guest()) ? " (*)" : "") . ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                                
                {{ Form::text("fechaNacimiento", (isset($alumno->fechaNacimiento) ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaNacimiento)->format("d/m/Y") : null), ["id" => "fecha-nacimiento", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
            {{ Form::label("sexo", "Sexo: ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-2">
              {{ Form::select("sexo", $sexos, null, ["class" => "form-control"]) }}
            </div>
          </div>            
          <div class="form-group">
            {{ Form::label("numeroDocumento", "Doc. de identidad" . ((Auth::guest()) ? " (*)" : "") . ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3" style="display:none">
              {{ Form::select("idTipoDocumento", $tiposDocumentos, null, ["class" => "form-control"]) }}
            </div>
            <div class="col-sm-3">
              {{ Form::number("numeroDocumento", null, ["class" => "form-control", "minlength" =>"8", "maxlength" =>"20"]) }}
            </div>                    
          </div> 
          <div class="form-group">
            {{ Form::label("correoElectronico", "Correo electrónico (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::email("correoElectronico", (isset($interesado) ? $interesado->correoElectronico : null), ["class" => "form-control", "maxlength" =>"245"]) }}
            </div>
          </div> 
          <div class="form-group">
            {{ Form::label("imagenPerfil", "Imagen de perfil: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-4">
              {{ Form::file("imagenPerfil", null) }}
            </div>
            @if (isset($alumno->imagenPerfil) && !empty($alumno->imagenPerfil))
            <div class="col-sm-3">
              <a href="{{ route("archivos", ["nombre" => $alumno->imagenPerfil]) }}" target="_blank">
                <img src="{{ route("archivos", ["nombre" => $alumno->imagenPerfil]) }}" width="40"/>
              </a>
            </div>
            @endif
          </div>          
        </div>
        <div id="sec-wiz-alumno-2" class="step-pane sample-pane alert" data-step="2">
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
              <b>Sugerimos seleccionar la ubicación exacta en el mapa del lugar donde se realizarán las clases.</b>
            </div>
            {{ Form::hidden("geoLatitud", null) }} 
            {{ Form::hidden("geoLongitud", null) }} 
          </div>
        </div>
        <div id="sec-wiz-alumno-3" class="step-pane sample-pane alert" data-step="3">
          <div class="form-group">
            {{ Form::label("idNivelIngles", "Nivel de ingles logrado (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idNivelIngles", $nivelesIngles, (isset($alumno) ? $alumno->idNivelIngles : null), ["class" => "form-control"]) }}
            </div>                   
          </div>                    
          <div class="form-group">
            {{ Form::label("inglesLugarEstudio", "Lugar donde estudió: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("inglesLugarEstudio", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                   
          <div class="form-group">
            {{ Form::label("inglesPracticaComo", "¿Lo practica?¿Cómo?: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("inglesPracticaComo", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                   
          <div class="form-group">
            {{ Form::label("inglesObjetivo", "Objetivos específicos: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("inglesObjetivo", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
        </div>                
        <div id="sec-wiz-alumno-4" class="step-pane sample-pane alert" data-step="4">
          <div class="form-group">
            <h4>En casa o en su oficina usted cuenta con:</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-3 col-sm-offset-1">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conComputadora", "Con computadora: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conComputadora", null, (isset($alumno->conComputadora) && $alumno->conComputadora == "1")) }}
                </label>
              </div>
            </div>                        
            <div class="col-sm-8">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conInternet", "Con internet: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conInternet", null, (isset($alumno->conInternet) && $alumno->conInternet == "1")) }}
                </label>
              </div>
            </div>    
            <div class="col-sm-3 col-sm-offset-1">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conAmbienteClase", "Un ambiente adecuado para la realización de la clase: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conAmbienteClase", null, (isset($alumno->conAmbienteClase) && $alumno->conAmbienteClase == "1")) }}
                </label>
              </div>
            </div>
            <div class="col-sm-8">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conPlumonPizarra", "Pizarra / plumones: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conPlumonPizarra", null, (isset($alumno->conPlumonPizarra) && $alumno->conPlumonPizarra == "1")) }}
                </label>
              </div>
            </div>
          </div><br/>
          <div class="form-group">
            <h4>Detalle del curso:</h4>
          </div>
          <div class="form-group">
            {{ Form::label("idCurso", "Curso de interes: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idCurso", $cursos, (isset($alumno) ? $alumno->idCurso : (isset($interesado) ? $interesado->idCurso : null)), ["class" => "form-control"]) }}
            </div>
            {{ Form::label("numero-horas-clase", "Número de horas por clase (*): ", ["class" => "col-sm-3 control-label"]) }}
            <div class="col-sm-2">
              {{ Form::select("numeroHorasClase", [], (isset($alumno->numeroHorasClase) ? $alumno->numeroHorasClase : null), ["id" => "numero-horas-clase", "class" => "form-control"]) }}     
              {{ Form::hidden("auxNumeroHorasClase", (isset($alumno->numeroHorasClase) ? $alumno->numeroHorasClase : null)) }}             
            </div>                    
          </div>
          <div class="form-group">
            {{ Form::label("fecha-inicio-clase", "Inicio de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                                    
                {{ Form::text("fechaInicioClase", (isset($alumno->fechaInicioClase) ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaInicioClase)->format("d/m/Y") : null), ["id" => "fecha-inicio-clase", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
            @if(isset($interesado))
            {{ Form::hidden("costoHoraClase", $interesado->costoHoraClase) }} 
            @else
            {{ Form::label("costo-hora-clase", "Costo hora de clase (*): ", ["class" => "col-sm-3 control-label"]) }}
            <div class="col-sm-2">
              <div class="input-group">
                <span class="input-group-addon">
                  <b>S/.</b>
                </span>
                {{ Form::text("costoHoraClase", (isset($alumno) ? number_format($alumno->costoHoraClase, 2, '.', ',') : null), ["id" => "costo-hora-clase", "class" => "form-control", "maxlength" =>"19"]) }}
              </div>
            </div>
            @endif 
          </div><br/>
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
            <h4>Comentarios adicionales:</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              {{ Form::textarea("comentarioAdicional", null, ["class" => "form-control", "rows" => "2", "maxlength" =>"255"]) }}
            </div>                                        
          </div>
        </div>
        <div class="box-footer">   
          <div class="form-group">
            <div class="col-sm-6">
              <span>(*) Campos obligatorios</span>
            </div>
            <div class="col-sm-6">   
              <button id="btn-guardar" type="button" class="btn btn-primary btn-next pull-right" data-last="{{ ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") }} datos">
                Siguiente
              </button>
              <button type="button" class="btn btn-default btn-prev pull-right">
                Anterior
              </button>
            </div>
          </div>
        </div>                
        {{ Form::hidden("usuarioNoLogueado", ((Auth::guest()) ? 1 : 0)) }}
        {{ Form::hidden("modoEditarRegistrar", 1) }} 
        {{ Form::hidden("modoEditar", ((isset($modo) && $modo == "registrar") ? 0: 1)) }} 
        {{ Form::hidden("idInteresado", (isset($interesado) ? $interesado->idEntidad : null)) }}  
        {{ Form::hidden("codigoVerificacion", (isset($codigoVerificacion) ? $codigoVerificacion : null)) }}
      </div>
    </div>       
  </div>
</div>