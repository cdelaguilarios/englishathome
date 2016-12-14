window.addEventListener("load", verificarJqueryClase, false);
function verificarJqueryClase() {
    ((window.jQuery && jQuery.ui) ? cargarSeccionClases() : window.setTimeout(verificarJqueryClase, 100));
}
function  cargarSeccionClases() {
    cargarListaClase();
    cargarFormularioPago();
    mostrarSeccionClase();
}

//Lista
function cargarListaClase() {
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
    urlPerfilAlumnoClase = (typeof (urlPerfilAlumnoClase) === "undefined" ? "" : urlPerfilAlumnoClase);
    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);

    if (urlListarClases !== "" && urlPerfilAlumnoClase !== "" && estadosClase !== "") {
        $("#tab-lista-clases").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": urlListarClases,
                "type": "POST",
                "data": function (d) {
                    d._token = $('meta[name=_token]').attr("content");
                }
            },
            autoWidth: false,
            order: [[0, "desc"]],
            columns: [
                {data: "id", name: "id", orderable: false, searchable: false},
                {data: "idAlumno", name: "idAlumno"},
                {data: "fechaInicio", name: "fechaInicio"},
                {data: "duracion", name: "duracion"},
                {data: "costoHoraProfesor", name: "costoHoraProfesor"},
                {data: "estado", name: "estado"}
            ],
            "createdRow": function (r, d, i) {
                //Id                            
                $("td", r).eq(0).html('<input type="checkbox" data-id="' + d.id + '" data-idalumno="' + d.idAlumno + '"/>');

                //Alumno                         
                $("td", r).eq(1).html('<a target="_blank" href="' + urlPerfilAlumnoClase.replace("/0", "/" + d.idAlumno) + '">' + d.nombreAlumno + ' ' + d.apellidoAlumno + '</a>');

                //Fecha                     
                $("td", r).eq(2).html(formatoFecha(d.fechaInicio) + ' - De ' + formatoFecha(d.fechaInicio, false, true) + ' a ' + formatoFecha(d.fechaFin, false, true));

                //Duración                
                $("td", r).eq(3).html(formatoHora(d.duracion));

                //Costo por hora profesor                
                $("td", r).eq(4).html("S/. " + redondear(d.costoHoraProfesor, 2));

                //Estado
                $("td", r).eq(5).html('<span class="label ' + estadosClase[d.estado][1] + ' btn_estado">' + estadosClase[d.estado][0] + '</span>');

            }
        });
    }
    $("#tab-lista-clases").find("input[type='checkbox']").live("change", function () {
        mostrarSeccionClase(($("#tab-lista-clases").find("input[type='checkbox']:checked").length > 0) ? [1, 1] : [1]);
    });
}

//Formulario pago
function cargarFormularioPago() {
    $("#formulario-pago-clase").validate({
        ignore: ":hidden",
        rules: {
            monto: {
                required: true,
                validarDecimal: true
            },
            imagenDocumentoVerificacion: {
                required: true,
                validarImagen: true
            },
            imagenComprobante: {
                validarImagen: true
            }
        },
        submitHandler: function (form) {
            var datosClases = "";
            $.each($("#tab-lista-clases").find("input[type='checkbox']:checked"), function (e, v) {
                datosClases += $(v).data("idalumno") + "-" + $(v).data("id") + ",";
            });
            $("input[name='datosClases']").val(datosClases);            
            if (confirm("¿Está seguro que desea registrar los datos de este pago?")) {
                form.submit();
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
        }
    });
    $("#btn-nuevo-pago-clase").click(function () {
        limpiarCamposPagoClase();
        mostrarSeccionClase([2]);
    });
    $("#btn-cancelar-pago-clase").click(function () {
        mostrarSeccionClase([1, 1]);
    });
}
//Util
function mostrarSeccionClase(numSecciones) {
    if (!numSecciones) {
        numSecciones = [1];
    }
    $('[id*="sec-clase-"]').hide();
    var auxSec = "";
    for (var i = 0; i < numSecciones.length; i++) {
        $("#sec-clase-" + auxSec + "" + numSecciones[i]).show();
        auxSec += "" + numSecciones[i];
    }
}
function limpiarCamposPagoClase() {
    $("#formulario-pago-clase input, #formulario-pago-clase select").each(function (i, e) {
        if (e.name !== "_token" && e.type !== "hidden") {
            if ($(e).is("select")) {
                $(e).prop("selectedIndex", 0);
            } else {
                e.value = "";
            }
        }
    });

} 