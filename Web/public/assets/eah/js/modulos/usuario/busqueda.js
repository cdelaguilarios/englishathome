var busquedaUsuario = {};
busquedaUsuario = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarCajaBusqueda();
  });

  function cargarCajaBusqueda()/* - */ {
    urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);

    idUsuario = (typeof (idUsuario) === "undefined" ? "" : idUsuario);
    nombreCompletoUsuario = (typeof (nombreCompletoUsuario) === "undefined" ? "" : nombreCompletoUsuario);

    if (urlBuscar !== "" && idUsuario !== "" && nombreCompletoUsuario !== "") {
      utilBusqueda.establecerListaBusqueda($("#sel-usuario"), urlBuscar);
      $("#sel-usuario").empty().append('<option value="' + idUsuario + '">' + nombreCompletoUsuario + '</option>').val(idUsuario);
      $("#sel-usuario").change(function () {
        if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlEditar.replace("/0", "/" + $(this).val());
        }
      });
    }
  }
}());