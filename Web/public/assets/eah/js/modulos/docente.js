window.addEventListener("load", verificarJqueryDocente, false);
function verificarJqueryDocente() {
  ((window.jQuery && jQuery.ui) ? cargarSeccionDocente() : window.setTimeout(verificarJqueryDocente, 100));
}

function cargarSeccionDocente() {
  cargarListaDocentes();
  cargarFormularioExperienciaLaboral();

  $("#bus-tipo, #bus-estado, #bus-sexo, #bus-curso, input[name='horario']").change(function () {
    $("#tab-lista").DataTable().ajax.reload();
  });
  
  if (util.obtenerParametroUrlXNombre("sec") === "experiencia-laboral") {
    $("a[href='#experiencia-laboral']").trigger("click");
  }
}

function cargarListaDocentes() {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  tipoDocenteProfesor = (typeof (tipoDocenteProfesor) === "undefined" ? "" : tipoDocenteProfesor);
  urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
  urlEditarPostulante = (typeof (urlEditarPostulante) === "undefined" ? "" : urlEditarPostulante);

  if (urlListar !== "" && estados !== "" && tipoDocenteProfesor !== "" && urlPerfilProfesor !== "" && urlEditarPostulante !== "") {
    $("#tab-lista").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: urlListar,
        type: "POST",
        data: function (d) {
          d._token = $("meta[name=_token]").attr("content");
          d.tipoDocente = $("#bus-tipo").val();
          d.estadoDocente = $("#bus-estado").val();
          d.sexoDocente = $("#bus-sexo").val();
          d.idCursoDocente = $("#bus-curso").val();
          d.horarioDocente = $("input[name='horario']").val();
        }
      },
      autoWidth: false,
      responsive: true,
      order: [[3, "desc"]],
      columns: [
        {data: "nombreCompleto", name: "nombreCompleto", render: function (e, t, d, m) {
            return '<a href=' + ((d.tipo === tipoDocenteProfesor ? urlPerfilProfesor : urlEditarPostulante).replace('/0', '/' + d.id)) + ' title="Ver perfil del profesor" target="_blank">' + d.nombreCompleto + '</a>';
          }},
        {data: "correoElectronico", name: "entidad.correoElectronico"},
        {data: "estado", name: "entidad.estado", render: function (e, t, d, m) {
            if (estados[d.estado] !== undefined) {
              return '<span class="label ' + estados[d.estado][1] + ' btn-estado">' + estados[d.estado][0] + '</span>';
            } else {
              return "";
            }
          }, className: "text-center"},
        {data: "fechaRegistro", name: "entidad.fechaRegistro", render: function (e, t, d, m) {
            return utilFechasHorarios.formatoFecha(d.fechaRegistro, true);
          }, className: "text-center"},
      ],
      initComplete: function (s, j) {
        utilTablas.establecerBotonRecargaTabla("tab-lista");
      }
    });
  }
}

function cargarFormularioExperienciaLaboral() {
  $("#formulario-experiencia-laboral-docente").validate({
    ignore: "",
    rules: {
      audio: {
        validarAudio: true,
        archivoTamanho: 2097152
      }
    },
    messages: {
      audio: {
        archivoTamanho: "Archivo debe ser menor a 2MB."
      }
    },
    submitHandler: function (f) {
      if (confirm("¿Está seguro que desea guardar los cambios de los datos del docente?")) {
        $.blockUI({message: "<h4>Guardando datos...</h4>"});
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
}


