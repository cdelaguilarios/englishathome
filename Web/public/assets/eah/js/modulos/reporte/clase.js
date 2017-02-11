google.charts.load("current", {"packages": ["bar"]});
google.charts.setOnLoadCallback(cargarReporteClases);

function cargarReporteClases() {
  cargarReporte(false, "clases", "clase", "clases");
}