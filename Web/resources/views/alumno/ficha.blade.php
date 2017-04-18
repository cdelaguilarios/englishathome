@extends("layouts.master")
@section("titulo", "Ficha " . ($alumno->sexo == "F" ? "de la alumna" : "del alumno") . " " . $alumno->nombre . " " .  $alumno->apellido)

@section("section_style")
<style>
  body{
    font-size: 16px;
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
  }
  .fa-calendar-check-o{
    display: none;
  }
  #sec-info-horario ul {
    margin-left: -40px
  }
</style>
@endsection

@section("content")
<table>
  <tbody>    
    <tr>
      <td><strong><i class="fa fa-envelope"></i> Correo electrónico:</strong></td>
      <td>{{ $alumno->correoElectronico }}</td>  
      <td colspan="2" rowspan="4">
        <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($alumno->imagenPerfil) && $alumno->imagenPerfil != "" ? $alumno->imagenPerfil : "-"), "tip" => ($alumno->sexo == "F" ? "f" : "m")]) }}" alt="Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}">
      </td>
    </tr><tr>
      <td><strong><i class="fa fa-fw fa-calendar"></i> Horario:</strong></td>
      <td>@include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar"])</td>
    </tr> 
    <tr>
      <td><strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso:</strong></td>
      <td>{{ $cursos[$alumno->idCurso] }}</td> 
    </tr>
    <tr>
      <td><strong><i class="fa fa-map-marker margin-r-5"></i> Dirección:</strong></td>
      <td>{{ $alumno->direccion }}{!! ((isset($alumno->numeroDepartamento) && $alumno->numeroDepartamento != "") ? "<br/>Depto./Int " . $alumno->numeroDepartamento : "") !!}{!! ((isset($alumno->referenciaDireccion) && $alumno->referenciaDireccion != "") ? " - " . $alumno->referenciaDireccion : "") !!}<br/>{{ $alumno->direccionUbicacion }}</td>
    </tr>
    <tr>
      <td><strong><i class="fa fa-phone margin-r-5"></i> Teléfono:</strong></td>
      <td>{{ $alumno->telefono }}</td>
    </tr>   
  </tbody>
</table>
@endsection

