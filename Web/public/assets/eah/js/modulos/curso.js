$(document).ready(function () {
  cargarLista();
  cargarFormulario();
  
  $("#sel-curso").select2();
  $("#sel-curso").change(function () {
    if (urlEditar !== "") {
      window.location.href = urlEditar.replace("/0", "/" + $(this).val());
    }
  });
});

//Lista
function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);

  if (urlListar !== "" && urlEditar !== "" && urlEliminar !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[0, "desc"]],
      columns: [
        {data: "nombre", name: "nombre", render: function (e, t, d, m) {
            return d.nombre;
          }},
        {data: "descripcion", name: "descripcion", render: function (e, t, d, m) {
            return ((d.descripcion.length > 200) ? (d.descripcion.substr(0, d.descripcion.lastIndexOf(' ', 197)) + '...') : d.descripcion);
          }},
        {data: "activo", name: "activo", orderable: false, "searchable": false, render: function (e, t, d, m) {
            return '<input type="checkbox"' + (d.activo.toString() === "1" ? ' checked="checked"' : '') + ' disabled="disabled"/>';
          }, className: "text-center"},
        {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li><a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar curso" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este curso?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
      }
    });
  }
}

$.validator.addMethod("validarCkEditorCurso", validarCkEditorCurso, "Este campo es obligatorio.");
function validarCkEditorCurso(value, element, param) {
  CKEDITOR.instances[$(element).attr("id")].updateElement();
  if ($(element).val().trim() !== "") {
    return true;
  } else {
    $(window).scrollTop($("#cke_" + $(element).attr("id")).offset().top);
    return false;
  }
}
function cargarFormulario() {
  if ($("#descripcion").length === 0) {
    return;
  }
  $("#formulario-curso").validate({
    ignore: "",
    rules: {
      nombre: {
        required: true,
        validarAlfabetico: true
      },
      descripcion: {
        validarCkEditorCurso: true
      },
      modulos: {
        validarCkEditorCurso: true
      },
      metodologia: {
        validarCkEditorCurso: true
      },
      incluye: {
        validarCkEditorCurso: true
      },
      inversion: {
        validarCkEditorCurso: true
      },
      inversionCuotas: {
        validarCkEditorCurso: true
      },
      notasAdicionales: {
        validarCkEditorCurso: true
      }
    },
    submitHandler: function (f) {
      if (confirm($("#btn-guardar").text().trim() === "Guardar"
          ? "¿Está seguro que desea guardar los cambios de los datos del curso?"
          : "¿Está seguro que desea registrar los datos de este curso?")) {
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

  CKEDITOR.replace("descripcion");
  CKEDITOR.replace("modulos");
  CKEDITOR.replace("metodologia");
  CKEDITOR.replace("incluye");
  CKEDITOR.replace("inversion");
  CKEDITOR.replace("inversion-cuotas");
  CKEDITOR.replace("notas-adicionales");
}