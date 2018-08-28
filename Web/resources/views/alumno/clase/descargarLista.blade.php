@extends("layouts.master")
@section("titulo", "Lista de clases " . ($alumno->sexo == "F" ? "de la alumna" : "del alumno") . " " . $alumno->nombre . " " .  $alumno->apellido)

@section("tituloImpresion")
<b>Lista de clases  {{ $alumno->sexo == "F" ? "de la alumna" : "del alumno" }}<br/>{{ $alumno->nombre . " " .  $alumno->apellido }}<br/><small><i class="fa fa-fw fa-envelope"></i> {{ $alumno->correoElectronico }}<br/><i class="fa fa-fw fa-phone"></i> {{ $alumno->telefono }}</small></b>
@endsection

@section("section_script")
@if(isset($impresionDirecta) && $impresionDirecta)
<script>
  window.onafterprint = function (e) {
    $(window).off('mousemove', window.onafterprint);
    window.close();
  };

  const $patchedStyle = $('<style media="print">')
  .text(`
    img { max-width: none !important; }
    a[href]:after { content: ""; }
  `)
  .appendTo('head');
  window.print();
  
  setTimeout(function () {
    $(window).one('mousemove', window.onafterprint);
  }, 500);
</script>
@endif
@endsection

@section("section_style")
<style>
  body{
    font-size: 14px;
  }
  .titulo{
    text-align: center;
  }
  .login-logo, .register-logo{
    font-size: 30px;
  }
  .sec-ficha-nombre-entidad small{
    font-size: 70%;
    line-height: 1.2;
    display: block;
    padding-top: 10px;
  }
  @media print {
    html, body {
      width: 210mm;
      height: 297mm;        
    }
    .pagina-impresion {
      margin: 0;
      page-break-after: always;
    }
    body {-webkit-print-color-adjust: exact;}
  }
</style>
@endsection

@section("content")
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
          <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Datos</th>
            <th>Estado</th>
          </tr>
          @php
          $numeroPeriodo = -1;
          $nroClase = 1;
          @endphp
          @foreach ($clases as $clase)
            @php
            $fechaInicio = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $clase->fechaInicio);
            $fechaFin = \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $clase->fechaFin);
            $estadosClase = App\Helpers\Enum\EstadosClase::listar();
            @endphp
            <tr>
              <td>{{ $nroClase  }}</td>
              <td>{{ $fechaInicio->format("d/m/Y") . " - De " . $fechaInicio->format("H:i") . " a " . $fechaFin->format("H:i") }}</td>
              <td>
                <b>Período:</b> {{ $clase->numeroPeriodo }}<br/>
                <b>Duración:</b> {{ App\Helpers\Util::formatoHora($clase->duracion) }}<br/>
                <b>Profesor:</b> {{ ($clase->idProfesor !== null && $clase->nombreProfesor !== null && $clase->nombreProfesor !== '' ? $clase->nombreProfesor . ' ' . $clase->apellidoProfesor : 'Sin profesor asignado') }}
              </td>
              <td><span class="label {{ $estadosClase[$clase->estado][1] }} btn-estado">{{ $estadosClase[$clase->estado][0] }}</span></td>
            </tr>
            @php
            $nroClase++;
            @endphp
          @endforeach          
        </table>
      </div>
    </div>
  </div>
</div>
@endsection