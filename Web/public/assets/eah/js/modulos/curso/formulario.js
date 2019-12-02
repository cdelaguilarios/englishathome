var formularioCurso = {};
formularioCurso = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarFormulario();

    $("#sel-curso").select2();
    $("#sel-curso").change(function () {
      if (urlEditar !== "") {
        window.location.href = urlEditar.replace("/0", "/" + $(this).val());
      }
    });
  });

  function cargarFormulario() {
    $("#formulario-curso").validate({
      ignore: "",
      rules: {
        nombre: {
          required: true
        },
        descripcion: {
          validarCkEditor: true
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
  }
}());
