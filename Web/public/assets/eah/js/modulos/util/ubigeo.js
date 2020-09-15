var ubigeo = {};
ubigeo = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }
  function cargarSeccion() {
    cargarUbigeo();
  }

  var primeraCargaProvincias = true;
  var codigoDepartamento = "", codigoProvincia = "", codigoDistrito = "";
  formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);
  function cargarUbigeo() {
    if ($("input[name='codigoUbigeo']").length > 0 && $("input[name='codigoUbigeo']").val().length === 6) {
      codigoDepartamento = $("input[name='codigoUbigeo']").val().slice(0, 2);
      codigoProvincia = $("input[name='codigoUbigeo']").val().slice(0, 4);
      codigoDistrito = $("input[name='codigoUbigeo']").val().slice(0, 6);
    }

    urlListarDepartamentos = (typeof (urlListarDepartamentos) === "undefined" ? "" : urlListarDepartamentos);
    $("#codigo-departamento, #codigo-provincia, #codigo-distrito").html("");
    cargarDatosUbigeo($("#codigo-departamento"), urlListarDepartamentos, {}, (formularioExternoPostulante ? "Select department" : "Seleccione un departamento"), codigoDepartamento, true);

    $("#codigo-departamento").change(cargarProvincias);
    $("#codigo-provincia").change(cargarDistritos);
    $("#codigo-distrito").change(function () {
      $("input[name='codigoUbigeo']").val($(this).val()).trigger("change");
    });

    //Selección departamento de Lima por defecto
    setTimeout(function () {
      if ($("#codigo-departamento").val() === "") {
        $("#codigo-departamento").val(15).trigger("change");
      }
    }, 1000);
  }
  function cargarProvincias() {
    urlListarProvincias = (typeof (urlListarProvincias) === "undefined" ? "" : urlListarProvincias);
    $("#codigo-provincia, #codigo-distrito").html("");
    if ($("#codigo-departamento").val() !== "") {
      cargarDatosUbigeo($("#codigo-provincia"), urlListarProvincias.replace("/0", "/" + $("#codigo-departamento").val()), {}, (formularioExternoPostulante ? "Select province" : "Seleccione una provincia"), codigoProvincia, false, function () {
        if (primeraCargaProvincias) {
          //Selección departamento de Lima por defecto
          if ($("#codigo-provincia").val() === "") {
            $("#codigo-provincia").val(1501).trigger("change");
          }
          primeraCargaProvincias = false;
        }
      });
    }
  }
  function cargarDistritos() {
    urlListarDistritos = (typeof (urlListarDistritos) === "undefined" ? "" : urlListarDistritos);
    $("#codigo-distrito").html("");
    if ($("#codigo-provincia").val() !== "") {
      cargarDatosUbigeo($("#codigo-distrito"), urlListarDistritos.replace("/0", "/" + $("#codigo-provincia").val()), {}, (formularioExternoPostulante ? "Select district" : "Seleccione un distrito"), codigoDistrito);
    }
  }

  function cargarDatosUbigeo(elementoUbigeoLista, urlListarUbigeo, parametros, textoSeleccionDefecto, codigoUbigeoSel, noMostrarMensajeBloqueo, funcionCompletado, numeroIntento) {
    numeroIntento = numeroIntento || 1;
    if (urlListarUbigeo !== "") {
      (!noMostrarMensajeBloqueo ? $.blockUI({message: "<h4>" + (formularioExternoPostulante ? "Loading" : "Cargando") + "...</h4>"}) : "");
      util.llamadaAjax(urlListarUbigeo, "POST", parametros, true,
              function (d) {
                var elementosUbigeo = d["elementosUbigeo"];
                var codigosUbigeo = Object.keys(elementosUbigeo);
                codigosUbigeo.sort();

                var html = "";
                for (var i = 0; i < codigosUbigeo.length; i++) {
                  var codUbigeo = codigosUbigeo[i];
                  var nombreElemento = elementosUbigeo[codUbigeo].replace("DEPARTAMENTO ", "");
                  html += '<option value="' + codUbigeo + '" ' + (codigoUbigeoSel === codUbigeo ? 'selected="selected"' : '') + '>' + nombreElemento + '</option>';
                }

                if (html !== "") {
                  elementoUbigeoLista.html('<option ' + (codigoUbigeoSel !== '' ? '' : 'selected="selected"') + ' value>' + textoSeleccionDefecto + '</option>' + html);
                }

                if (codigoUbigeoSel !== "") {
                  if (elementoUbigeoLista.attr("id") === "codigo-departamento") {
                    cargarProvincias();
                  } else if (elementoUbigeoLista.attr("id") === "codigo-provincia") {
                    cargarDistritos();
                  } else {
                    $("input[name='codigoUbigeo']").trigger("change");
                  }
                }
                if (funcionCompletado !== undefined) {
                  funcionCompletado(d);
                }
              },
              function (d) {
                $("body").unblock();
              },
              function (dataError) {
                if (numeroIntento < 2) {
                  cargarDatosUbigeo(elementoUbigeoLista, urlListarUbigeo, parametros, textoSeleccionDefecto, codigoUbigeoSel, noMostrarMensajeBloqueo, funcionCompletado, numeroIntento + 1);
                } else {
                  mensajes.agregar("errores",
                          ((dataError.responseJSON !== undefined && dataError.responseJSON["mensaje"] !== undefined) ?
                                  dataError["responseJSON"]["mensaje"] :
                                  "Ocurrió un problema durante la carga de datos de ubigeo. Por favor inténtelo nuevamente."), true);
                }
              }
      );
    }
  }
}());



