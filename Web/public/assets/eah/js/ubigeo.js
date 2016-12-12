window.addEventListener("load", verificarJqueryUbigeo, false);
function verificarJqueryUbigeo() {
    ((window.jQuery && jQuery.ui) ? cargarUbigeo() : window.setTimeout(verificarJqueryUbigeo, 100));
}

var codigoDepartamento = "";
var codigoProvincia = "";
var codigoDistrito = "";

function cargarUbigeo() {
    if ($("input[name='codigoUbigeo']").length > 0 && $("input[name='codigoUbigeo']").val().length === 6) {
        codigoDepartamento = $("input[name='codigoUbigeo']").val().slice(0, 2);
        codigoProvincia = $("input[name='codigoUbigeo']").val().slice(0, 4);
        codigoDistrito = $("input[name='codigoUbigeo']").val().slice(0, 6);
    }
    urlListarDepartamentos = (typeof (urlListarDepartamentos) === "undefined" ? "" : urlListarDepartamentos);
    $("#codigo-departamento").html("");
    $("#codigo-provincia").html("");
    $("#codigo-distrito").html("");
    cargarDatosUbigeo($("#codigo-departamento"), urlListarDepartamentos, {}, "Seleccione un departamento", codigoDepartamento, true);

    $("#codigo-departamento").change(cargarProvincias);
    $("#codigo-provincia").change(cargarDistritos);
    $("#codigo-distrito").change(function () {
        $("input[name='codigoUbigeo']").val($(this).val()).trigger('change');
    });
}
function cargarProvincias() {
    urlListarProvincias = (typeof (urlListarProvincias) === "undefined" ? "" : urlListarProvincias);
    $("#codigo-provincia").html("");
    $("#codigo-distrito").html("");
    if ($("#codigo-departamento").val() !== "") {
        cargarDatosUbigeo($("#codigo-provincia"), urlListarProvincias.replace("/0", "/" + $("#codigo-departamento").val()), {}, "Seleccione una provincia", codigoProvincia);
    }
}
function cargarDistritos() {
    urlListarDistritos = (typeof (urlListarDistritos) === "undefined" ? "" : urlListarDistritos);
    $("#codigo-distrito").html("");
    if ($("#codigo-provincia").val() !== "") {
        cargarDatosUbigeo($("#codigo-distrito"), urlListarDistritos.replace("/0", "/" + $("#codigo-provincia").val()), {}, "Seleccione un distrito", codigoDistrito);
    }
}
function cargarDatosUbigeo(eleUbigeoLista, urlListarUbigeo, parametros, textoSeleccionDef, codigoUbigeoSel, noMostrarMensajeBloq) {
    if (urlListarUbigeo !== "") {
        (!noMostrarMensajeBloq ? $.blockUI({message: "<h4>Cargando...</h4>"}) : "");
        llamadaAjax(urlListarUbigeo, "POST", parametros, true,
                function (data) {
                    var elementosUbigeo = data["elementosUbigeo"];
                    var codigosUbigeo = Object.keys(elementosUbigeo);
                    codigosUbigeo.sort();

                    var html = "";
                    for (var i = 0; i < codigosUbigeo.length; i++) {
                        var codUbigeo = codigosUbigeo[i];
                        var nombreElemento = elementosUbigeo[codUbigeo].replace("DEPARTAMENTO ", "");
                        html += '<option value="' + codUbigeo + '" ' + (codigoUbigeoSel === codUbigeo ? 'selected="selected"' : '') + '>' + nombreElemento + '</option>';
                    }


                    if (html !== "") {
                        eleUbigeoLista.html('<option ' + (codigoUbigeoSel !== '' ? '' : 'selected="selected"') + ' value>' + textoSeleccionDef + '</option>' + html);
                    }

                    if (codigoUbigeoSel !== "") {
                        if (eleUbigeoLista.attr("id") === "codigo-departamento") {
                            cargarProvincias();
                        } else if (eleUbigeoLista.attr("id") === "codigo-provincia") {
                            cargarDistritos();
                        } else {
                            $("input[name='codigoUbigeo']").trigger('change');
                        }
                    }
                },
                function (data) {
                    $("body").unblock();
                },
                function (dataError) {
                    agregarMensaje("errores",
                            ((dataError.responseJSON !== undefined && dataError.responseJSON["mensaje"] !== undefined) ?
                                    dataError["responseJSON"]["mensaje"] :
                                    "Ocurrió un problema durante la carga de datos ubigeo. Por favor inténtelo nuevamente."), true);
                }
        );
    }
}