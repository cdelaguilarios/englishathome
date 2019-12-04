var archivosAdjuntos = {};
archivosAdjuntos = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado 
  var objAdjuntos = [];
  function cargarSeccion()/* - */ {
    adjuntos = (typeof (adjuntos) === "undefined" ? [] : adjuntos);
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

  //Público
  function agregar(idHtml, nombreArchivo, nombreArchivoOriginal, esImagen)/* - */ {
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
    $("#" + idHtml).after(htmlElemento);
  }
  function eliminar(elemento, idElementoSec, nombreArchivo)/* - */ {
    $("#nombres-archivos-" + idElementoSec + "-eliminados").val(nombreArchivo + "," + $("#nombres-archivos-" + idElementoSec + "-eliminados").val());
    $(elemento).closest(".ajax-file-upload-container").remove();
  }
  function limpiarCampos(formulario, idCampo)/* - */ {
    objAdjuntos[idCampo].reset();
    
    $(".ajax-file-upload-container").html("");
    $(formulario).find("input[name^='nombresArchivos']").val("");
    $(formulario).find("input[name^='nombresOriginalesArchivos']").val("");
    $(formulario).find("input[name*='Eliminados']").val("");
  }

  return {
    agregar: agregar,
    eliminar: eliminar,
    limpiarCampos: limpiarCampos
  };
}());