function llamadaAjax(url, metodo, datos, async, funcionRetorno, funcionCompletado, funcionError) {
    jQuery.ajax({
        url: url,
        method: metodo,
        data: datos,
        async: async,
        headers: {'X-CSRF-Token': $('meta[name=_token]').attr('content')},
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

$.validator.addMethod('validarDecimal', validarDecimal, 'Ingreso no valido.');
function validarDecimal(value, element, param) {
    if (value.trim() === "")
        return true;
    var filter2 = /^[\d]{1,14}(\.[\d]{1,4})?$/;
    return filter2.test(value);
}
$.validator.addMethod('validarPorcentaje', validarPorcentaje, 'Ingreso no valido.');
function validarPorcentaje(value, element, param) {
    if (value.trim() === "")
        return true;
    var filter2 = /^100$|^100.0$|^100.00$|^[0-9]{1,2}$|^[0-9]{1,2}\.[0-9]{1,2}$/;
    return filter2.test(value);
}
$.validator.addMethod('validarEntero', validarEntero, 'Ingreso no válido.');
function validarEntero(value, element, param) {
    if (("" + value).trim() === "")
        return true;
    return (/^\d+$/.test(("" + value)));
}
$.validator.addMethod('validarImagen', validarImagen, 'Por favor seleccione una imagen válida (formatos válidos: jpg, jpeg, png y gif).');
function validarImagen(value, element, param) {
    var extension = value.split('.').pop().toLowerCase();
    return (value.trim() === "" || extension.trim() === "jpg" || extension.trim() === "jpeg" || extension.trim() === "png" || extension.trim() === "gif");
}
$.validator.addMethod('validarAlfanumerico', validarAlfanumerico, 'Por favor solo ingrese valores alfanuméricos.');
function validarAlfanumerico(value, element, param) {
    return this.optional(element) || /^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/i.test(value);
}
$.validator.addMethod('validarAlfabetico', validarAlfabetico, 'Por favor solo ingrese letras.');
function validarAlfabetico(value, element, param) {
    return this.optional(element) || /^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/i.test(value);
}

//CALENDARIO (DATEPICKER)
var meses = {1: "Enero", 2: "Febrero", 3: "Marzo", 4: "Abril", 5: "Mayo", 6: "Junio", 7: "Julio", 8: "Agosto", 9: "Setiembre", 10: "Octubre", 11: "Noviembre", 12: "Diciembre"};
﻿$(document).ready(function () {
    $.fn.datepicker.defaults.language = 'es';
});
function establecerCalendario(idElemento, soloFechasPasadas, soloFechasFuturas, funcionCierre) {
    $("#" + idElemento).keydown(function () {
        return false;
    });

    if (soloFechasPasadas) {
        var fechaFin = new Date();
        fechaFin.setFullYear(fechaFin.getFullYear() - 1);
    }
    if (soloFechasFuturas) {
        var fechaIni = new Date();
        fechaIni.setDate(fechaIni.getDate() + 1);
    }

    $("#" + idElemento).datepicker({
        format: 'dd/mm/yyyy',
        startDate: (soloFechasFuturas ? fechaIni : ""),
        endDate: (soloFechasPasadas ? fechaFin : ""),
        onClose: function (dateText, inst) {
            if (funcionCierre !== undefined)
                funcionCierre($(this));
        }
    });
    if (soloFechasPasadas) {
        $("#" + idElemento).datepicker('setDate', (new Date(1990, 0, 1)));
        $("#" + idElemento).datepicker('update');
        $("#" + idElemento).val('');
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
    var valorDefecto = parseInt(min);
    var cont = parseInt(min);

    while (cont <= parseInt(max) && cont <= 24) {
        $("#" + idElementoSel).append('<option value="' + (cont * 3600) + '">' + formatoHora(cont * 3600) + '</option>');
        if (tiempoSegundosDefecto && (tiempoSegundosDefecto === cont * 3600)) {
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
        return (soloTiempo ? (((horas < 10 ? "0" : "") + horas) + ":" + ((minutos < 10 ? "0" : "") + minutos)) : $.datepicker.formatDate('dd/mm/yy', fechaSel) + (contiempo ? " " + ((horas < 10 ? "0" : "") + horas) + ":" + ((minutos < 10 ? "0" : "") + minutos) + ":" + ((segundos < 10 ? "0" : "") + segundos) : ""));
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

$(document).ready(function () {
    setTimeout(function () {
        $('#secCargandoPrincipal').fadeOut("fast", function () {
            $('.wrapper').fadeIn("fast");
        });
    }, 100);
});
