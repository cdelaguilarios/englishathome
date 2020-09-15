var pagoDocente = {};
pagoDocente = (function () {
  $(document).ready(function () {
    cargarSeccion();
  });

  //Privado
  function cargarSeccion() {
    listaPagosDocente.mostrar();
  }
}());