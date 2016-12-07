$(document).ready(function () {
    urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
    estados = (typeof (estados) === "undefined" ? "" : estados);

    if (urlListar !== "" && urlEditar !== "" && urlEliminar !== "" && estados !== "") {
        $('#tab_lista').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": urlListar,
                "type": "POST",
                "data": function (d) {
                    d._token = $('meta[name=_token]').attr("content");
                }
            },
            autoWidth: false,
            columns: [
                {data: 'nombre', name: 'nombre'},
                {data: 'telefono', name: 'telefono'},
                {data: 'correoElectronico', name: 'correoElectronico'},
                {data: 'estado', name: 'estado'},
                {data: 'id', name: 'id', orderable: false, "searchable": false, width: "7%"}
            ],
            "createdRow": function (row, data, index) {
                //Nombre completo        
                $('td', row).eq(0).html((data.nombre !== null ? data.nombre : "") + " " + (data.apellido !== null ? data.apellido : ""));

                //Estado
                $('td', row).eq(3).html('<span class="label ' + estados[data.estado][1] + ' btn_estado">' + estados[data.estado][0] + '</span>');

                //Botones
                var tBotones = "<ul class='buttons'>" +
                        "<li>" +
                        "<a href='" + (urlEditar.replace("/0", "/" + data.id)) + "' title='Editar datos'><i class='fa fa-pencil'></i></a>" +
                        "</li>" +
                        "<li>" +
                        "<a href='javascript:void(0);' title='Eliminar interesado' onclick='eliminarElemento(this, \"¿Está seguro que desea eliminar los datos de esta persona interesada?\", \"tab_lista\")' data-id='" + data.id + "' data-urleliminar='" + ((urlEliminar.replace("/0", "/" + data.id))) + "'>" +
                        "<i class='fa fa-trash'></i>" +
                        "</a>" +
                        "</li>" +
                        "</ul>";
                $('td', row).eq(4).html(tBotones);
            }
        });
    }

    $('#formulario_interesado').validate({
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
            telefono: {
                required: true
            },
            correoElectronico: {
                required: true,
                email: true
            },
            cursoInteres: {
                required: true
            }
        },
        submitHandler: function (form) {
            if (confirm($("#btn-guardar").text() === "Guardar"
                    ? "¿Está seguro que desea guardar los cambios de los datos de la persona interesada?"
                    : "¿Está seguro que desea registrar los datos de esta persona interesada?"))
                form.submit();
        },
        highlight: function () {
        },
        unhighlight: function () {
        },
        errorElement: 'div',
        errorClass: 'help-block-error',
        errorPlacement: function (error, element) {
            if (element.closest('div[class*=col-sm-]').length > 0) {
                element.closest('div[class*=col-sm-]').append(error);
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
});