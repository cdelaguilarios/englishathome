<a id="btn-editar-imagen-perfil" href="javascript:void(0);" title="Editar imagen de perfil">
  <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($entidad->imagenPerfil) && $entidad->imagenPerfil != "" ? $entidad->imagenPerfil : "-"), "tip" => ($entidad->sexo == "F" ? "f" : "m")]) }}" alt="Alumn{{ $entidad->sexo == "F" ? "a" : "o" }} {{ $entidad->nombre . " " .  $entidad->apellido }}">
</a>
<div id="mod-editar-imagen-perfil" class="modal" data-keyboard="false" style="text-align: initial">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Editar imagen de perfil</h4>
      </div>
      {{ Form::open(["url" => route("entidad.actualizar.imagen.perfil", ["id" => $entidad->id]), "id" => "formulario-editar-imagen-perfil", "class" => "form-horizontal", "novalidate" => "novalidate", "files" => true]) }}
      <div class="modal-body">
        <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($entidad->imagenPerfil) && $entidad->imagenPerfil != "" ? $entidad->imagenPerfil : "-"), "tip" => ($entidad->sexo == "F" ? "f" : "m")]) }}" alt="Alumn{{ $entidad->sexo == "F" ? "a" : "o" }} {{ $entidad->nombre . " " .  $entidad->apellido }}">
        <div class="row">
          <div class="col-sm-12">
            <div class="box-body">
              <div class="form-group">
                {{ Form::label("imagenPerfil", "Imagen de perfil: ", ["class" => "col-sm-4 control-label"]) }}
                <div class="col-sm-8">
                  {{ Form::file("imagenPerfil", null) }}
                </div>
              </div>
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
<script type="text/javascript" src="{{ asset("assets/eah/js/imagenPerfil.js") }}"></script>