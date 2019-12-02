var docenteExperienciaLaboral = {};
docenteExperienciaLaboral = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }
  function cargarSeccion()/* - */ {
    cargarFormulario();
    
    if (util.obtenerParametroUrlXNombre("seccion") === "experiencia-laboral") {
      $("a[href='#experiencia-laboral']").trigger("click");
    }
  }

  function cargarFormulario() {
    $("#formulario-experiencia-laboral-docente").validate({
      ignore: "",
      rules: {
        audio: {
          validarAudio: true,
          archivoTamanho: 2097152
        }
      },
      messages: {
        audio: {
          archivoTamanho: "Archivo debe ser menor a 2MB."
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los cambios de los datos del docente?")) {
          $.blockUI({message: "<h4>Guardando datos...</h4>"});
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
}());