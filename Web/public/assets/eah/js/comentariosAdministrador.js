window.addEventListener("load", verificarJqueryComentariosAdministrador, false);
function verificarJqueryComentariosAdministrador() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionComentariosAdministrador() : window.setTimeout(verificarJqueryComentariosAdministrador, 100));
}


function validarCkEditorComentariosAdministrador(v, e, p) {
  CKEDITOR.instances[$(e).attr("id")].updateElement();
  if ($(e).val().trim() !== "") {
    return true;
  } else {
    $(window).scrollTop($("#cke_" + $(e).attr("id")).offset().top);
    return false;
  }
}
function  cargarSeccionComentariosAdministrador() {
  $.validator.addMethod("validarCkEditorComentariosAdministrador", validarCkEditorComentariosAdministrador, "Este campo es obligatorio.");
  CKEDITOR.replace("comentarioAdministrador");
}