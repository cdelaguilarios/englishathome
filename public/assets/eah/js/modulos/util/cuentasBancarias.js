var cuentasBancarias = {};
cuentasBancarias = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function cargar() {
    limpiarCampos();
    $("input[name='cuentasBancarias']").val("");

    //Cuentas bancarias registradas
    cuentasBancariasReg = (typeof (cuentasBancariasReg) === "undefined" ? [] : cuentasBancariasReg);
    for (var i = 0; i < cuentasBancariasReg.length; i++) {
      agregar(cuentasBancariasReg[i].banco, cuentasBancariasReg[i].numeroCuenta);
    }

    $("#cuenta-bancaria-agregar").click(function () {
      var banco = $("input[name='cuentaBancariaBanco']").val();
      var numero = $("input[name='cuentaBancariaNumero']").val();
      agregar(banco, numero);
      limpiarCampos();
    });
  }
  function agregar(banco, numero) {
    banco = banco.replaceAll("|", "").replaceAll(";", "");
    numero = numero.replaceAll("|", "").replaceAll(";", "");

    if (banco.trim() !== "" && numero.trim() !== "") {
      var cuentasBancarias = $("input[name='cuentasBancarias']").val();
      $("input[name='cuentasBancarias']").val(cuentasBancarias + banco + "|" + numero + ";");
      var htmlElemento = '<div class="form-group">' +
              '<div class="col-sm-offset-2 col-sm-10">' +
              '<span class="text-info"><b><i class="fa fa-money"></i> ' + banco + ' - ' + numero + '</b></span>' +
              '<a href="javascript:void(0)" onclick="cuentasBancarias.eliminar(this)" data-banco="' + banco + '" data-numero="' + numero + '">' +
              '<span class="text-danger"> <i class="fa fa-times"></i></span>' +
              '</a>' +
              '</div>' +
              '</div>';
      $("#cuentas-bancarias-lista").append(htmlElemento);
    }
  }
  function limpiarCampos() {
    $("input[name='cuentaBancariaBanco']").val("");
    $("input[name='cuentaBancariaNumero']").val("");
  }

  //Público
  function eliminar(elemento) {
    var banco = $(elemento).data("banco");
    var numero = $(elemento).data("numero");
    var cuentasBancarias = $("input[name='cuentasBancarias']").val();
    $("input[name='cuentasBancarias']").val(cuentasBancarias.replace(banco + "|" + numero + ";", ""));

    $(elemento).closest(".form-group").remove();
  }

  return {
    eliminar: eliminar
  };
}());