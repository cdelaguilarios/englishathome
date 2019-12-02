var busquedaAlumno = {};
busquedaAlumno = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    cargarCajaBusqueda();
  });

  function cargarCajaBusqueda()/* - */ {
    urlBuscar = (typeof (urlBuscar) === "undefined" ? "" : urlBuscar);
    urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
    urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
    idAlumno = (typeof (idAlumno) === "undefined" ? "" : idAlumno);
    nombreCompletoAlumno = (typeof (nombreCompletoAlumno) === "undefined" ? "" : nombreCompletoAlumno);

    if (urlBuscar !== "" && idAlumno !== "" && nombreCompletoAlumno !== "") {
      utilBusqueda.establecerListaBusqueda($("#sel-alumno"), urlBuscar);
      $("#sel-alumno").empty().append('<option value="' + idAlumno + '">' + nombreCompletoAlumno + '</option>').val(idAlumno);
      $("#sel-alumno").change(function () {
        if ($(this).data("seccion") === "perfil" && urlPerfil !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlPerfil.replace("/0", "/" + $(this).val());
        } else if (urlEditar !== "" && $(this).val() !== this.options[this.selectedIndex].innerHTML) {
          window.location.href = urlEditar.replace("/0", "/" + $(this).val());
        }
      });
    }
  }
}());