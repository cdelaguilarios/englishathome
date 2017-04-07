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
  jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "fecha-pre": function (f) {
      var dateTimeParts = f.split(' ');
      var timeParts = dateTimeParts[1].split(':');
      var dateParts = dateTimeParts[0].split('/');
      var fechaSel = new Date(dateParts[2], parseInt(dateParts[1], 10) - 1, dateParts[0], timeParts[0], timeParts[1], timeParts[2]);
      return fechaSel.getTime();
    },
    "fecha-asc": function (a, b) {
      return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "fecha-desc": function (a, b) {
      return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
  });
  jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "monto-pre": function (m) {
      return parseFloat(m.replace("S/. ", "")
          .replace(/<br\/><small><b>Saldo a favor de .*<\/b><\/small>/, '')
          .replace(/<br\/><small><b>Saldo a favor de .* (<span class="saldo-favor-utilizado">utilizado<\/span>)<\/b><\/small>/, ''));
    },
    "monto-asc": function (a, b) {
      return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "monto-desc": function (a, b) {
      return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
  });

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
function establecerBotonRecargaTabla(idTabla) {
  $("#" + idTabla + "_length").append('<a href="javascript:void(0)" onclick="recargarDatosTabla(\'' + idTabla + '\')" title="Recargar datos..." style="margin-left: 10px;"><i class="fa fa-refresh"></i></a>');
}
function recargarDatosTabla(idTabla) {
  $("#" + idTabla).DataTable().ajax.reload();
}
function establecerCambiosBusquedaEstados(idTabla, urlActualizarEstadoDis, estadosDis) {
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
      llamadaAjax(urlActualizarEstadoDis.replace("/0", "/" + id), "POST", {"estado": $(this).val()}, true, undefined, undefined, function (de) {
        var rj = de.responseJSON;
        if (rj !== undefined && rj.mensaje !== undefined) {
          agregarMensaje("errores", rj.mensaje, true);
        } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
          agregarMensaje("errores", rj[Object.keys(rj)[0]][0], true);
        }
        $("#" + idTabla).DataTable().ajax.reload();
      });
    }
    $(this).closest(".sec-btn-editar-estado").append('<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + id + '" data-estado="' + $(this).val() + '"><span class="label ' + estadosDis[$(this).val()][1] + ' btn-estado">' + estadosDis[$(this).val()][0] + '</span></a>');
    $(this).remove();
  });
  $("#bus-estado").change(function () {
    $("#" + idTabla).DataTable().ajax.reload();
  });
}
function eliminarElemento(ele, mensajePrevio, idTabla, noRecargarTabla, funcionCompletado) {
  mensajePrevio = (mensajePrevio !== undefined && mensajePrevio !== null && mensajePrevio.trim() !== "" ? mensajePrevio : "¿Está seguro que desea eliminar este elemento?");
  if (confirm(mensajePrevio)) {
    llamadaAjax($(ele).data("urleliminar"), "DELETE", {}, true,
        function (d) {
          if (idTabla !== undefined && idTabla !== null) {
            var eleEli = $("#" + idTabla).find("a[data-id='" + d["id"] + "']").closest("tr");
            eleEli.remove();
          }
          agregarMensaje("exitosos", d["mensaje"], true);
        },
        function (data) {
          if (idTabla !== undefined && idTabla !== null && !noRecargarTabla) {
            $("#" + idTabla).DataTable().ajax.reload();
          }
          if (funcionCompletado !== undefined) {
            funcionCompletado(data);
          }
        },
        function (de) {
          agregarMensaje("errores", de["responseJSON"]["mensaje"], true);
        }
    );
  }
}

//Clases
function obtenerDatosClase(idAlumno, idClase, funcionRetorno) {
  urlDatosClase = (typeof (urlDatosClase) === "undefined" ? "" : urlDatosClase);
  if (urlDatosClase !== "") {
    $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
    llamadaAjax(urlDatosClase.replace(encodeURI("/[ID_ALUMNO]"), "/" + idAlumno).replace("/0", "/" + idClase), "POST", {}, true,
        function (d) {
          if (funcionRetorno !== undefined)
            funcionRetorno(d);
          $("body").unblock();
        },
        function (d) {},
        function (de) {
          $('body').unblock({
            onUnblock: function () {
              agregarMensaje("errores", "Ocurrió un problema durante la carga de datos de la clase seleccionada. Por favor inténtelo nuevamente.", true);
            }
          });
        }
    );
  }
}
function verDatosClase(idAlumno, idClase) {
  estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  if (estadosClase !== "" && urlPerfilProfesor !== "") {
    obtenerDatosClase(idAlumno, idClase, function (d) {
      $("#dat-numero-periodo-clase").text(d.numeroPeriodo);
      $("#dat-estado-clase").html('<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>');

      $("#sec-dat-notificar-clase").hide();
      if (d.idHistorial !== null) {
        $("#sec-dat-notificar-clase").show();
        $("#dat-notificar-clase").html('<i class="fa fa-check-circle-o icon-notificar-clase"></i>');
      }
      $("#dat-fecha-clase").html(formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true));
      $("#dat-costo-hora-clase").html('S/. ' + redondear(d.costoHora, 2));
      $("#dat-codigo-pago-clase").html(d.idPago);
      $("#sec-dat-profesor-clase").hide();
      if (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '') {
        $("#sec-dat-profesor-clase").show();
        $("#dat-profesor-clase").html('<i class="fa flaticon-teach"></i> <b>' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + d.idProfesor)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');
        $("#dat-pago-hora-profesor-clase").html('S/. ' + redondear(d.costoHoraProfesor, 2));
      }
      $("#mod-datos-clase").modal("show");
    });
  }
}

//Wizard
var mapa, google;
function establecerWizard(idEntidad, modoEditar) {
  $("#wiz-registro-" + idEntidad).wizard();
  $("#wiz-registro-" + idEntidad).on("actionclicked.fu.wizard", function (e, d) {
    var campos = $("#formulario-" + idEntidad).find("#sec-wiz-" + idEntidad + "-" + d.step).find(":input, select");
    if (d.direction === "next" && !campos.valid()) {
      e.preventDefault();
    }
  }).on("changed.fu.wizard", function (e, d) {
    if (google !== undefined && mapa !== undefined) {
      google.maps.event.trigger(mapa, "resize");
      verificarPosicionSel();
    }
  }).on("finished.fu.wizard", function (e, d) {
    $("#formulario-" + idEntidad).submit();
  });

  if (modoEditar) {
    habilitarTodosPasosWizard(idEntidad);
    $("#wiz-registro-" + idEntidad).find('.steps-container').find('li').click(function (e) {
      var pasoActual = $("#wiz-registro-" + idEntidad).find('.steps-container').find('li.active').data("step");
      var campos = $("#formulario-" + idEntidad).find("#sec-wiz-" + idEntidad + "-" + pasoActual).find(":input, select");
      if (!campos.valid()) {
        e.preventDefault();
        return false;
      }
    });
    $("#wiz-registro-" + idEntidad).on("changed.fu.wizard", function (e, d) {
      habilitarTodosPasosWizard(idEntidad);
    });
  }
}
function habilitarTodosPasosWizard(idEntidad) {
  var pasos = $("#wiz-registro-" + idEntidad).find('.steps-container').find('li');
  $.each(pasos, function (i, v) {
    if (!pasos.eq(i).hasClass('active')) {
      pasos.eq(i).addClass('complete');
    }
  });
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
function incluirSeccionSubidaArchivos(idElemento, datosAdicionales, funcionSubirCompletado, funcionEliminarCompletado) {
  urlRegistrarArchivo = (typeof (urlRegistrarArchivo) === "undefined" ? "" : urlRegistrarArchivo);
  urlEliminarArchivo = (typeof (urlEliminarArchivo) === "undefined" ? "" : urlEliminarArchivo);
  if (urlRegistrarArchivo !== "" && urlEliminarArchivo !== "") {
    $("#" + idElemento).uploadFile($.extend(true, {}, {
      url: urlRegistrarArchivo,
      fileName: "archivo",
      multiple: true,
      dragDrop: true,
      formData: {"_token": $('meta[name=_token]').attr("content"), "idElemento": idElemento},
      showPreview: true,
      showDelete: true,
      onSuccess: function (f, d, xhr, pd)
      {
        var nombresArchivosSubidos = $("#nombres-archivos-" + d.idElemento).val();
        var nombresOriginalesArchivosSubidos = $("#nombres-originales-archivos-" + d.idElemento).val();
        $("#nombres-archivos-" + d.idElemento).val(nombresArchivosSubidos + d.nombre + ",");
        $("#nombres-originales-archivos-" + d.idElemento).val(nombresOriginalesArchivosSubidos + d.nombreOriginal + ",");
        if (funcionSubirCompletado !== undefined)
          funcionSubirCompletado(f, d, xhr, pd);
      },
      deleteCallback: function (d, p) {
        llamadaAjax(urlEliminarArchivo, "DELETE", {"nombre": d.nombre}, true);
        var nombresArchivosSubidos = $("#nombres-archivos-" + d.idElemento).val();
        var nombresOriginalesArchivosSubidos = $("#nombres-originales-archivos-" + d.idElemento).val();
        $("#nombres-archivos-" + d.idElemento).val(nombresArchivosSubidos.replace(d.nombre + ",", ""));
        $("#nombres-originales-archivos-" + d.idElemento).val(nombresOriginalesArchivosSubidos.replace(d.nombreOriginal + ",", ""));
        if (funcionEliminarCompletado !== undefined)
          funcionEliminarCompletado(d, p);
        p.statusbar.hide();
      },
      showFileCounter: true,
      dragDropStr: "<span><b>Arrastrar y soltar</b></span>",
      previewHeight: "50px",
      previewWidth: "50px",
      abortStr: "Cancelar",
      cancelStr: "Cancelar",
      deletelStr: "Eliminar",
      doneStr: "Realizado",
      downloadStr: "Descargar",
      maxFileCountErrorStr: " no se agregó. Máximo numero de archivos permitidos: ",
      multiDragErrorStr: "Arrastrar y soltar los archivos no esta permitido",
      extErrorStr: "Extensión permitidas: ",
      sizeErrorStr: "Tamaño máximo: "
    }, datosAdicionales));
  }
}