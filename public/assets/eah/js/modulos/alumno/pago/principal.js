var pagoAlumno = {};
pagoAlumno = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargarSeccion() {
    if (util.obtenerParametroUrlXNombre("seccion") === "pago") {
      $("a[href='#pago']").trigger("click");
    }
    listaPagosAlumno.mostrar();
  }
}());