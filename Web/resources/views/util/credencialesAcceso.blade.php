<span class="pull-right"><a id="btn-editar-credenciales-acceso" href="javascript:void(0);" title="Editar credenciales de acceso al sistema"><i class="fa fa-fw fa-user-secret"></i><i class="fa fa-fw fa-lock"></i></a></span>
<div id="mod-editar-credenciales-acceso" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Editar credenciales de acceso al sistema</h4>
      </div>
      {{ Form::open(["url" => route("entidades.actualizar.credenciales.acceso", ["id" => $entidad->id]), "id" => "formulario-editar-credenciales-acceso", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="box-body">
              <div class="form-group">
                {{ Form::label("email", "Correo electrónico (*): ", ["class" => "col-sm-2 control-label"]) }}
                <div class="col-sm-10">
                  {{ Form::email("email", $entidad->correoElectronico, ["class" => "form-control", "maxlength" =>"245"]) }}
                </div>
              </div>
              <div class="form-group">
                {{ Form::label("password", "Contraseña (*): ", ["class" => "col-sm-2 control-label"]) }}
                <div class="col-sm-10">
                  {{ Form::password("password", ["class" => "form-control", "minlength" =>"6", "maxlength" =>"30"]) }}
                </div>
              </div>
              <div class="form-group">
                {{ Form::label("password_confirmation", "Confirmar contraseña (*): ", ["class" => "col-sm-2 control-label"]) }}
                <div class="col-sm-10">
                  {{ Form::password("password_confirmation", ["class" => "form-control", "minlength" =>"6", "maxlength" =>"30"]) }}
                </div>
              </div>
              {{ Form::hidden("id", $entidad->id) }}
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
<script type="text/javascript" src="{{ asset("assets/eah/js/credencialesAcceso.js") }}"></script>