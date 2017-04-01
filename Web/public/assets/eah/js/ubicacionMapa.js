function verificarJqueryUbicacionMapa() {
  ((window.jQuery && jQuery.ui) ? inicializarMapa() : window.setTimeout(verificarJqueryUbicacionMapa, 100));
}

var posicionSel = null;
var mapa, umto = null;
function inicializarMapa() {
  var cen = {lat: -12.069890396610955, lng: -77.10353526868857};
  mapa = new google.maps.Map(document.getElementById("mapa"), {
    zoom: 12,
    center: cen
  });

  modoVisualizarMapa = (typeof (modoVisualizarMapa) === "undefined" ? "" : modoVisualizarMapa);
  if (!(typeof modoVisualizarMapa !== "" && modoVisualizarMapa === true)) {
    mapa.addListener("click", function (e) {
      umto = setTimeout(function () {
        seleccionarPosicionMapa(e.latLng);
      }, 200);
    });
    mapa.addListener("dblclick", function (e) {
      clearTimeout(umto);
    });
  }

  if ($("#mapa-bus").length > 0) {
    var iBusqueda = document.getElementById("mapa-bus");
    var cajaBusqueda = new google.maps.places.SearchBox(iBusqueda);
    mapa.controls[google.maps.ControlPosition.TOP_LEFT].push(iBusqueda);
    mapa.addListener("bounds_changed", function () {
      cajaBusqueda.setBounds(mapa.getBounds());
    });
    cajaBusqueda.addListener("places_changed", function () {
      var lugares = cajaBusqueda.getPlaces();
      if (lugares.length === 0) {
        return;
      }
      seleccionarPosicionMapa(lugares[0].geometry.location, true);
    });
  }
  $("#btn-ubicacion-mapa").click(function () {
    $("#mod-ubicacion-mapa").modal("show");
    google.maps.event.trigger(mapa, "resize");
    verificarPosicionSel();
  });
}
function verificarPosicionSel() {
  if ($("input[name='geoLatitud']").val() !== "" && $("input[name='geoLongitud']").val() !== "") {
    seleccionarPosicionMapa({lat: parseFloat($("input[name='geoLatitud']").val()), lng: parseFloat($("input[name='geoLongitud']").val())}, true);
  }
}
function buscarDireccionMapa(direccion) {
  if (direccion !== undefined && direccion !== null && direccion !== "") {
    var mapaBus = document.getElementById("mapa-bus");
    mapaBus.value = direccion;
    google.maps.event.trigger(mapaBus, "focus");
    google.maps.event.trigger(mapaBus, "keydown", {
      keyCode: 13
    });
  }
}
function seleccionarPosicionMapa(pos, cambiarZoom) {
  if (posicionSel !== null) {
    posicionSel.setMap(null);
  }
  posicionSel = new google.maps.Marker({
    position: pos,
    map: mapa
  });
  if (cambiarZoom) {
    mapa.setZoom(18);
  }
  mapa.setCenter(pos);
  $("input[name='geoLatitud']").val((typeof pos.lat === 'function') ? pos.lat() : pos.lat);
  $("input[name='geoLongitud']").val((typeof pos.lng === 'function') ? pos.lng() : pos.lng);
}
var plvdbm = true;
function verificarDatosBusquedaMapa() {
  if (plvdbm) {
    plvdbm = false;
  } else {
    if ($("#direccion").val() !== "" && $("#codigo-distrito option:selected").text() !== "" &&
        $("#codigo-provincia option:selected").text() !== "" && $("#codigo-departamento option:selected").text() !== "") {
      buscarDireccionMapa($("#direccion").val() + " " + $("#codigo-distrito option:selected").text() +
          ", " + $("#codigo-provincia option:selected").text() + ", " + $("#codigo-departamento option:selected").text());
    }
  }
}