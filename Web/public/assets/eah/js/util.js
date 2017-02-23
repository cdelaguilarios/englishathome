function llamadaAjax(url, metodo, datos, async, funcionRetorno, funcionCompletado, funcionError) {
  jQuery.ajax({
    url: url,
    method: metodo,
    data: datos,
    async: async,
    headers: {"X-CSRF-Token": $("meta[name=_token]").attr("content")},
    success: function (result) {
      if (funcionRetorno !== undefined)
        funcionRetorno(result);
    },
    complete: function (data) {
      if (funcionCompletado !== undefined)
        funcionCompletado(data);
    },
    error: function (error) {
      if (funcionError !== undefined)
        funcionError(error);
    }
  });
}
function limpiarCampos() {
  $("form input, form select").each(function (i, e) {
    if (e.type !== "hidden") {
      if ($(e).is("select")) {
        $(e).prop("selectedIndex", 0);
      } else if ($(e).is(":checkbox")) {
        $(e).attr("checked", false);
        $(e).closest("label").removeClass("checked");
      } else {
        e.value = "";
      }
    }
  });
  $("form textarea").each(function (i, e) {
    e.value = "";
  });
  $("form  .help-block-error").remove();
  return false;
}
function obtenerParametroUrlXNombre(nombre, url) {
  if (!url)
    url = window.location.href;
  nombre = nombre.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + nombre + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
  if (!results)
    return null;
  if (!results[2])
    return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

$.validator.addMethod("validarDecimal", validarDecimal, "Ingreso no valido.");
function validarDecimal(value, element, param) {
  if (value.trim() === "")
    return true;
  var filter2 = /^[\d]{1,14}(\.[\d]{1,4})?$/;
  return filter2.test(value);
}
$.validator.addMethod("validarDecimalNegativo", validarDecimalNegativo, "Ingreso no valido.");
function validarDecimalNegativo(value, element, param) {
  if (value.trim() === "")
    return true;
  var filter2 = /^-?[\d]{1,14}(\.[\d]{1,4})?$/;
  return filter2.test(value);
}
$.validator.addMethod("validarPorcentaje", validarPorcentaje, "Ingreso no valido.");
function validarPorcentaje(value, element, param) {
  if (value.trim() === "")
    return true;
  var filter2 = /^100$|^100.0$|^100.00$|^[0-9]{1,2}$|^[0-9]{1,2}\.[0-9]{1,2}$/;
  return filter2.test(value);
}
$.validator.addMethod("validarEntero", validarEntero, "Ingreso no válido.");
function validarEntero(value, element, param) {
  if (("" + value).trim() === "")
    return true;
  return (/^\d+$/.test(("" + value)));
}
$.validator.addMethod("validarImagen", validarImagen, "Por favor seleccione una imagen válida (formatos válidos: jpg, jpeg, png y gif).");
function validarImagen(value, element, param) {
  var extension = value.split(".").pop().toLowerCase();
  return (value.trim() === "" || extension.trim() === "jpg" || extension.trim() === "jpeg" || extension.trim() === "png" || extension.trim() === "gif");
}
$.validator.addMethod("validarAlfanumerico", validarAlfanumerico, "Por favor solo ingrese valores alfanuméricos.");
function validarAlfanumerico(value, element, param) {
  return this.optional(element) || /^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/i.test(value);
}
$.validator.addMethod("validarAlfabetico", validarAlfabetico, "Por favor solo ingrese letras.");
function validarAlfabetico(value, element, param) {
  return this.optional(element) || /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/i.test(value);
}
$.validator.addMethod("validarFecha", validarFecha, "Por favor ingrese una fecha válida (formato válido: dd/mm/aaaa)");
function validarFecha(value, element, param) {
  return this.optional(element) || /(^(((0[1-9]|1[0-9]|2[0-8])[\/](0[1-9]|1[012]))|((29|30|31)[\/](0[13578]|1[02]))|((29|30)[\/](0[4,6,9]|11)))[\/](19|[2-9][0-9])\d\d$)|(^29[\/]02[\/](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/i.test(value);
}

﻿$(document).ready(function () {
  $.fn.datepicker.defaults.language = "es";
  $.fn.dataTable.ext.errMode = "none";

  setTimeout(function () {
    $("#secCargandoPrincipal").fadeOut("fast", function () {
      $(".wrapper").show();
    });
  }, 100);
});

//Fechas y horarios
var meses = {1: "Enero", 2: "Febrero", 3: "Marzo", 4: "Abril", 5: "Mayo", 6: "Junio", 7: "Julio", 8: "Agosto", 9: "Setiembre", 10: "Octubre", 11: "Noviembre", 12: "Diciembre"};
function establecerCalendario(idElemento, incluirHora, soloFechasPasadas, soloFechasFuturas, funcionCierre, soloMeses, soloAnhos) {
  /*$("#" + idElemento).keydown(function () {
   return false;
   });*/

  if (soloFechasPasadas) {
    var fechaFin = new Date();
    fechaFin.setFullYear(fechaFin.getFullYear() - 1);
  }
  if (soloFechasFuturas) {
    var fechaIni = new Date();
    fechaIni.setDate(fechaIni.getDate() + 1);
  }
  if (incluirHora) {
    $("#" + idElemento).datetimepicker({
      format: "dd/mm/yyyy hh:ii:ss",
      startDate: (soloFechasFuturas ? fechaIni : ""),
      endDate: (soloFechasPasadas ? fechaFin : "")
    });
    if (funcionCierre) {
      $("#" + idElemento).datetimepicker().on("changeDate", funcionCierre);
      $("#" + idElemento).keyup(function () {
        $(this).datetimepicker().trigger("changeDate");
      });
    }
    if (soloFechasPasadas) {
      $("#" + idElemento).datetimepicker("setDate", (new Date(1990, 0, 1)));
      $("#" + idElemento).datetimepicker("update");
      $("#" + idElemento).val("");
    }
  } else {
    $("#" + idElemento).datepicker({
      format: (soloMeses ? "mm/yyyy" : (soloAnhos ? "yyyy" : "dd/mm/yyyy")),
      minViewMode: (soloMeses ? 1 : (soloAnhos ? 2 : 0)),
      maxViewMode: (soloMeses || soloAnhos ? 2 : 4),
      startDate: (soloFechasFuturas ? fechaIni : ""),
      endDate: (soloFechasPasadas ? fechaFin : "")
    });
    if (funcionCierre) {
      $("#" + idElemento).datepicker().on("changeDate", funcionCierre);
    }
    if (soloFechasPasadas) {
      $("#" + idElemento).datepicker("setDate", (new Date(1990, 0, 1)));
      $("#" + idElemento).datepicker("update");
      $("#" + idElemento).val("");
    }
  }

}
function establecerCampoDuracion(idElementoSel, tiempoSegundosDefecto) {
  minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
  maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);

  if (minHorasClase !== "" && maxHorasClase !== "") {
    establecerCampoHoras(idElementoSel, minHorasClase, maxHorasClase, 0.5, tiempoSegundosDefecto);
  }
}
function establecerCampoHorario(idElementoSel, tiempoSegundosDefecto) {
  minHorario = (typeof (minHorario) === "undefined" ? "" : minHorario);
  maxHorario = (typeof (maxHorario) === "undefined" ? "" : maxHorario);

  if (minHorario !== "" && maxHorario !== "") {
    establecerCampoHoras(idElementoSel, minHorario, maxHorario, 0.5, tiempoSegundosDefecto);
  }
}
function establecerCampoHoras(idElementoSel, min, max, intervalo, tiempoSegundosDefecto) {
  $("#" + idElementoSel).html("");
  var valorDefecto = (parseFloat(min) * 3600);
  var cont = parseFloat(min);

  while (cont <= parseInt(max) && cont <= 24) {
    $("#" + idElementoSel).append('<option value="' + (cont * 3600) + '">' + formatoHora(cont * 3600) + '</option>');
    if (tiempoSegundosDefecto && (parseFloat(tiempoSegundosDefecto) === cont * 3600)) {
      valorDefecto = (cont * 3600);
    }
    cont += intervalo;
  }
  $("#" + idElementoSel).val(valorDefecto);
}
function formatoFecha(fecha, contiempo, soloTiempo) {
  if (!isNaN(Date.parse(fecha))) {
    var fechaSel = new Date(fecha);
    var horas = fechaSel.getHours();
    var minutos = fechaSel.getMinutes();
    var segundos = fechaSel.getSeconds();
    return (soloTiempo ? (((horas < 10 ? "0" : "") + horas) + ":" + ((minutos < 10 ? "0" : "") + minutos)) : $.datepicker.formatDate("dd/mm/yy", fechaSel) + (contiempo ? " " + ((horas < 10 ? "0" : "") + horas) + ":" + ((minutos < 10 ? "0" : "") + minutos) + ":" + ((segundos < 10 ? "0" : "") + segundos) : ""));
  }
  return "";
}
function formatoHora(tiempoSegundos, incluirSegundos) {
  tiempoSegundos = Number(tiempoSegundos);
  var h = Math.floor(tiempoSegundos / 3600);
  var m = Math.floor(tiempoSegundos % 3600 / 60);
  var s = Math.floor(tiempoSegundos % 3600 % 60);
  return ((h > 0 ? h + ":" + (m < 10 ? "0" : "") : "") + m + (incluirSegundos ? ":" + (s < 10 ? "0" : "") + s : ""));

}
function tiempoSegundos(fecha) {
  if (!isNaN(Date.parse(fecha))) {
    var fechaSel = new Date(fecha);
    var horas = fechaSel.getHours();
    var minutos = fechaSel.getMinutes();
    var segundos = fechaSel.getSeconds();
    return horas * 3600 + minutos * 60 + segundos;
  }
  return 0;
}

//Grillas
$.extend(true, $.fn.dataTable.defaults, {
  "oLanguage": {
    "sUrl": urlBase + "/assets/plugins/datatables/languages/Spanish.json"
  }
});
function establecerCambiosEstados(urlActualizarEstadoDis, estadosDis) {
  $(window).click(function (e) {
    if (!$(e.target).closest(".sec-btn-editar-estado").length) {
      $(".sec-btn-editar-estado select").trigger("change");
    }
  });
  $(".btn-editar-estado").live("click", function () {
    $("#sel-estados").clone().val($(this).data("estado")).data("id", $(this).data("id")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado"));
    $(this).remove();
    event.stopPropagation();
  });
  $(".sec-btn-editar-estado select").live("change", function () {
    var id = $(this).data("id");
    if (urlActualizarEstadoDis !== "" && $(this).data("estado") !== $(this).val()) {
      llamadaAjax(urlActualizarEstadoDis.replace("/0", "/" + id), "POST", {"estado": $(this).val()}, true);
    }
    $(this).closest(".sec-btn-editar-estado").append('<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + id + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosDis[$(this).val()][1] + ' btn-estado">' + estadosDis[$(this).val()][0] + '</span></a>');
    $(this).remove();
  });
}
function eliminarElemento(ele, mensajePrevio, idTabla, noRecargarTabla, funcionCompletado) {
  mensajePrevio = (mensajePrevio !== undefined && mensajePrevio !== null && mensajePrevio.trim() !== "" ? mensajePrevio : "¿Está seguro que desea eliminar este elemento?");
  if (confirm(mensajePrevio)) {
    llamadaAjax($(ele).data("urleliminar"), "DELETE", {}, true,
        function (data) {
          if (idTabla !== undefined && idTabla !== null) {
            var eleEli = $("#" + idTabla).find("a[data-id='" + data["id"] + "']").closest("tr");
            if (eleEli !== undefined) {
              $("#" + idTabla).DataTable().row((eleEli.index() + 1)).remove().draw();
            }
            eleEli.remove();
          }
          agregarMensaje("exitosos", data["mensaje"], true);
        },
        function (data) {
          if (idTabla !== undefined && idTabla !== null && !noRecargarTabla) {
            $("#" + idTabla).DataTable().draw(false);
          }
          if (funcionCompletado !== undefined) {
            funcionCompletado(data);
          }
        },
        function (dataError) {
          agregarMensaje("errores", dataError["responseJSON"]["mensaje"], true);
        }
    );
  }
}

//Util
function letraCapital(texto) {
  return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
}
function pad(num, size) {
  var s = num + "";
  while (s.length < size)
    s = "0" + s;
  return s;
}
function redondear(numero, numDecimales) {
  numVal = parseFloat(numero + "");
  if (isNaN(numVal))
    return 0;
  return numVal.toFixed(numDecimales);
}
function procesarDatosFormulario(f) {
  var datos = {};
  var fDatos = $(f).serializeArray();
  $(fDatos).each(function (i, o) {
    if ($(f).find("[name='" + o.name + "']:eq(0)")[0].type === "checkbox") {
      datos[o.name] = ($(f).find("[name='" + o.name + "']:eq(0)").is(":checked") ? "on" : "");
    } else {
      datos[o.name] = o.value;
    }
  });
  return datos;
}