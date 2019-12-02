var formularioProfesor = {};
formularioProfesor = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarFormulario();
  });

  function cargarFormulario()/* - */ {
    urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);
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
        fechaNacimiento: {
          validarFecha: true
        },
        idTipoDocumento: {
          required: true
        },
        numeroDocumento: {
          number: true
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
        },
        "idCursos[]": {
          required: true
        },
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
        if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
          if (confirm($("input[name='modoEditar']").val() === "1"
                  ? "¿Está seguro que desea guardar los cambios de los datos del profesor?"
                  : "¿Está seguro que desea registrar estos datos?")) {
            $.blockUI({message: "<h4>" + ($("input[name='modoEditar']").val() === "1" ? "Guardando" : "Registrando") + " datos...</h4>"});
            f.submit();
          }
        } else {
          mensajes.agregar("advertencias", "Debe ingresar un horario disponible", true, "#sec-men-alerta-horario");
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
      invalidHandler: function (e, v) {
        if (v.errorList.length > 0 && $(v.errorList[0].element).closest(".step-pane").data("step") !== undefined) {
          $('#wiz-registro-profesor').wizard('selectedItem', {step: $(v.errorList[0].element).closest(".step-pane").data("step")});
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });
    utilFormularios.establecerWizard("profesor", ($("input[name='modoEditar']").length > 0 && $("input[name='modoEditar']").val() === "1"));

    var fechaNacimiento = $("#fecha-nacimiento").val();
    utilFechasHorarios.establecerCalendario($("#fecha-nacimiento"), false, true, false);
    if (fechaNacimiento !== "") {
      if (Date.parse(fechaNacimiento)) {
        var datFechaNacimiento = fechaNacimiento.split("/");
        $("#fecha-nacimiento").datepicker("setDate", (new Date(datFechaNacimiento[1] + "/" + datFechaNacimiento[0] + "/" + datFechaNacimiento[2])));
      } else {
        $("#fecha-nacimiento").datepicker("setDate", (new Date()));
      }
    }

    $("#direccion").focusout(ubicacionMapa.verificarDatosBusquedaMapa);
    $("input[name='codigoUbigeo']").change(ubicacionMapa.verificarDatosBusquedaMapa);
  }
}());




