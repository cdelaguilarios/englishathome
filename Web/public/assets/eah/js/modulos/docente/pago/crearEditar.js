var crearEditarPagoDocente = {};
crearEditarPagoDocente = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado 
  function obtenerReferenciaFormulario() {
    return $("#formulario-pago");
  }
  function cargar() {
    var formulario = obtenerReferenciaFormulario();
    formularioPagoDocente.cargar(formulario);
  }

  //Público
  function registrar(datos)/* - */ {
    var formulario = obtenerReferenciaFormulario();
    formularioPagoDocente.establecerDatos(formulario, datos);
    $(formulario).submit();
  }
  function editar(datos)/* - */ {
    var formulario = obtenerReferenciaFormulario();
    formularioPagoDocente.establecerDatos(formulario, datos);
    $("#mod-pago").find(".modal-title").html("Pago al profesor(a) " + datos.profesor);
    $("#mod-pago").modal("show");
  }

  return {
    registrar: registrar,
    editar: editar
  };
}());