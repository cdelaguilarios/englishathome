if (typeof (archivosAdjuntos) === "undefined") {
  var archivosAdjuntos = {};
  archivosAdjuntos = (function () {
    //Público
    var objAdjuntos = [];
    function cargar(adjuntos) {
      formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);

      if (adjuntos.length > 0) {
        adjuntos.forEach(function (adjunto) {
          var soloImagenes = (typeof (adjunto.soloImagenes) === "undefined" ? false : adjunto.soloImagenes);
          objAdjuntos[adjunto.idCampo] = utilFormularios.incluirSeccionSubidaArchivos($("#" + adjunto.idHtml),
                  {
                    onSubmit: function () {
                      return true;
                    },
                    acceptFiles: (soloImagenes ? "image/*" : "*"),
                    uploadStr: (formularioExternoPostulante ? "Upload file" : "Subir archivo"),
                    maxFileCount: (adjunto.cantidadMaximaArchivos ? adjunto.cantidadMaximaArchivos : 20)
                  });
        });
      }
    }

    function limpiarCampos(formulario, idCampo) {
      objAdjuntos[idCampo].reset();

      $(".ajax-file-upload-container").html("");
      $(formulario).find("input[name^='nombresArchivos']").val("");
      $(formulario).find("input[name^='nombresOriginalesArchivos']").val("");
      $(formulario).find("input[name*='Eliminados']").val("");
    }
    function agregar(formulario, idHtml, nombreArchivo, nombreArchivoOriginal, esImagen) {
      var urlArchivo = urlArchivos.replace("/0", "/" + nombreArchivo);
      var htmlElemento = '<div class="ajax-file-upload-container">' +
              '<div class="ajax-file-upload-statusbar" style="width: 400px;">' +
              '<div class="ajax-file-upload-filename">' +
              (esImagen ? '<a href="' + urlArchivo + '" target="_blank"><img src="' + urlArchivo + '" alt="' + nombreArchivoOriginal + '" width="200" /></a>'
                      : '<a href="' + urlArchivo + '" download="' + nombreArchivoOriginal + '">' + nombreArchivoOriginal + '</a>') +
              '</div>' +
              '<div class="ajax-file-upload-progress">' +
              '<div class="ajax-file-upload-bar" style="width: 100%;"></div>' +
              '</div>' +
              '<div class="ajax-file-upload-red" onclick="archivosAdjuntos.eliminar(this, \'' + idHtml + '\', \'' + nombreArchivo + '\')">' + (formularioExternoPostulante ? "Delete" : "Eliminar") + '</div>' +
              '</div>' +
              '</div>';
      $(formulario).find("#" + idHtml).after(htmlElemento);
    }
    function eliminar(elemento, idHtml, nombreArchivo) {
      $("#nombres-archivos-" + idHtml + "-eliminados").val(nombreArchivo + "," + $("#nombres-archivos-" + idHtml + "-eliminados").val());
      $(elemento).closest(".ajax-file-upload-container").remove();
    }

    return {
      cargar: cargar,
      agregar: agregar,
      eliminar: eliminar,
      limpiarCampos: limpiarCampos
    };
  }());
}