<div class="row">
  <div class="col-sm-12">
    <div id="wiz-registro-postulante" class="box box-info wizard" data-initialize="wizard">
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
            <span class="badge">4</span>Datos adicionales
            <span class="chevron"></span>
          </li>
        </ul>
      </div>
      <div class="step-content box-body">
        <div id="sec-wiz-postulante-1" class="step-pane active sample-pane alert" data-step="1">
          <div class="form-group">
            {{ Form::label("nombre", (Auth::guest() ? "Name" : "Nombres") . " (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("nombre", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label("apellido", (Auth::guest() ? "Last name" : "Apellidos") . " (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("apellido", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>  
          <div class="form-group">
            {{ Form::label("telefono", (Auth::guest() ? "Cell phone number" : "Teléfono") . ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::text("telefono", null, ["class" => "form-control", "maxlength" =>"30"]) }}
            </div>
          </div>                 
          <div class="form-group">
            {{ Form::label("fechaNacimiento", (Auth::guest() ? "Birthday (*)" : "Fecha nacimiento") .  ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                          
                {{ Form::text("fechaNacimiento", (isset($postulante->fechaNacimiento) ? \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $postulante->fechaNacimiento)->format("d/m/Y") : null), ["id" => "fecha-nacimiento", "class" => "form-control  pull-right", "placeholder" => (Auth::guest() ? "dd/mm/yyyy" : "dd/mm/aaaa")]) }}
              </div>
            </div>
            {{ Form::label("sexo", (Auth::guest() ? "Gender" : "Sexo") .  ": ", ["class" => "col-sm-1 control-label"]) }}
            <div class="col-sm-2">
              {{ Form::select("sexo", App\Helpers\Enum\SexosEntidad::listar(Auth::guest()), null, ["class" => "form-control"]) }}
            </div>
          </div>            
          <div class="form-group">
            {{ Form::label("numeroDocumento", (Auth::guest() ? "ID card (*)" : "Doc. de identidad") .  ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3" style="display:none">
              {{ Form::select("idTipoDocumento", App\Models\TipoDocumento::listarSimple(), null, ["class" => "form-control"]) }}
            </div>
            <div class="col-sm-3">
              {{ Form::number("numeroDocumento", null, ["class" => "form-control", "minlength" =>"8", "maxlength" =>"20"]) }}
            </div>                    
          </div> 
          <div class="form-group">
            {{ Form::label("correoElectronico", (Auth::guest() ? "E-mail adress" : "Correo electrónico") .  " (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::email("correoElectronico", null, ["class" => "form-control", "maxlength" =>"245"]) }}
            </div>
          </div> 
          <div class="form-group">
            {{ Form::label("imagenPerfil", (Auth::guest() ? "Profile Image" : "Imagen de perfil") .  ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-4">
              {{ Form::file("imagenPerfil", null) }}
            </div>
            @if (isset($postulante->imagenPerfil) && !empty($postulante->imagenPerfil))
            <div class="col-sm-3">
              <a href="{{ route("archivos", ["nombre" => $postulante->imagenPerfil]) }}" target="_blank">
                <img src="{{ route("archivos", ["nombre" => $postulante->imagenPerfil]) }}" width="40"/>
              </a>
            </div>
            @endif
          </div>          
        </div>
        <div id="sec-wiz-postulante-2" class="step-pane sample-pane alert" data-step="2">
          @include("util.ubigeo")  
          <div class="form-group">    
            {{ Form::label("direccion", (Auth::guest() ? "Address" : "Dirección") .  " (*): ", ["class" => "col-sm-2 control-label"]) }}                
            <div class="col-sm-10">
              {{ Form::text("direccion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label("numeroDepartamento", (Auth::guest() ? "Apartment number" : "Depto./Int") .  ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("numeroDepartamento", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>  
          <div class="form-group">
            {{ Form::label("referenciaDireccion", (Auth::guest() ? "Reference" : "Referencia") .  ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("referenciaDireccion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                
          <div class="form-group">
            {{ Form::label("geoLocalizacion", (Auth::guest() ? "Current location" : "Ubicación mapa") .  ": ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10 sec-mapa">
              @include("util.ubicacionMapa")              
            </div>
            @if(!Auth::guest())
            <div class="col-sm-10 col-sm-offset-2">
              <b>Sugerimos seleccionar la ubicación exacta en el mapa de la dirección del postulante.</b>
            </div>
            @endif
            {{ Form::hidden("geoLatitud", null) }} 
            {{ Form::hidden("geoLongitud", null) }} 
          </div>
        </div>               
        <div id="sec-wiz-postulante-3" class="step-pane sample-pane alert" data-step="3">  
          <div class="form-group">
            {{ Form::label("ultimosTrabajos", (Auth::guest() ? "Mention last two teaching jobs (*)" : "Últimos dos trabajos como profesor") .  ": ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("ultimosTrabajos", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>               
          </div> 
          <div class="form-group">
            {{ Form::label("experienciaOtrosIdiomas", (Auth::guest() ? "Do you have experience teaching other languages? (*)" : "Experiencia como profesor de otros idiomas") .  ": ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("experienciaOtrosIdiomas", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>               
          </div>
          <div class="form-group">
            {{ Form::label("descripcionPropia", (Auth::guest() ? "Do you consider yourself a good teacher? why? (*)" : "Descripción propia como profesor") .  ": ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              {{ Form::textarea("descripcionPropia", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>               
          </div>
          <div class="form-group">
            {{ Form::label("ensayo", (Auth::guest() ? "Write a short Essay (150 words) (Be original, don´t copy and paste, please) (*)" : "Ensayo") .  ": ", ["class" => "col-sm-2 control-label"]) }}            
            <div class="col-sm-10">
              @if(Auth::guest())
              <b>What are the positive and/or negative aspects of the internet:</b><br/>
              @endif 
              {{ Form::textarea("ensayo", null, ["class" => "form-control", "rows" => "4", "maxlength" =>"1000"]) }}
            </div>              
          </div>
          <div class="form-group">
            {{ Form::label("documentosPersonales", (Auth::guest() ? "Personal documents (International Certificates, CV, etc)" : "Documentos personales (Certificados internacioales, CV, etc). Max. 3 documentos") .  ": ", ["class" => "col-sm-2 control-label"]) }}   
            <div class="col-sm-10">
              <div id="documentos-personales">{{ (Auth::guest() ? "Upload" : "Subir") }}</div>
              @if(isset($postulante) && $postulante->documentosPersonales != null)
              @php
                $documentosPersonales = explode(",", $postulante->documentosPersonales);
              @endphp
              @for($i=0; $i < count($documentosPersonales); $i++)
                @if($documentosPersonales[$i] != "")  
                  @php
                    $datosDocumentoPersonal = explode(":", $documentosPersonales[$i]);
                  @endphp         
                  @if(count($datosDocumentoPersonal) == 2)
                  <div class="ajax-file-upload-container">
                    <div class="ajax-file-upload-statusbar" style="width: 400px;">
                      <div class="ajax-file-upload-filename">
                        <a href="{{ route("archivos", ["nombre" => $datosDocumentoPersonal[0]]) }}" download="{{ $datosDocumentoPersonal[1] }}">{{ $datosDocumentoPersonal[1] }}</a>
                      </div>
                      <div class="ajax-file-upload-progress">
                        <div class="ajax-file-upload-bar" style="width: 100%;"></div>
                      </div>
                      <div class="ajax-file-upload-red" onclick="eliminarDocumentoPersonal(this, '{{ $datosDocumentoPersonal[0] }}')">Eliminar</div>
                    </div>
                  </div>
                  @endif
                @endif
              @endfor
              @endif
              {{ Form::hidden("nombresDocumentosPersonales", "", ["id" => "nombres-archivos-documentos-personales"]) }}
              {{ Form::hidden("nombresDocumentosPersonalesEliminados", "", ["id" => "nombres-archivos-documentos-personales-eliminados"]) }}
              {{ Form::hidden("nombresOriginalesDocumentosPersonales", "", ["id" => "nombres-originales-archivos-documentos-personales"]) }}
            </div>
            <div class="clearfix"></div>
          </div>
        </div>             
        <div id="sec-wiz-postulante-4" class="step-pane sample-pane alert" data-step="4">              
          @if(!(Auth::guest()))      
          <div class="form-group">
            <h4>Cursos asignados:</h4>
          </div>
          <div class="form-group">
            {{ Form::label("curso-interes", "Cursos (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-5">
              {{ Form::select("idCursos[]", $cursos, null, ["id" => "curso-interes", "class" => "form-control", "multiple" => "multiple", "style" => "width: 100%"]) }}
            </div>    
            {{ Form::hidden("cursos", (isset($postulante) ? $postulante->cursos : null)) }}                
          </div>
          @endif
          <div class="form-group">
            <h4>{{ (Auth::guest() ? "Schedule available to work" : "Horario disponible") }} (*):</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              <div id="sec-men-alerta-horario"></div>
              @include("util.horario")  
            </div>                                        
          </div>
          <div class="form-group">
            <h4>Audio{{ (Auth::guest() ? " (*)" : "") }}:</h4>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              {{ Form::file("audio", null) }}
            </div>  
          </div>
          @if (!Auth::guest() && isset($postulante) && isset($postulante->audio) && !empty($postulante->audio))
          <div class="form-group">
            <div class="col-sm-offset-1 col-sm-10">
              <audio controls>
                <source src="{{ route("archivos", ["nombre" => ($postulante->audio), "audio" => 1]) }}">
                Tu explorador no soporta este elemento de audio
              </audio>
            </div>            
          </div>
          @endif
          {{ Form::hidden("registrarComoProfesor", null) }}
        </div>
        <div class="box-footer">   
          <div class="form-group">
            <div class="col-sm-6">
              <span>(*) {{ (Auth::guest() ? "Fields required" : "Campos obligatorios") }}</span>
            </div>
            <div class="col-sm-6">  
              @if(!(isset($modo) && $modo == "registrar"))
              <button id="btn-guardar-secundario" type="submit" class="btn btn-primary pull-right">Guardar datos</button>  
              @endif
              <button id="btn-guardar" type="button" class="btn btn-primary btn-next pull-right" data-last="{{ (Auth::guest() ? "Save" : ((isset($modo) && $modo == "registrar") ? "Registrar datos" : "Guardar datos")) }}">
                {{ (Auth::guest() ? "Next" : "Siguiente") }}
              </button>
              <button type="button" class="btn btn-default btn-prev pull-right">
                {{ (Auth::guest() ? "Prev" : "Anterior") }}
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