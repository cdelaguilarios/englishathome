var claseProfesor = {};
claseProfesor = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargarSeccion() {
    listaClasesProfesor.mostrar();
  }
}());