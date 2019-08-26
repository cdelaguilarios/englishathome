window.addEventListener("load", verificarJqueryCalculadoraInversion, false);
function verificarJqueryCalculadoraInversion() {
  ((window.jQuery && jQuery.ui) ? cargarCalculadoraInversion() : window.setTimeout(verificarJqueryCalculadoraInversion, 100));
}

function cargarCalculadoraInversion() {
  $(document).on('keyup change', '#inversion-numero-cuotas', function () {
    agregarCamposCalculoInversionCuotas();
  });
  $(document).on('keyup change', '[id*="inversion-numero-horas-cta-"], [id*="inversion-materiales-cta-"]', function () {
    recalcularInversionCuotas();
  });
}
function agregarCamposCalculoInversionCuotas(establecerNumeroCuotasIniciales) {
  var auxNroCuotas = $('<div/>').html(CKEDITOR.instances["inversion-cuotas"].getData()).find("table").length;
  if (establecerNumeroCuotasIniciales && auxNroCuotas > 0)
    $("#inversion-numero-cuotas").val(auxNroCuotas + 1);

  var datosInversion = $('<div/>').html(CKEDITOR.instances["inversion"].getData()).find("tr:eq(1)");
  if ($(datosInversion).find("td").length === 3) {
    var inversionNroCuotas = parseInt($("#inversion-numero-cuotas").val());
    inversionNroCuotas = ((isNaN(inversionNroCuotas) || inversionNroCuotas === 1) ? 2 : inversionNroCuotas);
    $("#inversion-numero-cuotas").val(inversionNroCuotas);

    var nroHoras = parseFloat($(datosInversion).find("td:eq(0)").text().reemplazarDatosTexto(["s/", ".", ","], ["", "", ""]));
    var materiales = parseFloat($(datosInversion).find("td:eq(1)").text().reemplazarDatosTexto(["s/", ".", ","], ["", "", ""]));
    var inversion = parseFloat($(datosInversion).find("td:eq(2)").text().reemplazarDatosTexto(["s/", ".", ","], ["", "", ""])) - materiales;
    var moneda = ($(datosInversion).find("td:eq(1)").text().toLowerCase().includes("s/.") ? "s/." : ($(datosInversion).find("td:eq(1)").text().toLowerCase().includes("$") ? "$" : ""));

    if (!isNaN(nroHoras) && !isNaN(materiales) && !isNaN(inversion)) {
      $("#sec-inversion-cuotas-calculo").html('<input id="inversion-moneda" type="hidden" value="' + moneda + '" />');
      for (var i = 2; i <= inversionNroCuotas; i++) {
        var htmlCuota =
            '<div class="col-sm-10 col-sm-offset-2">' +
            '<div class="box">' +
            '<div class="box-header">' +
            '<h3 class="box-title">Inversión en ' + i + ' cuota' + (i > 1 ? 's' : '') + '</h3>' +
            '</div>' +
            '<div class="box-body no-padding">' +
            '<table class="table table-condensed">' +
            '<tr>' +
            '<th class="text-center"><b>Nro. de horas</b></th>' +
            '<th class="text-center"><b>Materiales ' + (moneda !== '' ? '(' + moneda + ')' : '') + '</b></th>' +
            '<th class="text-center"><b>Inversión total ' + (moneda !== '' ? '(' + moneda + ')' : '') + '</b></th>' +
            '</tr>';
        var nroHorasCta = parseFloat(util.redondear((nroHoras / i), 2));
        var inversionCta = parseFloat(util.redondear((inversion / i), 2));
        var inversionHora = (inversion / nroHoras);
        var auxTotalNroHoras = 0, auxTotalInversion = 0;

        for (var j = 1; j <= i; j++) {
          var th = parseInt((auxTotalNroHoras + nroHorasCta >= nroHoras) || (j === i) ? (nroHoras - auxTotalNroHoras) : nroHorasCta);
          var tm = (j === 1 ? util.redondear(materiales, 2) : '');
          htmlCuota += '<tr>' +
              '<td class="text-center"><input id="inversion-numero-horas-cta-' + i + '-' + j + '" type="number" value="' + th + '" style="width: 40px; margin: 0 4px;" /></td>' +
              '<td class="text-center"><input id="inversion-materiales-cta-' + i + '-' + j + '" type="text" value="' + tm + '" /></td>' +
              '<td class="text-center"><label id="inversion-total-cta-' + i + '-' + j + '"></label></td>' +
              '</tr>';
          auxTotalNroHoras += nroHorasCta;
          auxTotalInversion += inversionCta;
        }
        htmlCuota += '<tr>' +
            '<td colspan="2"></td>' +
            '<td class="text-center">' +
            '<label id="inversion-total-' + i + '"></label>' +
            '<input id="inversion-hora-' + i + '" type="hidden" value="' + inversionHora + '" />' +
            '</td></tr></table></div></div></div>';
        $("#sec-inversion-cuotas-calculo").append(htmlCuota);
      }
      recalcularInversionCuotas();
    }
  }
}
function recalcularInversionCuotas() {
  var inversionNroCuotas = parseInt($("#inversion-numero-cuotas").val());
  inversionNroCuotas = ((isNaN(inversionNroCuotas) || inversionNroCuotas === 1) ? 2 : inversionNroCuotas);
  $("#inversion-numero-cuotas").val(inversionNroCuotas);

  var moneda = $("#inversion-moneda").val();
  var htmlInversionCuota = '';
  for (var i = 2; i <= inversionNroCuotas; i++) {
    htmlInversionCuota += '<p><span><strong>En ' + i + ' cuota' + (i > 1 ? 's' : '') + '</strong></span></p>' +
        '<table>' +
        '<tbody>' +
        '<tr class="fila-cabecera">' +
        '<td>Nro De horas</td>' +
        '<td>Materiales</td>' +
        '<td>Inversión Total</td>' +
        '</tr>';

    var ti = 0;
    for (var j = 1; j <= i; j++) {
      var thc = parseFloat($("#inversion-numero-horas-cta-" + i + "-" + j).val());
      var tmc = parseFloat($("#inversion-materiales-cta-" + i + "-" + j).val());
      var ihc = parseFloat($("#inversion-hora-" + i).val());
      thc = (isNaN(thc) ? 0 : thc);
      tmc = (isNaN(tmc) ? 0 : tmc);
      ihc = (isNaN(ihc) ? 0 : ihc);

      var tic = (thc * ihc) + tmc;
      ti += tic;
      $("#inversion-total-cta-" + i + "-" + j).text(util.redondear(tic, 2));
      htmlInversionCuota += '<tr class="' + (j % 2 === 0 ? 'fila-par' : 'fila-impar') + '">' +
          '<td>' + thc + '</td>' +
          '<td>' + (tmc === 0 ? '&nbsp;' : (moneda + tmc)) + '</td>' +
          '<td>' + (moneda + util.redondear(tic, 2)) + '</td>' +
          '</tr>';
    }
    htmlInversionCuota += '<tr class="fila-total">' +
        '<td colspan="2">TOTAL</td>' +
        '<td>' + (moneda + util.redondear(ti, 2)) + '</td>' +
        '</tr>' +
        '</tbody>' +
        '</table>';
    $("#inversion-total-" + i).text("Total " + util.redondear(ti, 2));
  }
  CKEDITOR.instances["inversion-cuotas"].setData(htmlInversionCuota);
}