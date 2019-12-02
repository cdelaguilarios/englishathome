var busquedaInteresado = {};
busquedaInteresado = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarCajaBusqueda();
  });

  function cargarCajaBusqueda()/* - */ {
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
    urlCotizar = (typeof (urlCotizar) === "undefined" ? "" : urlCotizar);
    idInteresado = (typeof (idInteresado) === "undefined" ? "" : idInteresado);
    nombreCompletoInteresado = (typeof (nombreCompletoInteresado) === "undefined" ? "" : nombreCompletoInteresado);

    if (urlBuscar !== "" && idInteresado !== "" && nombreCompletoInteresado !== "") {
      utilBusqueda.establecerListaBusqueda($("#sel-interesado"), urlBuscar);
      $("#sel-interesado").empty().append('<option value="' + idInteresado + '">' + nombreCompletoInteresado + '</option>').val(idInteresado);
      $("#sel-interesado").change(function () {
        if ($(this).val() !== this.options[this.selectedIndex].innerHTML) {
          if ($(this).data("seccion") === "cotizar" && urlCotizar !== "") {
            window.location.href = urlCotizar.replace("/0", "/" + $(this).val());
          } else if (urlEditar !== "") {
            window.location.href = urlEditar.replace("/0", "/" + $(this).val());
          }
        }
      });
    }
  }

  function copiarEnlaceFichaInscripcion(enlace)/* - */ {
    window.prompt("Copiar enlace ficha de inscripción: Ctrl+C, Enter", enlace);
    return false;
  }

  return {
    copiarEnlaceFichaInscripcion: copiarEnlaceFichaInscripcion
  };
}());