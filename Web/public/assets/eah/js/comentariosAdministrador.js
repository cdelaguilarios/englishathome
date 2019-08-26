window.addEventListener("load", verificarJqueryComentariosAdministrador, false);
function verificarJqueryComentariosAdministrador() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionComentariosAdministrador() : window.setTimeout(verificarJqueryComentariosAdministrador, 100));
}

function  cargarSeccionComentariosAdministrador() {
  $("#formulario-comentarios-administrador").validate({
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea guardar los cambios de estos comentarios?")) {
        $.blockUI({message: "<h4>Guardando...</h4>"});
        CKEDITOR.instances["comentarios-administrador"].updateElement();
        var datos = procesarDatosFormulario(f);
        util.llamadaAjax($(f).attr("action"), "POST", datos, true,
            function (d) {
              $("body").unblock({
                onUnblock: function () {
                  agregarMensaje("exitosos", d["mensaje"], true);
                }
              });
            },
            function (d) {
            },
            function (de) {
              $("body").unblock({
                onUnblock: function () {
                  agregarMensaje("errores", de["responseJSON"]["mensaje"], true);
                }
              });
            }
        );
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
  CKEDITOR.replace("comentarios-administrador");
}