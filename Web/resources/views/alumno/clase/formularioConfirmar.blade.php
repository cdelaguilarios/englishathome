<script src="{{ asset("assets/eah/js/modulos/alumno/clase/formularioConfirmarClase.js")}}"></script>
<button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#mod-confirmar-clase">
  Confirmar clase
</button>
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
        {{ Form::open(["url" => route("alumnos.clases.confirmar", ['id' => $alumno->id]), "id" => "formulario-confirmar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              <div class="box-body">
                <div class="form-group">
                  {{ Form::label("", "Profesor: ", ["class" => "col-sm-3 control-label"]) }}
                  <div class="col-sm-9">
                    {{ Form::text("", $alumno->ultimoPago->nombreProfesor . " " .  $alumno->ultimoPago->apellidoProfesor, ["class" => "form-control", "disabled" =>""]) }}
                  </div>
                  {{ Form::hidden("idProfesor", $alumno->ultimoPago->idProfesor) }}
                </div>
                <div class="form-group"> 
                  {{ Form::label("fecha-clase", "Fecha (*): ", ["class" => "col-sm-3 control-label"]) }}
                  <div class="col-sm-5">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>                                
                      {{ Form::text("fecha", null, ["id" => "fecha-clase", "class" => "form-control", "placeholder" => "dd/mm/aaaa"]) }}
                    </div>
                  </div> 
                </div>
                <div class="form-group">    
                  {{ Form::label("hora-inicio-clase", "Hora inicio: ", ["class" => "col-sm-3 control-label"]) }}
                  <div class="col-sm-4">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa  fa-clock-o"></i>
                      </div>    
                      {{ Form::select("horaInicio", [], null, ["id" => "hora-inicio-clase", "class" => "form-control"]) }}
                    </div>
                  </div>
                </div>
                <div class="form-group">  
                  {{ Form::label("duracion-clase", "Duración: ", ["class" => "col-sm-3 control-label"]) }}
                  <div class="col-sm-4">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa  fa-clock-o"></i>
                      </div>    
                      {{ Form::select("duracion", [], null, ["id" => "duracion-clase", "class" => "form-control"]) }}
                    </div>
                  </div>
                </div>
              </div>   
            </div>
          </div>
        </div>
        <div class="modal-footer">         
          <button type="submit" class="btn btn-success">Confirmar</button>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>