@extends("layouts.masterAlumnoProfesor")
@section("titulo", "Clases")

@section("section_script")
<script type="text/javascript">
  var urlListar = "{{ route('clases.propias.listar') }}";
</script>
<script src="{{ asset("assets/eah/js/clasesPropias.js")}}"></script>
@endsection

@section("breadcrumb")
<li class="active">Clases</li>
@endsection

@section("content")
@include("partials/errors")
@if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Profesor && isset($alumnos) && count($alumnos) > 0)
<div class="row">
  <div class="col-sm-12 sec-btn-confirmar-clase">
    <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#mod-confirmar-clase">
      Confirmar clase
    </button>
    <div id="mod-confirmar-clase" class="modal" data-keyboard="false" style="text-align: initial">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Confirmación de clase</h4>
          </div>
          {{ Form::open(["url" => route("clases.propias.confirmar.profesor.alumno"), "id" => "formulario-confirmar-clase", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <div class="box-body">
                  <div class="form-group">
                    {{ Form::label("duracion", "Duracion (*): ", ["class" => "col-sm-6 control-label"]) }}
                    <div class="col-sm-3">
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa  fa-clock-o"></i>
                        </div>    
                        {{ Form::select("duracion", [], null, ["id" => "duracion-clase", "class" => "form-control"]) }}
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    {{ Form::label("idAlumno", "Alumno(a) (*):", ["class" => "col-sm-6 control-label"]) }}
                    <div class="col-sm-6">
                      {{ Form::select("idAlumno", $alumnos, null, ["class" => "form-control"]) }}
                    </div>
                  </div> 
                  <div class="form-group">
                    {{ Form::label("codigoVerificacionClases", "Código de verificación del alumno (*): ", ["class" => "col-sm-6 control-label"]) }}
                    <div class="col-sm-4">
                      {{ Form::password("codigoVerificacionClases", ["class" => "form-control", "minlength" =>"4", "maxlength" =>"6"]) }}
                    </div>
                  </div>
                </div>   
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-sm">Confirmar</button>
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endif
<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 class="box-title">Lista de clases</h3>
      </div>         
      <div class="box-body">
        <table id="tab-lista" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>N°</th>    
              <th>Datos</th>
              <th>Estado</th>
              <th>Comentarios</th>
              <th>Comentarios de English At Home</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div id="mod-comentarios" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Déjanos tus comentarios</h4>
      </div>
      {{ Form::open(["url" => route("clases.propias.actualizar.comentarios"), "id" => "formulario-comentarios", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="box-body">
              <div class="form-group">
                <div class="col-sm-12">
                  {{ Form::textarea("comentario", null, ["class" => "form-control", "rows" => "10", "maxlength" =>"8000"]) }}
                </div>
              </div>
              {{ Form::hidden("idClase") }}
              {{ Form::hidden("idAlumno") }}
            </div>   
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-sm">Guardar</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection
