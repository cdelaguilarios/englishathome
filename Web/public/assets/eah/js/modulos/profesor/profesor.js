var mapa;
var uto = null;

$(document).ready(function () {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
    estados = (typeof (estados) === "undefined" ? "" : estados);

    if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && urlEliminar !== "" && estados !== "") {
        $("#tab-lista").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": urlListar,
                "type": "POST",
                "data": function (d) {
                    d._token = $("meta[name=_token]").attr("content");
                }
            },
            autoWidth: false,
            columns: [
                {data: "nombre", name: "nombre"},
                {data: "correoElectronico", name: "correoElectronico"},
                {data: "estado", name: "estado"},
                {data: "id", name: "id", orderable: false, "searchable": false, width: "10%"}
            ],
            "createdRow": function (row, data, index) {
                //Nombre completo        
                $("td", row).eq(0).html((data.nombre !== null ? data.nombre : "") + " " + (data.apellido !== null ? data.apellido : ""));

                //Estado
                $("td", row).eq(2).html('<span class="label ' + estados[data.estado][1] + ' btn_estado">' + estados[data.estado][0] + '</span>');

                //Botones
                var tBotones = '<ul class="buttons">' +
                        '<li>' +
                        '<a href="' + (urlPerfil.replace("/0", "/" + data.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                        '</li>' +
                        '<li>' +
                        '<a href="' + (urlEditar.replace("/0", "/" + data.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                        '</li>' +
                        '<li>' +
                        '<a href="javascript:void(0);" title="Eliminar profesor" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este profesor?\', \'tab-lista\')" data-id="' + data.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + data.id))) + '">' +
                        '<i class="fa fa-trash"></i>' +
                        '</a>' +
                        '</li>' +
                        '</ul>';
                $("td", row).eq(3).html(tBotones);
            }
        });
    }
    $("#formulario-profesor").validate({
        ignore: "",
        rules: {
            nombre: {
                required: true,
                validarAlfabetico: true
            },
            apellido: {
                required: true,
                validarAlfabetico: true
            },
            idTipoDocumento: {
                required: true
            },
            numeroDocumento: {
                required: true,
                number: true
            },
            telefono: {
                required: true
            },
            fechaNacimiento: {
                required: true
            },
            correoElectronico: {
                required: true,
                email: true
            },
            imagenPerfil: {
                validarImagen: true
            },
            codigoDepartamento: {
                required: true
            },
            codigoProvincia: {
                required: true
            },
            codigoDistrito: {
                required: true
            },
            direccion: {
                required: true
            }
        },
        submitHandler: function (form) {
            if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
                if (confirm($("#btn-guardar").text() === "Guardar"
                        ? "¿Está seguro que desea guardar los cambios de los datos del profesor?"
                        : "¿Está seguro que desea registrar los datos de este profesor?"))
                    form.submit();
            } else {
                agregarMensaje("advertencias", "Debe ingresar un horario disponible", true, "#sec-men-alerta-horario");
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
    if ($("input[name='modoRegistrar']").val() === "1") {
        $("#wiz-registro-profesor").wizard();
        $("#wiz-registro-profesor").on("actionclicked.fu.wizard", function (e, data) {
            var campos = $("#formulario-profesor").find("#sec-wiz-profesor-" + data.step).find(":input, select");
            if (data.direction === "next" && !campos.valid()) {
                e.preventDefault();
            }
        }).on("changed.fu.wizard", function (evt, data) {
            google.maps.event.trigger(mapa, "resize");
            verificarPosicionSel();
        }).on("finished.fu.wizard", function (evt, data) {
            $("#formulario-profesor").submit();
        });
        establecerCalendario("fecha-nacimiento", true, false);

        $("#direccion").focusout(verificarDatosBusquedaMapa);
        $("input[name='codigoUbigeo']").change(verificarDatosBusquedaMapa);
    }
});
function verificarDatosBusquedaMapa() {
    if ($("#direccion").val() !== "" && $("#codigo-distrito option:selected").text() !== "" &&
            $("#codigo-provincia option:selected").text() !== "" && $("#codigo-departamento option:selected").text() !== "") {
        buscarDireccionMapa($("#direccion").val() + " " + $("#codigo-distrito option:selected").text() +
                ", " + $("#codigo-provincia option:selected").text() + ", " + $("#codigo-departamento option:selected").text());
    }
}


