<div class="row">
    <div class="col-md-12">
        <div id="sec-mensajes-clase"></div>
        <div id="sec-clase-1">      
            <div class="box-body">
                <table id="tab-lista-clases" class="table table-bordered table-hover">
                    <thead>
                        <tr> 
                            <th>Seleccionar</th>  
                            <th>Alumno</th>    
                            <th>Fecha</th>
                            <th>Duración</th>
                            <th>Pago por hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                </table>  
            </div>
            <div id="sec-clase-11">
                <a id="btn-nuevo-pago-clase" type="button" class="btn btn-primary btn-sm">Nueva pago</a>                 
            </div>
        </div>         
        @include("profesor.clase.formularioPago") 
    </div>
    <script>
        var urlListarClases = "{{ route('profesores.clases.listar', ['id' => $idProfesor]) }}";
        var urlPerfilAlumnoClase = "{{ route('alumnos.perfil', ['id' => 0]) }}";
        var estadosClase = {!!  json_encode(App\Helpers\Enum\EstadosClase::Listar()) !!};
    </script>
    <script src='{{ asset('assets/eah/js/modulos/profesor/clase.js')}}'></script>