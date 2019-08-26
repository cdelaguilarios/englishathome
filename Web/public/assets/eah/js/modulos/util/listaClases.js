var listaClases = {};
listaClases = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }
  function cargarSeccion()/* - */ {
    cargarListaClase();
    cargarFormularioComentarios();
  }
  
  function cargarListaClase() {
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
    urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);

    if (urlListarClases !== "" && urlPerfilProfesor !== "" && estadosClase !== "") {
      $("#tab-lista-clases").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: urlListarClases,
          type: "POST",
          data: function (d) {
            d._token = $("meta[name=_token]").attr("content");
          }
        },
        autoWidth: false,
        responsive: true,
        orderCellsTop: true,
        fixedHeader: true,
        order: [[1, "desc"]],
        rowId: 'id',
        columns: [
          {data: "", name: "", orderable: false, "searchable": false, "className": "text-center not-mobile",
            render: function (e, t, d, m) {
              return m.row + m.settings._iDisplayStart + 1;
            }},
          {data: "fechaInicio", name: "fechaInicio", render: function (e, t, d, m) {
              var fechaConfirmacionIni = "";
              if (d.fechaConfirmacion !== null && !isNaN(Date.parse(d.fechaConfirmacion))) {
                fechaConfirmacionIni = new Date(d.fechaConfirmacion);
                fechaConfirmacionIni.setSeconds(fechaConfirmacionIni.getSeconds() - d.duracion);
              }
              return '<b>Fecha:</b> ' + utilFechasHorarios.formatoFecha(d.fechaInicio) + ' - De ' + utilFechasHorarios.formatoFecha(d.fechaInicio, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaFin, false, true) + '<br/>' +
                      (d.fechaConfirmacion !== null ? '<b>Fecha de confirmación:</b> ' + utilFechasHorarios.formatoFecha(d.fechaConfirmacion) + ' - De ' + utilFechasHorarios.formatoFecha(fechaConfirmacionIni, false, true) + ' a ' + utilFechasHorarios.formatoFecha(d.fechaConfirmacion, false, true) + '<br/>' : '') +
                      '<b>Duración:</b> ' + utilFechasHorarios.formatoHora(d.duracion) + '<br/>' +
                      (d.idHistorial !== null ? '<b>Notificar:</b> ' + ' <i class="fa fa-check icon-notificar-clase"></i>' + '<br/>' : '') +
                      '<b>Profesor(a):</b> ' + (d.idProfesor !== null ? '<a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' : 'Sin profesor asignad');
            }},
          {data: "estado", name: "estado", width: "13%", render: function (e, t, d, m) {
              var estado = '';
              if (estadosClase[d.estado] !== undefined && estadosClaseDisponibleCambio[d.estado] !== undefined) {
                estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-clases" data-idselestados="sel-estados-clase" data-tipocambio="1">' +
                        '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                        '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' +
                        '</a>' +
                        '</div>';
              } else if (estadosClase[d.estado] !== undefined) {
                estado = '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>';
              }
              return estado;
            }, className: "text-center"},
          {data: "comentarioAlumno", name: "comentarioAlumno", width: "50%", render: function (e, t, d, m) {
              var incluirComentario = function (idClase, titulo, tipo, comentario) {
                var maxTexto = 200;
                return '<b>' + titulo + ':</b> ' + (comentario ? comentario.substring(0, maxTexto) + (comentario.length > maxTexto ? '...' : '') : '<i>Sin comentarios</i>') + ' <a href="javascript:void(0);" onclick="listaClases.abrirModalFormularioComentarios(' + idClase + ', ' + tipo + ');" title="Ver/editar comentarios"><i class="fa fa-eye"></i></a>' + '<br/><br/>';
              };
              return incluirComentario(d.id, 'Del alumno', 1, d.comentarioAlumno) +
                      incluirComentario(d.id, 'Del profesor', 2, d.comentarioProfesor) +
                      incluirComentario(d.id, 'De EAH para el alumno', 3, d.comentarioParaAlumno) +
                      incluirComentario(d.id, 'De EAH para el profesor', 4, d.comentarioParaProfesor);
            }, "className": "not-mobile"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla("tab-lista-clases");
          utilTablas.establecerCabecerasBusquedaTabla("tab-lista-clases");
        }
      });
    }
  }
  function actualizarListaClases(idAlumno, funcionRetorno, funcionError) {
    urlListarClases = (typeof (urlListarClases) === "undefined" ? "" : urlListarClases);
    if (urlListarClases !== "") {
      $("#tab-lista-clases").DataTable().ajax.url(urlListarClases.replace("/0", "/" + idAlumno)).load(funcionRetorno);
    } else {
      funcionError();
    }
  }

  function cargarFormularioComentarios() {
    $("#formulario-comentarios").validate({
      rules: {
        comentario: {
          required: true
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los cambios de estos comentarios?")) {
          $.blockUI({message: "<h4>Guardando...</h4>"});
          var datos = procesarDatosFormulario(f);
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock({
                      onUnblock: function () {
                        agregarMensaje("exitosos", d["mensaje"], true, "#sec-men-lista-clases");
                      }
                    });
                  },
                  function (d) {
                    $("#mod-comentarios").modal("hide");
                    $("#formulario-comentarios").find("textarea[name='comentario']").val("");
                    $("#tab-lista-clases").DataTable().ajax.reload();
                  },
                  function (de) {
                    $("body").unblock({
                      onUnblock: function () {
                        var res = de["responseJSON"];
                        if (res["mensaje"]) {
                          agregarMensaje("errores", res["mensaje"], true, "#sec-men-lista-clases");
                        } else {
                          agregarMensaje("errores", res[Object.keys(res)[0]][0], true, "#sec-men-lista-clases");
                        }
                      }
                    });
                  }
          );
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
  }
  function abrirModalFormularioComentarios(idClase, tipo) {
    var tr = $("#" + idClase);
    var fila = $("#tab-lista-clases").DataTable().row(tr);
    var datosFila = fila.data();
    $("#mod-comentarios").find(".modal-title").html("Comentarios " + (tipo === 1 ? "del alumno" : (tipo === 2 ? "del profesor" : (tipo === 3 ? "De EAH para el alumno" : "De EAH para el profesor"))));
    $("#formulario-comentarios").find("textarea[name='comentario']").val(tipo === 1 ? datosFila.comentarioAlumno : (tipo === 2 ? datosFila.comentarioProfesor : (tipo === 3 ? datosFila.comentarioParaAlumno : datosFila.comentarioParaProfesor)));
    $("#formulario-comentarios").find("input[name='idClase']").val(datosFila.id);
    $("#formulario-comentarios").find("input[name='idAlumno']").val(datosFila.idAlumno);
    $("#formulario-comentarios").find("input[name='tipo']").val(tipo);
    $("#mod-comentarios").modal("show").on('hidden.bs.modal', function () {
      $("body").addClass("modal-open");
    });
  }
    
  return {
    actualizarListaClases: actualizarListaClases,
    abrirModalFormularioComentarios: abrirModalFormularioComentarios
  };
}());