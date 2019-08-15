$(document).ready(function () {
  cargarLista();
});

/* - */function cargarLista() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlPerfil = (typeof (urlPerfil) === "undefined" ? "" : urlPerfil);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);

  estados = (typeof (estados) === "undefined" ? "" : estados);
  estadosCambio = (typeof (estadosCambio) === "undefined" ? "" : estadosCambio);
  estadoCuotaProgramada = (typeof (estadoCuotaProgramada) === "undefined" ? "" : estadoCuotaProgramada);

  if (urlListar !== "" && urlPerfil !== "" && urlEditar !== "" && urlEliminar !== "" && urlPerfilProfesor !== "" && estados !== "" && estadosCambio !== "" && estadoCuotaProgramada !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.estado = $("#bus-estado").val();
        }
      },
      autoWidth: false,
      responsive: true,
      orderCellsTop: true,
      fixedHeader: true,
      order: [[6, "desc"]],
      rowId: 'idEntidad',
      columns: [
        {data: "", name: "", orderable: false, "searchable": false, render: function (e, t, d, m) {
            return m.row + m.settings._iDisplayStart + 1;
          }, "className": "text-center not-mobile"},
        {data: "nombre", name: "entidad.nombre", render: function (e, t, d, m) {
            return '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '">' + (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "") + '</a>' +
                    (d.distritoAlumno ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + letraCapital(d.distritoAlumno) + '</span>' : '') +
                    (d.nombreProfesor ?
                            '<br/><br/>Profesor(a): <a href="' + (urlPerfilProfesor.replace("/0", "/" + d.idProfesor)) + '">' + (d.nombreProfesor !== null ? d.nombreProfesor : "") + " " + (d.apellidoProfesor !== null ? d.apellidoProfesor : "") + '</a>' +
                            (d.distritoProfesor ? '<br/><span class="text-info"><i class="fa fa-street-view"></i> ' + letraCapital(d.distritoProfesor) + '</span>' : '')
                            : '');
          }},
        {data: "porcentajeAvanceClases", name: "porcentajeAvanceClases", width: "25%", render: function (e, t, d, m) {
            if (d.duracionTotalClases) {
              var porcentajeAvance = (d.duracionTotalClasesRealizadas ? d.porcentajeAvanceClases : 0);
              return '<div class="clearfix">' +
                      '<span class="pull-left">Total de clases: ' + d.totalClases + '</span>' +
                      '<a href="javascript:void(0);" onclick="abrirModalListaClases(' + d.idEntidad + ');" title="Ver lista de clases" class="btn-ver-lista-clases">' +
                      '<i class="fa fa-eye"></i>' +
                      '</a>' +
                      '<small class="pull-right">' + redondear(porcentajeAvance, 2) + ' %</small>' +
                      '</div>' +
                      '<div class="progress xs">' +
                      '<div class="progress-bar progress-bar-green" style="width: ' + porcentajeAvance + '%;"></div>' +
                      '</div>' +
                      '<div class="clearfix">' +
                      '<span class="pull-left">' +
                      '<span class="text-green" data-toggle="tooltip" title="" data-original-title="Horas realizadas">' +
                      '<i class="fa fa-clock-o"></i> ' + formatoHora(d.duracionTotalClasesRealizadas) +
                      '</span>  de  <span class="text-info" data-toggle="tooltip" title="" data-original-title="Horas programadas"><i class="fa fa-clock-o"></i> ' + formatoHora(d.duracionTotalClases) + '</span>' +
                      '</span>' +
                      '</div>';
            } else {
              return 'Sin clases registradas';
            }
          }, "className": "not-mobile"},
        {data: "curso", name: "curso", render: function (e, t, d, m) {
            return d.curso + '<div id="sec-info-horario-' + d.id + '"></div>';
          }, "className": "not-mobile"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            var estado = '';
            if (estados[d.estado] !== undefined && estadosCambio[d.estado] !== undefined) {
              estado = '<div class="sec-btn-editar-estado" data-idtabla="tab-lista" data-idselestados="sel-estados" data-tipocambio="1">' +
                      '<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '">' +
                      '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>' +
                      '</a>' +
                      '</div>' +
                      (d.estado === estadoCuotaProgramada && d.fechaUltimaClase ? '<small class="text-red">(Última clase: ' + formatoFecha(d.fechaUltimaClase) + ')</small><br/>' : '');
            } else if (estados[d.estado] !== undefined) {
              estado = '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span><br/>' +
                      (d.estado === estadoCuotaProgramada && d.fechaUltimaClase ? '<small class="text-red">(Última clase: ' + formatoFecha(d.fechaUltimaClase) + ')</small><br/>' : '');
            }
            return estado + '<span class="text-info">(Nivel ' + d.nivelIngles + ')</span>';
          }, "className": "text-center not-mobile"},
        {data: "totalPagos", name: "totalPagos", render: function (e, t, d, m) {
            return 'S/. ' + redondear(d.pagoAcumulado, 2) + '<br/>' +
                    '<span class="text-info">(' + d.totalPagos + ' pago' + (d.totalPagos > 1 ? 's' : '') + ')</span>';
          }, "className": "text-center not-mobile"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", width: "12%", render: function (e, t, d, m) {
            return formatoFecha(d.fechaRegistro, true) + '<br/>' +
                    '<span class="text-info">(Inicio de clases:<br/>' + formatoFecha(d.fechaInicioClase) + ')</span>';
          }, "className": "text-center not-mobile"},
        {data: "id", name: "entidad.id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                    '<li>' +
                    '<a href="' + (urlPerfil.replace("/0", "/" + d.id)) + '" title="Ver perfil"><i class="fa fa-eye"></i></a>' +
                    '</li>' +
                    '<li>' +
                    '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                    '</li>' +
                    '<li>' +
                    '<a href="javascript:void(0);" title="Eliminar alumno" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de este alumno?\', \'tab-lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                    '<i class="fa fa-trash"></i>' +
                    '</a>' +
                    '</li>' +
                    '</ul>';
          }, className: "text-center"}
      ],
      initComplete: function (s, j) {
        establecerBotonRecargaTabla("tab-lista");
        establecerCabecerasBusquedaTabla("tab-lista");
      },
      drawCallback: function (s) {
        CargarHorarios();
      }
    });
  }
}

var datosHorariosCargados = [];
/* - */function CargarHorarios() {
  urlHorarioMultiple = (typeof (urlHorarioMultiple) === "undefined" ? "" : urlHorarioMultiple);
  if (urlHorarioMultiple !== "") {
    //Horarios
    var ids = jQuery.map($("#tab-lista").DataTable().rows().ids(), function (ele) {
      if (!datosHorariosCargados.some(function (dhc) {
        return dhc.idEntidad === ele;
      })) {
        return ele;
      }
    });
    if (ids.length > 0) {
      llamadaAjax(urlHorarioMultiple, "POST", {"idsEntidades": ids}, true, function (datos) {
        datosHorariosCargados = datosHorariosCargados.concat(datos);
        MostrarHorarios();
      });
    } else {
      MostrarHorarios();
    }
  }
}
/* - */function MostrarHorarios() {
  datosHorariosCargados.forEach(function (d) {
    $("#sec-info-horario-" + d.idEntidad).html(obtenerTextoHorario($.parseJSON(d.datosHorario)));
  });
}

function abrirModalListaClases(id) {
  var tr = $("#" + id);
  var fila = $("#tab-lista").DataTable().row(tr);
  var datosAlumno = fila.data();

  $.blockUI({message: "<h4>Cargando...</h4>"});
  actualizarListaClases(datosAlumno.id, function () {
    $("#mod-lista-clases").modal("show");
    $("#mod-lista-clases").find(".modal-title").html("Lista de clases " + (datosAlumno.sexo === "F" ? "de la alumna " : "del alumno ") + datosAlumno.nombre + " " + datosAlumno.apellido);
    $("body").unblock();
  }, function () {
    $("body").unblock();
  });
}