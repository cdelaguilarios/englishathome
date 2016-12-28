$(document).ready(function () {
  urlListar = (typeof (urlListar) === "undefined" ? "" : urlListar);
  urlEditar = (typeof (urlEditar) === "undefined" ? "" : urlEditar);
  urlActualizarEstado = (typeof (urlActualizarEstado) === "undefined" ? "" : urlActualizarEstado);
  urlCotizacion = (typeof (urlCotizacion) === "undefined" ? "" : urlCotizacion);
  urlEliminar = (typeof (urlEliminar) === "undefined" ? "" : urlEliminar);
  estados = (typeof (estados) === "undefined" ? "" : estados);
  if (urlListar !== "" && urlEditar !== "" && urlCotizacion !== "" && urlEliminar !== "" && estados !== "") {
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
      columns: [
        {data: "nombre", name: "nombre", render: function (e, t, d, m) {
            return (d.nombre !== null ? d.nombre : "") + " " + (d.apellido !== null ? d.apellido : "");
          }},
        {data: "telefono", name: "telefono"},
        {data: "correoElectronico", name: "correoElectronico"},
        {data: "estado", name: "estado", render: function (e, t, d, m) {
            return '<div class="sec-btn-editar-estado"><a href="javascript:void(0);" class="btn-editar-estado" data-id="' + d.id + '" data-estado="' + d.estado + '"><span class="label ' + estados[d.estado][1] + ' btn_estado">' + estados[d.estado][0] + '</span></a></div>';
          }},
        {data: "id", name: "id", orderable: false, "searchable": false, width: "5%", render: function (e, t, d, m) {
            return '<ul class="buttons">' +
                '<li>' +
                '<a href="' + (urlEditar.replace("/0", "/" + d.id)) + '" title="Editar datos"><i class="fa fa-pencil"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="' + (urlCotizacion.replace("/0", "/" + d.id)) + '" title="Enviar cotización"><i class="fa fa-dollar"></i></a>' +
                '</li>' +
                '<li>' +
                '<a href="javascript:void(0);" title="Eliminar interesado" onclick="eliminarElemento(this, \'¿Está seguro que desea eliminar los datos de esta persona interesada?\', \'tab_lista\')" data-id="' + d.id + '" data-urleliminar="' + ((urlEliminar.replace("/0", "/" + d.id))) + '">' +
                '<i class="fa fa-trash"></i>' +
                '</a>' +
                '</li>' +
                '</ul>';
          }}
      ],
      createdRow: function (r, data, index) {
        $("td", r).eq(3).addClass("text-center");
        $("td", r).eq(4).addClass("text-center");
      }
    });
  }
  $("#bus-estado").change(function () {
    $("#tab-lista").DataTable().ajax.reload();
  });
  $(window).click(function (e) {
    if (!$(e.target).closest('.sec-btn-editar-estado').length) {
      $(".sec-btn-editar-estado select").trigger("change");
    }
  });
  $(".btn-editar-estado").live("click", function () {
    $("#sel-estados").clone().val($(this).data("estado")).data("id", $(this).data("id")).data("estado", $(this).data("estado")).appendTo($(this).closest(".sec-btn-editar-estado"));
    $(this).remove();
    event.stopPropagation();
  });
  $(".sec-btn-editar-estado select").live("change", function () {
    var id = $(this).data("id");
    if (urlActualizarEstado !== "" && $(this).data("estado") !== $(this).val()) {
      llamadaAjax(urlActualizarEstado.replace("/0", "/" + id), "POST", {"estado": $(this).val()}, true);
    }
    $(this).closest(".sec-btn-editar-estado").append('<a href="javascript:void(0);" class="btn-editar-estado" data-id="' + id + '" data-estado="' + $(this).val() + '"><span class="label ' + estados[$(this).val()][1] + ' btn_estado">' + estados[$(this).val()][0] + '</span></a>');
    $(this).remove();
  });



  $("#formulario-interesado").validate({
    ignore: ":hidden",
    rules: {
      nombre: {
        required: true,
        validarAlfabetico: true
      },
      apellido: {
        required: true,
        validarAlfabetico: true
      },
      telefono: {
        required: true
      },
      correoElectronico: {
        required: true,
        email: true
      },
      cursoInteres: {
        required: true
      }
    },
    submitHandler: function (form) {
      if (confirm($("#btn-guardar").text() === "Guardar"
          ? "¿Está seguro que desea guardar los cambios de los datos de la persona interesada?"
          : "¿Está seguro que desea registrar los datos de esta persona interesada?"))
        form.submit();
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
    }
  });

  $("#formulario-interesado-cotizacion").validate({
    ignore: ":hidden,:not(:visible)",
    rules: {
      idCurso: {
        required: true
      },
      descripcionCurso: {
        required: true
      },
      metodologia: {
        required: true
      },
      cursoIncluye: {
        required: true
      },
      numeroHorasInversion: {
        required: true,
        number: true,
        min: 1
      },
      costoMaterialesIversion: {
        required: true,
        validarDecimal: true
      },
      totalInversion: {
        required: true,
        validarDecimal: true
      },
      correoCotizacionPrueba: {
        required: true,
        email: true
      }
    },
    submitHandler: function (form) {
      if (confirm("¿Está seguro que desea enviar esta cotización?"))
        form.submit();
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
    }
  });
  $("#id-curso").change(function () {
    urlDatosCurso = (typeof (urlDatosCurso) === "undefined" ? "" : urlDatosCurso);
    if (urlDatosCurso !== "") {
      llamadaAjax(urlDatosCurso.replace("/0", "/" + $(this).val()), "POST", {}, true,
          function (d) {
            $("#descripcion-curso").val(d.descripcion);
            $("#metodologia").val(d.metodologia);
            $("#curso-incluye").val(d.incluye);
          }
      );
    }
  });
  $("#id-curso").trigger("change");
  $("#btn-envio-cotización").click(function () {
    $("#correo-cotizacion-prueba").val("");
    $("#formulario-interesado-cotizacion").submit();
  });
  $("#btn-envio-cotización-prueba").click(function () {
    var camposFormularioInteresadoCotizacion = $("#formulario-interesado-cotizacion").not(":hidden,:not(:visible)");
    if (!camposFormularioInteresadoCotizacion.valid()) {
      return false;
    }
    $("#mod-correo-cotizacion-prueba").modal("show");
  });
});