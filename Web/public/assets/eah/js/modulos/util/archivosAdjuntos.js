var archivosAdjuntos = {};
archivosAdjuntos = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }
  function cargarSeccion()/* - */ {
    adjuntos = (typeof (adjuntos) === "undefined" ? [] : adjuntos);
    formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);

    if (adjuntos.length > 0) {
      adjuntos.forEach(function (adjunto) {
        utilFormularios.incluirSeccionSubidaArchivos($("#" + adjunto.idHtml), {onSubmit: function () {
            return true;
          }, acceptFiles: "*", uploadStr: (formularioExternoPostulante ? "Upload file" : "Subir archivo"), maxFileCount: (adjunto.cantidadMaximaArchivos ? adjunto.cantidadMaximaArchivos : 20)});
      });
    }
  }

  function eliminar(elemento, idElementoSec, nombreArchivo) {
    $("#nombres-archivos-" + idElementoSec + "-eliminados").val(nombreArchivo + "," + $("#nombres-archivos-" + idElementoSec + "-eliminados").val());
    $(elemento).closest(".ajax-file-upload-container").remove();
  }

  return {
    eliminar: eliminar
  };
}());