var seccionPaso1 = new function () {
  this.cargar = function (contenedor) {
    $(contenedor).on('click', ".btn-entidad", function () {
      $(contenedor).find(".btn-entidad").removeClass("btn-activo");
      $(this).addClass("btn-activo");
      var entidad = $(this).attr("rel");
      util.listarCampos(entidad, function (campos) {
        motor.entidadSel[entidad] = {};
        motor.entidadSel[entidad].campos = campos;
        motor.entidadSel[entidad].camposSel = [];
        motor.siguienteSeccion();
      });
      util.listarEntidadesRelacionadas(entidad, function (entidadesRelacionadas) {
        motor.entidadesRelacionadas = entidadesRelacionadas;
      });
    });
  };
  this.preMostrar = function (contenedor) {
    $("#sec-titulo").text("Seleccione una de las entidades");
    motor.entidadSel = [];
    motor.entidadesRelacionadas = [];
    motor.entidadesRelacionadasSel = [];
    motor.cambioEntidad = true;
    $("#btn-anterior, #btn-siguiente, #btn-guardar").hide();
    $(contenedor).find(".btn-entidad").removeClass("btn-activo");
    return true;
  };
  this.preOcultar = function () {
    return true;
  };
};
var seccionPaso2 = new function () {
  this.cargar = function (contenedor) {
    $(contenedor).on('click', "[id*='cb-campo-']", function () {
      var camposSel = motor.entidadSel[Object.keys(motor.entidadSel)[0]].camposSel;
      if ($(this).is(':checked'))
        camposSel.push($(this).val());
      else
        camposSel.splice(camposSel.indexOf($(this).val()), 1);
    });
  };
  this.preMostrar = function (contenedor, solicitudSiguiente) {
    if (Object.keys(motor.entidadSel).length) {
      $("#sec-titulo").text("Seleccione los campos");
      if (solicitudSiguiente) {
        $(contenedor).find("#sec-campos").html("");
        $.each(motor.entidadSel[Object.keys(motor.entidadSel)[0]].campos, function (id, ele) {
          $(contenedor).find("#sec-campos").append(
              '<div class="col-sm-3">' +
              '<input id="cb-campo-' + id.toLowerCase() + '" type="checkbox" name="campos" value="' + id + '"> ' +
              '<label for="cb-campo-' + id.toLowerCase() + '">' + ele.titulo + '</label>' +
              '</div>');
        });
      }
      $("#btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();
      return true;
    } else {
      motor.anteriorSeccion();
      return false;
    }
  };
  this.preOcultar = function (contenedor, solicitudSiguiente) {
    if (!$(contenedor).find("input[name='campos']:checked").length && solicitudSiguiente) {
      agregarMensaje("advertencias", "Debe seleccionar por lo menos un campo.", true, "#sec-men-alerta", true);
      return false;
    }
    return true;
  };
};
var seccionPaso3 = new function () {
  this.cargar = function (contenedor) {
    var self = this;
    $(contenedor).on('click', ".btn-entidad", function () {
      var entidadRelacionada = $(this).attr("rel");
      if ($(this).hasClass("btn-activo")) {
        delete motor.entidadesRelacionadasSel[entidadRelacionada];
        $(this).removeClass("btn-activo");
        self.mostrarDatosEntidadesRelacionadas(contenedor);
      } else {
        var boton = this;
        util.listarCampos(entidadRelacionada, function (campos) {
          motor.entidadesRelacionadasSel[entidadRelacionada] = {};
          motor.entidadesRelacionadasSel[entidadRelacionada].campos = campos;
          motor.entidadesRelacionadasSel[entidadRelacionada].camposSel = [];
          motor.entidadesRelacionadasSel[entidadRelacionada].tipoSel = "cantidad-total";
          $(boton).addClass("btn-activo");
          self.mostrarDatosEntidadesRelacionadas(contenedor);
        });
      }
    });
    $(contenedor).on('click', "[name*='cb-tipo-seleccion-']", function () {
      if ($(this).val() !== "campos")
        $("#sec-campos-" + $(this).attr("name").replace("cb-tipo-seleccion-", "")).hide();
      else
        $("#sec-campos-" + $(this).attr("name").replace("cb-tipo-seleccion-", "")).show();
      motor.entidadesRelacionadasSel[$(this).data("entidad")].tipoSel = $(this).val();
    });
    $(contenedor).on('click', "[id*='cb-campo-']", function () {
      var camposSel = motor.entidadesRelacionadasSel[$(this).data("entidad")].camposSel;
      if ($(this).is(':checked'))
        camposSel.push($(this).val());
      else
        camposSel.splice(camposSel.indexOf($(this).val()), 1);
    });
  };
  this.preMostrar = function (contenedor, solicitudSiguiente) {
    if (Object.keys(motor.entidadesRelacionadas).length) {
      $("#sec-titulo").text("Seleccione las entidad relacionadas (opcional)");
      if (motor.cambioEntidad) {
        motor.cambioEntidad = false;
        $(contenedor).find("#sec-entidades-relacionadas").html("");
        $.each(motor.entidadesRelacionadas, function (id, ele) {
          $(contenedor).find("#sec-entidades-relacionadas").append(
              '<div class="col-sm-3">' +
              '<button type="button" class="btn-entidad" rel="' + id + '">' + ele[4] + ' ' + ele[0] + '</button>' +
              '</div>');
        });
        this.mostrarDatosEntidadesRelacionadas(contenedor);
      }
      $("#btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();
      return true;
    } else {
      (solicitudSiguiente ? motor.siguienteSeccion() : motor.anteriorSeccion());
      return false;
    }
  };
  this.preOcultar = function () {
    return true;
  };
  this.mostrarDatosEntidadesRelacionadas = function (contenedor) {
    $(contenedor).find("#sec-campos-entidades-relacionadas").html("");
    if (Object.keys(motor.entidadesRelacionadasSel).length) {
      var contenido = "";
      for (var entidad in motor.entidadesRelacionadasSel) {
        if (entidad) {
          var campos = motor.entidadesRelacionadasSel[entidad].campos;
          var tituloEntidad = motor.entidadesRelacionadas[entidad][0];
          var titulo = '<h5>Campos - ' + tituloEntidad + '</h5>';
          var contenidoCampos = '<div class="col-sm-12">' +
              '<input type="radio" id="cb-tipo-seleccion-' + entidad.toLowerCase() + '-cantidad-total" name="cb-tipo-seleccion-' + entidad.toLowerCase() + '" data-entidad="' + entidad + '" value="cantidad-total" checked>' +
              '<label for="cb-tipo-seleccion-' + entidad.toLowerCase() + '-cantidad-total">Selecionar cantidad total de ' + tituloEntidad.toLowerCase() + '</label><br>' +
              '<input type="radio" id="cb-tipo-seleccion-' + entidad.toLowerCase() + '-campos" name="cb-tipo-seleccion-' + entidad.toLowerCase() + '" data-entidad="' + entidad + '" value="campos">' +
              '<label for="cb-tipo-seleccion-' + entidad.toLowerCase() + '-campos">Seleccionar campos especificos</label>' +
              '</div><div id="sec-campos-' + entidad.toLowerCase() + '" class="col-sm-12" style="margin-top: 10px; display: none">';
          $.each(campos, function (id, ele) {
            if (id !== "tipoSel" && id !== "camposSel") {
              contenidoCampos += '<div class="col-sm-3">' +
                  '<input id="cb-campo-' + entidad.toLowerCase() + '-' + id.toLowerCase() + '" type="checkbox" name="campos-' + entidad.toLowerCase() + '" data-entidad="' + entidad + '" value="' + id + '"> ' +
                  '<label for="cb-campo-' + entidad.toLowerCase() + '-' + id.toLowerCase() + '">' + ele.titulo + '</label>' +
                  '</div>';
            }
          });
          contenido += '<div class="form-group"><div class="col-sm-12">' + titulo + contenidoCampos + '</div></div></div>';
        }
      }
      $(contenedor).find("#sec-campos-entidades-relacionadas").html(contenido);
    }
  };
};
var seccionPaso4 = new function () {
  this.cargar = function () {};
  this.preMostrar = function (contenedor) {
    var self = this;
    $("#sec-titulo").text("Ingrese los filtros (opcional)");

    var idSeccion = "#sec-filtro";
    var entidad = Object.keys(motor.entidadSel)[0];
    var campos = motor.entidadSel[entidad].campos;
    var camposSel = motor.entidadSel[Object.keys(motor.entidadSel)[0]].camposSel;

    $(contenedor).find(idSeccion).html("");
    self.procesarFiltros(contenedor, idSeccion, entidad, campos, camposSel);
    for (var entidadRel in motor.entidadesRelacionadasSel) {
      var datos = motor.entidadesRelacionadasSel[entidadRel];
      if (datos.tipoSel === "campos") {
        var tituloEntidad = motor.entidadesRelacionadas[entidadRel][0];
        $(idSeccion).append('<div class="col-sm-12"><h4>Filtros - ' + tituloEntidad + '</h4>');
        self.procesarFiltros(contenedor, idSeccion, entidadRel, datos.campos, datos.camposSel);
        $(idSeccion).append('</div>');
      }
    }
    $("#btn-guardar").hide();
    $("#btn-siguiente, #btn-anterior").show();
    return true;
  };
  this.preOcultar = function () {
    return true;
  };
  this.procesarFiltros = function (contenedor, idSeccion, entidad, campos, camposSel) {
    var self = this;
    tiposSexos = (typeof (tiposSexos) === "undefined" ? [] : tiposSexos);
    tiposDocumentos = (typeof (tiposDocumentos) === "undefined" ? [] : tiposDocumentos);
    $.each(camposSel, function (num, id) {
      if (!campos[id].tipo)
        return true;
      if ((["varchar", "text"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroTexto(contenedor, idSeccion, entidad, campos, id);
      else if ((["int", "float"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroNumero(contenedor, idSeccion, entidad, campos, id);
      else if ((["datetime", "timestamp"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroFecha(contenedor, idSeccion, entidad, campos, id);
      else if ((["tinyint"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroVerdaderoFalso(contenedor, idSeccion, entidad, campos, id);
      else if ((["sexo"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroListaOpciones(contenedor, idSeccion, entidad, campos, id, tiposSexos);
      else if ((["tipodocumento"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroListaOpciones(contenedor, idSeccion, entidad, campos, id, tiposDocumentos);
    });
  };
  this.agregarFiltroTexto = function (contenedor, idSeccion, entidad, campos, idCampo) {
    $(contenedor).find(idSeccion).append(
        '<div class="form-group">' +
        '<label class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select name="sel-filtro-' + entidad.toLowerCase() + '-' + idCampo.toLowerCase() + '" class="form-control">' +
        '<option value="=">Igual a</option>' +
        '<option value="LIKE">Contiene</option>' +
        '<option value="NOT LIKE">No contiene</option>' +
        '</select>' +
        '</div>' +
        '<div class="col-sm-8">' +
        '<input type="text" name="inp-filtro-' + entidad.toLowerCase() + '-' + idCampo.toLowerCase() + '" class="form-control" />' +
        '</div>' +
        '</div>');
  };
  this.agregarFiltroNumero = function (contenedor, idSeccion, entidad, campos, idCampo) {
    $(contenedor).find(idSeccion).append(
        '<div class="form-group">' +
        '<label class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select name="sel-filtro-' + entidad.toLowerCase() + '-' + idCampo.toLowerCase() + '" class="form-control">' +
        '<option value="=">Igual a</option>' +
        '<option value="LIKE">Contiene</option>' +
        '<option value="NOT LIKE">No contiene</option>' +
        '<option value=">">Mayor a</option>' +
        '<option value=">=">Mayor o igual a</option>' +
        '<option value="<">Menor a</option>' +
        '<option value="<=">Menor o igual a</option>' +
        '</select>' +
        '</div>' +
        '<div class="col-sm-8">' +
        '<input type="number" name="inp-filtro-' + entidad.toLowerCase() + '-' + idCampo.toLowerCase() + '" class="form-control" />' +
        '</div>' +
        '</div>');
  };
  this.agregarFiltroFecha = function (contenedor, idSeccion, entidad, campos, idCampo) {
    var idFiltroSel = "sel-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltroFechaIni = "inp-filtro-fecha-inicio-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltroFechaFin = "inp-filtro-fecha-fin-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    $(contenedor).find(idSeccion).append(
        '<div class="form-group">' +
        '<label class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select id="' + idFiltroSel + '" name="' + idFiltroSel + '" class="form-control">' +
        '<option value="=">Igual a</option>' +
        '<option value=">">Mayor a</option>' +
        '<option value=">=">Mayor o igual a</option>' +
        '<option value="<">Menor a</option>' +
        '<option value="<=">Menor o igual a</option>' +
        '<option value="BETWEEN">Entre</option>' +
        '</select>' +
        '</div>' +
        '<div class="col-sm-2">' +
        '<input type="text" id="' + idFiltroFechaIni + '" name="' + idFiltroFechaIni + '" class="form-control" />' +
        '</div>' +
        '<div class="col-sm-2">' +
        '<input type="text" id="' + idFiltroFechaFin + '" name="' + idFiltroFechaFin + '" class="form-control" style="display:none" />' +
        '</div>' +
        '</div>');

    $(contenedor).find(idSeccion).on('change', "#" + idFiltroSel, function () {
      if ($(this).val() !== "BETWEEN")
        $("#" + idFiltroFechaFin).hide();
      else
        $("#" + idFiltroFechaFin).show();
    });
    establecerCalendario(idFiltroFechaIni);
    establecerCalendario(idFiltroFechaFin);
  };
  this.agregarFiltroVerdaderoFalso = function (contenedor, idSeccion, entidad, campos, idCampo) {
    var idFiltro = "cb-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    $(contenedor).find(idSeccion).append(
        '<div class="col-sm-1"></div>' +
        '<div class="col-sm-11">' +
        '<div class="checkbox">' +
        '<label class="checkbox-custom" data-initialize="checkbox">' +
        '<label for="' + idFiltro + '" class="checkbox-label">' + campos[idCampo].titulo + '</label>' +
        '<input id="' + idFiltro + '" name="' + idFiltro + '" type="checkbox">' +
        '</label>' +
        '</div>' +
        '</div>');
  };
  this.agregarFiltroListaOpciones = function (contenedor, idSeccion, entidad, campos, idCampo, listaOpciones) {
    var contenidoOpciones = '';
    $.each(listaOpciones, function (id, val) {
      contenidoOpciones += '<option value="' + id + '">' + val + '</option>';
    });
    $(contenedor).find(idSeccion).append(
        '<div class="form-group">' +
        '<label class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select name="sel-filtro-' + entidad.toLowerCase() + '-' + idCampo.toLowerCase() + '" class="form-control">' +
        contenidoOpciones +
        '</select>' +
        '</div>' +
        '</div>');
  };
};
var seccionPaso5 = new function () {
  this.cargar = function () {};
  this.preMostrar = function () {
    $("#sec-titulo").text("Ingrese los datos finales");
    $("#btn-siguiente").hide();
    $("#btn-guardar, #btn-anterior").show();
    return true;
  };
  this.preOcultar = function () {
    return true;
  };
};

var motor = new function () {
  this.seccionActual = 0;

  this.entidadSel = [];
  this.entidadesRelacionadas = [];
  this.entidadesRelacionadasSel = [];
  this.cambioEntidad = true;

  this.mostrarSeccion = function (solicitudSiguiente) {
    var seccion = $("#sec-paso-" + this.seccionActual);
    if (seccion.length) {
      var permitido = true;
      if (window["seccionPaso" + this.seccionActual])
        permitido = window["seccionPaso" + this.seccionActual].preMostrar(seccion, solicitudSiguiente);
      if (permitido)
        $("#sec-paso-" + this.seccionActual).show();
      else
        return false;
    }
    return true;
  };
  this.ocultarSeccion = function (solicitudSiguiente) {
    var seccion = $("#sec-paso-" + this.seccionActual);
    var permitido = true;
    if (seccion.length) {
      if (window["seccionPaso" + this.seccionActual])
        permitido = window["seccionPaso" + this.seccionActual].preOcultar(seccion, solicitudSiguiente);
      if (permitido)
        $("#sec-paso-" + this.seccionActual).hide();
      else
        return false;
    }
    return true;
  };
  this.anteriorSeccion = function () {
    if (this.seccionActual <= 1)
      return;
    $("#sec-men-alerta").html("");
    if (this.ocultarSeccion()) {
      this.seccionActual--;
      this.mostrarSeccion();
    }
  };
  this.siguienteSeccion = function () {
    if (this.seccionActual >= $("[id*='sec-paso-']").length)
      return;
    $("#sec-men-alerta").html("");
    if (this.ocultarSeccion(true)) {
      this.seccionActual++;
      this.mostrarSeccion(true);
    }
  };

  var self = this;
  this.Inicializar = function () {
    $("[id*='sec-paso-']").each(function () {
      if (window["seccionPaso" + $(this).attr("id").replace("sec-paso-", "")])
        window["seccionPaso" + $(this).attr("id").replace("sec-paso-", "")].cargar($(this));
    });
    $("#btn-siguiente").click(function () {
      self.siguienteSeccion();
    });
    $("#btn-anterior").click(function () {
      self.anteriorSeccion();
    });
    this.siguienteSeccion();
  };
  $(function () {
    self.Inicializar();
  });
};
var util = new function () {
  this.listarCampos = function (entidad, retrollamada) {
    urlListarCampos = (typeof (urlListarCampos) === "undefined" ? "" : urlListarCampos);
    if (urlListarCampos !== "") {
      $.blockUI({message: "<h4>Cargando datos...</h4>"});
      llamadaAjax(urlListarCampos, "POST", {entidad: entidad}, true,
          function (campos) {
            $("body").unblock({
              onUnblock: function () {
                retrollamada(campos);
              }
            });
          },
          function () {
          },
          function () {
            $("body").unblock({
              onUnblock: function () {
                agregarMensaje("errores", "Ocurrió un problema durante la obtención de campos por favor intente nuevamente.", true, "#sec-men-alerta", true);
              }
            });


          }
      );
    }
  };
  this.listarEntidadesRelacionadas = function (entidad, retrollamada) {
    urlListarEntidadesRelacionadas = (typeof (urlListarEntidadesRelacionadas) === "undefined" ? "" : urlListarEntidadesRelacionadas);
    if (urlListarEntidadesRelacionadas !== "") {
      llamadaAjax(urlListarEntidadesRelacionadas, "POST", {entidad: entidad}, true,
          function (datos) {
            retrollamada(datos);
          },
          function () {
          },
          function () {
            retrollamada(null);
          }
      );
    }
  };
};

