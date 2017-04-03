window.addEventListener("load", verificarJqueryClase, false);
function verificarJqueryClase() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionClases() : window.setTimeout(verificarJqueryClase, 100));
}
function  cargarSeccionClases() {
  cargarListaClase();
  cargarFormularioPagoClase();
  mostrarSeccionClase();

  //Común   
  if (obtenerParametroUrlXNombre("sec") === "clase") {
    $("a[href='#clase']").trigger("click");
  }
  $("a[href='#clase']").click(function () {
    $(this).tab("show");
    $("#tab-lista-clases").DataTable().responsive.recalc();
  });
}

//Lista
var primeraRecargaListaClases = true;
function cargarListaClase() {
  urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
  urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  estadosPago = (typeof (estadosPago) === "undefined" ? "" : estadosPago);
  if (urlListarClases !== "" && urlPerfilAlumno !== "" && estadosClase !== "" && estadosPago !== "") {
    $("#tab-lista-clases").DataTable({
      processing: true,
      serverSide: false,
      ajax: {
        url: urlListarClases,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          $.extend(d, obtenerDatosFiltrosBusqueda());
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[2, "desc"]],
      columns: [
        {data: "id", name: "id", orderable: false, searchable: false, render: function (e, t, d, m) {
            return d.estadoPago !== null ? '' : '<input type="checkbox" data-id="' + d.id + '" data-idalumno="' + d.idAlumno + '" data-duracion="' + d.duracion + '" data-pagoxhora="' + d.costoHoraProfesor + '" ' + (d.pagoTotalProfesor !== null ? 'data-pagototal="' + d.pagoTotalProfesor + '"' : '') + '/>';
          }, className: "text-center"},
        {data: "nombreAlumno", name: "nombreAlumno", render: function (e, t, d, m) {
            return '<a target="_blank" href="' + urlPerfilAlumno.replace("/0", "/" + d.idAlumno) + '">' + d.nombreAlumno + ' ' + d.apellidoAlumno + '</a>';
          }},
        {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
            return formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true);
          }, className: "text-center", type: "fecha"},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">Clase - ' + estadosClase[d.estado][0] + '</span>' + (d.estadoPago !== null ? '<br/><span class="label ' + estadosPago[d.estadoPago][1] + ' btn-estado">Pago ' + estadosPago[d.estadoPago][0] + '</span>' : '');
          }, className: "text-center"},
        {data: "duracion", name: "duracion", render: function (e, t, d, m) {
            return formatoHora(d.duracion);
          }, className: "text-center"},
        {data: "costoHoraProfesor", name: "costoHoraProfesor", render: function (e, t, d, m) {
            return "S/. " + redondear(d.costoHoraProfesor, 2) + (d.pagoTotalProfesor !== null ? ("<br/>(Pago total de S/. " + redondear(d.pagoTotalProfesor, 2) + ")") : "");
          }, className: "text-center", type: "monto"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista-clases");
      },
      footerCallback: function (r, d, s, e, di) {
        var api = this.api();

        var totalPagoProfesor = 0, totalPagoProfesorPagina = 0;
        $('#tab-lista-clases').DataTable().rows({filter: 'applied'}).data().each(function (i) {
          totalPagoProfesor += (i.pagoTotalProfesor !== null ? parseFloat(i.pagoTotalProfesor) : ((i.duracion !== 0 ? (i.duracion / 3600) : 0) * parseFloat(i.costoHoraProfesor)));
        });
        $('#tab-lista-clases').DataTable().rows({page: 'current'}).data().each(function (i) {
          totalPagoProfesorPagina += (i.pagoTotalProfesor !== null ? parseFloat(i.pagoTotalProfesor) : ((i.duracion !== 0 ? (i.duracion / 3600) : 0) * parseFloat(i.costoHoraProfesor)));
        });
        $(api.column(4).footer()).html("Total S/. " + redondear(totalPagoProfesor, 2) + (totalPagoProfesor !== totalPagoProfesorPagina ? "<br/>Total de la página S/." + redondear(totalPagoProfesorPagina, 2) : ""));
      }
    });
    cargarFiltrosBusqueda(function () {
      if (!primeraRecargaListaClases) {
        $("#tab-lista-clases").DataTable().ajax.reload();
      } else {
        primeraRecargaListaClases = false;
      }
    });
  }
  $("#tab-lista-clases").find("input[type='checkbox']").live("change", function () {
    mostrarSeccionClase(($("#tab-lista-clases").find("input[type='checkbox']:checked").length > 0) ? [1, 1] : [1]);
  });
}

//Formulario pago
function cargarFormularioPagoClase() {
  $("#formulario-pago-clase").validate({
    ignore: ":hidden",
    rules: {
      monto: {
        required: true,
        validarDecimal: true
      },
      imagenDocumentoVerificacion: {
        required: true,
        validarImagen: true
      },
      imagenComprobante: {
        validarImagen: true
      }
    },
    submitHandler: function (f) {
      var datosClases = "";
      $.each($("#tab-lista-clases").find("input[type='checkbox']:checked"), function (e, v) {
        datosClases += $(v).data("idalumno") + "-" + $(v).data("id") + ",";
      });
      if (datosClases !== "") {
        if (confirm("¿Está seguro que desea registrar los datos de este pago?")) {
          $("input[name='datosClases']").val(datosClases);
          $.blockUI({message: "<h4>Registrando datos...</h4>"});
          f.submit();
        }
      } else {
        $("#tab-lista-clases").find("input[type='checkbox']").trigger("change");
        agregarMensaje("advertencias", "Debe seleccionar una o más clases.", true, "#sec-mensajes-clase");
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
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
  $("#btn-registrar-pago-clase").click(function () {
    limpiarCamposPagoClase();
    var totalPago = 0;
    $.each($("#tab-lista-clases").find("input[type='checkbox']:checked"), function (e, v) {
      if ($(v).data("pagototal") !== undefined && $(v).data("pagototal") !== null) {
        totalPago += parseFloat($(v).data("pagototal"));
      } else {
        totalPago += ($(v).data("duracion") !== 0 ? ($(v).data("duracion") / 3600) : 0) * parseFloat($(v).data("pagoxhora"));
      }
    });
    $("#monto-clase-pago").val(parseFloat(redondear(totalPago, 2)));
    mostrarSeccionClase([2]);
  });
  $("#btn-cancelar-pago-clase").click(function () {
    $("#tab-lista-clases").find("input[type='checkbox']").trigger("change");
  });
}
function limpiarCamposPagoClase() {
  $("#formulario-pago-clase input, #formulario-pago-clase select").each(function (i, e) {
    if (e.name !== "_token" && e.type !== "hidden") {
      if ($(e).is("select")) {
        $(e).prop("selectedIndex", 0);
      } else {
        e.value = "";
      }
    }
  });

}

//Util
function mostrarSeccionClase(numSecciones) {
  if (!numSecciones) {
    numSecciones = [1];
  }
  $('[id*="sec-clase-"]').hide();
  var auxSec = "";
  for (var i = 0; i < numSecciones.length; i++) {
    $("#sec-clase-" + auxSec + "" + numSecciones[i]).show();
    auxSec += "" + numSecciones[i];
  }
  ((numSecciones[0].toString() === "1") ? $("#sec-clase-filtros-busqueda").show() : $("#sec-clase-filtros-busqueda").hide());
}