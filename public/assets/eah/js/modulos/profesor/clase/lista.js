var listaClasesProfesor = {};
listaClasesProfesor = (function () {  
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargarSeccion() {
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);

    if (urlListarClases !== "") {
      listaClases.actualizar(urlListarClases);
    }
  }
  
  //Público
  function mostrar() {
    $("div[id^=sec-clase-]").hide();
    $("#sec-clase-lista").show();
  }

  return {
    mostrar: mostrar
  };
}());