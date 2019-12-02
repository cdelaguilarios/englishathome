var pagoAlumno = {};
pagoAlumno = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargarSeccion() {
    registroHistorial = (typeof (registroHistorial) === "undefined" ? false : registroHistorial);
    if (util.obtenerParametroUrlXNombre("seccion") === "pago" && !registroHistorial) {
      $("a[href='#pago']").trigger("click");
    }
    listaPagosAlumno.mostrar();
  }
}());