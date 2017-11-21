var seccionPaso1 = new function () {
  this.titulo = "Seleccione una de las entidades";
  this.cargar = function (contenedor) {
    $(contenedor).on('click', ".btn-entidad", function () {
      $(contenedor).find(".btn-entidad").removeClass("btn-activo");
      $(this).addClass("btn-activo");
      var entidad = $(this).attr("rel");
      if (motor.entidadSel.nombre !== entidad) {
        motor.cambioEntidad();
        util.listarCampos(entidad, function (campos) {
          motor.entidadSel = {
            nombre: entidad,
            campos: campos,
            camposSel: []
          };
          motor.siguienteSeccion();
        });
        util.listarEntidadesRelacionadas(entidad, function (entidadesRelacionadas) {
          motor.entidadesRelacionadas = entidadesRelacionadas;
        });
      } else {
        motor.siguienteSeccion();
      }
    });
  };
  this.preMostrar = function () {
    $("#sec-titulo").text(this.titulo);
    $("#sec-mensaje-campos-obligatorios, #btn-anterior, #btn-siguiente, #btn-guardar").hide();
    return true;
  };
  this.preOcultar = function () {
    return true;
  };
};
var seccionPaso2 = new function () {
  this.titulo = "Seleccione los campos";
  this.cambioEntidad = true;
  this.cargar = function (contenedor) {
    $(contenedor).on('click', "[id*='cb-campo-']", function () {
      var camposSel = motor.entidadSel.camposSel;
      if ($(this).is(':checked'))
        camposSel.push($(this).val());
      else
        camposSel.splice(camposSel.indexOf($(this).val()), 1);
    });
  };
  this.preMostrar = function (contenedor) {
    if (Object.keys(motor.entidadSel).length) {
      $("#sec-titulo").text(this.titulo);
      $("#sec-mensaje-campos-obligatorios, #btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();

      if (this.cambioEntidad) {
        this.cambioEntidad = false;
        $(contenedor).find("#sec-campos").html("");
        $.each(motor.entidadSel.campos, function (id, ele) {
          $(contenedor).find("#sec-campos").append(
              '<div class="col-sm-3">' +
              '<input id="cb-campo-' + id.toLowerCase() + '" type="checkbox" name="campos" value="' + id + '"> ' +
              '<label for="cb-campo-' + id.toLowerCase() + '">' + ele.titulo + '</label>' +
              '</div>');
        });
      }
      return true;
    } else {
      motor.anteriorSeccion();
      return false;
    }
  };
  this.preOcultar = function (contenedor, solicitudSiguiente) {
    if (!$(contenedor).find("input[name='campos']:checked").length && solicitudSiguiente) {
      agregarMensaje("advertencias", "Debe seleccionar por lo menos un campo.", true, "#sec-mensajes-alerta", true);
      return false;
    }
    return true;
  };
};
var seccionPaso3 = new function () {
  this.titulo = "Seleccione las entidad relacionadas (opcional)";
  this.cambioEntidad = true;
  this.cargar = function (contenedor) {
    var self = this;
    $(contenedor).on('click', ".btn-entidad", function () {
      var entidadRelacionada = $(this).attr("rel");
      if ($(this).hasClass("btn-activo")) {
        $(this).removeClass("btn-activo");
        motor.entidadesRelacionadasSel = motor.entidadesRelacionadasSel.filter(function (ent) {
          return ent.nombre !== entidadRelacionada;
        });
        $("#sec-contenedor-campos-" + entidadRelacionada.toLowerCase()).remove();
        $("#sec-filtros-" + entidadRelacionada.toLowerCase()).remove();
      } else {
        var boton = this;
        util.listarCampos(entidadRelacionada, function (campos) {
          $(boton).addClass("btn-activo");
          motor.entidadesRelacionadasSel.push({
            nombre: entidadRelacionada,
            campos: campos,
            camposSel: [],
            tipoSel: "cantidad-total"
          });
          self.mostrarCamposEntidadRelacionada(contenedor, entidadRelacionada);
        });
      }
    });
    $(contenedor).on('click', "[name*='cb-tipo-seleccion-']", function () {
      var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada($(this).data("entidad"));
      if ($(this).val() !== "campos")
        $("#sec-campos-" + $(this).attr("name").replace("cb-tipo-seleccion-", "")).hide();
      else
        $("#sec-campos-" + $(this).attr("name").replace("cb-tipo-seleccion-", "")).show();
      entidadRelacionadaSel.tipoSel = $(this).val();
    });
    $(contenedor).on('click', "[id*='cb-campo-']", function () {
      var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada($(this).data("entidad"));
      var camposSel = entidadRelacionadaSel.camposSel;
      if ($(this).is(':checked'))
        camposSel.push($(this).val());
      else
        camposSel.splice(camposSel.indexOf($(this).val()), 1);
    });
  };
  this.preMostrar = function (contenedor, solicitudSiguiente) {
    if (Object.keys(motor.entidadesRelacionadas).length) {
      $("#sec-titulo").text(this.titulo);
      $("#sec-mensaje-campos-obligatorios, #btn-guardar").hide();
      $("#btn-siguiente, #btn-anterior").show();

      if (this.cambioEntidad) {
        this.cambioEntidad = false;
        $(contenedor).find("#sec-entidades-relacionadas").html("");
        $(contenedor).find("#sec-campos-entidades-relacionadas").html("");
        $.each(motor.entidadesRelacionadas, function (id, ele) {
          $(contenedor).find("#sec-entidades-relacionadas").append(
              '<div class="col-sm-3">' +
              '<button type="button" class="btn-entidad" rel="' + id + '">' + ele[5] + ' ' + ele[0] + '</button>' +
              '</div>');
        });
      }
      return true;
    } else {
      (solicitudSiguiente ? motor.siguienteSeccion() : motor.anteriorSeccion());
      return false;
    }
  };
  this.preOcultar = function () {
    return true;
  };
  this.mostrarCamposEntidadRelacionada = function (contenedor, entidadRel) {
    var entidadRelacionadaSel = motor.obtenerDatosEntidadRelacionada(entidadRel);
    var campos = entidadRelacionadaSel.campos;
    var tituloEntidad = motor.entidadesRelacionadas[entidadRel][0];
    var titulo = '<h5>Campos - ' + tituloEntidad + '</h5>';
    var contenidoCampos = '<div class="col-sm-12">' +
        '<input type="radio" id="cb-tipo-seleccion-' + entidadRel.toLowerCase() + '-cantidad-total" name="cb-tipo-seleccion-' + entidadRel.toLowerCase() + '" data-entidad="' + entidadRel + '" value="cantidad-total" checked>' +
        '<label for="cb-tipo-seleccion-' + entidadRel.toLowerCase() + '-cantidad-total">Selecionar cantidad total de ' + tituloEntidad.toLowerCase() + '</label><br>' +
        '<input type="radio" id="cb-tipo-seleccion-' + entidadRel.toLowerCase() + '-campos" name="cb-tipo-seleccion-' + entidadRel.toLowerCase() + '" data-entidad="' + entidadRel + '" value="campos">' +
        '<label for="cb-tipo-seleccion-' + entidadRel.toLowerCase() + '-campos">Seleccionar campos especificos</label>' +
        '</div>' +
        '<div id="sec-campos-' + entidadRel.toLowerCase() + '" class="col-sm-12" style="margin-top: 10px; display: none">';
    $.each(campos, function (id, ele) {
      if (id !== "tipoSel" && id !== "camposSel") {
        contenidoCampos += '<div class="col-sm-3">' +
            '<input id="cb-campo-' + entidadRel.toLowerCase() + '-' + id.toLowerCase() + '" type="checkbox" name="campos-' + entidadRel.toLowerCase() + '" data-entidad="' + entidadRel + '" value="' + id + '"> ' +
            '<label for="cb-campo-' + entidadRel.toLowerCase() + '-' + id.toLowerCase() + '">' + ele.titulo + '</label>' +
            '</div>';
      }
    });
    contenidoCampos += '</div>';
    $(contenedor).find("#sec-campos-entidades-relacionadas").append('<div id="sec-contenedor-campos-' + entidadRel.toLowerCase() + '" class="form-group"><div class="col-sm-12">' + titulo + contenidoCampos + '</div></div>');
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

    var self = this;
    var idSeccion = "#sec-filtros";
    var entidad = motor.entidadSel.nombre;
    var campos = motor.entidadSel.campos;
    var camposSel = motor.entidadSel.camposSel;

    if (this.cambioEntidad) {
      this.cambioEntidad = false;
      this.camposCargados = [];
      $(contenedor).find(idSeccion).html("");
    }
    self.procesarFiltros(contenedor, idSeccion, entidad, campos, camposSel);
    motor.entidadesRelacionadasSel.forEach(function (entidadRel)
    {
      if (entidadRel.tipoSel === "campos") {
        var idSeccionEntidadRel = "#sec-filtros-" + entidadRel.nombre.toLowerCase();
        var seccionAgregada = (self.camposCargados[entidadRel.nombre.toLowerCase()] !== undefined);
        if (!seccionAgregada) {
          var tituloEntidad = motor.entidadesRelacionadas[entidadRel.nombre][0];
          $(contenedor).find(idSeccion).append('<div id="' + idSeccionEntidadRel.replace("#", "") + '" class="col-sm-12">' +
              '<h4>Filtros - ' + tituloEntidad + '</h4>');
        }
        self.procesarFiltros(contenedor, idSeccionEntidadRel, entidadRel.nombre, entidadRel.campos, entidadRel.camposSel);
        if (!seccionAgregada) {
          self.agregarFiltroBusqueda(contenedor, idSeccionEntidadRel, entidadRel.nombre);
          $(contenedor).find(idSeccion).append('</div>');
        }
      }
    });
    return true;
  };
  this.preOcultar = function (contenedor, solicitudSiguiente) {
    return !(solicitudSiguiente && !$(contenedor).find(":input, select").valid());
  };
  this.procesarFiltros = function (contenedor, idSeccion, entidad, campos, camposSel) {
    var self = this;
    tiposSexos = (typeof (tiposSexos) === "undefined" ? [] : tiposSexos);
    tiposDocumentos = (typeof (tiposDocumentos) === "undefined" ? [] : tiposDocumentos);
    this.camposCargados[entidad.toLowerCase()] = (this.camposCargados[entidad.toLowerCase()] !== undefined ? this.camposCargados[entidad.toLowerCase()] : []);
    $.each(camposSel, function (num, id) {
      if (!campos[id].tipo)
        return true;
      if (self.camposCargados[entidad.toLowerCase()].indexOf(id) !== -1)
        return true;
      self.camposCargados[entidad.toLowerCase()].push(id);
      if ((["varchar", "text", "char"]).indexOf(campos[id].tipo.toLowerCase()) !== -1)
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

    var camposEli = $(this.camposCargados[entidad.toLowerCase()]).not(camposSel).get();
    $.each(camposEli, function (num, id) {
      $("#sec-filtro-" + entidad.toLowerCase() + "-" + id.toLowerCase()).remove();
      self.camposCargados[entidad.toLowerCase()].splice(self.camposCargados[entidad.toLowerCase()].indexOf(id), 1);
    });
  };
  this.agregarFiltroTexto = function (contenedor, idSeccion, entidad, campos, idCampo) {
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
        '<input type="text" id="' + idFiltro + '" name="' + idFiltro + '" class="form-control" />' +
        '</div>' +
        '</div>');
    $("#" + idFiltro).rules("add", {
      maxlength: 255
    });
  };
  this.agregarFiltroNumero = function (contenedor, idSeccion, entidad, campos, idCampo) {
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
        '<input type="number" id="' + idFiltro + '" name="' + idFiltro + '" class="form-control" />' +
        '</div>' +
        '</div>');
    $("#" + idFiltro).rules("add", {
      validarDecimal: true
    });
  };
  this.agregarFiltroFecha = function (contenedor, idSeccion, entidad, campos, idCampo) {
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
  };
  this.agregarFiltroVerdaderoFalso = function (contenedor, idSeccion, entidad, campos, idCampo) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

    $(contenedor).find(idSeccion).append(
        '<div id="' + idContenedor + '" >' +
        '<div class="col-sm-1"></div>' +
        '<div class="col-sm-11">' +
        '<div class="checkbox">' +
        '<label class="checkbox-custom" data-initialize="checkbox">' +
        '<label for="' + idFiltro + '" class="checkbox-label">' + campos[idCampo].titulo + '</label>' +
        '<input id="' + idFiltro + '" name="' + idFiltro + '" type="checkbox">' +
        '</label>' +
        '</div>' +
        '</div>' +
        '</div>');
  };
  this.agregarFiltroListaOpciones = function (contenedor, idSeccion, entidad, campos, idCampo, listaOpciones) {
    var idContenedor = "sec-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();
    var idFiltro = "sel-filtro-" + entidad.toLowerCase() + "-" + idCampo.toLowerCase();

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
  };
  this.agregarFiltroBusqueda = function (contenedor, idSeccion, entidad) {
    if (motor.entidades[entidad][4] !== "") {
      var idFiltro = "inp-filtro-" + entidad.toLowerCase() + "-busqueda";
      var idSelTipo = "sel-tipo-filtro-" + entidad.toLowerCase() + "-busqueda";

      $(contenedor).find(idSeccion).append(
          '<div class="form-group">' +
          '<label for="' + idFiltro + '" class="col-sm-2 control-label">Lista de ' + motor.entidades[entidad][0].toLowerCase() + ' (*)</label>' +
          '<div class="col-sm-2">' +
          '<select name="' + idSelTipo + '" class="form-control">' +
          '<option value="=">Igual a</option>' +
          '</select>' +
          '</div>' +
          '<div class="col-sm-8">' +
          '<select id="' + idFiltro + '" name="' + idFiltro + '" class="form-control" multiple="multiple" style="width: 100%"></select>' +
          '</div>' +
          '</div>');
      establecerListaBusqueda("#" + idFiltro, motor.entidades[entidad][4]);
      $("#" + idFiltro).rules("add", "required");
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
  var self = this;
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
    self.entidadSel = {};
    self.entidadesRelacionadas = [];
    self.entidadesRelacionadasSel = [];
  };
  this.obtenerDatosEntidadRelacionada = function (entidadRel) {
    return motor.entidadesRelacionadasSel.filter(function (ent) {
      return ent.nombre === entidadRel;
    })[0];
  };

  this.Inicializar = function () {
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
    self.siguienteSeccion();
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