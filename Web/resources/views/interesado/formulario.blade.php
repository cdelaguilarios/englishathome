<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Datos</h3>
                <button class="btn btn-default btn-clean pull-right" onclick="limpiarCampos();" type="button">Limpiar campos</button>
            </div>
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label('nombre', 'Nombres: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('nombre', null, ['class' => 'form-control', 'maxlength' =>'255']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('apellido', 'Apellidos: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('apellido', null, ['class' => 'form-control', 'maxlength' =>'255']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('telefono', 'Teléfono: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'maxlength' =>'30']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('correoElectronico', 'Correo electrónico (*): ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::email('correoElectronico', null, ['class' => 'form-control', 'maxlength' =>'245']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('consulta', 'Consulta: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('consulta', null, ['class' => 'form-control', 'maxlength' =>'255']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('cursoInteres', 'Curso de interes: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('cursoInteres', null, ['class' => 'form-control', 'maxlength' =>'255']) !!}
                    </div>
                </div>
                {!! Form::hidden('id') !!}
            </div>
            <div class="box-footer">    
                <span>(*) Campos obligatorios</span>
                <button id="btn-guardar" type="submit" class="btn btn-success pull-right" >{!! ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") !!}</button>
                <a href="{{ route('interesados') }}" type="button" class="btn btn-default pull-right" >Cancelar</a>
            </div>
        </div>
    </div>
</div>
