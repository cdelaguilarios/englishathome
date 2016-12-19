<div class="row">
  <div class="col-md-12">
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
              {{ Form::text("nombre", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label("apellido", "Apellidos (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("apellido", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>  
          <div class="form-group">
            {{ Form::label("telefono", "Teléfono (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("telefono", NULL, ["class" => "form-control", "maxlength" =>"30"]) }}
            </div>
          </div>                 
          <div class="form-group">
            {{ Form::label("fechaNacimiento", "Fecha nacimiento (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                                
                {{ Form::text("fechaNacimiento", NULL, ["id" => "fecha-nacimiento", "class" => "form-control  pull-right"]) }}
              </div>
            </div>
          </div>            
          <div class="form-group">
            {{ Form::label("numeroDocumento", "Doc. de identidad (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idTipoDocumento", $tiposDocumentos, NULL, ["class" => "form-control"]) }}
            </div>
            <div class="col-sm-3">
              {{ Form::number("numeroDocumento", NULL, ["class" => "form-control", "minlength" =>"8", "maxlength" =>"20"]) }}
            </div>                    
          </div> 
          <div class="form-group">
            {{ Form::label("correoElectronico", "Correo electrónico (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::email("correoElectronico", NULL, ["class" => "form-control", "maxlength" =>"245"]) }}
            </div>
          </div> 
          <div class="form-group">
            {{ Form::label("imagenPerfil", "Imagen de perfil: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-4">
              {{ Form::file("imagenPerfil", NULL) }}
            </div>
            @if (isset($alumno->rutaImagenPerfil) && !empty($alumno->rutaImagenPerfil))
            <div class="col-sm-3">
              <a href="{{ route("imagenes", ["rutaImagen" => $alumno->rutaImagenPerfil]) }}" target="_blank">
                <img src="{{ route("imagenes", ["rutaImagen" => $alumno->rutaImagenPerfil]) }}" width="40"/>
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
              {{ Form::text("direccion", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div> 
          <div class="form-group">
            {{ Form::label("referenciaDireccion", "Referencia: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("referenciaDireccion", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                
          <div class="form-group">
            {{ Form::label("geoLocalizacion", "Ubicación mapa: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10 sec-mapa">
              @include("util.ubicacionMapa")
              <div>Sugerimos seleccionar la ubicación exacta en el mapa del lugar donde se realizarán las clases.</div>
            </div>
            {{ Form::hidden("geoLatitud", NULL) }} 
            {{ Form::hidden("geoLongitud", NULL) }} 
          </div>
        </div>
        <div id="sec-wiz-alumno-3" class="step-pane sample-pane alert" data-step="3">
          <div class="form-group">
            {{ Form::label("idNivelIngles", "Nivel de ingles logrado (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idNivelIngles", $nivelesIngles, (isset($alumno) ? $alumno->idNivelIngles : NULL), ["class" => "form-control"]) }}
            </div>                   
          </div>                    
          <div class="form-group">
            {{ Form::label("inglesLugarEstudio", "Lugar donde estudió: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("inglesLugarEstudio", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                   
          <div class="form-group">
            {{ Form::label("inglesPracticaComo", "¿Lo practica?¿Cómo?: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("inglesPracticaComo", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                   
          <div class="form-group">
            {{ Form::label("inglesObjetivo", "Objetivos específicos: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("inglesObjetivo", NULL, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
        </div>                
        <div id="sec-wiz-alumno-4" class="step-pane sample-pane alert" data-step="4">
          <div class="form-group">
            <h4>En casa o en su oficina usted cuenta con:</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-2">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conComputadora", "Con computadora: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conComputadora", NULL, (isset($alumno->conComputadora) && $alumno->conComputadora == "1")) }}
                </label>
              </div>
            </div>                        
            <div class="col-sm-2">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conInternet", "Con internet: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conInternet", NULL, (isset($alumno->conInternet) && $alumno->conInternet == "1")) }}
                </label>
              </div>
            </div>    
            <div class="col-sm-3">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conPlumonPizarra", "Pizarra / plumones: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conPlumonPizarra", NULL, (isset($alumno->conPlumonPizarra) && $alumno->conPlumonPizarra == "1")) }}
                </label>
              </div>
            </div>
            <div class="col-sm-5">
              <div class="checkbox">
                <label class="checkbox-custom" data-initialize="checkbox">
                  {{ Form::label("conAmbienteClase", "Un ambiente adecuado para la realización de la clase: ", ["class" => "checkbox-label"]) }}
                  {{ Form::checkbox("conAmbienteClase", NULL, (isset($alumno->conAmbienteClase) && $alumno->conAmbienteClase == "1")) }}
                </label>
              </div>
            </div>
          </div><br/>
          <div class="form-group">
            <h4>Detalle del curso:</h4>
          </div>
          <div class="form-group">
            {{ Form::label("idCurso", "Curso de interes (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idCurso", $cursos, (isset($alumno) ? $alumno->idCurso : NULL), ["class" => "form-control"]) }}
            </div>
            {{ Form::label("numeroHorasClase", "Número de horas por clase (*): ", ["class" => "col-sm-3 control-label"]) }}
            <div class="col-sm-1">
              {{ Form::select("numeroHorasClase", [], (isset($alumno->numeroHorasClase) ? $alumno->numeroHorasClase : NULL), ["id" => "numero-horas-clase", "class" => "form-control"]) }}     
              {{ Form::hidden("auxNumeroHorasClase", (isset($alumno->numeroHorasClase) ? $alumno->numeroHorasClase : NULL)) }}             
            </div>                    
          </div>
          <div class="form-group">
            {{ Form::label("fechaInicioClase", "Inicio de clases (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                                
                {{ Form::text("fechaInicioClase", NULL, ["id" => "fecha-inicio-clase", "class" => "form-control  pull-right"]) }}
              </div>
            </div>
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
              {{ Form::textarea("comentarioAdicional", NULL, ["class" => "form-control", "rows" => "2", "maxlength" =>"255"]) }}
            </div>                                        
          </div>
        </div>
        <div class="box-footer">    
          <span>(*) Campos obligatorios</span>          
          <button id="btn-guardar" type="button" class="btn btn-success btn-next pull-right" data-last="{!! ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") !!} datos">
                  Siguiente
        </button>
        <button type="button" class="btn btn-default btn-prev pull-right">
          Anterior
        </button>
      </div>                
      {{ Form::hidden("modoEditarRegistrar", 1) }} 
    </div>
  </div>       
</div>
</div>