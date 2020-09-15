var claseAlumno = {};
claseAlumno = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargarSeccion() {
    if (util.obtenerParametroUrlXNombre("seccion") === "clase") {
      $("a[href='#clase']").trigger("click");
    }
    listaClasesAlumno.mostrar();
  }
}());