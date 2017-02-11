google.charts.load("current", {"packages": ["bar"]});
google.charts.setOnLoadCallback(cargarReportePagos);

function cargarReportePagos() {
  cargarReporte(true, "pagos", "sol", "soles");
}
