var listaClases = {};
listaClases = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  var _url = null;
  var _tabla = null;
  var _esListaClasesAlumno = false;
  var _incluirBotonEliminar = false;

  function cargarSeccion()/* - */ {
    cargarFormularioComentarios();
  }

  function cargar()/* - */ {
    urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
    urlPerfilAlumno = (typeof (urlPerfilAlumno) === "undefined" ? "" : urlPerfilAlumno);

    estadosClase = (typeof (estadosClase) === "undefined" ? "" : estadosClase);

    if (urlPerfilProfesor !== "" && urlPerfilAlumno !== "" && estadosClase !== "") {
      if (!_incluirBotonEliminar) {
        $("#tab-lista-clases>thead>tr>th:last-child").remove();
      }

      _tabla = $("#tab-lista-clases").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: _url,
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
          {data: "fechaConfirmacion", name: "fechaConfirmacion", render: function (e, t, d, m) {
              var fechaConfirmacionIni = "";
              var fechaConfirmacionFin = "";
              if (d.fechaConfirmacion !== null && !isNaN(Date.parse(d.fechaConfirmacion))) {
                fechaConfirmacionIni = new Date(d.fechaConfirmacion);
                fechaConfirmacionIni.setSeconds(fechaConfirmacionIni.getSeconds() - d.duracion);
                fechaConfirmacionFin = new Date(d.fechaConfirmacion);
              } else {
                fechaConfirmacionIni = d.fechaInicio;
                fechaConfirmacionFin = d.fechaFin;
              }

              var datos = '<b>Período:</b> ' + d.numeroPeriodo +
                      '<br/><b>Fecha:</b> ' + utilFechasHorarios.formatoFecha(fechaConfirmacionIni) + ' - De ' + utilFechasHorarios.formatoFecha(fechaConfirmacionIni, false, true) + ' a ' + utilFechasHorarios.formatoFecha(fechaConfirmacionFin, false, true) +
                      '<br/><b>Duración:</b> ' + utilFechasHorarios.formatoHora(d.duracion);
              if (_esListaClasesAlumno) {
                datos += (d.nombreProfesor !== null ? '<br/><b>Profesor(a):</b> <a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '" target="_blank">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' : '');
                datos += (d.idPagoAlumno !== null && d.idPagoAlumno !== "" ? '<br/><b>Código de pago:</b> ' + d.idPagoAlumno : '');
              } else {
                datos += '<br/><b>Alumno(a):</b> <a href="' + (urlPerfilAlumno.replace("/0", "/" + d.idAlumno)) + '" target="_blank">' + (d.nombreAlumno !== null ? d.nombreAlumno : "") + " " + (d.apellidoAlumno !== null ? d.apellidoAlumno : "") + '</a>';
                datos += (d.idPagoProfesor !== null && d.idPagoProfesor !== "" ? '<br/><b>Código de pago:</b> ' + d.idPagoProfesor : '');
              }
              return datos;
            }},
          {data: "estado", name: "estado", width: "10%", render: function (e, t, d, m) {
              var estado = '';
              if (estadosClase[d.estado] !== undefined) {
                if (estadosClaseDisponibleCambio[d.estado] !== undefined) {
                  estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista-clases" data-idselestados="sel-estados-clase" data-tipocambio="1">' +
                          '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                          '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>' +
                          '</a>' +
                          '</div>';
                } else {
                  estado = '<span class="label ' + estadosClase[d.estado][1] + ' btn-estado">' + estadosClase[d.estado][0] + '</span>';
                }
              }
              return estado;
            }, className: "text-center"},
          {data: "comentarioAlumno", name: "comentarioAlumno", width: "50%", orderable: false, render: function (e, t, d, m) {
              var incluirComentario = function (idClase, seccion, tipo, comentario) {
                var maxTexto = 200;
                return '<b>' + seccion + ':</b> ' + (comentario ? comentario.substring(0, maxTexto) + (comentario.length > maxTexto ? '...' : '') : '<i>Sin comentarios</i>') +
                        ' <a href="javascript:void(0);" onclick="listaClases.abrirModalFormularioComentarios(' + idClase + ', ' + tipo + ');" title="Ver/editar comentarios">' +
                        '<i class="fa fa-eye"></i>' +
                        '</a><br/><br/>';
              };
              return incluirComentario(d.id, 'Del alumno', 1, d.comentarioAlumno) +
                      incluirComentario(d.id, 'Del profesor', 2, d.comentarioProfesor) +
                      incluirComentario(d.id, 'De EAH para el alumno', 3, d.comentarioParaAlumno) +
                      incluirComentario(d.id, 'De EAH para el profesor', 4, d.comentarioParaProfesor);
            }, "className": "not-mobile"},
          {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
              if (_incluirBotonEliminar) {
                urlEliminarClase = (typeof (urlEliminarClase) === "undefined" ? "" : urlEliminarClase);
                return '<ul class="buttons">' +
                        '<li>' +
                        '<a href="javascript:void(0);" title="Eliminar clase" onclick="utilTablas.eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta clase?\', \'tab-lista-clases\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminarClase.replace("/0", "/" + d.id))) + '">' +
                        '<i class="fa fa-trash"></i>' +
                        '</a>' +
                        '</li>' +
                        '</ul>';
              } else {
                return '';
              }
            }, className: "text-center"}
        ],
        initComplete: function (s, j) {
          utilTablas.establecerBotonRecargaTabla($("#tab-lista-clases"));
          utilTablas.establecerCabecerasBusquedaTabla($("#tab-lista-clases"));

          if (!_incluirBotonEliminar) {
            var columnaOpciones = _tabla.column(4);
            columnaOpciones.visible(false);

            setTimeout(function () {
              tablaCargaCompleta();
            }, 1000);
          }
        }
      });
    }
  }
  function cargarFormularioComentarios()/* - */ {
    $("#formulario-comentarios").validate({
      rules: {
        comentario: {
          required: true
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los cambios de estos comentarios?")) {
          $.blockUI({message: "<h4>Guardando...</h4>"});
          var datos = utilFormularios.procesarDatos(f);
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock({
                      onUnblock: function () {
                        mensajes.agregar("exitosos", d["mensaje"], true, "#sec-men-lista-clases");
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
                          mensajes.agregar("errores", res["mensaje"], true, "#sec-men-lista-clases");
                        } else {
                          mensajes.agregar("errores", res[Object.keys(res)[0]][0], true, "#sec-men-lista-clases");
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
    $("#mod-comentarios").find(".close").click(function () {
      $("#mod-comentarios").modal("hide");
    });
  }
  function tablaCargaCompleta() {
    if (_tabla !== null && !_incluirBotonEliminar) {
      $("#" + _tabla.table().node().id + ">tbody>tr").each(function () {
        if (!$(this).hasClass("tabla-sec-busqueda")) {
          $(this).find("td:last-child").remove();
        }
      });
    }
  }

  //Público
  function actualizar(url, esListaClasesAlumno, incluirBotonEliminar, funcionRetorno, funcionError)/* - */ {
    if (url !== "") {
      _url = url;
      _esListaClasesAlumno = esListaClasesAlumno;
      _incluirBotonEliminar = incluirBotonEliminar;

      if (_tabla === null) {
        cargar();
        if (funcionRetorno !== undefined) {
          funcionRetorno();
        }
      } else {
        _tabla.ajax.url(_url).load(function (c, r) {
          tablaCargaCompleta();

          if (funcionRetorno !== undefined) {
            funcionRetorno(c, r);
          }
        });
      }
    } else if (funcionError !== undefined) {
      funcionError("URL para listar clases no disponible");
    }
  }
  function abrirModalFormularioComentarios(idClase, tipo)/* - */ {
    var tr = $("#tab-lista-clases").find("#" + idClase)[0];
    var fila = $("#tab-lista-clases").DataTable().row(tr);
    var datosFila = fila.data();

    var comentarios = "";
    if (datosFila !== undefined) {
      comentarios = (tipo === 1 ? datosFila.comentarioAlumno : (tipo === 2 ? datosFila.comentarioProfesor : (tipo === 3 ? datosFila.comentarioParaAlumno : datosFila.comentarioParaProfesor)));
    }

    $("#mod-comentarios").find(".modal-title").html("Comentarios " + (tipo === 1 ? "del alumno" : (tipo === 2 ? "del profesor" : (tipo === 3 ? "De EAH para el alumno" : "De EAH para el profesor"))));
    $("#formulario-comentarios").find("textarea[name='comentario']").val(comentarios);
    $("#formulario-comentarios").find("input[name='id']").val(datosFila.id);
    $("#formulario-comentarios").find("input[name='tipo']").val(tipo);
    $("#mod-comentarios").modal("show").on('hidden.bs.modal', function () {
      $(".modal").each(function (i) {
        if ($(this).hasClass("in")) {
          $("body").addClass("modal-open");
          return false;
        }
      });
    });
  }

  return {
    actualizar: actualizar,
    abrirModalFormularioComentarios: abrirModalFormularioComentarios
  };
}());