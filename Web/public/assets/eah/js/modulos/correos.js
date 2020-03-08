var correos = {};
correos = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarFiltrosBusqueda();
    cargarFormulario();
  });

  function cargarFiltrosBusqueda()/* - */ {
    urlBuscarEntidades = (typeof (urlBuscarEntidades) === "undefined" ? "" : urlBuscarEntidades);
    urlArchivos = (typeof (urlArchivos) === "undefined" ? "" : urlArchivos);
    tiposEntidades = (typeof (tiposEntidades) === "undefined" ? "" : tiposEntidades);
    tipoEntidadInteresado = (typeof (tipoEntidadInteresado) === "undefined" ? "" : tipoEntidadInteresado);
    
    if (urlBuscarEntidades !== "" && urlArchivos !== "" && tiposEntidades !== "" && tipoEntidadInteresado !== "") {
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
          var tipoEntidad = tiposEntidades[d.tipo][(d.sexo === "F" ? 3 : 2)];
          var rutaImagenPerfil = urlArchivos.replace("/0", "/" + (d.imagenPerfil !== null && d.imagenPerfil !== "" ? d.imagenPerfil : "-")) + "?tip=" + (d.sexo === "F" ? "f" : "m");
          return '<div class="clearfix">' +
                  '<div><img src="' + rutaImagenPerfil + '" width="25"/> ' + tipoEntidad + " " + d.nombre + " " + d.apellido + ' (' + d.correoElectronico + ')</div>' +
                  '<div>';
        },
        templateSelection: function (d) {
          return (d.nombre && d.apellido && d.correoElectronico ? (d.nombre + " " + d.apellido + " (" + d.correoElectronico + ")") : d.text);
        }
      });
      
      $("#tipo-entidad-correos").change(function () {
        $('[id*="sec-estados-"]').hide();
        if ($(this).val() !== "") {
          $("#sec-estados-" + $(this).val()).show();
          $("#sec-estados-entidades").show();
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
      $("#tipo-entidad-correos").trigger("change");

      datosEntidadSel = (typeof (datosEntidadSel) === "undefined" ? false : datosEntidadSel);
      if (datosEntidadSel) {
        $("#entidades-seleccionadas-correos").select2("trigger", "select", {data: datosEntidadSel});
      }
    }
  }
  function cargarFormulario()/* - */ {
    $("#formulario-correos").validate({
      ignore: "",
      rules: {
        asunto: {
          required: true
        },
        mensaje: {
          validarCkEditor: true
        }
      },
      submitHandler: function (f) {
        if ($("#tipo-entidad-correos").val() === "" && $("#entidades-seleccionadas-correos").val() === null && $("#correos-adicionales-correos").val() === "") {
          mensajes.agregar("advertencias", "Debe seleccionar por lo menos una entidad o ingresar un correo adicional.", true);
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
  }
}());




