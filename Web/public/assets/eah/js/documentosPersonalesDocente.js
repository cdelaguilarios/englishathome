window.addEventListener("load", verificarJqueryDocumentosPersonalesDocente, false);
function verificarJqueryDocumentosPersonalesDocente() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionDocumentosPersonalesDocente() : window.setTimeout(cargarSeccionDocumentosPersonalesDocente, 100));
}

function  cargarSeccionDocumentosPersonalesDocente() {
  formularioExternoPostulante = (typeof (formularioExternoPostulante) === "undefined" ? false : formularioExternoPostulante);

  incluirSeccionSubidaArchivos("documento-personal-cv", {onSubmit: function () {
      return true;
    }, acceptFiles: "*", uploadStr: (formularioExternoPostulante ? "Upload file" : "Subir archivo"), maxFileCount: 1});
  incluirSeccionSubidaArchivos("documento-personal-certificado-internacional", {onSubmit: function () {
      return true;
    }, acceptFiles: "*", uploadStr: (formularioExternoPostulante ? "Upload file" : "Subir archivo"), maxFileCount: 1});
  incluirSeccionSubidaArchivos("documento-personal-imagen-documento-identidad", {onSubmit: function () {
      return true;
    }, acceptFiles: "*", uploadStr: (formularioExternoPostulante ? "Upload file" : "Subir archivo"), maxFileCount: 1});
}

function eliminarDocumentoPersonalDocente(ele, seccion, nombreArchivo) {
  $("#nombres-archivos-documento-personal-" + seccion + "-eliminado").val(nombreArchivo + "," + $("#nombres-archivos-documento-personal-" + seccion + "-eliminado").val());
  $(ele).closest(".ajax-file-upload-container").remove();
}