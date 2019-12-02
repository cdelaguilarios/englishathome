$.validator.addMethod("validarPassword", validarPassword, "Este campo es obligatorio.");
function validarPassword(value, element, param) {
  return (($("#modo-edicion") === undefined && value.trim() !== "") ||
      ($("#modo-edicion") !== undefined && ($("#modo-edicion").val() === "1" || ($("#modo-edicion").val() === "0" && value.trim() !== ""))));
}
$(document).ready(function () {
  cargarLista();
  cargarFormulario();

  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
  idUsuario = (typeof (idUsuario) === "undefined" ? "" : idUsuario);
  nombreCompletoUsuario = (typeof (nombreCompletoUsuario) === "undefined" ? "" : nombreCompletoUsuario);
  utilBusqueda.establecerListaBusqueda($("#sel-usuario"), urlBuscar);
  $("#sel-usuario").empty().append('<option value="' + idUsuario + '">' + nombreCompletoUsuario + '</option>').val(idUsuario);
  $("#sel-usuario").change(function () {
    if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML)
      window.location.href = urlEditar.replace("/0", "/" + $(this).val());
  });
});
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  roles = (typeof (roles) === "undefined" ? "" : roles);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosDisponibleCambio = (typeof (estadosDisponibleCambio) === "undefined" ? "" : estadosDisponibleCambio);

  if (urlListar !== "" && urlEditar !== "" && urlEliminar !== "" && roles !== "" && estados !== "" && estadosDisponibleCambio !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.estado = $("#bus-estado").val();
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[4, "desc"]],
      columns: [
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>';
          }},
        {data: "email", name: "usuario.email"},
        {data: "rol", name: "usuario.rol", render: function (e, t, d, m) {
            return roles[d.rol];
          }},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            if (estados[d.estado] !== undefined && estadosDisponibleCambio[d.estado] !== undefined) {
              return '<div class="sec-btn-editar-estado" data-idtabla="tab-lista" data-idselestados="sel-estados" data-tipocambio="2">'+
                      '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span></a></div>';
            } else if (estados[d.estado] !== undefined) {
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
            } else {
              return "";
            }
          }, className: "text-center"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
        {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar usuario" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este usuario?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla($("#tab-lista"));
      }
    });
  }
}
function cargarFormulario() {
  $("#formulario-usuario").validate({
    ignore: ":hidden",
    rules: {
      nombre: {
        validarAlfabetico: true
      },
      apellido: {
        validarAlfabetico: true
      },
      email: {
        required: true,
        email: true
      },
      imagenPerfil: {
        validarImagen: true
      },
      password: {
        validarPassword: true
      },
      password_confirmation: {
        validarPassword: true,
        equalTo: "#password"
      }
    },
    submitHandler: function (f) {
      if (confirm($("#btn-guardar").text().trim() === "Guardar"
          ? "¿Está seguro que desea guardar los cambios de los datos del usuario?"
          : "¿Está seguro que desea registrar los datos de este usuario?")) {
        $.blockUI({message: "<h4>" + ($("#btn-guardar").text().trim() === "Guardar" ? "Guardando" : "Registrando") + " datos...</h4>"});
        f.submit();
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
    },
    onfocusout: false,
    onkeyup: false,
    onclick: false
  });
}
