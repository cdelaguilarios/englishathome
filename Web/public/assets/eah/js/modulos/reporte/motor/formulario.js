var seccionPaso1 = new function () {
  this.titulo = "Seleccione una de las entidades";
  this.cargar = function (contenedor) {
    var self = this;
    $(contenedor).on('click', ".btn-entidad", function () {
      self.seleccionarEntidad(contenedor, $(this).attr("rel"));
    });
  };
  this.preMostrar = function () {
    $("#sec-titulo").text(this.titulo);
    $("#sec-mensaje-campos-obligatorios, #btn-anterior, #btn-siguiente, #btn-guardar").hide();
    if (motor.entidadSel.nombre)
      $("#btn-siguiente").show();
    return true;
  };
  this.preOcultar = function () {
    return true;
  };
  this.seleccionarEntidad = function (contenedor, nombreEntidad, retrollamada) {
    $(contenedor).find(".btn-entidad").removeClass("btn-activo");
    $(contenedor).find(".btn-entidad[rel='" + nombreEntidad + "']").addClass("btn-activo");
    if (motor.entidadSel.nombre !== nombreEntidad) {
      motor.cambioEntidad();
      util.listarCampos(nombreEntidad, function (campos) {
        motor.entidadSel = {
          nombre: nombreEntidad,
          campos: campos,
          camposSel: []
        };
        $("#btn-siguiente").show();
        if (retrollamada)
          retrollamada();
      });
      util.listarEntidadesRelacionadas(nombreEntidad, function (entidadesRelacionadas) {
        motor.entidadesRelacionadas = entidadesRelacionadas;
      });
    } else {
      $("#btn-siguiente").show();
      if (retrollamada)
        retrollamada();
    }
  };
};
var seccionPaso2 = new function () {
  this.titulo = "Seleccione los campos";
  this.cambioEntidad = true;
  this.cargar = function (contenedor) {
    $(contenedor).on('click', "[id*='cb-campo-']", function () {
      var camposSel = motor.entidadSel.camposSel;
      (($(this).is(':checked')) ? camposSel.push($(this).val()) : camposSel.splice(camposSel.indexOf($(this).val()), 1));
    });
  };
  this.preMostrar = function (contenedor) {
    if (Object.keys(motor.entidadSel).length) {
      $("#sec-titulo").text(this.titulo);
      $("#sec-mensaje-campos-obligatorios, #btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();
      if (this.cambioEntidad)
        this.agregarCampos(contenedor);
      return true;
    } else {
      motor.anteriorSeccion();
      return false;
    }
  };
  this.preOcultar = function (contenedor, solicitudSiguiente) {
    if (solicitudSiguiente && !$(contenedor).find("input[name='campos']:checked").length) {
      agregarMensaje("advertencias", "Debe seleccionar por lo menos un campo.", true, "#sec-mensajes-alerta", true);
      return false;
    }
    return true;
  };
  this.agregarCampos = function (contenedor, camposSeleccionados) {
    this.cambioEntidad = false;
    $(contenedor).find("#sec-campos").html("");
    $.each(motor.entidadSel.campos, function (nombreCampo, datosCampo) {
      var txtCampoSeleccionado = '';
      if (camposSeleccionados) {
        var datCampoSel = $.grep(camposSeleccionados, function (campoSeleccionado) {
          return campoSeleccionado.nombre.toLowerCase() === nombreCampo.toLowerCase();
        });
        if (datCampoSel.length) {
          txtCampoSeleccionado = ' checked';
          motor.entidadSel.camposSel.push(nombreCampo);
        }
      }
      $(contenedor).find("#sec-campos").append(
          '<div class="col-sm-3">' +
          '<input id="cb-campo-' + nombreCampo.toLowerCase() + '" type="checkbox" name="campos" value="' + nombreCampo + '"' + txtCampoSeleccionado + '>' +
          '<label for="cb-campo-' + nombreCampo.toLowerCase() + '">' + datosCampo.titulo + '</label>' +
          '</div>');
    });
  };
};
var seccionPaso3 = new function () {
  this.titulo = "Seleccione las entidad relacionadas (opcional)";
  this.cambioEntidad = true;
  this.cargar = function (contenedor) {
    var self = this;
    $(contenedor).on('click', ".btn-entidad", function () {
      var nombreEntidadRelacionada = $(this).attr("rel");
      if ($(this).hasClass("btn-activo")) {
        $(this).removeClass("btn-activo");
        motor.entidadesRelacionadasSel = motor.entidadesRelacionadasSel.filter(function (entidadRelacionadaSel) {
          return entidadRelacionadaSel.nombre !== nombreEntidadRelacionada;
        });
        $("#sec-contenedor-campos-" + nombreEntidadRelacionada.toLowerCase()).remove();
        $("#sec-filtros-" + nombreEntidadRelacionada.toLowerCase()).remove();
      } else {
        self.seleccionarEntidadRelacionada(contenedor, nombreEntidadRelacionada);
      }
    });
    $(contenedor).on('click', "[name*='cb-tipo-seleccion-']", function () {
      var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada($(this).data("entidad"));
      $("#sec-campos-" + $(this).data("entidad").toLowerCase()).css('display', ($(this).val() !== "campos" ? "none" : "inline-block"));
      entidadRelacionadaSel.tipoSel = $(this).val();
    });
    $(contenedor).on('click', "[id*='cb-campo-']", function () {
      var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada($(this).data("entidad"));
      var camposSel = entidadRelacionadaSel.camposSel;
      (($(this).is(':checked')) ? camposSel.push($(this).val()) : camposSel.splice(camposSel.indexOf($(this).val()), 1));
    });
  };
  this.preMostrar = function (contenedor, solicitudSiguiente) {
    if (Object.keys(motor.entidadesRelacionadas).length) {
      $("#sec-titulo").text(this.titulo);
      $("#sec-mensaje-campos-obligatorios, #btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();
      if (this.cambioEntidad)
        this.agregarEntidadesRelacionadas(contenedor);
      return true;
    } else {
      (solicitudSiguiente ? motor.siguienteSeccion() : motor.anteriorSeccion());
      return false;
    }
  };
  this.preOcultar = function () {
    return true;
  };
  this.agregarEntidadesRelacionadas = function (contenedor) {
    this.cambioEntidad = false;
    $(contenedor).find("#sec-entidades-relacionadas").html("");
    $(contenedor).find("#sec-campos-entidades-relacionadas").html("");
    $.each(motor.entidadesRelacionadas, function (nombreEntidadRel, datosEntidadRel) {
      $(contenedor).find("#sec-entidades-relacionadas").append(
          '<div class="col-sm-3">' +
          '<button type="button" class="btn-entidad" rel="' + nombreEntidadRel + '">' + datosEntidadRel[5] + ' ' + datosEntidadRel[0] + '</button>' +
          '</div>');
    });
  };
  this.seleccionarEntidadRelacionada = function (contenedor, nombreEntidadRelacionada, camposSeleccionados, retrollamada) {
    var self = this;
    util.listarCampos(nombreEntidadRelacionada, function (campos) {
      motor.entidadesRelacionadasSel.push({
        nombre: nombreEntidadRelacionada,
        campos: campos,
        camposSel: [],
        tipoSel: (camposSeleccionados ? "campos" : "cantidad-total")
      });
      self.mostrarCamposEntidadRelacionada(contenedor, nombreEntidadRelacionada, camposSeleccionados);
      $(contenedor).find(".btn-entidad[rel='" + nombreEntidadRelacionada + "']").addClass("btn-activo");
      if (retrollamada)
        retrollamada();
    });
  };
  this.mostrarCamposEntidadRelacionada = function (contenedor, nombreEntidadRelacionada, camposSeleccionados) {
    var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada(nombreEntidadRelacionada);
    var campos = entidadRelacionadaSel.campos;
    var tituloEntidad = motor.entidadesRelacionadas[nombreEntidadRelacionada][0];
    var titulo = '<h5>Campos - ' + tituloEntidad + '</h5>';

    var idTipoSelBase = "cb-tipo-seleccion-" + nombreEntidadRelacionada.toLowerCase();
    var idTipoSelCantidadTot = idTipoSelBase + "-cantidad-total";
    var idTipoSelCampos = idTipoSelBase + "-campos";

    var contenidoCampos = '<div class="col-sm-12">' +
        '<input type="radio" id="' + idTipoSelCantidadTot + '" name="' + idTipoSelBase + '" data-entidad="' + nombreEntidadRelacionada + '" value="cantidad-total"' + (camposSeleccionados ? '' : ' checked') + '>' +
        '<label for="' + idTipoSelCantidadTot + '">Selecionar cantidad total de ' + tituloEntidad.toLowerCase() + '</label><br>' +
        '<input type="radio" id="' + idTipoSelCampos + '" name="' + idTipoSelBase + '" data-entidad="' + nombreEntidadRelacionada + '" value="campos"' + (camposSeleccionados ? ' checked' : '') + '>' +
        '<label for="' + idTipoSelCampos + '">Seleccionar campos especificos</label>' +
        '</div>' +
        '<div id="sec-campos-' + nombreEntidadRelacionada.toLowerCase() + '" class="col-sm-12" style="margin-top: 10px;' + (camposSeleccionados ? '' : 'display: none;') + '">';
    $.each(campos, function (nombreCampo, datosCampo) {
      var txtCampoSeleccionado = "";
      if (camposSeleccionados) {
        var datCampoSel = $.grep(camposSeleccionados, function (e) {
          return e.nombre.toLowerCase() === nombreCampo.toLowerCase();
        });
        if (datCampoSel.length) {
          txtCampoSeleccionado = ' checked';
          entidadRelacionadaSel.camposSel.push(nombreCampo);
        }
      }
      if (nombreCampo !== "tipoSel" && nombreCampo !== "camposSel") {
        var idCampo = "cb-campo-" + nombreEntidadRelacionada.toLowerCase() + "-" + nombreCampo.toLowerCase();
        contenidoCampos += '<div class="col-sm-3">' +
            '<input id="' + idCampo + '" type="checkbox" name="campos-' + nombreEntidadRelacionada.toLowerCase() + '" data-entidad="' + nombreEntidadRelacionada + '" value="' + nombreCampo + '"' + txtCampoSeleccionado + '> ' +
            '<label for="' + idCampo + '">' + datosCampo.titulo + '</label>' +
            '</div>';
      }
    });
    contenidoCampos += '</div>';
    $(contenedor).find("#sec-campos-entidades-relacionadas").append('<div id="sec-contenedor-campos-' + nombreEntidadRelacionada.toLowerCase() + '" class="form-group"><div class="col-sm-12">' + titulo + contenidoCampos + '</div></div>');
  };
};
var seccionPaso4 = new function () {
  this.titulo = "Ingrese los filtros";
  this.cambioEntidad = true;
  this.camposCargados = [];
  this.cargar = function () {};
  this.preMostrar = function (contenedor, solicitudSiguiente) {
    $("#sec-titulo").text(this.titulo);
    $("#btn-guardar").hide();
    $("#sec-mensaje-campos-obligatorios, #btn-siguiente, #btn-anterior").show();
    if (!solicitudSiguiente)
      return true;
    this.agregarFiltros(contenedor);
    return true;
  };
  this.preOcultar = function (contenedor, solicitudSiguiente) {
    return !(solicitudSiguiente && !$(contenedor).find(":input, select").valid());
  };
  this.agregarFiltros = function (contenedor, datos) {
    var self = this;
    var idSeccion = "#sec-filtros";
    var nombreEntidad = motor.entidadSel.nombre;
    var campos = motor.entidadSel.campos;
    var camposSel = motor.entidadSel.camposSel;

    if (this.cambioEntidad) {
      this.cambioEntidad = false;
      this.camposCargados = [];
      $(contenedor).find(idSeccion).html("");
    }
    self.procesarFiltros(contenedor, idSeccion, nombreEntidad, campos, camposSel, (datos ? datos.camposSeleccionados : null));
    motor.entidadesRelacionadasSel.forEach(function (datosEntidadRel)
    {
      var idSeccionEntidadRel = "#sec-filtros-" + datosEntidadRel.nombre.toLowerCase();
      if (datosEntidadRel.tipoSel === "campos") {
        var datosCamposSel = null;
        if (datos && datos.entiadesRelacionadas) {
          var datEntidadRelacionada = $.grep(datos.entiadesRelacionadas, function (e) {
            return e.entidad.toLowerCase() === datosEntidadRel.nombre.toLowerCase();
          });
          if (datEntidadRelacionada.length)
            datosCamposSel = datEntidadRelacionada[0].camposSeleccionados;
        }
        var seccionAgregada = (self.camposCargados[datosEntidadRel.nombre.toLowerCase()] !== undefined);
        if (!seccionAgregada) {
          var tituloEntidad = motor.entidadesRelacionadas[datosEntidadRel.nombre][0];
          $(contenedor).find(idSeccion).append('<div id="' + idSeccionEntidadRel.replace("#", "") + '" class="col-sm-12">' +
              '<h4>Filtros - ' + tituloEntidad + '</h4>');
        }
        self.procesarFiltros(contenedor, idSeccionEntidadRel, datosEntidadRel.nombre, datosEntidadRel.campos, datosEntidadRel.camposSel, datosCamposSel);
        if (!seccionAgregada) {
          self.agregarFiltroBusqueda(contenedor, idSeccionEntidadRel, datosEntidadRel.nombre, datosCamposSel);
          $(contenedor).find(idSeccion).append('</div>');
        }
      } else {
        var reglasValidacion = $("#formulario-reporte").validate().settings.rules;
        for (var campo in reglasValidacion)
          if (campo.indexOf("-" + datosEntidadRel.nombre.toLowerCase() + "-") >= 0)
            delete reglasValidacion[campo];

        $(idSeccionEntidadRel).remove();
        if (self.camposCargados[datosEntidadRel.nombre.toLowerCase()] !== undefined)
          self.camposCargados[datosEntidadRel.nombre.toLowerCase()] = undefined;
      }
    });
  };
  this.procesarFiltros = function (contenedor, idSeccion, nombreEntidad, campos, camposSel, datosCamposSel) {
    var self = this;
    tiposSexos = (typeof (tiposSexos) === "undefined" ? [] : tiposSexos);
    tiposDocumentos = (typeof (tiposDocumentos) === "undefined" ? [] : tiposDocumentos);
    this.camposCargados[nombreEntidad.toLowerCase()] = (this.camposCargados[nombreEntidad.toLowerCase()] !== undefined ? this.camposCargados[nombreEntidad.toLowerCase()] : []);
    $.each(camposSel, function (num, id) {
      if (!campos[id].tipo)
        return true;
      if (self.camposCargados[nombreEntidad.toLowerCase()].indexOf(id) !== -1)
        return true;
      self.camposCargados[nombreEntidad.toLowerCase()].push(id);
      if ((["varchar", "text", "char"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroTexto(contenedor, idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["int", "float"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroNumero(contenedor, idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["datetime", "timestamp"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroFecha(contenedor, idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["tinyint"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroVerdaderoFalso(contenedor, idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["sexo"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroListaOpciones(contenedor, idSeccion, nombreEntidad, campos, id, tiposSexos, datosCamposSel);
      else if ((["tipodocumento"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroListaOpciones(contenedor, idSeccion, nombreEntidad, campos, id, tiposDocumentos, datosCamposSel);
    });

    var camposEli = $(this.camposCargados[nombreEntidad.toLowerCase()]).not(camposSel).get();
    $.each(camposEli, function (num, id) {
      $("#sec-filtro-" + nombreEntidad.toLowerCase() + "-" + id.toLowerCase()).remove();
      self.camposCargados[nombreEntidad.toLowerCase()].splice(self.camposCargados[nombreEntidad.toLowerCase()].indexOf(id), 1);
    });
  };
  this.agregarFiltroTexto = function (contenedor, idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();


    $(contenedor).find(idSeccion).append(
        '<div id="' + idContenedor + '" class="form-group">' +
        '<label for="' + idFiltro + '" class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select name="' + idSelTipo + '" class="form-control">' +
        '<option value="=">Igual a</option>' +
        '<option value="<>">Diferente a</option>' +
        '<option value="LIKE">Contiene</option>' +
        '<option value="NOT LIKE">No contiene</option>' +
        '</select>' +
        '</div>' +
        '<div class="col-sm-8">' +
        '<input type="text" id="' + idFiltro + '" name="' + idFiltro + '" class="form-control" maxlength="255" />' +
        '</div>' +
        '</div>');

    if (datosCamposSel) {
      var datCampo = $.grep(datosCamposSel, function (e) {
        return e.nombre.toLowerCase() === idCampo.toLowerCase();
      });
      if (datCampo.length) {
        var datFiltro = datCampo[0].filtro;
        $("select[name='" + idSelTipo + "']").val(datFiltro.tipo);
        $("#" + idFiltro).val(datFiltro.valores[0]);
      }
    }
  };
  this.agregarFiltroNumero = function (contenedor, idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    $(contenedor).find(idSeccion).append(
        '<div id="' + idContenedor + '" class="form-group">' +
        '<label for="' + idFiltro + '" class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select name="' + idSelTipo + '" class="form-control">' +
        '<option value="=">Igual a</option>' +
        '<option value="<>">Diferente a</option>' +
        '<option value="LIKE">Contiene</option>' +
        '<option value="NOT LIKE">No contiene</option>' +
        '<option value=">">Mayor a</option>' +
        '<option value=">=">Mayor o igual a</option>' +
        '<option value="<">Menor a</option>' +
        '<option value="<=">Menor o igual a</option>' +
        '</select>' +
        '</div>' +
        '<div class="col-sm-8">' +
        '<input type="number" id="' + idFiltro + '" name="' + idFiltro + '" class="form-control" maxlength="19" />' +
        '</div>' +
        '</div>');
    $("#" + idFiltro).rules("add", {
      validarDecimal: true
    });

    if (datosCamposSel) {
      var datCampo = $.grep(datosCamposSel, function (e) {
        return e.nombre.toLowerCase() === idCampo.toLowerCase();
      });
      if (datCampo.length) {
        var datFiltro = datCampo[0].filtro;
        $("select[name='" + idSelTipo + "']").val(datFiltro.tipo);
        $("#" + idFiltro).val(datFiltro.valores[0]);
      }
    }
  };
  this.agregarFiltroFecha = function (contenedor, idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltroFechaIni = "inp-filtro-fecha-inicio-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltroFechaFin = "inp-filtro-fecha-fin-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    $(contenedor).find(idSeccion).append(
        '<div id="' + idContenedor + '" class="form-group">' +
        '<label for="' + idFiltroFechaIni + '" class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select id="' + idSelTipo + '" name="' + idSelTipo + '" class="form-control">' +
        '<option value="=">Igual a</option>' +
        '<option value="<>">Diferente a</option>' +
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

    $(contenedor).find(idSeccion).on('change', "#" + idSelTipo, function () {
      ($(this).val() !== "BETWEEN" ? $("#" + idFiltroFechaFin).hide() : $("#" + idFiltroFechaFin).show());
    });
    establecerCalendario(idFiltroFechaIni);
    establecerCalendario(idFiltroFechaFin);

    $("#" + idFiltroFechaIni).rules("add", {
      validarFecha: true
    });
    $("#" + idFiltroFechaFin).rules("add", {
      validarFecha: true
    });

    if (datosCamposSel) {
      var datCampo = $.grep(datosCamposSel, function (e) {
        return e.nombre.toLowerCase() === idCampo.toLowerCase();
      });
      if (datCampo.length) {
        var datFiltro = datCampo[0].filtro;
        $("select[name='" + idSelTipo + "']").val(datFiltro.tipo);

        var datFechaInicio = datFiltro.valores[0].split("/");
        $("#" + idFiltroFechaIni).datepicker("setDate", (new Date(datFechaInicio[1] + "/" + datFechaInicio[0] + "/" + datFechaInicio[2])));
        if (datFiltro.valores.length > 1) {
          var datFechaFin = datFiltro.valores[1].split("/");
          $("#" + idFiltroFechaFin).datepicker("setDate", (new Date(datFechaFin[1] + "/" + datFechaFin[0] + "/" + datFechaFin[2])));
        }
      }
    }
  };
  this.agregarFiltroVerdaderoFalso = function (contenedor, idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    var txtSeleccionado = "";
    if (datosCamposSel) {
      var datCampo = $.grep(datosCamposSel, function (e) {
        return e.nombre.toLowerCase() === idCampo.toLowerCase();
      });
      if (datCampo.length && datCampo[0].filtro.valores[0] === "on")
        txtSeleccionado = " checked";
    }

    $(contenedor).find(idSeccion).append(
        '<div id="' + idContenedor + '" class="form-group">' +
        '<div class="col-sm-1"></div>' +
        '<div class="col-sm-11">' +
        '<div class="checkbox">' +
        '<label class="checkbox-custom' + txtSeleccionado + '" data-initialize="checkbox">' +
        '<label for="' + idFiltro + '" class="checkbox-label">' + campos[idCampo].titulo + '</label>' +
        '<input id="' + idFiltro + '" name="' + idFiltro + '" type="checkbox"' + txtSeleccionado + '>' +
        '</label>' +
        '</div>' +
        '</div>' +
        '</div>');
  };
  this.agregarFiltroListaOpciones = function (contenedor, idSeccion, entidad, campos, idCampo, listaOpciones, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    var contenidoOpciones = '';
    $.each(listaOpciones, function (id, val) {
      contenidoOpciones += '<option value="' + id + '">' + val + '</option>';
    });
    $(contenedor).find(idSeccion).append(
        '<div id="' + idContenedor + '" class="form-group">' +
        '<label for="' + idFiltro + '" class="col-sm-2 control-label">' + campos[idCampo].titulo + '</label>' +
        '<div class="col-sm-2">' +
        '<select id="' + idFiltro + '" name="' + idFiltro + '" class="form-control">' +
        contenidoOpciones +
        '</select>' +
        '</div>' +
        '</div>');

    if (datosCamposSel) {
      var datCampo = $.grep(datosCamposSel, function (e) {
        return e.nombre.toLowerCase() === idCampo.toLowerCase();
      });
      if (datCampo.length) {
        var datFiltro = datCampo[0].filtro;
        $("#" + idFiltro).val(datFiltro.valores[0]);
      }
    }
  };
  this.agregarFiltroBusqueda = function (contenedor, idSeccion, entidad, datosCamposSel) {
    if (motor.entidades[entidad][4] !== "") {
      var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-busqueda";
      var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-busqueda";

      $(contenedor).find(idSeccion).append(
          '<div class="form-group">' +
          '<label for="' + idFiltro + '" class="col-sm-2 control-label">Lista de ' + motor.entidades[entidad][0].toLowerCase() + ' (*)</label>' +
          '<div class="col-sm-2">' +
          '<select name="' + idSelTipo + '" class="form-control">' +
          '<option value="=">Igual a</option>' +
          '</select>' +
          '</div>' +
          '<div class="col-sm-8">' +
          '<select id="' + idFiltro + '" name="' + idFiltro + '[]" class="form-control" multiple="multiple" style="width: 100%"></select>' +
          '</div>' +
          '</div>');
      establecerListaBusqueda("#" + idFiltro, motor.entidades[entidad][4]);
      $("#" + idFiltro).rules("add", "required");

      if (datosCamposSel) {
        var datCampo = $.grep(datosCamposSel, function (e) {
          return e.nombre.toLowerCase() === "busqueda";
        });
        if (datCampo.length) {
          var datFiltro = datCampo[0].filtro;
          $("select[name='" + idSelTipo + "']").val(datFiltro.tipo);
          var ids = datFiltro.valores[0].split(",");
          var nombres = ((datFiltro.valores.length > 1) ? datFiltro.valores[1].split(",") : ids);
          if (ids.length === nombres.length) {
            for (var i = 0; i < ids.length; i++) {
              $("#" + idFiltro).select2("trigger", "select", {
                data: {
                  id: ids[i],
                  text: nombres[i]
                }
              });
            }
          }
        }
      }
    }
  };
};
var seccionPaso5 = new function () {
  this.titulo = "Ingrese los datos finales";
  this.cambioEntidad = true;
  this.cargar = function () {};
  this.preMostrar = function () {
    $("#sec-titulo").text(this.titulo);
    $("#btn-siguiente").hide();
    $("#sec-mensaje-campos-obligatorios, #btn-guardar, #btn-anterior").show();


    $("input[name='entidad']").val(JSON.stringify(motor.entidadSel));
    $("input[name='entidadesRelacionadas']").val(JSON.stringify(motor.entidadesRelacionadasSel));
    return true;
  };
  this.preOcultar = function () {
    return true;
  };
};

var motor = new function () {
  this.seccionActual = 0;

  this.entidades = (typeof (entidades) === "undefined" ? "" : entidades);
  this.entidadSel = {};
  this.entidadesRelacionadas = [];
  this.entidadesRelacionadasSel = [];

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
    $("#sec-mensajes-alerta").html("");
    if (this.ocultarSeccion()) {
      this.seccionActual--;
      this.mostrarSeccion();
    }
  };
  this.siguienteSeccion = function () {
    if (this.seccionActual >= $("[id*='sec-paso-']").length)
      return;
    $("#sec-mensajes-alerta").html("");
    if (this.ocultarSeccion(true)) {
      this.seccionActual++;
      this.mostrarSeccion(true);
    }
  };
  this.cambioEntidad = function () {
    $("[id*='sec-paso-']").each(function () {
      if (window["seccionPaso" + $(this).attr("id").replace("sec-paso-", "")])
        window["seccionPaso" + $(this).attr("id").replace("sec-paso-", "")].cambioEntidad = true;
    });
    this.entidadSel = {};
    this.entidadesRelacionadas = [];
    this.entidadesRelacionadasSel = [];
  };
  this.obtenerDatosEntidadRelacionada = function (nombreEntidadRel) {
    return motor.entidadesRelacionadasSel.filter(function (ent) {
      return ent.nombre.toLowerCase() === nombreEntidadRel.toLowerCase();
    })[0];
  };

  this.cargarDatos = function (datos, retrollamada) {
    var self = this;
    if (datos.entidad) {
      seccionPaso1.seleccionarEntidad($("#sec-paso-1"), datos.entidad, function () {
        seccionPaso2.agregarCampos($("#sec-paso-2"), datos.camposSeleccionados);
        seccionPaso3.agregarEntidadesRelacionadas($("#sec-paso-3"));

        if (datos.entiadesRelacionadas.length) {
          var entidadesRelacionadasCargadas = 0;
          $.each(datos.entiadesRelacionadas, function (i, datosEntidadRelacionada) {
            self.cargarDatosEntidadRelacionada($("#sec-paso-3"), datosEntidadRelacionada, function () {
              entidadesRelacionadasCargadas++;
              if (entidadesRelacionadasCargadas >= datos.entiadesRelacionadas.length) {
                seccionPaso4.agregarFiltros($("#sec-paso-4"), datos);
                if (retrollamada)
                  retrollamada();
              }
            });
          });
        } else if (retrollamada) {
          retrollamada();
        }
      });
    } else if (retrollamada) {
      retrollamada();
    }
  };
  this.cargarDatosEntidadRelacionada = function (contenedor, datosEntidadRelacionada, retrollamada) {
    if (datosEntidadRelacionada.entidad) {
      seccionPaso3.seleccionarEntidadRelacionada(contenedor, datosEntidadRelacionada.entidad, datosEntidadRelacionada.camposSeleccionados, function () {
        if (retrollamada)
          retrollamada();
      });
    } else if (retrollamada) {
      retrollamada();
    }
  };

  this.Inicializar = function () {
    var self = this;
    $("[id*='sec-paso-']").each(function () {
      if (window["seccionPaso" + $(this).attr("id").replace("sec-paso-", "")])
        window["seccionPaso" + $(this).attr("id").replace("sec-paso-", "")].cargar($(this));
    });
    $("#btn-anterior").click(function () {
      self.anteriorSeccion();
    });
    $("#btn-siguiente").click(function () {
      self.siguienteSeccion();
    });
    $("#formulario-reporte").validate({
      ignore: "",
      rules: {
        titulo: {
          required: true
        }
      },
      submitHandler: function (f) {
        if (confirm($("#btn-guardar").text().trim() === "Guardar"
            ? "¿Está seguro que desea guardar los cambios de los datos del reporte?"
            : "¿Está seguro que desea registrar los datos de este reporte?")) {
          $.blockUI({message: "<h4>" + ($("#btn-guardar").text().trim() === "Guardar" ? "Guardando" : "Registrando") + " datos...</h4>"});
          f.submit();
        }
      },
      highlight: function () {
      },
      unhighlight: function () {
      },
      errorElement: "div",
      errorClass: "help-block-error",
      errorPlacement: function (error, element) {
        if (element.closest("div[class*=col-sm-]").length > 0) {
          element.closest("div[class*=col-sm-]").append(error);
        } else if (element.parent(".input-group").length) {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });

    datos = (typeof (datos) === "undefined" ? false : datos);
    if (datos) {
      this.cargarDatos(datos, function () {
        self.siguienteSeccion();
      });
    } else {
      this.siguienteSeccion();
    }
  };
  $(function () {
    motor.Inicializar();
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
                agregarMensaje("errores", "Ocurrió un problema durante la obtención de campos por favor intente nuevamente.", true, "#sec-mensajes-alerta", true);
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