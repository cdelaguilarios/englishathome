@extends("layouts.master")
@section("titulo", "Ficha " . ($profesor->sexo == "F" ? "de la profesora" : "del profesor") . " " . $profesor->nombre . " " .  $profesor->apellido)

@section("section_script")
@if(isset($impresionDirecta) && $impresionDirecta)
<script>
  window.onafterprint = function (e) {
    $(window).off('mousemove', window.onafterprint);
    window.close();
  };
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
  table{
    width: 100%;
    margin-top: 50px;
  }
  td {
    padding: 6px 0;
    vertical-align: top;
  }
  .profile-user-img{
    padding: 0;
    border: 0;
  }
  .fa-calendar-check-o{
    display: none;
  }
  #sec-info-horario ul {
    margin-left: -40px
  }
  ul{
    margin-left: -25px;
  }
  .login-logo, .register-logo{
    font-size: 30px;
  }
</style>
@endsection

@section("content")
<table>
  <tbody>    
    <tr>
      <td style="width: 28%"><strong><i class="fa fa-envelope"></i> Correo electrónico:</strong></td>
      <td style="width: 57%">{{ $profesor->correoElectronico }}</td>  
      <td colspan="2" rowspan="4">
        <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($profesor->imagenPerfil) && $profesor->imagenPerfil != "" ? $profesor->imagenPerfil : "-"), "tip" => ($profesor->sexo == "F" ? "f" : "m")]) }}" alt="Profesor{{ $profesor->sexo == "F" ? "a" : "" }} {{ $profesor->nombre . " " .  $profesor->apellido }}">
      </td>
    </tr>
    <tr>
      <td><strong><i class="fa fa-fw fa-calendar"></i> Horario:</strong></td>
      <td>@include("util.horario", ["horario" => $profesor->horario, "modo" => "visualizar"])</td>
    </tr> 
    @if(isset($profesor->cursos) && count($profesor->cursos) > 0) 
    <tr>
      <td><strong><i class="fa fa-fw flaticon-favorite-book"></i> Cursos:</strong></td>
      <td>
        <ul>
          @foreach($profesor->cursos as $curso)
          <li>{{ $cursos[$curso->idCurso] }}</li>
          @endforeach
        </ul>
      </td> 
    </tr>
    @endif
    <tr>
      <td><strong><i class="fa fa-map-marker margin-r-5"></i> Dirección:</strong></td>
      <td>{{ $profesor->direccion }}{!! ((isset($profesor->numeroDepartamento) && $profesor->numeroDepartamento != "") ? "<br/>Depto./Int " . $profesor->numeroDepartamento : "") !!}{!! ((isset($profesor->referenciaDireccion) && $profesor->referenciaDireccion != "") ? " - " . $profesor->referenciaDireccion : "") !!}<br/>{{ $profesor->direccionUbicacion }}</td>
    </tr>
    @if(isset($profesor->telefono) && trim($profesor->telefono) != "")
    <tr>
      <td><strong><i class="fa fa-phone margin-r-5"></i> Teléfono:</strong></td>
      <td>{{ $profesor->telefono }}</td>
    </tr>
    @endif
  </tbody>
</table>
@endsection

