var busquedaProfesor = {};
busquedaProfesor = (function () {
  $(document).ready(function () {
    cargarCajaBusqueda();
  });

  function cargarCajaBusqueda() {
    urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
    idProfesor = (typeof (idProfesor) === "undefined" ? "" : idProfesor);
    nombreCompletoProfesor = (typeof (nombreCompletoProfesor) === "undefined" ? "" : nombreCompletoProfesor);

    if (urlBuscar !== "" && idProfesor !== "" && nombreCompletoProfesor !== "") {
      utilBusqueda.establecerListaBusqueda($("#sel-profesor"), urlBuscar);
      $("#sel-profesor").empty().append('<option value="' + idProfesor + '">' + nombreCompletoProfesor + '</option>').val(idProfesor);
      $("#sel-profesor").change(function () {
        if ($(this).data("seccion") === "perfil" && urlPerfil !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlPerfil.replace("/0", "/" + $(this).val());
        } else if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlEditar.replace("/0", "/" + $(this).val());
        }
      });
    }
  }
}());