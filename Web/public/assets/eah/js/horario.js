window.addEventListener("load", verificarJqueryHorario, false);
function verificarJqueryHorario() {
    ((window.jQuery && jQuery.ui) ? cargarHorario() : window.setTimeout(verificarJqueryHorario, 100));
}

var horarioSel = [], horarioFin = [];
var diasNum = [1, 2, 3, 4, 5, 6, 7];
var diasLet = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];
var minutosIntervalo = 30;
function cargarHorario() {
    if ($("input[name='horario']").length > 0 && $("input[name='horario']").val() !== "") {
        horarioFin = $.parseJSON($("input[name='horario']").val());
        mostrarTextoHorario();
    }

    $("#calendario").dayScheduleSelector({
        days: diasNum,
        startTime: "08:00",
        endTime: "23:00",
        interval: minutosIntervalo,
        stringDays: diasLet
    });
    $("#calendario").on("selected.artsy.dayScheduleSelector", function (e, selected) {
        for (var i = 0; i < selected.length; i++) {
            var dia = $(selected[i]).data("day");
            horarioSel[dia] = (horarioSel[dia] !== undefined ? horarioSel[dia] : []);
            horarioSel[dia].push($(selected[i]).data("time"));
        }
    });
    $("#btnHorario").click(function () {
        $("#mod-horario").modal("show");
    });
    $("#btn-confirmar-horario").click(function () {
        simplificarHorario();
        mostrarTextoHorario();
        $("input[name='horario']").val(JSON.stringify(horarioFin));
        $("#mod-horario").modal("hide");
    });
    $("#btn-limpiar-seleccion").click(function () {
        horarioSel = [];
        $("#calendario").find(".time-slot").removeAttr("data-selected");
    });
}
function mostrarTextoHorario() {
    var textoHorario = '<ul>';
    for (var i = 0; i < horarioFin.length; i++) {
        var dias = horarioFin[i].dias.split(","), horas = horarioFin[i].horas;
        textoHorario += '<li><i class="fa fa-fw fa-calendar-check-o"></i> <b>';
        for (var j = 0; j < dias.length; j++)
            textoHorario += (j > 0 ? (j === (dias.length - 1) ? (" y ") : " , ") : "") + diasLet[dias[j] - 1];
        textoHorario += '</b>';
        for (var k = 0; k < horas.length; k++)
            textoHorario += (k > 0 ? (k === (horas.length - 1) ? (" y ") : " , ") : "    ") + horas[k];
        textoHorario += '</li>';
    }
    textoHorario += '</ul>';
    $("#sec-info-horario").html(textoHorario);
}
function simplificarHorario() {
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
function buscarDiasMismoHorario(horarioSim, numeroDia, horario) {
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
function hhmm(fecha) {
    var horas = fecha.getHours(), minutos = fecha.getMinutes();
    return ("0" + horas).slice(-2) + ":" + ("0" + minutos).slice(-2);
}