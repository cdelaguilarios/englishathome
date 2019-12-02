var crearPagoProfesor = {};
crearPagoProfesor = (function ()/* - */ {
  //Público
  var idSeccion = "";
  function establecerIdSeccion(id)/* - */ {
    idSeccion = id;
  }
  function mostrar()/* - */ {
    $("div[id^=sec-pago-]").hide();
    formularioPagoProfesor.cargar(idSeccion);
    $("#sec-pago-crear").show();
  }

  return {
    establecerIdSeccion: establecerIdSeccion,
    mostrar: mostrar
  };
}());