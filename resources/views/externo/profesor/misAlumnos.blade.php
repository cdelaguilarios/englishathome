@extends("externo.layouts.master")
@section("titulo", "Mis alumnos")

@section("content")
<div class="row">
  <div class="col-md-12">    
    <div class="nav-tabs-custom">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#alumnos-vigentes" data-toggle="tab">Alumnos vigentes</a></li>
        @if(isset($alumnosAntiguos))
        <li><a href="#alumnos-antiguos" data-toggle="tab">Alumnos antiguos</a></li>
        @endif
      </ul>
      <div class="tab-content">
        <div id="alumnos-vigentes" class="active tab-pane"><div class="row">
            <div class="col-md-12">
              <div class="box-body no-padding">
                @if(isset($alumnosVigentes))
                @foreach ($alumnosVigentes as $alumnoVigente)      
                @include("externo.profesor.util.elementoAlumno", ["alumno" => $alumnoVigente]) 
                @endforeach
                @else
                <div class="row text-center">
                  <h4>No tiene asignados alumnos actualmente</h4>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        @if(isset($alumnosAntiguos))
        <div id="alumnos-antiguos" class="tab-pane"><div class="row">
            <div class="col-md-12">
              <div class="box-body no-padding">
                @foreach ($alumnosAntiguos as $alumnoAntiguo)       
                @include("externo.profesor.util.elementoAlumno", ["alumno" => $alumnoAntiguo]) 
                @endforeach
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection