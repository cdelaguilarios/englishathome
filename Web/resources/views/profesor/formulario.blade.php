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
            <span class="badge">3</span>Datos de cursos asignados
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
            {{ Form::label("telefono", "Teléfono (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("telefono", null, ["class" => "form-control", "maxlength" =>"30"]) }}
            </div>
          </div>                 
          <div class="form-group">
            {{ Form::label("fechaNacimiento", "Fecha nacimiento (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>                          
                {{ Form::text("fechaNacimiento", NULL, ["id" => "fecha-nacimiento", "class" => "form-control  pull-right", "placeholder" => "dd/mm/aaaa"]) }}
              </div>
            </div>
          </div>            
          <div class="form-group">
            {{ Form::label("numeroDocumento", "Doc. de identidad (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idTipoDocumento", $tiposDocumentos, null, ["class" => "form-control"]) }}
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
            @if (isset($profesor->rutaImagenPerfil) && !empty($profesor->rutaImagenPerfil))
            <div class="col-sm-3">
              <a href="{{ route("imagenes", ["rutaImagen" => $profesor->rutaImagenPerfil]) }}" target="_blank">
                <img src="{{ route("imagenes", ["rutaImagen" => $profesor->rutaImagenPerfil]) }}" width="40"/>
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
            {{ Form::label("referenciaDireccion", "Referencia: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10">
              {{ Form::text("referenciaDireccion", null, ["class" => "form-control", "maxlength" =>"255"]) }}
            </div>
          </div>                
          <div class="form-group">
            {{ Form::label("geoLocalizacion", "Ubicación mapa: ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-10 sec-mapa">
              @include("util.ubicacionMapa", ["modo" => (isset($modo) ? $modo : "editar")])
              <div>Sugerimos seleccionar la ubicación exacta en el mapa del lugar donde se realizarán las clases.</div>
            </div>
            {{ Form::hidden("geoLatitud", null) }} 
            {{ Form::hidden("geoLongitud", null) }} 
          </div>
        </div>               
        <div id="sec-wiz-profesor-3" class="step-pane sample-pane alert" data-step="3">                    
          <div class="form-group">
            <h4>Cursos asignados:</h4>
          </div>
          <div class="form-group">
            {{ Form::label("idCurso", "Curso de interes (*): ", ["class" => "col-sm-2 control-label"]) }}
            <div class="col-sm-3">
              {{ Form::select("idCurso", $cursos, null, ["class" => "form-control"]) }}
            </div>                   
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
        </div>
        <div class="box-footer">    
          <span>(*) Campos obligatorios</span>
          <button type="button" class="btn btn-success btn-next pull-right" data-last="Registrar datos">
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