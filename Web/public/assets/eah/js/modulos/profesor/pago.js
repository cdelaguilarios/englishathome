window.addEventListener("load", verificarJqueryPago, false);
function verificarJqueryPago() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionPagos() : window.setTimeout(verificarJqueryPago, 100));
}
function cargarSeccionPagos() {
  //Urls y datos  
  urlListarPagos = (typeof (urlListarPagos) === "undefined" ? "" : urlListarPagos);
  urlActualizarEstadoPago = (typeof (urlActualizarEstadoPago) === "undefined" ? "" : urlActualizarEstadoPago);
  urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
  urlEliminarPago = (typeof (urlEliminarPago) === "undefined" ? "" : urlEliminarPago);

  urlImagenes = (typeof (urlImagenes) === "undefined" ? "" : urlImagenes);
  motivosPago = (typeof (motivosPago) === "undefined" ? "" : motivosPago);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);

  cargarListaPago();
  cargarFormularioPago();
  mostrarSeccionPago();
}

//Lista
function cargarListaPago() {
  if (urlListarPagos !== "" && urlEliminarPago !== "" && motivosPago !== "" && estadosPago !== "") {
    $("#tab-lista-pagos").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        "url": urlListarPagos,
        "type": "POST",
        "data": function (d) {
          d._token = $("meta[name=_token]").attr("content");
        }
      },
      autoWidth: false,
      columns: [
        {data: "motivo", name: "motivo"},
        {data: "monto", name: "monto"},
        {data: "fechaRegistro", name: "fechaRegistro"},
        {data: "estado", name: "estado"},
        {data: "id", name: "id", orderable: false, searchable: false, width: "10%"}
      ],
      createdRow: function (r, d, i) {
        //Motivo              
        $("td", r).eq(0).html(motivosPago[d.motivo]);

        //Monto              
        $("td", r).eq(1).html('S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : ''));

        //Fecha registro                
        $("td", r).eq(2).html(formatoFecha(d.fechaRegistro, true));

        //Estado
        $("td", r).eq(3).addClass("text-center");
        $("td", r).eq(3).html('<div class="sec-btn-editar-estado-pago"><a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + d.id + '" data-idprofesor="' + d.idProfesor + '" data-estado="' + d.estado + '"><span class="label ' + estadosPago[d.estado][1] + ' btn_estado">' + estadosPago[d.estado][0] + '</span></a></div>');

        //Botones
        var tBotones = '<ul class="buttons">' +
            '<li>' +
            '<a href="javascript:void(0);" onclick="verDatosPago(' + d.id + ');" title="Ver datos del pago"><i class="fa fa-eye"></i></a>' +
            '</li>' +
            '<li>' +
            '<a href="javascript:void(0);" title="Eliminar pago" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este pago?\', \'tab-lista-pagos\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarPago.replace("/0", "/" + d.id))) + '">' +
            '<i class="fa fa-trash"></i>' +
            '</a>' +
            '</li>' +
            '</ul>';
        $("td", r).eq(4).addClass("text-center");
        $("td", r).eq(4).html(tBotones);
      }
    });

    $(window).click(function (e) {
      if (!$(e.target).closest('.sec-btn-editar-estado-pago').length) {
        $(".sec-btn-editar-estado-pago select").trigger("change");
      }
    });
    $(".btn-editar-estado-pago").live("click", function () {
      $("#sel-estados-pago").clone().val($(this).data("estado")).data("idpago", $(this).data("idpago")).data("idprofesor", $(this).data("idprofesor")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado-pago"));
      $(this).remove();
      event.stopPropagation();
    });
    $(".sec-btn-editar-estado-pago select").live("change", function () {
      var idpago = $(this).data("idpago");
      var idprofesor = $(this).data("idprofesor");
      if (urlActualizarEstadoPago !== "" && $(this).data("estado") !== $(this).val()) {
        llamadaAjax(urlActualizarEstadoPago, "POST", {"idPago": idpago, "idProfesor": idprofesor, "estado": $(this).val()}, true);
      }
      $(this).closest(".sec-btn-editar-estado-pago").append('<a href="javascript:void(0);" class="btn-editar-estado-pago" data-idpago="' + idpago + '" data-idprofesor="' + idprofesor + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosPago[$(this).val()][1] + ' btn_estado">' + estadosPago[$(this).val()][0] + '</span></a>');
      $(this).remove();
    });
  }
}

//Formulario
function cargarFormularioPago() {
  $("#formulario-pago").validate({
    ignore: ":hidden",
    rules: {
      monto: {
        required: true,
        validarDecimal: true
      },
      imagenComprobante: {
        validarImagen: true
      }
    },
    submitHandler: function (form) {
      if ("¿Está seguro que desea registrar los datos de este pago?") {
        form.submit();
      }
    },
    highlight: function () {
    },
    unhighlight: function () {
    },
    errorElement: "div",
    errorClass: "help-block-error",
    errorPlacement: function (error, element) {
      if (element.closest("div[class*=col-sm-]").length > 0) {
        element.closest("div[class*=col-sm-]").append(error);
      } else if (element.parent(".input-group").length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    }
  });
  $("#btn-nuevo-pago").click(function () {
    limpiarCamposPago();
    mostrarSeccionPago([2]);
  });
  $("#btn-cancelar-pago").click(function () {
    mostrarSeccionPago();
  });
  if ($("#formulario-pago .contenedor-alerta").length > 0) {
    $("a[href='#pago']").tab("show");
    $("#btn-anterior-pago, #btn-registrar-pago").hide();
    $("#btn-generar-clases-pago").show();
    mostrarSeccionPago([2]);
  }
}

//Datos
function verDatosPago(idPago) {
  if (urlDatosPago !== "" && motivosPago !== "" && urlImagenes !== "" && estadosPago !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosPago.replace("/0", "/" + idPago), "POST", {}, true,
        function (d) {
          $("#sec-descripcion-pago").hide();
          if (d.descripcion !== null && d.descripcion.trim() !== "") {
            $("#sec-descripcion-pago").show();
          }
          $("#dat-motivo-pago").text(motivosPago[d.motivo]);
          $("#dat-descripcion-pago").text(d.descripcion);
          $("#dat-monto-pago").html('S/. ' + redondear(d.monto, 2) + (d.saldoFavor !== null && parseFloat(d.saldoFavor + "") > 0 ? '<br/><small><b>Saldo a favor de S/. ' + redondear(d.saldoFavor, 2) + (d.saldoFavorUtilizado !== null && d.saldoFavorUtilizado === 1 ? ' (<span class="saldo-favor-utilizado">utilizado</span>)' : '') + '</b></small>' : ''));
          $("#dat-estado-pago").html('<span class="label ' + estadosPago[d.estado][1] + ' btn_estado">' + estadosPago[d.estado][0] + '</span>');
          $("#dat-fecha-registro-pago").text(formatoFecha(d.fechaRegistro, true));
          if (d.rutasImagenesComprobante !== null && d.rutasImagenesComprobante !== "") {
            var imagenes = d.rutasImagenesComprobante.split(",");
            var rutaImagen = urlImagenes.replace("/0", "/" + imagenes[0]);
            $("#dat-imagen-comprobante-pago").attr("href", rutaImagen);
            $("#dat-imagen-comprobante-pago").find("img").attr("src", rutaImagen);
          }
          $("#mod-datos-pago").modal("show");
          $("body").unblock();
        },
        function (d) {
        },
        function (de) {
          $("body").unblock({
            onUnblock: function () {
              agregarMensaje("errores",
                  ((de.responseJSON !== undefined && de.responseJSON["mensaje"] !== undefined) ?
                      de["responseJSON"]["mensaje"] :
                      "Ocurrió un problema durante la carga de datos del pago seleccionado. Por favor inténtelo nuevamente."), true, "#sec-mensajes-mod-datos-pago");
            }
          });
        }
    );
  }
}

//Util
function mostrarSeccionPago(numSecciones) {
  if (!numSecciones) {
    numSecciones = [1];
  }
  $("[id*='sec-pago']").hide();
  var auxSec = "";
  for (var i = 0; i < numSecciones.length; i++) {
    $("#sec-pago-" + auxSec + "" + numSecciones[i]).show();
    auxSec += "" + numSecciones[i];
  }
}
function limpiarCamposPago() {
  $("#formulario-pago input, #formulario-pago select").each(function (i, e) {
    if (e.name !== "_token" && e.type !== "hidden") {
      if ($(e).is("select")) {
        $(e).prop("selectedIndex", 0);
      } else {
        e.value = "";
      }
    }
  });
} 