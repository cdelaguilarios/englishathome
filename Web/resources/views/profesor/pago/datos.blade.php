<div id="mod-datos-pago" class="modal" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Datos de pago</h4>
      </div>
      <div class="modal-body"> 
        <div class="row">
          <div class="col-md-6">
            <div id="sec-mensajes-mod-datos-pago"></div>
            <div class="box-body">
              <strong><i class="fa  fa-check-square margin-r-5"></i> Motivo</strong>
              <p id="dat-motivo-pago" class="text-muted"></p>
              <div id="sec-descripcion-pago">
                <strong><i class="fa  fa-check-square margin-r-5"></i> Descripción</strong>
                <p id="dat-descripcion-pago" class="text-muted"></p>
              </div>
              <strong><i class="fa  fa-check-square margin-r-5"></i> Monto</strong>
              <p id="dat-monto-pago" class="text-muted"></p>
              <strong><i class="fa  fa-check-square margin-r-5"></i> Estado</strong>
              <p id="dat-estado-pago" class="text-muted"></p>   
              <strong><i class="fa  fa-check-square margin-r-5"></i> Fecha registro</strong>
              <p id="dat-fecha-registro-pago" class="text-muted"></p>
            </div>
          </div>
          <div class="col-md-6">
            <a id="dat-imagen-comprobante-pago" href="{{ route("imagenes", ["rutaImagen" => "0"]) }}" target="_blank">
              <img class="img-responsive" src="{{ route("imagenes", ["rutaImagen" => "0"]) }}" alt="Imagen comprobante"> 
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>