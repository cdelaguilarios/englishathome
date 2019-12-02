var seccionPaso1 = new function () {
  this.contenedor = null;
  this.titulo = "Seleccione una de las entidades";
  this.cargar = function (contenedor) {
    var self = this;
    this.contenedor = contenedor;
    $(this.contenedor).on('click', ".btn-entidad", function () {
      self.seleccionarEntidad($(this).attr("rel"));
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
  this.seleccionarEntidad = function (nombreEntidad, retrollamada) {
    $(this.contenedor).find(".btn-entidad").removeClass("btn-activo");
    $(this.contenedor).find(".btn-entidad[rel='" + nombreEntidad + "']").addClass("btn-activo");
    if (motor.entidadSel.nombre !== nombreEntidad) {
      motor.cambioEntidad();
      util.listarCampos(nombreEntidad, function (campos) {
        motor.entidadSel = {
          nombre: nombreEntidad,
          campos: campos,
          camposSel: [],
          entidadesRelacionadasSel: []
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
  this.contenedor = null;
  this.cambioEntidad = true;
  this.titulo = "Seleccione los campos";
  this.cargar = function (contenedor) {
    this.contenedor = contenedor;
    $(this.contenedor).on('click', "[id*='cb-campo-']", function () {
      var camposSel = motor.entidadSel.camposSel;
      (($(this).is(':checked')) ? camposSel.push($(this).val()) : camposSel.splice(camposSel.indexOf($(this).val()), 1));
    });
  };
  this.preMostrar = function () {
    if (Object.keys(motor.entidadSel).length) {
      $("#sec-titulo").text(this.titulo);
      $("#sec-mensaje-campos-obligatorios, #btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();
      if (this.cambioEntidad)
        this.agregarCampos();
      return true;
    } else {
      motor.anteriorSeccion();
      return false;
    }
  };
  this.preOcultar = function (solicitudSiguiente) {
    if (solicitudSiguiente && !$(this.contenedor).find("input[name='campos']:checked").length) {
      mensajes.agregar("advertencias", "Debe seleccionar por lo menos un campo.", true, "#sec-mensajes-alerta", true);
      return false;
    }
    return true;
  };
  this.agregarCampos = function (camposSeleccionados) {
    var self = this;
    this.cambioEntidad = false;
    $(this.contenedor).find("#sec-campos").html("");
    $.each(motor.entidadSel.campos, function (nombreCampo, datosCampo) {
      //Modo edición
      var chkCampoSeleccionado = '';
      if (camposSeleccionados) {
        var datCampoSel = $.grep(camposSeleccionados, function (campoSeleccionado) {
          return campoSeleccionado.nombre.toLowerCase() === nombreCampo.toLowerCase();
        });
        if (datCampoSel.length) {
          chkCampoSeleccionado = ' checked';
          motor.entidadSel.camposSel.push(nombreCampo);
        }
      }
      $(self.contenedor).find("#sec-campos").append(
          '<div class="col-sm-3">' +
          '<input id="cb-campo-' + nombreCampo.toLowerCase() + '" type="checkbox" name="campos" value="' + nombreCampo + '"' + chkCampoSeleccionado + '>' +
          '<label for="cb-campo-' + nombreCampo.toLowerCase() + '">' + datosCampo.titulo + '</label>' +
          '</div>');
    });
  };
};
var seccionPaso3 = new function () {
  this.contenedor = null;
  this.cambioEntidad = true;
  this.titulo = "Seleccione las entidad relacionadas (opcional)";
  this.cargar = function (contenedor) {
    var self = this;
    this.contenedor = contenedor;
    $(this.contenedor).on('click', ".btn-entidad", function () {
      var nombreEntidadRelacionada = $(this).attr("rel");
      if ($(this).hasClass("btn-activo")) {
        $(this).removeClass("btn-activo");
        $("#sec-contenedor-campos-" + nombreEntidadRelacionada.toLowerCase()).remove();
        $("#sec-filtros-" + nombreEntidadRelacionada.toLowerCase()).remove();
        motor.entidadSel.entidadesRelacionadasSel = motor.entidadSel.entidadesRelacionadasSel.filter(function (entidadRelacionadaSel) {
          return entidadRelacionadaSel.nombre !== nombreEntidadRelacionada;
        });
      } else {
        self.seleccionarEntidadRelacionada(nombreEntidadRelacionada);
      }
    });
    $(this.contenedor).on('click', "[name*='cb-tipo-seleccion-']", function () {
      var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada($(this).data("entidad"));
      entidadRelacionadaSel.tipoSel = $(this).val();
      $("#sec-campos-" + $(this).data("entidad").toLowerCase()).css('display', ($(this).val() !== "campos" ? "none" : "inline-block"));
    });
    $(this.contenedor).on('click', "[id*='cb-campo-']", function () {
      var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada($(this).data("entidad"));
      var camposSel = entidadRelacionadaSel.camposSel;
      (($(this).is(':checked')) ? camposSel.push($(this).val()) : camposSel.splice(camposSel.indexOf($(this).val()), 1));
    });
  };
  this.preMostrar = function (solicitudSiguiente) {
    if (Object.keys(motor.entidadesRelacionadas).length) {
      $("#sec-titulo").text(this.titulo);
      $("#sec-mensaje-campos-obligatorios, #btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();
      if (this.cambioEntidad)
        this.agregarEntidadesRelacionadas();
      return true;
    } else {
      (solicitudSiguiente ? motor.siguienteSeccion() : motor.anteriorSeccion());
      return false;
    }
  };
  this.preOcultar = function () {
    return true;
  };
  this.agregarEntidadesRelacionadas = function () {
    var self = this;
    this.cambioEntidad = false;
    $(this.contenedor).find("#sec-entidades-relacionadas").html("");
    $(this.contenedor).find("#sec-campos-entidades-relacionadas").html("");
    $.each(motor.entidadesRelacionadas, function (nombreEntidadRel, datosEntidadRel) {
      $(self.contenedor).find("#sec-entidades-relacionadas").append(
          '<div class="col-sm-3">' +
          '<button type="button" class="btn-entidad" rel="' + nombreEntidadRel + '">' + datosEntidadRel[5] + ' ' + datosEntidadRel[0] + '</button>' +
          '</div>');
    });
  };
  this.seleccionarEntidadRelacionada = function (nombreEntidadRelacionada, tipoCamposSeleccionados, retrollamada) {
    var self = this;
    util.listarCampos(nombreEntidadRelacionada, function (campos) {
      motor.entidadSel.entidadesRelacionadasSel.push({
        nombre: nombreEntidadRelacionada,
        campos: campos,
        camposSel: [],
        tipoSel: (tipoCamposSeleccionados ? "campos" : "cantidad-total")
      });
      self.mostrarCamposEntidadRelacionada(nombreEntidadRelacionada, tipoCamposSeleccionados);
      $(self.contenedor).find(".btn-entidad[rel='" + nombreEntidadRelacionada + "']").addClass("btn-activo");
      if (retrollamada)
        retrollamada();
    });
  };
  this.mostrarCamposEntidadRelacionada = function (nombreEntidadRelacionada, tipoCamposSeleccionados) {
    var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada(nombreEntidadRelacionada);
    var campos = entidadRelacionadaSel.campos;
    var tituloEntidad = motor.entidadesRelacionadas[nombreEntidadRelacionada][0];
    var titulo = '<h5>Campos - ' + tituloEntidad + '</h5>';

    var idTipoSelBase = "cb-tipo-seleccion-" + nombreEntidadRelacionada.toLowerCase();
    var idTipoSelCantidadTot = idTipoSelBase + "-cantidad-total";
    var idTipoSelCampos = idTipoSelBase + "-campos";

    var contenidoCampos = '<div class="col-sm-12">' +
        '<input type="radio" id="' + idTipoSelCantidadTot + '" name="' + idTipoSelBase + '" data-entidad="' + nombreEntidadRelacionada + '" value="cantidad-total"' + (tipoCamposSeleccionados ? '' : ' checked') + '>' +
        '<label for="' + idTipoSelCantidadTot + '">Selecionar cantidad total de ' + tituloEntidad.toLowerCase() + '</label><br>' +
        '<input type="radio" id="' + idTipoSelCampos + '" name="' + idTipoSelBase + '" data-entidad="' + nombreEntidadRelacionada + '" value="campos"' + (tipoCamposSeleccionados ? ' checked' : '') + '>' +
        '<label for="' + idTipoSelCampos + '">Seleccionar campos especificos</label>' +
        '</div>' +
        '<div id="sec-campos-' + nombreEntidadRelacionada.toLowerCase() + '" class="col-sm-12" style="margin-top: 10px;' + (tipoCamposSeleccionados ? '' : 'display: none;') + '">';
    $.each(campos, function (nombreCampo, datosCampo) {
      var chkCampoSeleccionado = "";
      if (tipoCamposSeleccionados) {
        var datCampoSel = $.grep(tipoCamposSeleccionados, function (e) {
          return e.nombre.toLowerCase() === nombreCampo.toLowerCase();
        });
        if (datCampoSel.length) {
          chkCampoSeleccionado = ' checked';
          entidadRelacionadaSel.camposSel.push(nombreCampo);
        }
      }
      if (nombreCampo !== "tipoSel" && nombreCampo !== "camposSel") {
        var idCampo = "cb-campo-" + nombreEntidadRelacionada.toLowerCase() + "-" + nombreCampo.toLowerCase();
        contenidoCampos += '<div class="col-sm-3">' +
            '<input id="' + idCampo + '" type="checkbox" name="campos-' + nombreEntidadRelacionada.toLowerCase() + '" data-entidad="' + nombreEntidadRelacionada + '" value="' + nombreCampo + '"' + chkCampoSeleccionado + '> ' +
            '<label for="' + idCampo + '">' + datosCampo.titulo + '</label>' +
            '</div>';
      }
    });
    contenidoCampos += '</div>';
    $(this.contenedor).find("#sec-campos-entidades-relacionadas").append('<div id="sec-contenedor-campos-' + nombreEntidadRelacionada.toLowerCase() + '" class="form-group"><div class="col-sm-12">' + titulo + contenidoCampos + '</div></div>');
  };
};
var seccionPaso4 = new function () {
  this.contenedor = null;
  this.camposCargados = [];
  this.cambioEntidad = true;
  this.titulo = "Ingrese los filtros";
  this.cargar = function (contenedor) {
    this.contenedor = contenedor;
  };
  this.preMostrar = function (solicitudSiguiente) {
    $("#sec-titulo").text(this.titulo);
    $("#btn-guardar").hide();
    $("#sec-mensaje-campos-obligatorios, #btn-siguiente, #btn-anterior").show();
    if (!solicitudSiguiente)
      return true;
    this.agregarFiltros();
    return true;
  };
  this.preOcultar = function (solicitudSiguiente) {
    return !(solicitudSiguiente && !$(this.contenedor).find(":input, select").valid());
  };
  this.agregarFiltros = function (datos) {
    var self = this;
    var idSeccion = "#sec-filtros";
    var nombreEntidad = motor.entidadSel.nombre;
    var campos = motor.entidadSel.campos;
    var camposSel = motor.entidadSel.camposSel;

    if (this.cambioEntidad) {
      this.cambioEntidad = false;
      this.camposCargados = [];
      $(this.contenedor).find(idSeccion).html("");
    }
    self.procesarFiltros(idSeccion, nombreEntidad, campos, camposSel, (datos ? datos.camposSeleccionados : null));

    //Entidades relacionadas
    motor.entidadSel.entidadesRelacionadasSel.forEach(function (datosEntidadRel)
    {
      var idSeccionEntidadRel = "#sec-filtros-" + datosEntidadRel.nombre.toLowerCase();

      var tituloEntidadRel = motor.entidadesRelacionadas[datosEntidadRel.nombre][0];
      var seccionAgregada = (self.camposCargados[datosEntidadRel.nombre.toLowerCase()] !== undefined);
      if (!seccionAgregada)
        $(self.contenedor).find(idSeccion).append('<div id="' + idSeccionEntidadRel.replace("#", "") + '" class="col-sm-12">' +
            '<h4>Filtros - ' + tituloEntidadRel + '</h4>');

      if (datosEntidadRel.tipoSel === "campos") {
        //Modo edición
        var datosCamposSel = null;
        if (datos && datos.entiadesRelacionadas) {
          var datEntidadRelacionada = $.grep(datos.entiadesRelacionadas, function (e) {
            return e.entidad.toLowerCase() === datosEntidadRel.nombre.toLowerCase();
          });
          if (datEntidadRelacionada.length)
            datosCamposSel = datEntidadRelacionada[0].camposSeleccionados;
        }
        self.procesarFiltros(idSeccionEntidadRel, datosEntidadRel.nombre, datosEntidadRel.campos, datosEntidadRel.camposSel, datosCamposSel);
      } else {
        var reglasValidacion = $("#formulario-reporte").validate().settings.rules;
        for (var campo in reglasValidacion)
          if (campo.indexOf("-" + datosEntidadRel.nombre.toLowerCase() + "-") >= 0)
            delete reglasValidacion[campo];

        self.procesarFiltros(idSeccionEntidadRel, datosEntidadRel.nombre, [], [], datosCamposSel);

        /*$(idSeccionEntidadRel).remove();
        if (self.camposCargados[datosEntidadRel.nombre.toLowerCase()] !== undefined)
          self.camposCargados[datosEntidadRel.nombre.toLowerCase()] = undefined;*/
      }

      if (!seccionAgregada)
        $(self.contenedor).find(idSeccion).append('</div>');
    });
  };
  this.procesarFiltros = function (idSeccion, nombreEntidad, campos, camposSel, datosCamposSel) {
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
        self.agregarFiltroTexto(idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["int", "float"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroNumero(idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["datetime", "timestamp"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroFecha(idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["tinyint"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroVerdaderoFalso(idSeccion, nombreEntidad, campos, id, datosCamposSel);
      else if ((["sexo"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroListaOpciones(idSeccion, nombreEntidad, campos, id, tiposSexos, datosCamposSel);
      else if ((["tipodocumento"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
        self.agregarFiltroListaOpciones(idSeccion, nombreEntidad, campos, id, tiposDocumentos, datosCamposSel);
    });

    //Filtro de búsqueda
    var idBusqueda = "busqueda";
    if (self.camposCargados[nombreEntidad.toLowerCase()].indexOf(idBusqueda) === -1) {
      self.camposCargados[nombreEntidad.toLowerCase()].push(idBusqueda);
      self.agregarFiltroBusqueda(idSeccion, nombreEntidad, datosCamposSel);
    }

    var camposEli = $(this.camposCargados[nombreEntidad.toLowerCase()]).not(camposSel).get();
    $.each(camposEli, function (num, id) {
      if (id !== idBusqueda) {
        $("#sec-filtro-" + nombreEntidad.toLowerCase() + "-" + id.toLowerCase()).remove();
        self.camposCargados[nombreEntidad.toLowerCase()].splice(self.camposCargados[nombreEntidad.toLowerCase()].indexOf(id), 1);
      }
    });
  };
  //Util
  this.agregarFiltroTexto = function (idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    $(this.contenedor).find(idSeccion).append(
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
  this.agregarFiltroNumero = function (idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    $(this.contenedor).find(idSeccion).append(
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
  this.agregarFiltroFecha = function (idSeccion, entidad, campos, idCampo, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltroFechaIni = "inp-filtro-fecha-inicio-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltroFechaFin = "inp-filtro-fecha-fin-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    $(this.contenedor).find(idSeccion).append(
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

    $(this.contenedor).find(idSeccion).on('change', "#" + idSelTipo, function () {
      ($(this).val() !== "BETWEEN" ? $("#" + idFiltroFechaFin).hide() : $("#" + idFiltroFechaFin).show());
    });
    utilFechasHorarios.establecerCalendario($("#" + idFiltroFechaIni));
    utilFechasHorarios.establecerCalendario($("#" + idFiltroFechaFin));

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
  this.agregarFiltroVerdaderoFalso = function (idSeccion, entidad, campos, idCampo, datosCamposSel) {
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

    $(this.contenedor).find(idSeccion).append(
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
  this.agregarFiltroListaOpciones = function (idSeccion, entidad, campos, idCampo, listaOpciones, datosCamposSel) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    var contenidoOpciones = '';
    $.each(listaOpciones, function (id, val) {
      contenidoOpciones += '<option value="' + id + '">' + val + '</option>';
    });
    $(this.contenedor).find(idSeccion).append(
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
  this.agregarFiltroBusqueda = function (idSeccion, entidad, datosCamposSel) {
    if (motor.entidades[entidad][4] !== "") {
      var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-busqueda";
      var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-busqueda";

      $(this.contenedor).find(idSeccion).append(
          '<div class="form-group">' +
          '<label for="' + idFiltro + '" class="col-sm-2 control-label">Lista de ' + motor.entidades[entidad][0].toLowerCase() + '</label>' +
          '<div class="col-sm-2">' +
          '<select name="' + idSelTipo + '" class="form-control">' +
          '<option value="=">Igual a</option>' +
          '</select>' +
          '</div>' +
          '<div class="col-sm-8">' +
          '<select id="' + idFiltro + '" name="' + idFiltro + '[]" class="form-control" multiple="multiple" style="width: 100%"></select>' +
          '</div>' +
          '</div>');
      utilBusqueda.establecerListaBusqueda($("#" + idFiltro), motor.entidades[entidad][4]);
      //$("#" + idFiltro).rules("add", "required");

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
  this.contenedor = null;
  this.cambioEntidad = true;
  this.titulo = "Ingrese los datos finales";
  this.cargar = function (contenedor) {
    this.contenedor = contenedor;
  };
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
  this.entidadesRelacionadas = [];
  this.entidadSel = {};

  this.mostrarOcultarSeccion = function (ocultar, solicitudSiguiente) {
    var seccion = $("#sec-paso-" + this.seccionActual);
    if (seccion.length) {
      var permitido = true;
      if (window["seccionPaso" + this.seccionActual])
        permitido = (ocultar ? window["seccionPaso" + this.seccionActual].preOcultar(solicitudSiguiente) : window["seccionPaso" + this.seccionActual].preMostrar(solicitudSiguiente));
      if (permitido)
        (ocultar ? $("#sec-paso-" + this.seccionActual).hide() : $("#sec-paso-" + this.seccionActual).show());
      else
        return false;
    }
    return true;
  };
  this.anteriorSeccion = function () {
    if (this.seccionActual <= 1)
      return;
    $("#sec-mensajes-alerta").html("");
    if (this.mostrarOcultarSeccion(true)) {
      this.seccionActual--;
      this.mostrarOcultarSeccion();
    }
  };
  this.siguienteSeccion = function () {
    if (this.seccionActual >= $("[id*='sec-paso-']").length)
      return;
    $("#sec-mensajes-alerta").html("");
    if (this.mostrarOcultarSeccion(true, true)) {
      this.seccionActual++;
      this.mostrarOcultarSeccion(false, true);
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
    return motor.entidadSel.entidadesRelacionadasSel.filter(function (entidad) {
      return entidad.nombre.toLowerCase() === nombreEntidadRel.toLowerCase();
    })[0];
  };

  //Modo edición
  this.cargarDatos = function (datosGuardados, retrollamada) {
    var self = this;
    if (datosGuardados.entidad) {
      seccionPaso1.seleccionarEntidad(datosGuardados.entidad, function () {
        seccionPaso2.agregarCampos(datosGuardados.camposSeleccionados);
        seccionPaso3.agregarEntidadesRelacionadas();

        if (datosGuardados.entiadesRelacionadas.length) {
          var entidadesRelacionadasCargadas = 0;
          $.each(datosGuardados.entiadesRelacionadas, function (i, datosEntidadRelacionada) {
            self.cargarDatosEntidadRelacionada(datosEntidadRelacionada, function () {
              entidadesRelacionadasCargadas++;
              if (entidadesRelacionadasCargadas >= datosGuardados.entiadesRelacionadas.length) {
                seccionPaso4.agregarFiltros(datosGuardados);
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
  this.cargarDatosEntidadRelacionada = function (datosEntidadRelacionada, retrollamada) {
    if (datosEntidadRelacionada.entidad) {
      seccionPaso3.seleccionarEntidadRelacionada(datosEntidadRelacionada.entidad, datosEntidadRelacionada.camposSeleccionados, function () {
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

    //Modo edición
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
      util.llamadaAjax(urlListarCampos, "POST", {entidad: entidad}, true,
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
                mensajes.agregar("errores", "Ocurrió un problema durante la obtención de campos por favor intente nuevamente.", true, "#sec-mensajes-alerta", true);
              }
            });
          }
      );
    }
  };
  this.listarEntidadesRelacionadas = function (entidad, retrollamada) {
    urlListarEntidadesRelacionadas = (typeof (urlListarEntidadesRelacionadas) === "undefined" ? "" : urlListarEntidadesRelacionadas);
    if (urlListarEntidadesRelacionadas !== "") {
      util.llamadaAjax(urlListarEntidadesRelacionadas, "POST", {entidad: entidad}, true,
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