<script type="text/javascript">
  var duracionTotalXClasesPendientes = parseInt("{{ $alumno->duracionTotalXClasesPendientes }}") / 3600;
</script>
<script src="{{ asset("assets/eah/js/modulosExternos/profesor/formularioConfirmarClase.js")}}"></script>
<div class="row">
  <div class="col-sm-12">
    <div id="mod-confirmar-clase" class="modal" data-keyboard="false" style="text-align: initial">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Confirmación de clase {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }} {{ $alumno->nombre . " " .  $alumno->apellido }}</h4>
          </div>
          {{ Form::open(["url" => route("profesores.mis.alumnos.confirmar.clase", ['id' => $alumno->id]), "id" => "formulario-confirmar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="box-body">
                  <div class="form-group">
                    {{ Form::label("duracion", "Duracion: ", ["class" => "col-sm-4 control-label"]) }}
                    {{ Form::label("", ($alumno->duracionTotalXClasesPendientes/3600 >= 2 && $minHorasClase <= 1 ? 2 : $minHorasClase), ["id" => "sec-duracion", "class" => "col-sm-1 control-label"]) }}
                    <div id="sec-cambio-duracion" class="col-sm-4" style="display: none">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa  fa-clock-o"></i>
                        </div>    
                        {{ Form::select("duracion", [], null, ["id" => "duracion-clase", "class" => "form-control"]) }}
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    {{ Form::label("codigoVerificacion", "Código del alumno (*): ", ["class" => "col-sm-4 control-label"]) }}
                    <div class="col-sm-4">
                      {{ Form::password("codigoVerificacion", ["class" => "form-control", "minlength" =>"4", "maxlength" =>"6"]) }}
                    </div>
                  </div>
                  <div class="form-group">
                    {{ Form::label("comentario", "Avances de la clase: ", ["class" => "col-sm-4 control-label"]) }}
                    <div class="col-sm-8">
                      {{ Form::textarea("comentario", null, ["class" => "form-control", "rows" => "6", "maxlength" =>"8000"]) }}
                    </div>  
                  </div>
                </div>   
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="btn-cambiar-duracion" type="button" class="btn btn-warning btn-next pull-left">Cambiar duración de la clase</button>            
            <button type="submit" class="btn btn-success">Confirmar</button>
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>