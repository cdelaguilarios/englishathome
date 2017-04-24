$.validator.addMethod("validarCkEditorCorreos", validarCkEditorCorreos, "Este campo es obligatorio.");
function validarCkEditorCorreos(v, e, p) {
  CKEDITOR.instances[$(e).attr("id")].updateElement();
  if ($(e).val().trim() !== "") {
    return true;
  } else {
    $(window).scrollTop($("#cke_" + $(e).attr("id")).offset().top);
    return false;
  }
}

$(document).ready(function () {
  urlBuscarEntidades = (typeof (urlBuscarEntidades) === "undefined" ? "" : urlBuscarEntidades);
  urlImagenes = (typeof (urlImagenes) === "undefined" ? "" : urlImagenes);
  tiposEntidades = (typeof (tiposEntidades) === "undefined" ? "" : tiposEntidades);
  tipoEntidadInteresado = (typeof (tipoEntidadInteresado) === "undefined" ? "" : tipoEntidadInteresado);
  if (urlBuscarEntidades !== "" && urlImagenes !== "" && tiposEntidades !== "" && tipoEntidadInteresado !== "") {
    $("#entidades-seleccionadas-correos, #entidades-excluidas-correos").select2({
      ajax: {
        url: urlBuscarEntidades,
        dataType: 'json',
        type: "POST",
        delay: 250,
        data: function (p) {
          return {
            texto: p.term,
            pagina: p.page,
            _token: $("meta[name=_token]").attr("content")
          };
        },
        processResults: function (d, p) {
          p.pagina = p.page || 1;
          return {
            results: d.entidades,
            pagination: {
              more: (p.pagina * 6) < d.total
            }
          };
        },
        cache: true
      },
      escapeMarkup: function (markup) {
        return markup;
      },
      minimumInputLength: 3,
      templateResult: function (d) {
        if (d.loading)
          return d.text;
        var tipoEntidad = tiposEntidades[d.tipo][(d.sexo === "F" ? 1 : 0)]
        var rutaImagenPerfil = urlImagenes.replace("/0", "/" + (d.imagenPerfil !== null && d.imagenPerfil !== "" ? d.imagenPerfil : "-")) + "?tip=" + (d.sexo === "F" ? "f" : "m");
        return '<div class="clearfix">' +
            '<div><img src="' + rutaImagenPerfil + '" width="25"/> ' + tipoEntidad + " " + d.nombre + " " + d.apellido + ' (' + d.correoElectronico + ')</div>' +
            '<div>';
      },
      templateSelection: function (d) {
        return (d.nombre + " " + d.apellido + " (" + d.correoElectronico + ")") || d.text;
      }
    });
    $("#tipo-entidad-correos").change(function () {
      if ($(this).val() !== "") {
        $("#sec-entidades-seleccionadas-correos").hide();
      } else {
        $("#sec-entidades-seleccionadas-correos").show();
      }
      if ($(this).val() === tipoEntidadInteresado) {
        $("#sec-interesados-cursos-interes-correos").show();
      } else {
        $("#sec-interesados-cursos-interes-correos").hide();
      }
    });
  }

  $("#formulario-correos").validate({
    ignore: "",
    rules: {
      asunto: {
        required: true
      },
      mensaje: {
        validarCkEditorCorreos: true
      }
    },
    submitHandler: function (f) {
      if ($("#tipo-entidad-correos").val() === "" && $("#entidades-seleccionadas-correos").val() === null && $("#correos-adicionales-correos").val() === "") {
        agregarMensaje("advertencias", "Debe seleccionar por lo menos una entidad o ingresar un correo adicional.", true);
      } else if (confirm("¿Está seguro que desea enviar este mensaje?")) {
        $.blockUI({message: "<h4>Registrando datos...</h4>"});
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
  CKEDITOR.replace("mensaje");
});




