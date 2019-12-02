var cotizacionInteresado = {};
cotizacionInteresado = (function () {
  $(document).ready(function () {
    cargarFormulario();

    $("#id-curso").change(function () {
      urlDatosCurso = (typeof (urlDatosCurso) === "undefined" ? "" : urlDatosCurso);
      urlBaseImagen = (typeof (urlBaseImagen) === "undefined" ? "" : urlBaseImagen);

      if (urlDatosCurso !== "" && urlBaseImagen !== "") {
        $.blockUI({message: "<h4>Cargando...</h4>"});
        util.llamadaAjax(urlDatosCurso.replace("/0", "/" + $(this).val()), "POST", {}, true,
                function (d) {
                  var rutaImagenCurso = (d.imagen !== null ? urlBaseImagen.replace(encodeURI("[RUTA_IMAGEN]"), d.imagen) : "");
                  $("input[name='imagenCurso']").val(rutaImagenCurso);
                  $("#sec-imagen-curso").html(rutaImagenCurso !== "" ? '<img src="' + rutaImagenCurso + '" width="120"/>' : "");

                  var idHtmlAdjuntos = "adjuntos";
                  $("." + idHtmlAdjuntos + "-registrado").remove();
                  $("#nombres-archivos-" + idHtmlAdjuntos + "-eliminados").val("");
                  if (d.adjuntos !== null) {
                    var archivosRegistrados = d.adjuntos.split(",");
                    archivosRegistrados.forEach(function (archivoReg) {
                      var datosArchivoReg = (archivoReg !== "" ? archivoReg.split(":") : []);
                      if (datosArchivoReg.length === 2) {
                        var rutaArchivo = urlBaseImagen.replace(encodeURI("[RUTA_IMAGEN]"), datosArchivoReg[0]);
                        $("#" + idHtmlAdjuntos).after('<div class="ajax-file-upload-container ' + idHtmlAdjuntos + '-registrado">' +
                                '<div class="ajax-file-upload-statusbar" style="width: 400px;">' +
                                '<div class="ajax-file-upload-filename">' +
                                (util.urlEsImagen(rutaArchivo) ?
                                        '<a href="' + rutaArchivo + '" target="_blank"><img src="' + rutaArchivo + '" alt="' + datosArchivoReg[1] + '" width="300" /></a>' :
                                        '<a href="' + rutaArchivo + '" download="' + datosArchivoReg[1] + '">' + datosArchivoReg[1] + '</a>') +
                                '</div>' +
                                '<div class="ajax-file-upload-progress">' +
                                '<div class="ajax-file-upload-bar" style="width: 100%;"></div>' +
                                '</div>' +
                                '<div class="ajax-file-upload-red" onclick="archivosAdjuntos.eliminar(this, \'' + idHtmlAdjuntos + '\', \'' + datosArchivoReg[0] + '\')">Eliminar</div>' +
                                '</div>' +
                                '</div>');
                      }
                    });
                  }
                  $("body").unblock();
                }
        );
      }
    });
  });

  var editorCargado = false;
  function cargarFormulario() {
    $("#formulario-interesado-cotizacion").validate({
      ignore: "#correo-cotizacion-prueba:not(:visible)",
      rules: {
        idCurso: {
          required: true
        },
        contenidoCorreo: {
          validarCkEditor: true
        },
        costoXHoraClase: {
          required: true,
          validarDecimal: true
        },
        correoCotizacionPrueba: {
          required: true,
          email: true
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea enviar esta cotización?")) {
          $("#mod-correo-cotizacion-prueba").modal("hide");
          $.blockUI({message: "<h4>Enviando cotización...</h4>"});

          if ($("#correo-cotizacion-prueba").val() !== "") {
            var datos = utilFormularios.procesarDatos(f);
            util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                    function (d) {
                      $("body").unblock({
                        onUnblock: function () {
                          mensajes.agregar("exitosos", "Cotización enviada.", true);
                        }
                      });
                    },
                    function (d) {
                    },
                    function (de) {
                      $("body").unblock({
                        onUnblock: function () {
                          mensajes.agregar("errores", "Ocurrió un problema durante el envio de la cotización. Por favor inténtelo nuevamente.", true);
                        }
                      });
                    }
            );
          } else {
            f.submit();
          }
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
    CKEDITOR.replace("contenido-correo");
    CKEDITOR.on("instanceReady", function (e) {
      if (!editorCargado) {
        editorCargado = true;
        $("#id-curso").trigger("change");
      }
    });

    $("#btn-envio-cotización").click(function () {
      $("#correo-cotizacion-prueba").val("");
      $("#formulario-interesado-cotizacion").submit();
    });
    $("#btn-envio-cotización-prueba").click(function () {
      var camposFormularioInteresadoCotizacion = $("#formulario-interesado-cotizacion").not("#correo-cotizacion-prueba:not(:visible)");
      if (!camposFormularioInteresadoCotizacion.valid()) {
        return false;
      }
      $("#mod-correo-cotizacion-prueba").modal("show");
    });
  }
}());