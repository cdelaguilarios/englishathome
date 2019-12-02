var busquedaPostulante = {};
busquedaPostulante = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarCajaBusqueda();
  });

  function cargarCajaBusqueda()/* - */ {
    urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
    idPostulante = (typeof (idPostulante) === "undefined" ? "" : idPostulante);
    nombreCompletoPostulante = (typeof (nombreCompletoPostulante) === "undefined" ? "" : nombreCompletoPostulante);

    if (urlBuscar !== "" && idPostulante !== "" && nombreCompletoPostulante !== "") {
      utilBusqueda.establecerListaBusqueda($("#sel-postulante"), urlBuscar);
      $("#sel-postulante").empty().append('<option value="' + idPostulante + '">' + nombreCompletoPostulante + '</option>').val(idPostulante);
      $("#sel-postulante").change(function () {
        if ($(this).data("seccion") === "perfil" && urlPerfil !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlPerfil.replace("/0", "/" + $(this).val());
        } else if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlEditar.replace("/0", "/" + $(this).val());
        }
      });
    }
  }
}());