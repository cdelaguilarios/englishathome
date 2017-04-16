@extends("layouts.master")
@section("titulo", "Alumnos")

@section("section_script")
@endsection

@section("section_style")
<style>
  table{
    width: 100%;
  }
  .titulo{
    text-align: center;
  }
</style>
@endsection

@section("breadcrumb")
@endsection

@section("content")
<table>
  <tbody>
    <tr>
      <td>
        <h2 class="titulo">Ficha del alumn{{ $alumno->sexo == "F" ? "a" : "o" }}<br/>{{ $alumno->nombre . " " .  $alumno->apellido }}</h2>
      </td>
    </tr>
  </tbody>
</table>
<table>
  <thead>
    <tr>
      <th colspan="4">
        <h3>Datos principales</h3>
      </th>
    </tr>
  </thead>
  <tbody>    
    <tr>
      <td><strong><i class="fa fa-envelope"></i> Correo electrónico:</strong></td>
      <td>{{ $alumno->correoElectronico }}</td>  
      <td colspan="2" rowspan="4">
        <img class="profile-user-img img-responsive img-circle" src="{{ route("archivos", ["nombre" => (isset($alumno->imagenPerfil) && $alumno->imagenPerfil != "" ? $alumno->imagenPerfil : "-"), "tip" => ($alumno->sexo == "F" ? "f" : "m")]) }}" alt="Alumn{{ $alumno->sexo == "F" ? "a" : "o" }} {{ $alumno->nombre . " " .  $alumno->apellido }}">
      </td>
    </tr>
    <tr>
      <td><strong><i class="fa fa-phone margin-r-5"></i> Teléfono:</strong></td>
      <td>{{ $alumno->telefono }}</td>
    </tr>
    <tr>
      @if(isset($alumno->numeroDocumento))
      <td><strong><i class="fa fa-user margin-r-5"></i> {{ (isset($alumno->idTipoDocumento) ? App\Models\TipoDocumento::listarSimple()[$alumno->idTipoDocumento] . ":" : "") }}</strong></td>
      <td>{{ $alumno->numeroDocumento }}</td>
      @endif
    </tr>
    <tr>
      @if(isset($alumno->fechaNacimiento))
      <td><strong><i class="fa fa-birthday-cake margin-r-5"></i> Fecha de nacimiento:</strong></td>
      <td>{{ \Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $alumno->fechaNacimiento)->format("d/m/Y") }}</td> 
      @endif 
    </tr>
  </tbody>
</table>
<table>
  <thead>
    <tr>
      <th colspan="2">
        <h3>Datos de clase</h3>
      </th>
    </tr>
  </thead>
  <tbody>    
    <tr>
      <td><strong><i class="fa fa-fw flaticon-favorite-book"></i> Curso:</strong></td>
      <td>{{ $cursos[$alumno->idCurso] }}</td>  
    </tr>
    <tr>
      <td><strong><i class="fa fa-map-marker margin-r-5"></i> Dirección:</strong></td>
      <td>{{ $alumno->direccion }}{!! ((isset($alumno->numeroDepartamento) && $alumno->numeroDepartamento != "") ? "<br/>Depto./Int " . $alumno->numeroDepartamento : "") !!}{!! ((isset($alumno->referenciaDireccion) && $alumno->referenciaDireccion != "") ? " - " . $alumno->referenciaDireccion : "") !!}<br/>{{ $alumno->direccionUbicacion }}</td>
    </tr>
    <tr>
      <td><strong><i class="fa fa-fw fa-calendar"></i> Horario:</strong></td>
      <td>@include("util.horario", ["horario" => $alumno->horario, "modo" => "visualizar", "bloquearEdicion" => TRUE])</td>
    </tr>
  </tbody>
</table>
@endsection

