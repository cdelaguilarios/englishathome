var util = {};
util = (function () {
  $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    setTimeout(function () {
      $("#secCargandoPrincipal").fadeOut("fast", function () {
        $(".wrapper").show();
      });
    }, 100);
  });
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
  var jQueryCargado = function () {
    return (window.jQuery !== undefined && typeof (jQuery) !== "undefined");
  };
  function cargarUrl(url) {
    var win = window.open(url, '_blank');
    win.focus();
  }
  function obtenerParametroUrlXNombre(nombre, url) {
    if (!url)
      url = window.location.href;
    nombre = nombre.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + nombre + "(=([^&#]*)|&|#|$)"),
            resultados = regex.exec(url);
    if (!resultados)
      return null;
    if (!resultados[2])
      return '';
    return decodeURIComponent(resultados[2].replace(/\+/g, " "));
  }


  function letraCapital(texto) {
    if (!texto)
      return "";
    return texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase();
  }
  function redondear(numero, numDecimales) {
    var numVal = parseFloat(numero + "");
    if (isNaN(numVal))
      return 0;
    return numVal.toFixed(numDecimales);
  }
  function pad(num, size) {
    var s = num + "";
    while (s.length < size)
      s = "0" + s;
    return s;
  }
  function rgb2hex(rgb) {
    if (/^#[0-9A-F]{6}$/i.test(rgb)) {
      return rgb;
    }

    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (rgb === null) {
      return "#fff";
    }
    function hex(x) {
      return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
  }
  function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
  }
  function urlEsImagen(url) {
    return(url.match(/\.(jpeg|jpg|gif|png)$/) !== null);
  }
  function incluirEnlaceWhatsApp(numero) {
    var regexp = new RegExp(/^[0-9]+$/);
    var numeroFinal = (numero !== undefined && numero !== null ? numero.trim().replaceAll(" ", "").replace("+", "") : "");

    if (numeroFinal !== "" && numeroFinal.length >= 9 && regexp.test(numeroFinal)) {
      numeroFinal = (numeroFinal.length !== 9 ? numeroFinal : "51" + numeroFinal);
      return '<a href="https://wa.me/' + numeroFinal + '" target="_blank">' + numero + '</a>';
    }
    return numero;
  }

  var _cls_ = {};
  function obtenerClaseHtmlXNombre(nombreCls) {
    if (!_cls_[nombreCls]) {
      if (nombreCls.match(/^[a-zA-Z0-9_]+$/)) {
        _cls_[nombreCls] = eval(nombreCls);
      } else {
        throw new Error("ERROR");
      }
    }
    return _cls_[nombreCls];
  }

  return {
    llamadaAjax: llamadaAjax,
    jQueryCargado: jQueryCargado,
    cargarUrl: cargarUrl,
    obtenerParametroUrlXNombre: obtenerParametroUrlXNombre,
    letraCapital: letraCapital,
    redondear: redondear,
    pad: pad,
    rgb2hex: rgb2hex,
    escapeRegExp: escapeRegExp,
    urlEsImagen: urlEsImagen,
    incluirEnlaceWhatsApp: incluirEnlaceWhatsApp,
    obtenerClaseHtmlXNombre: obtenerClaseHtmlXNombre
  };
}());

var utilFechasHorarios = {};
utilFechasHorarios = (function () {
  formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);
  ﻿$(document).ready(function () {
    if (!formularioExternoPostulante) {
      $.fn.datepicker.defaults.language = "es";
    }
  });

  function establecerCampoHoras(elemento, min, max, intervalo, tiempoSegundosDefecto) {
    $(elemento).html("");
    var valorDefecto = (parseFloat(min) * 3600);
    var cont = parseFloat(min);

    while (cont <= parseFloat(max) && cont <= 24) {
      $(elemento).append('<option value="' + (cont * 3600) + '">' + utilFechasHorarios.formatoHora(cont * 3600) + '</option>');
      if (tiempoSegundosDefecto && (parseFloat(tiempoSegundosDefecto) === cont * 3600)) {
        valorDefecto = (cont * 3600);
      }
      cont += intervalo;
    }
    $(elemento).val(valorDefecto);
  }

  function obtenerMeses() {
    return {1: "Enero", 2: "Febrero", 3: "Marzo", 4: "Abril", 5: "Mayo", 6: "Junio", 7: "Julio", 8: "Agosto", 9: "Setiembre", 10: "Octubre", 11: "Noviembre", 12: "Diciembre"};
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
    return ((h >= 0 ? ((h < 10 ? "0" : "") + h) + ":" + (m < 10 ? "0" : "") : "") + m + (incluirSegundos ? ":" + (s < 10 ? "0" : "") + s : ""));
  }
  function establecerCalendario(elemento, incluirHora, soloFechasPasadas, soloFechasFuturas, funcionCierre, soloMeses, soloAnhos) {
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
      $(elemento).datetimepicker({
        format: "dd/mm/yyyy hh:ii:ss",
        startDate: (soloFechasFuturas ? fechaIni : ""),
        endDate: (soloFechasPasadas ? fechaFin : ""),
        language: 'es'
      });
      if (funcionCierre) {
        $(elemento).datetimepicker().on("changeDate", funcionCierre);
        $(elemento).keyup(function () {
          $(this).datetimepicker().trigger("changeDate");
        });
      }
      if (soloFechasPasadas) {
        $(elemento).datetimepicker("setDate", (new Date(1990, 0, 1)));
        $(elemento).datetimepicker("update");
        $(elemento).val("");
      }
      if (!soloFechasPasadas && !soloFechasFuturas) {
        $(elemento).datetimepicker("setDate", (new Date()));
      }
    } else {
      $(elemento).datepicker({
        format: (soloMeses ? "mm/yyyy" : (soloAnhos ? "yyyy" : "dd/mm/yyyy")),
        minViewMode: (soloMeses ? 1 : (soloAnhos ? 2 : 0)),
        maxViewMode: (soloMeses || soloAnhos ? 2 : 4),
        startDate: (soloFechasFuturas ? fechaIni : ""),
        endDate: (soloFechasPasadas ? fechaFin : "")
      });
      if (funcionCierre) {
        $(elemento).datepicker().on("changeDate", funcionCierre);
      }
      if (soloFechasPasadas) {
        $(elemento).datepicker("setDate", (new Date(1990, 0, 1)));
        $(elemento).datepicker("update");
        $(elemento).val("");
      }
    }
  }
  function establecerCampoDuracion(elemento, tiempoSegundosDefecto) {
    minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
    maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);

    if (minHorasClase !== "" && maxHorasClase !== "") {
      establecerCampoHoras(elemento, minHorasClase, maxHorasClase, 0.5, tiempoSegundosDefecto);
    }
  }
  function establecerCampoHorario(elemento, tiempoSegundosDefecto) {
    minHorario = (typeof (minHorario) === "undefined" ? "" : minHorario);
    maxHorario = (typeof (maxHorario) === "undefined" ? "" : maxHorario);

    if (minHorario !== "" && maxHorario !== "") {
      establecerCampoHoras(elemento, minHorario, maxHorario, 0.5, tiempoSegundosDefecto);
    }
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

  return {
    obtenerMeses: obtenerMeses,
    formatoFecha: formatoFecha,
    formatoHora: formatoHora,
    establecerCalendario: establecerCalendario,
    establecerCampoDuracion: establecerCampoDuracion,
    establecerCampoHorario: establecerCampoHorario,
    tiempoSegundos: tiempoSegundos
  };
}());

var utilTablas = {};
utilTablas = (function () {
  $.extend(true, $.fn.dataTable.defaults, {
    "oLanguage": {
      "sUrl": urlBase + "/assets/plugins/datatables/languages/Spanish.json"
    },
    "pageLength": 50
  });

  ﻿$(document).ready(function () {
    $.fn.dataTable.ext.errMode = "none";
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
      "fecha-pre": function (f) {
        var dateTimeParts = f.split(' ');
        var dateParts = dateTimeParts[0].split('/');
        var fechaSel = new Date();

        if (dateTimeParts.length === 2) {
          var timeParts = dateTimeParts[1].split(':');
          fechaSel = new Date(dateParts[2], parseInt(dateParts[1], 10) - 1, dateParts[0], timeParts[0], timeParts[1], timeParts[2]);
        } else {
          fechaSel = new Date(dateParts[2], parseInt(dateParts[1], 10) - 1, dateParts[0]);
        }
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
        return parseFloat(m.replace("S/. ", ""));
      },
      "monto-asc": function (a, b) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
      },
      "monto-desc": function (a, b) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
      }
    });

    establecerCambioEstados();
    $("#bus-estado").change(function () {
      var idTabla = $(this).data("idtabla");
      $("#" + idTabla).DataTable().ajax.reload();
    });
  });
  function establecerCambioEstados() {
    $(window).click(function (e) {
      if (!$(e.target).closest(".sec-btn-editar-estado").length)
        $(".sec-btn-editar-estado select").trigger("change");
    });
    $(".btn-editar-estado").live("click", function () {
      var tipoCambioDirecto = $(this).closest(".sec-btn-editar-estado").data("tipocambio");
      var idSelEstados = $(this).closest(".sec-btn-editar-estado").data("idselestados");

      if (tipoCambioDirecto === 1) {
        var sel = $("#" + idSelEstados).clone();
        $(sel).val($(this).data("estado")).data("id", $(this).data("id")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado"));
        $(this).remove();
        $(sel).prop('selectedIndex', (($(sel)[0].length !== ($(sel)[0].selectedIndex + 1)) ? ($(sel)[0].selectedIndex + 1) : 0)).trigger("change");
        event.stopPropagation();
      } else {
        $("#" + idSelEstados).clone().val($(this).data("estado")).data("id", $(this).data("id")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado"));
        $(this).remove();
        event.stopPropagation();
      }
    });
    $(".sec-btn-editar-estado select").live("change", function () {
      var idTabla = $(this).closest(".sec-btn-editar-estado").data("idtabla");
      var idSelEstados = $(this).closest(".sec-btn-editar-estado").data("idselestados");
      var funcionDatosAdicionales = $(this).closest(".sec-btn-editar-estado").data("funciondatosadicionales");
      var urlActualizarEstado = $("#" + idSelEstados).data("urlactualizar");
      var estados = $("#" + idSelEstados).data("estados");
      var id = $(this).data("id");

      if (urlActualizarEstado !== "" && $(this).data("estado") !== $(this).val()) {
        var datos = {"estado": $(this).val()};
        if (funcionDatosAdicionales !== undefined && funcionDatosAdicionales !== null && funcionDatosAdicionales !== "") {
          var datosAdicionales = {};

          var datFuncionDatosAdicionales = funcionDatosAdicionales.split(".");
          if (datFuncionDatosAdicionales === 1) {
            datosAdicionales = window[funcionDatosAdicionales]();
          } else {
            var clase = util.obtenerClaseHtmlXNombre(datFuncionDatosAdicionales[0]);
            datosAdicionales = clase[datFuncionDatosAdicionales[1]]();
          }

          $.extend(datos, datosAdicionales);
        }

        util.llamadaAjax(urlActualizarEstado.replace("/0", "/" + id), "POST", datos, true, undefined, undefined, function (de) {
          var rj = de.responseJSON;
          if (rj !== undefined && rj.mensaje !== undefined) {
            mensajes.agregar("errores", rj.mensaje, true);
          } else if (rj !== undefined && rj[Object.keys(rj)[0]] !== undefined) {
            mensajes.agregar("errores", rj[Object.keys(rj)[0]][0], true);
          }

          if (idTabla !== undefined) {
            $("#" + idTabla).DataTable().ajax.reload();
          }
        });
      }
      $(this).closest(".sec-btn-editar-estado").append(
              '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + id + '" data-estado="' + $(this).val() + '">' +
              '<span class="label ' + estados[$(this).val()][1] + ' btn-estado">' + estados[$(this).val()][0] + '</span>' +
              '</a>');
      $(this).remove();
    });
  }

  function iniciarTabla(tabla, datos, incluirBotonExcel, indicesColumnasExcel) {
    if (incluirBotonExcel && indicesColumnasExcel && indicesColumnasExcel.length) {
      var datosAdicionales = {
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" + "<'row'<'col-sm-6'i><'col-sm-6 text-right'B>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        lengthMenu: [
          [10, 25, 50, 100, -1],
          ['10', '25', '50', '100', 'todos']
        ],
        pageLength: 50,
        buttons: [
          {
            extend: 'excelHtml5',
            text: 'Exportar a excel (solo registros visibles)',
            className: 'btn btn-primary btn-clean btn-xs',
            exportOptions: {
              columns: indicesColumnasExcel,
              modifier: {
                search: 'applied'
              }
            },
            autoFilter: true,
            customize: function (xlsx) {
              var xmlEstilos = xlsx.xl['styles.xml'];
              var ultimoXfInd = $('cellXfs xf', xmlEstilos).length - 1;

              var estiloFilas = '<xf borderId="1" />';
              var estiloCabecera1 = '<xf numFmtId="0" fontId="2" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1" xfId="0" applyAlignment="1">' +
                      '<alignment horizontal="center"/></xf>';
              var estiloCabecera2 = '<xf numFmtId="0" fontId="2" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1" xfId="0" applyAlignment="1">' +
                      '<alignment horizontal="center" wrapText="1"/></xf>';
              xmlEstilos.childNodes[0].childNodes[5].innerHTML += estiloFilas + estiloCabecera1 + estiloCabecera2;

              var hojaExcel = xlsx.xl.worksheets['sheet1.xml'];
              $('row c', hojaExcel).attr('s', ultimoXfInd + 1);//Aplicando estilos comunes a todas las filas
              $('row:eq(0) c', hojaExcel).attr('s', ultimoXfInd + 2);//Aplicando estilos a la primera cabecera
              $('row:eq(1) c', hojaExcel).attr('s', ultimoXfInd + 3);//Aplicando estilos a la segunda cabecera
            }
          }
        ]
      };
      datos = $.extend({}, datos, datosAdicionales);
    }

    return $(tabla).on('preXhr.dt', function (e, s, d) {
      var idTabla = $(tabla).attr("id");
      $("#" + idTabla + "_wrapper").find('.buttons-excel').prop('disabled', true);
    }).on('xhr.dt', function (e, s, json, xhr) {
      var idTabla = $(tabla).attr("id");
      $("#" + idTabla + "_wrapper").find('.buttons-excel').prop('disabled', false);
    }).DataTable(datos);
  }
  function establecerBotonRecargaTabla(tabla) {
    var idTabla = $(tabla).attr("id");
    $("#" + idTabla + "_length").append('<a href="javascript:void(0)" onclick="utilTablas.recargarDatosTabla($(\'#' + idTabla + '\'))" title="Recargar datos..." style="margin-left: 10px;"><i class="fa fa-refresh"></i></a>');
  }
  function recargarDatosTabla(tabla) {
    var idTabla = $(tabla).attr("id");
    if ($.fn.DataTable.isDataTable("#" + idTabla)) {
      $(tabla).DataTable().ajax.reload();
    }
  }
  function establecerCabecerasBusquedaTabla(tabla) {
    var idTabla = $(tabla).attr("id");
    $("#" + idTabla + " thead tr").clone(true).appendTo("#" + idTabla + " thead").addClass("tabla-sec-busqueda");
    $("#" + idTabla + " thead tr:eq(1) th").each(function (numEle) {
      var permiteBus = $(tabla).DataTable().settings().init().columns[numEle].searchable;
      var clasesCss = $(tabla).DataTable().settings().init().columns[numEle].className;

console.log($(this).text().toLowerCase() + " - " + clasesCss);
      if (clasesCss && clasesCss.includes("never")) {
        $(this).remove();
      } else {
        if (permiteBus || permiteBus === undefined) {
          $(this).html('<input type="text" placeholder="Buscar por ' + $(this).text().toLowerCase() + '" />');
          $("input", this).on("click", function (e) {
            e.preventDefault();
            return false;
          });
          $("input", this).on("keyup change", function () {
            if ($(tabla).DataTable().column(numEle).search() !== this.value)
              $(tabla).DataTable().column(numEle).search(this.value).draw();
          });
        } else {
          $(this).html("");
        }
      }
    });
  }
  function eliminarElemento(elemento, mensajePrevio, idTabla, noRecargarTabla, funcionCompletado, excluirMensajeConfirmacion) {
    mensajePrevio = (mensajePrevio !== undefined && mensajePrevio !== null && mensajePrevio.trim() !== "" ? mensajePrevio : "¿Está seguro que desea eliminar este elemento?");
    if (confirm(mensajePrevio)) {
      util.llamadaAjax($(elemento).data("urleliminar"), "DELETE", {}, true,
              function (d) {
                if (idTabla !== undefined && idTabla !== null) {
                  var eleEli = $("#" + idTabla).find("a[data-id='" + d["id"] + "']").closest("tr");
                  eleEli.remove();
                }
                if (!excluirMensajeConfirmacion) {
                  mensajes.agregar("exitosos", d["mensaje"], true);
                }
              },
              function (d) {
                if (idTabla !== undefined && idTabla !== null && !noRecargarTabla)
                  $("#" + idTabla).DataTable().ajax.reload();
                if (funcionCompletado)
                  funcionCompletado(d);
              },
              function (de) {
                mensajes.agregar("errores", de["responseJSON"]["mensaje"], true);
              }
      );
    }
  }

  return {
    iniciarTabla: iniciarTabla,
    establecerBotonRecargaTabla: establecerBotonRecargaTabla,
    recargarDatosTabla: recargarDatosTabla,
    establecerCabecerasBusquedaTabla: establecerCabecerasBusquedaTabla,
    eliminarElemento: eliminarElemento
  };
}());

var utilFormularios = {};
utilFormularios = (function () {
  formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);

  $.validator.addMethod("validarAlfabetico", validarAlfabetico, (formularioExternoPostulante ? "Please enter only letters." : "Por favor solo ingrese solo letras."));
  function validarAlfabetico(value, element, param) {
    return this.optional(element) || /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/i.test(value);
  }

  $.validator.addMethod("validarAlfanumerico", validarAlfanumerico, (formularioExternoPostulante ? "Please enter only alphanumeric values." : "Por favor solo ingrese valores alfanuméricos."));
  function validarAlfanumerico(value, element, param) {
    return this.optional(element) || /^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/i.test(value);
  }

  $.validator.addMethod("validarEntero", validarEntero, "Ingreso no válido.");
  function validarEntero(value, element, param) {
    if (("" + value).trim() === "")
      return true;
    return (/^\d+$/.test(("" + value)));
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

  $.validator.addMethod('archivoTamanho', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param);
  });
  $.validator.addMethod("validarImagen", validarImagen, (formularioExternoPostulante ? "Invalid image (valid formats: jpg, jpeg, png and gif)" : "Por favor seleccione una imagen válida (formatos válidos: jpg, jpeg, png y gif)."));
  function validarImagen(value, element, param) {
    var extension = value.split(".").pop().toLowerCase();
    return (value.trim() === "" || extension.trim() === "jpg" || extension.trim() === "jpeg" || extension.trim() === "png" || extension.trim() === "gif");
  }

  $.validator.addMethod("validarAudio", validarAudio, (formularioExternoPostulante ? "Invalid audio (valid formats: mp3, wav and ogg)" : "Por favor seleccione un audio válido (formatos válidos: mp3, wav y ogg)."));
  function validarAudio(value, element, param) {
    var extension = value.split(".").pop().toLowerCase();
    return (value.trim() === "" || extension.trim() === "mp3" || extension.trim() === "wav" || extension.trim() === "ogg");
  }

  $.validator.addMethod("validarFecha", validarFecha, (formularioExternoPostulante ? "Please enter a valid date (valid format: dd/mm/yyyy)." : "Por favor ingrese una fecha válida (formato válido: dd/mm/aaaa)"));
  function validarFecha(value, element, param) {
    return this.optional(element) || /(^(((0[1-9]|1[0-9]|2[0-8])[\/](0[1-9]|1[012]))|((29|30|31)[\/](0[13578]|1[02]))|((29|30)[\/](0[4,6,9]|11)))[\/](19|[2-9][0-9])\d\d$)|(^29[\/]02[\/](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/i.test(value);
  }

  $.validator.addMethod("validarFechaHora", validarFechaHora, (formularioExternoPostulante ? "Please enter a valid date (valid format: dd/mm/yyyy HH:mm:ss)." : "Por favor ingrese una fecha válida (formato válido: dd/mm/aaaa HH:mm:ss)"));
  function validarFechaHora(value, element, param) {
    if (value.trim() === "")
      return true;

    var validacionFecha = this.optional(element) || /(^(((0[1-9]|1[0-9]|2[0-8])[\/](0[1-9]|1[012]))|((29|30|31)[\/](0[13578]|1[02]))|((29|30)[\/](0[4,6,9]|11)))[\/](19|[2-9][0-9])\d\d$)|(^29[\/]02[\/](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/i.test(value.split(" ")[0]);
    var validacionHora = false;
    if (value.split(" ").length === 2)
      validacionHora = this.optional(element) || /^([01]?[0-9]|2[0-3])(:[0-5][0-9]){2}$/.test(value.split(" ")[1]);
    return validacionFecha && validacionHora;
  }

  $.validator.addMethod("validarCkEditor", validarCkEditor, "Este campo es obligatorio.");
  function validarCkEditor(v, e, p) {
    CKEDITOR.instances[$(e).attr("id")].updateElement();
    if ($(e).val().trim() !== "") {
      return true;
    } else {
      $(window).scrollTop($("#cke_" + $(e).attr("id")).offset().top);
      return false;
    }
  }

  ﻿$(document).ready(function () {
    CKEDITOR.config.removePlugins = 'save,newpage,print';
  });

  //Wizard
  function establecerWizard(tipoEntidad, modoEditar) {
    $("#wiz-registro-" + tipoEntidad).wizard();
    $("#wiz-registro-" + tipoEntidad).on("actionclicked.fu.wizard", function (e, d) {
      var campos = $("#formulario-" + tipoEntidad).find("#sec-wiz-" + tipoEntidad + "-" + d.step).find(":input, select");
      $("#formulario-" + tipoEntidad).find("#btn-guardar-secundario").show();
      if (d.direction === "next" && campos.length > 0 && !campos.valid()) {
        e.preventDefault();
      } else if (d.direction === "next" && $("#formulario-" + tipoEntidad).find(".step-pane:last").attr("id").replace("sec-wiz-" + tipoEntidad + "-", "") === ((parseInt(d.step) + 1) + "")) {
        $("#formulario-" + tipoEntidad).find("#btn-guardar-secundario").hide();
      }
    }).on("changed.fu.wizard", function (e, d) {
      var mapa = ubicacionMapa.obtenerMapa();
      if (google !== undefined && mapa !== undefined) {
        google.maps.event.trigger(mapa, "resize");
        ubicacionMapa.verificarPosicionSel();
      }
    }).on("finished.fu.wizard", function (e, d) {
      $("#formulario-" + tipoEntidad).submit();
    });

    if (modoEditar) {
      habilitarTodosPasosWizard(tipoEntidad);
      $("#wiz-registro-" + tipoEntidad).find('.steps-container').find('li').click(function (e) {
        var pasoActual = $("#wiz-registro-" + tipoEntidad).find('.steps-container').find('li.active').data("step");
        var campos = $("#formulario-" + tipoEntidad).find("#sec-wiz-" + tipoEntidad + "-" + pasoActual).find(":input, select");
        $("#formulario-" + tipoEntidad).find("#btn-guardar-secundario").show();
        if (!campos.valid()) {
          e.preventDefault();
          return false;
        } else if ($("#formulario-" + tipoEntidad).find(".step-pane:last").attr("id").replace("sec-wiz-" + tipoEntidad + "-", "") === ($(this).data("step") + "")) {
          $("#formulario-" + tipoEntidad).find("#btn-guardar-secundario").hide();
        }
      });
      $("#wiz-registro-" + tipoEntidad).on("changed.fu.wizard", function (e, d) {
        habilitarTodosPasosWizard(tipoEntidad);
      });
      setInterval(function () {
        if ($("#formulario-" + tipoEntidad).find(".step-pane:last").attr("id").replace("sec-wiz-" + tipoEntidad + "-", "") === ($("#wiz-registro-" + tipoEntidad).find('.steps-container').find('li.active').data("step") + "")) {
          $("#formulario-" + tipoEntidad).find("#btn-guardar-secundario").hide();
        }
      }, 100);
    }
  }
  function habilitarTodosPasosWizard(tipoEntidad) {
    var pasos = $("#wiz-registro-" + tipoEntidad).find('.steps-container').find('li');
    $.each(pasos, function (i, v) {
      if (!pasos.eq(i).hasClass('active')) {
        pasos.eq(i).addClass('complete');
      }
    });
  }

  function procesarDatos(f) {
    var datos = {};
    var formularioDatos = $(f).serializeArray();
    $(formularioDatos).each(function (i, o) {
      if ($(f).find("[name='" + o.name + "']:eq(0)")[0].type === "checkbox") {
        datos[o.name] = ($(f).find("[name='" + o.name + "']:eq(0)").is(":checked") ? "on" : "");
      } else {
        datos[o.name] = o.value;
      }
    });
    return datos;
  }

  function incluirSeccionSubidaArchivos(elemento, datosAdicionales, funcionSubirCompletado, funcionEliminarCompletado) {
    var idElemento = $(elemento).attr("id");
    urlRegistrarArchivo = (typeof (urlRegistrarArchivo) === "undefined" ? "" : urlRegistrarArchivo);
    urlEliminarArchivo = (typeof (urlEliminarArchivo) === "undefined" ? "" : urlEliminarArchivo);
    maxTamanioArchivoSubida = (typeof (maxTamanioArchivoSubida) === "undefined" ? "" : maxTamanioArchivoSubida);
    formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);

    if (urlRegistrarArchivo !== "" && urlEliminarArchivo !== "" && maxTamanioArchivoSubida !== "") {
      var datIni = {
        url: urlRegistrarArchivo,
        fileName: "archivo",
        multiple: true,
        dragDrop: true,
        formData: {"_token": $('meta[name=_token]').attr("content"), "idElemento": idElemento},
        showPreview: true,
        showDelete: true,
        maxFileSize: maxTamanioArchivoSubida,
        onSuccess: function (f, d, xhr, pd)
        {
          var nombresArchivosSubidos = $("#nombres-archivos-" + d.idElemento).val();
          var nombresOriginalesArchivosSubidos = $("#nombres-originales-archivos-" + d.idElemento).val();
          $("#nombres-archivos-" + d.idElemento).val(nombresArchivosSubidos + d.nombre + ",");
          $("#nombres-originales-archivos-" + d.idElemento).val(nombresOriginalesArchivosSubidos + d.nombreOriginal + ",");
          if (funcionSubirCompletado !== undefined) {
            funcionSubirCompletado(f, d, xhr, pd);
          }
        },
        deleteCallback: function (d, p) {
          util.llamadaAjax(urlEliminarArchivo, "DELETE", {"nombre": d.nombre}, true);
          var nombresArchivosSubidos = $("#nombres-archivos-" + d.idElemento).val();
          if (nombresArchivosSubidos !== undefined && nombresArchivosSubidos !== null) {
            $("#nombres-archivos-" + d.idElemento).val(nombresArchivosSubidos.replace(d.nombre + ",", ""));
          }

          var nombresOriginalesArchivosSubidos = $("#nombres-originales-archivos-" + d.idElemento).val();
          if (nombresOriginalesArchivosSubidos !== undefined && nombresOriginalesArchivosSubidos !== null) {
            $("#nombres-originales-archivos-" + d.idElemento).val(nombresOriginalesArchivosSubidos.replace(d.nombreOriginal + ",", ""));
          }

          if (funcionEliminarCompletado !== undefined) {
            funcionEliminarCompletado(d, p);
          }
          p.statusbar.hide();
        },
        showFileCounter: true
      };
      if (!formularioExternoPostulante) {
        datIni.dragDropStr = "<span><b>Arrastrar y soltar</b></span>";
        datIni.previewHeight = "50px";
        datIni.previewWidth = "50px";
        datIni.abortStr = "Cancelar";
        datIni.cancelStr = "Cancelar";
        datIni.deletelStr = "Eliminar";
        datIni.doneStr = "Realizado";
        datIni.downloadStr = "Descargar";
        datIni.maxFileCountErrorStr = " no se agregó. Máximo numero de archivos permitidos: ";
        datIni.multiDragErrorStr = "Arrastrar y soltar los archivos no esta permitido";
        datIni.extErrorStr = "Extensión permitidas: ";
        datIni.sizeErrorStr = "Tamaño máximo: ";
      }
      return $(elemento).uploadFile($.extend(true, {}, datIni, datosAdicionales));
    }
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
    $("form .help-block-error").remove();
    return false;
  }

  return {
    establecerWizard: establecerWizard,
    procesarDatos: procesarDatos,
    incluirSeccionSubidaArchivos: incluirSeccionSubidaArchivos,
    limpiarCampos: limpiarCampos
  };
}());

var utilBusqueda = {};
utilBusqueda = (function () {
  function establecerListaBusqueda(elemento, urlBuscar) {
    $(elemento).select2({
      minimumInputLength: 2,
      tags: false,
      ajax: {
        url: urlBuscar,
        dataType: 'json',
        type: "GET",
        quietMillis: 50,
        data: function (term) {
          return {
            termino: term
          };
        },
        results: function (data) {
          return {
            results: data
          };
        }
      }
    });
  }

  return {
    establecerListaBusqueda: establecerListaBusqueda
  };
}());

var utilAlumno = {};
utilAlumno = (function () {
  function mostrarOcultarCodigoVerificacionClases(elemento) {
    if ($(elemento).html().indexOf('<i class="fa fa-eye"></i>') >= 0) {
      $("input[name='codigoVerificacionClases']").val($("input[name='auxCodigoVerificacionClases']").val().trim());
      $("input[name='auxCodigoVerificacionClases']").val("");
      $(elemento).html('<i class="fa fa-eye-slash"></i>');
    } else {
      $("input[name='auxCodigoVerificacionClases']").val($("input[name='codigoVerificacionClases']").val().trim());
      $("input[name='codigoVerificacionClases']").val("");
      $(elemento).html('<i class="fa fa-eye"></i>');
    }
  }

  return {
    mostrarOcultarCodigoVerificacionClases: mostrarOcultarCodigoVerificacionClases
  };
}());

var utilClase = {};
utilClase = (function () {
  function verDatos(idAlumno, idClase) {
    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);
    urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
    if (estadosClase !== "" && urlPerfilProfesor !== "") {
      obtenerDatosClase(idAlumno, idClase, function (d) {
        $("#dat-numero-periodo-clase").text(d.numeroPeriodo);
        $("#dat-estado-clase").html('<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>');
        $("#dat-fecha-clase").html(utilFechasHorarios.formatoFecha(d.fechaInicio) + ' - De ' + utilFechasHorarios.formatoFecha(d.fechaInicio, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaFin, false, true));
        $("#dat-alumno-clase").html('<i class="fa fa-mortar-board"></i> <b>' + d.nombreAlumno + ' ' + d.apellidoAlumno + '</b> <a href=' + (urlPerfilAlumno.replace('/0', '/' + d.idAlumno)) + ' title="Ver perfil del alumno" target="_blank"><i class="fa fa-eye"></i></a>');
        $("#dat-costo-hora-clase").html('S/. ' + util.redondear(d.costoPromedioXHoraClase, 2));
        $("#dat-codigo-pago-clase").html(d.idPago);
        $("#sec-dat-profesor-clase").hide();
        if (d.idProfesor !== null && d.nombreProfesor !== null && d.nombreProfesor !== '') {
          $("#sec-dat-profesor-clase").show();
          $("#dat-profesor-clase").html('<i class="fa flaticon-teach"></i> <b>' + d.nombreProfesor + ' ' + d.apellidoProfesor + '</b> <a href=' + (urlPerfilProfesor.replace('/0', '/' + d.idProfesor)) + ' title="Ver perfil del profesor" target="_blank"><i class="fa fa-eye"></i></a>');
          $("#dat-pago-hora-profesor-clase").html('S/. ' + util.redondear(d.pagoPromedioXHoraProfesor, 2));
        }
        $("#mod-datos-clase").modal("show");
      });
    }
  }
  
  function obtenerDatosClase(idAlumno, idClase, funcionRetorno) {
    urlDatosClase = (typeof (urlDatosClase) === "undefined" ? "" : urlDatosClase);
    if (urlDatosClase !== "") {
      $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
      util.llamadaAjax(urlDatosClase.replace(encodeURI("/[ID_ALUMNO]"), "/" + idAlumno).replace("/0", "/" + idClase), "POST", {}, true,
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

  return {
    verDatos: verDatos
  };
}());

//Util
String.prototype.reemplazarDatosTexto = function (datos, valores) {
  var ele = this;
  for (var i = 0; i < datos.length; i++) {
    ele = ele.toLowerCase().split(datos[i]).join(valores[i]);
  }
  return ele;
};
String.prototype.replaceAll = function (search, replacement) {
  var target = this;
  return target.replace(new RegExp(util.escapeRegExp(search), 'g'), replacement);
};
window.mobilecheck = function () {
  var check = false;
  (function (a) {
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))
      check = true;
  })(navigator.userAgent || navigator.vendor || window.opera);
  return check;
};