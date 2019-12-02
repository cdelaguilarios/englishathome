var crearPagoAlumno = {};
crearPagoAlumno = (function ()/* - */ {
  //Público
  var idSeccion = "";
  function establecerIdSeccion(id)/* - */ {
    idSeccion = id;
  }
  function mostrar()/* - */ {
    $("div[id^=sec-pago-]").hide();
    formularioPagoAlumno.cargar(idSeccion);
    $("#sec-pago-crear").show();
  }

  return {
    establecerIdSeccion: establecerIdSeccion,
    mostrar: mostrar
  };
}());