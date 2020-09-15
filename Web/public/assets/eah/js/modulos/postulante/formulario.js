var formularioPostulante = {};
formularioPostulante = (function () {
  $(document).ready(function () {
    cargarFormulario();
  });

  function cargarFormulario() {
    formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);
    urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);

    $("#formulario-postulante").validate({
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
          required: formularioExternoPostulante,
          validarFecha: true
        },
        idTipoDocumento: {
          required: true
        },
        numeroDocumento: {
          required: formularioExternoPostulante,
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
        ultimosTrabajos: {
          required: formularioExternoPostulante
        },
        experienciaOtrosIdiomas: {
          required: formularioExternoPostulante
        },
        descripcionPropia: {
          required: formularioExternoPostulante
        },
        ensayo: {
          required: formularioExternoPostulante
        },
        "idCursos[]": {
          required: !formularioExternoPostulante
        },
        audio: {
          validarAudio: true,
          archivoTamanho: 2097152
        }
      },
      messages: {
        audio: {
          archivoTamanho: (formularioExternoPostulante ? "File must be less than 2MB" : "Archivo debe ser menor a 2MB.")
        }
      },
      submitHandler: function (f) {
        if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
          var mensajeConfirmacion = "¿Está seguro que desea registrar a este postulante como un nuevo profesor?";
          if ($("input[name='registrarComoProfesor']").val() !== "1") {
            mensajeConfirmacion = ($("input[name='modoEditar']").val() === "1"
                    ? "¿Está seguro que desea guardar los cambios de los datos del postulante?"
                    : (formularioExternoPostulante ? "Are you sure you want to register this data?" : "¿Está seguro que desea registrar estos datos?"));
          }
          if (confirm(mensajeConfirmacion)) {
            $.blockUI({message: "<h4>" + ($("input[name='modoEditar']").val() === "1" ? "Guardando datos..." : (formularioExternoPostulante ? "Saving Your Data" : "Registrando datos...")) + "</h4>"});
            f.submit();
          } else {
            $("input[name='registrarComoProfesor']").val("0");
          }
        } else {
          mensajes.agregar("advertencias", (formularioExternoPostulante ? "Must enter your schedule available to work" : "Debe ingresar un horario disponible"), true, "#sec-men-alerta-horario");
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
          $('#wiz-registro-postulante').wizard('selectedItem', {step: $(v.errorList[0].element).closest(".step-pane").data("step")});
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });

    utilFormularios.establecerWizard("postulante", ($("input[name='modoEditar']").length > 0 && $("input[name='modoEditar']").val() === "1"));

    if (!formularioExternoPostulante) {
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
    }

    $("#direccion").focusout(ubicacionMapa.verificarDatosBusquedaMapa);
    $("input[name='codigoUbigeo']").change(ubicacionMapa.verificarDatosBusquedaMapa);

    $("input[name='registrarComoProfesor']").val("0");
    $("#btn-registrar-profesor").click(function () {
      $("input[name='registrarComoProfesor']").val("1");
      $("#formulario-postulante").submit();
    });
    $("#btn-guardar").click(function () {
      $("input[name='registrarComoProfesor']").val("0");
    });
  }
}());




