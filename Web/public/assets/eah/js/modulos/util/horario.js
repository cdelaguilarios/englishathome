var horario = {};
horario = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarHorario() : window.setTimeout(esperarCargaJquery, 100));
  }

  var horarioSel = [], horarioFin = [], horarioInicial = new Object();
  var diasNumeros = [1, 2, 3, 4, 5, 6, 7];
  var diasNombres = ["Lu.", "Ma.", "Mi.", "Ju.", "Vi.", "Sá.", "Do."];
  var minutosIntervalo = 30;
  function cargarHorario()/* - */ {
    minHorario = (typeof (minHorario) === "undefined" ? "" : minHorario);
    maxHorario = (typeof (maxHorario) === "undefined" ? "" : maxHorario);
    if (!(minHorario !== "" && maxHorario !== "")) {
      return;
    }

    if ($("input[name='horario']").length > 0 && $("input[name='horario']").val() !== "") {
      horarioFin = $.parseJSON($("input[name='horario']").val());
      mostrarTexto();
    }
    if ($("#sec-calendario-horario").length > 0) {
      inicializarHorario();
      
      var datIni = {
        days: diasNumeros,
        startTime: utilFechasHorarios.formatoHora(parseInt(minHorario) * 3600),
        endTime: utilFechasHorarios.formatoHora(parseInt(maxHorario) * 3600),
        interval: minutosIntervalo
      };
      
      formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);
      if (!formularioExternoPostulante) {
        datIni.stringDays = diasNombres;
      }
      
      $("#sec-calendario-horario").dayScheduleSelector(datIni);
      $("#sec-calendario-horario").data("artsy.dayScheduleSelector").deserialize(horarioInicial);
      $("#sec-calendario-horario").on("selected.artsy.dayScheduleSelector", function (e, selected) {
        for (var i = 0; i < selected.length; i++) {
          var dia = $(selected[i]).data("day");
          horarioSel[dia] = (horarioSel[dia] !== undefined ? horarioSel[dia] : []);
          horarioSel[dia].push($(selected[i]).data("time"));
        }
      });
      $("#sec-calendario-horario").on("deselected.artsy.dayScheduleSelector", function (e, selected, dia) {
        horarioSel[dia] = [];
        for (var i = 0; i < selected.length; i++) {
          if (util.rgb2hex($('.time-slot[data-time="' + $(selected[i]).data("time") + '"][data-day="' + dia + '"]').css("background-color")) !== "#fff") {
            horarioSel[dia].push($(selected[i]).data("time"));
          }
        }
      });
            
      $("#btn-horario").click(function () {
        $("#mod-horario").modal("show");
      });
      $("#btn-instrucciones-horario").click(function () {
        $("#sec-horario").hide();
        $("#sec-instrucciones-horario").show();
      });
      $("#btn-regresar-horario").click(function () {
        $("#sec-horario").show();
        $("#sec-instrucciones-horario").hide();
      });
      $("#btn-confirmar-horario").click(function () {
        simplificarHorario();
        mostrarTexto();
        $("input[name='horario']").val(JSON.stringify(horarioFin)).trigger('change');
        $("#mod-horario").modal("hide");
      });
      $("#btn-limpiar-seleccion").click(function () {
        horarioSel = [];
        $("#sec-calendario-horario").find(".time-slot").removeAttr("data-selected");
      });
    }
  }
  function inicializarHorario()/* - */ {
    var datosHorarioInicial = new Object();
    var datosHorarioInicialSel = [];

    for (var i = 0; i < horarioFin.length; i++) {
      var dias = horarioFin[i].dias.split(",");
      for (var j = 0; j < dias.length; j++) {
        datosHorarioInicial["" + dias[j]] = (datosHorarioInicial["" + dias[j]] !== undefined ? datosHorarioInicial["" + dias[j]] : []);
        datosHorarioInicialSel["" + dias[j]] = (datosHorarioInicialSel["" + dias[j]] !== undefined ? datosHorarioInicialSel["" + dias[j]] : []);

        for (var k = 0; k < horarioFin[i].horas.length; k++) {
          var horas = horarioFin[i].horas[k].split("-");
          datosHorarioInicial[dias[j]].push([horas[0], horas[1]]);

          var auxFecha1 = new Date(2000, 0, 1, horas[0].split(":")[0], horas[0].split(":")[1]);
          var auxFecha2 = new Date(2000, 0, 1, horas[1].split(":")[0], horas[1].split(":")[1]);
          var auxCant = (auxFecha2.getTime() - auxFecha1.getTime()) / (60000 * 1 * minutosIntervalo);
          datosHorarioInicialSel[dias[j]].push(horas[0]);
          for (var l = 0; l < auxCant - 1; l++) {
            auxFecha1 = new Date(auxFecha1.getTime() + 1 * minutosIntervalo * 60000);
            var horasTiempoSig = auxFecha1.getHours();
            var minutosTiempoSig = auxFecha1.getMinutes();
            datosHorarioInicialSel[dias[j]].push(((horasTiempoSig < 10 ? "0" : "") + horasTiempoSig) + ":" + ((minutosTiempoSig < 10 ? "0" : "") + minutosTiempoSig));
          }
        }
      }
    }
    horarioSel = datosHorarioInicialSel;
    horarioInicial = datosHorarioInicial;
  }  
  
  function mostrarTexto()/* - */ {
    var textoHorario = obtenerTexto(horarioFin);
    $("#sec-info-horario").html(textoHorario);
  }
  function obtenerTexto(horario)/* - */ {
    var textoHorario = '<ul>';
    for (var i = 0; i < horario.length; i++) {
      var dias = horario[i].dias.split(","), horas = horario[i].horas;
      textoHorario += '<li><i class="fa fa-fw fa-calendar-check-o"></i> <b>';
      for (var j = 0; j < dias.length; j++){
        textoHorario += (j > 0 ? (j === (dias.length - 1) ? (" y ") : " , ") : "") + diasNombres[dias[j] - 1];
      }
      textoHorario += '</b>';
      for (var k = 0; k < horas.length; k++){
        textoHorario += (k > 0 ? (k === (horas.length - 1) ? (" y ") : " , ") : "    ") + horas[k];
      }
      textoHorario += '</li>';
    }
    textoHorario += '</ul>';
    return textoHorario;
  }
  
  function simplificarHorario()/* - */ {
    var horarioSim = [];
    horarioFin = [];
    $.each(horarioSel, function (i, v) {
      if (v === undefined)
        return true;
      var tiempoIni = "", tiempoAnt = "", tiempos = [];
      v.sort();
      for (var j = 0; j < v.length; j++) {
        var tiempoAct = new Date(2000, 0, 1, v[j].split(":")[0], v[j].split(":")[1]);
        var tiempoSig = new Date(tiempoAct.getTime() + 1 * minutosIntervalo * 60000);
        if (tiempoAnt === "") {
          tiempoIni = tiempoAct;
        } else if (tiempoAct.getTime() !== tiempoAnt.getTime()) {
          tiempos.push(hhmm(tiempoIni) + "-" + hhmm(tiempoAnt));
          tiempoIni = tiempoAct;
        }
        ((j === (v.length - 1)) ? (tiempos.push(hhmm(tiempoIni) + "-" + hhmm(tiempoSig))) : (tiempoAnt = tiempoSig));
      }
      horarioSim[i] = (horarioSim[i] !== undefined ? horarioSim[i] : []);
      horarioSim[i].push(tiempos);
    });
    $.each(horarioSim, function (i, v) {
      if (v === undefined)
        return true;
      for (var j = 0; j < v[0].length; j++) {
        var horarioEnc = $.grep(horarioFin, function (n, i) {
          for (var k = 0; k < n.horas.length; k++)
            if (n.horas[k] === v[0][j])
              return true;
          return false;
        });
        if (horarioEnc.length === 0)
        {
          var diasSel = [i];
          $.merge(diasSel, buscarDiasMismoHorario(horarioSim, i, v[0][j]));
          var iDiasEnc = "";
          $.each(horarioFin, function (iH, vH) {
            if (vH.dias === diasSel.join(",")) {
              iDiasEnc = iH;
              return false;
            }
          });
          ((iDiasEnc !== "") ? horarioFin[iDiasEnc].horas.push(v[0][j]) : horarioFin.push({"dias": diasSel.join(","), "horas": [v[0][j]]}));
        }
      }
    });
  }
  function buscarDiasMismoHorario(horarioSim, numeroDia, horario)/* - */ {
    var diasHorarioIgual = [];
    $.each(horarioSim, function (i, v) {
      if (v !== undefined && i !== numeroDia) {
        for (var j = 0; j < v[0].length; j++) {
          if (v[0][j] === horario) {
            diasHorarioIgual.push(i);
            break;
          }
        }
      }
    });
    return diasHorarioIgual;
  }
  function hhmm(fecha)/* - */ {
    var horas = fecha.getHours(), minutos = fecha.getMinutes();
    return ("0" + horas).slice(-2) + ":" + ("0" + minutos).slice(-2);
  }


  return {
    obtenerTexto: obtenerTexto
  };
}());