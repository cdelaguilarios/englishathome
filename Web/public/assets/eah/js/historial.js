window.addEventListener("load", verificarJqueryHistorial, false);
function verificarJqueryHistorial() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionHistorial() : window.setTimeout(verificarJqueryHistorial, 100));
}
function  cargarSeccionHistorial() {
  cargarListaHistorial();
  cargarFormularioHistorial();
  registroHistorial = (typeof (registroHistorial) === "undefined" ? false : registroHistorial);
  if (registroHistorial) {
    $("a[href='#historial']").trigger("click");
  }
}

//Lista
var ultimaFechaCargada = "";
function cargarListaHistorial() {
  urlCargarHistorial = (typeof (urlCargarHistorial) === "undefined" ? "" : urlCargarHistorial);
  urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);
  meses = (typeof (meses) === "undefined" ? "" : meses);

  var numeroCarga = $("input[name='numeroCarga']").val();
  if (urlCargarHistorial !== "" && urlArchivos !== "" && meses !== "" && !isNaN(parseInt(numeroCarga))) {
    $("#sec-historial").find("#sec-cierre-historial").hide("slow");
    $("#sec-historial").find("#sec-cierre-historial").remove();
    $("#sec-boton-carga-mas-historial").hide();
    $("#sec-historial").append('<li id="sec-cargando-historial">' +
        '<div class="box cargando">' +
        '<div class="overlay">' +
        '<i class="fa fa-refresh fa-spin"></i>' +
        '</div>' +
        '</div>' +
        '</li>');

    var datosLLamada = {numeroCarga: numeroCarga};
    if (obtenerParametroUrlXNombre("id", window.location.href) !== null) {
      datosLLamada.id = obtenerParametroUrlXNombre("id", window.location.href);
    }
    llamadaAjax(urlCargarHistorial, "POST", datosLLamada, true,
        function (d) {
          var datosHistorial = d.datos;
          var htmlHistorial = "";
          for (var fecha in datosHistorial) {
            if (!datosHistorial.hasOwnProperty(fecha))
              continue;

            if (ultimaFechaCargada !== fecha) {
              ultimaFechaCargada = fecha;
              var fechaBase = new Date(fecha);
              htmlHistorial += '<li class="time-label">' +
                  '<span class="bg-blue">' +
                  fechaBase.getDate() + ' ' + meses[fechaBase.getMonth() + 1] + ' ' + fechaBase.getFullYear() +
                  '</span>' +
                  '</li>';
            }
            var datHistorial = datosHistorial[fecha];
            for (var i = 0; i < datHistorial.length; i++) {
              htmlHistorial += '<li>' +
                  '<i class="fa ' + datHistorial[i].icono + ' ' + datHistorial[i].claseColorIcono + '"></i>' +
                  '<div class="timeline-item">' +
                  '<span class="time"><i class="fa fa-clock-o"></i> ' + datHistorial[i].horaNotificacion + '</span>' +
                  '<h3 class="timeline-header">' + datHistorial[i].titulo + '</h3>';
              if (datHistorial[i].mensaje !== "" || datHistorial[i].imagenes !== null || datHistorial[i].adjuntos !== null) {
                htmlHistorial += '<div class="timeline-body">' + (datHistorial[i].mensaje !== "" ? datHistorial[i].mensaje : "");
                if (datHistorial[i].imagenes !== null) {
                  var imagenes = datHistorial[i].imagenes.split(",");
                  $.each(imagenes, function (e, v) {
                    if (v !== null && v !== "") {
                      var rutaImagen = urlArchivos.replace("/0", "/" + v);
                      htmlHistorial += '<a href="' + rutaImagen + '" target="_blank"><img src="' + rutaImagen + '" class="margin" width="100"></a>';
                    }
                  });
                }
                if (datHistorial[i].adjuntos !== null && datHistorial[i].adjuntos !== "") {
                  var adjuntos = datHistorial[i].adjuntos.split(",");
                  if (adjuntos.length > 0) {
                    htmlHistorial += "<br/><br/><b>Adjuntos</b><br/>";
                  }
                  $.each(adjuntos, function (e, v) {
                    if (v !== null && v !== "") {
                      var datosAdjunto = v.split(":");
                      if (datosAdjunto.length === 2) {
                        var rutaAdjunto = urlArchivos.replace("/0", "/" + datosAdjunto[0]);
                        htmlHistorial += '<a href="' + rutaAdjunto + '" target="_blank">' + datosAdjunto[1] + '</a><br/>';
                      }
                    }
                  });
                }
                htmlHistorial += '</div>';
              }
              htmlHistorial += '</div></li>';
            }
          }
          htmlHistorial += (htmlHistorial !== "" ? '<li id="sec-cierre-historial"><i class="fa fa-clock-o bg-gray"></i></li>' : '');
          var nuevoDatos = $(htmlHistorial).hide();

          $("#sec-historial").find("#sec-cargando-historial").fadeOut('slow', function () {
            $("input[name='numeroCarga']").val(parseInt($("input[name='numeroCarga']").val()) + 1);
            $("#sec-historial").append(nuevoDatos);
            nuevoDatos.show("normal");
            $("#sec-historial").find("#sec-cargando-historial").remove();
            if (d.mostrarBotonCargar) {
              $("#sec-boton-carga-mas-historial").show("slow");
            } else {
              $("#sec-boton-carga-mas-historial").remove();
            }
          });
        }
    );
  }
}
function reiniciarHistorial() {
  $("input[name='numeroCarga']").val(0);
  $("#sec-historial").html("");
  ultimaFechaCargada = "";
  cargarListaHistorial();
}

//Formulario
function cargarFormularioHistorial() {
  $("#formulario-registrar-historial").validate({
    ignore: ":hidden",
    rules: {
      titulo: {
        required: true
      },
      fechaNotificacion: {
        validarFecha: true
      }
    },
    submitHandler: function (f) {
      if (!($("#enviar-correo-evento-historial").is(":checked") || $("#mostrar-perfil-evento-historial").is(":checked"))) {
        agregarMensaje("advertencias", 'Por favor selecione por lo menos una de las siguientes opciones: "Enviar correo" o "Mostrar en perfil"', true, "#sec-men-historial");
      } else if (confirm("¿Está seguro que desea registrar los datos de este evento?")) {
        $.blockUI({message: "<h4>Registrando datos...</h4>"});
        f.submit();
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
  establecerCalendario("fecha-notificacion-evento-historial", false, false);
  incluirSeccionSubidaArchivos("adjuntos", {onSubmit: function () {
      return true;
    }, acceptFiles: "*", uploadStr: "Subir archivo", maxFileCount: 20});

  $("#btn-nuevo-evento-historial").click(function () {
    limpiarCamposHistorial();
    mostrarSeccionHistorial([2]);
  });
  $("#btn-cancelar-evento-historial").click(function () {
    mostrarSeccionHistorial();
  });
  $("#notificar-inmediatamente-evento-historial").change(function () {
    (($(this).is(":visible") && $(this).is(":checked")) ? $("#sec-historial-21").hide() : $("#sec-historial-21").show());
  });
}
function limpiarCamposHistorial() {
  $("#formulario-registrar-historial").find(":input, select").each(function (i, e) {
    if (e.name !== "_token") {
      if ($(e).is("select")) {
        $(e).prop("selectedIndex", 0);
      } else if ($(e).is(":checkbox")) {
      } else {
        e.value = "";
      }
    }
  });
  $("form .help-block-error").remove();
}

//Común - Util
function mostrarSeccionHistorial(numSecciones) {
  if (!numSecciones) {
    numSecciones = [1];
  }
  $('[id*="sec-historial-"]').hide();
  var auxSec = "";
  for (var i = 0; i < numSecciones.length; i++) {
    $("#sec-historial-" + auxSec + "" + numSecciones[i]).show();
    auxSec += "" + numSecciones[i];
  }
}
