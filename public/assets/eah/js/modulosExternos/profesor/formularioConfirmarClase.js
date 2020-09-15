var formularioConfirmarClase = {};
formularioConfirmarClase = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarFormulario() : window.setTimeout(esperarCargaJquery, 100));
  }
  function cargarFormulario() {    
    minHorasClase = (typeof (minHorasClase) === "undefined" ? 0 : minHorasClase);
    maxHorasClase = (typeof (maxHorasClase) === "undefined" ? 0 : maxHorasClase);
    maxHorasClase = (duracionTotalXClasesPendientes > maxHorasClase ? maxHorasClase : duracionTotalXClasesPendientes);    
    
    $("#formulario-confirmar-clase").validate({
      ignore: ":hidden",
      rules: {
        duracion: {
          required: true,
          validarDecimal: true,
          range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea realizar la confirmación de esta clase?")) {
          $.blockUI({message: "<h4>" + "Guardando datos...</h4>"});
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

    $("#btn-cambiar-duracion").click(function () {
      $(this).hide();
      $("#sec-duracion").hide();
      $("#sec-cambio-duracion").show();
    });
    utilFechasHorarios.establecerCampoDuracion($("#duracion-clase"), (parseFloat((duracionTotalXClasesPendientes >= 2 && minHorasClase <= 1) ? 2 : minHorasClase) * 3600));
  }
}());