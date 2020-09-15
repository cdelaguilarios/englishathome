var formularioAlumno = {};
formularioAlumno = (function () {
  $(document).ready(function () {
    cargarFormulario();
  });

  function cargarFormulario() {
    minHorasClase = (typeof (minHorasClase) === "undefined" ? "" : minHorasClase);
    maxHorasClase = (typeof (maxHorasClase) === "undefined" ? "" : maxHorasClase);
    urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);

    $("#formulario-alumno").validate({
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
          required: ($("input[name='usuarioNoLogueado']").val() === "1")
        },
        fechaNacimiento: {
          required: ($("input[name='usuarioNoLogueado']").val() === "1"),
          validarFecha: true
        },
        idTipoDocumento: {
          required: true
        },
        numeroDocumento: {
          required: ($("input[name='usuarioNoLogueado']").val() === "1"),
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
        numeroHorasClase: {
          required: true,
          validarDecimal: true,
          range: [(minHorasClase * 3600), (maxHorasClase * 3600)]
        },
        fechaInicioClase: {
          required: true,
          validarFecha: true
        },
        costoXHoraClase: {
          required: true,
          validarDecimal: true
        }
      },
      submitHandler: function (f) {
        if ($.parseJSON($("input[name='horario']").val()) !== null && $.parseJSON($("input[name='horario']").val()).length > 0) {
          if (confirm($("input[name='modoEditar']").val() === "1"
                  ? "¿Está seguro que desea guardar los cambios de los datos del alumno?"
                  : "¿Está seguro que desea registrar estos datos?")) {
            $.blockUI({message: "<h4>" + ($("input[name='modoEditar']").val() === "1" ? "Guardando" : "Registrando") + " datos...</h4>"});
            f.submit();
          }
        } else {
          mensajes.agregar("advertencias", "Debe ingresar un horario disponible para sus clases.", true, "#sec-men-alerta-horario");
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
          $('#wiz-registro-alumno').wizard('selectedItem', {step: $(v.errorList[0].element).closest(".step-pane").data("step")});
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });
    
    utilFormularios.establecerWizard("alumno", ($("input[name='modoEditar']").length > 0 && $("input[name='modoEditar']").val() === "1"));

    if (!($("input[name='idInteresado']").length > 0 && $("input[name='idInteresado']").val() !== "")) {
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

    var fechaInicioClase = $("#fecha-inicio-clase").val();
    utilFechasHorarios.establecerCalendario($("#fecha-inicio-clase"), false, false, false);
    if (fechaInicioClase !== "") {
      if (Date.parse(fechaInicioClase)) {
        var datFechaInicioClase = fechaInicioClase.split("/");
        $("#fecha-inicio-clase").datepicker("setDate", (new Date(datFechaInicioClase[1] + "/" + datFechaInicioClase[0] + "/" + datFechaInicioClase[2])));
      } else {
        $("#fecha-inicio-clase").datepicker("setDate", (new Date()));
      }
    }

    var numeroHorasClase = $("input[name='auxNumeroHorasClase']").val();
    utilFechasHorarios.establecerCampoDuracion($("#numero-horas-clase"), (numeroHorasClase !== "" ? numeroHorasClase : 7200));

    $("#direccion").focusout(ubicacionMapa.verificarDatosBusquedaMapa);
    $("input[name='codigoUbigeo']").change(ubicacionMapa.verificarDatosBusquedaMapa);
  }
}());




