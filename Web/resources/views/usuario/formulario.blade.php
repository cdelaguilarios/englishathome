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
                    {!! Form::label('email', 'Correo electrónico (*): ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::email('email', null, ['class' => 'form-control', 'maxlength' =>'245']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('imagenPerfil', 'Imagen de perfil: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::file('imagenPerfil', null) !!}
                    </div>
                    @if (isset($usuario->rutaImagenPerfil) && !empty($usuario->rutaImagenPerfil) && $usuario->rutaImagenPerfil != "NULL")
                    <div class="col-sm-3">
                        <a href="{{ route('imagenes', ['rutaImagen' => $usuario->rutaImagenPerfil]) }}" target="_blank">
                            <img src="{{ route('imagenes', ['rutaImagen' => $usuario->rutaImagenPerfil]) }}" width="40"/>
                        </a>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::hidden('ModoEdicion', ((isset($modo) && $modo == "registrar") ? "0" : "1"), ['id' => 'ModoEdicion']) !!}
                    {!! Form::label('password', ((isset($modo) && $modo == "registrar") ? 'Contraseña (*): ' : 'Nueva contraseña: '), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::password('password', ['class' => 'form-control', 'minlength' =>'6', 'maxlength' =>'30']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('password_confirmation', 'Confirmar contraseña' . ((isset($modo) && $modo == "registrar") ? ' (*): ' : ': '), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::password('password_confirmation', ['class' => 'form-control', 'minlength' =>'6', 'maxlength' =>'30']) !!}
                    </div>
                </div>
                @if($usuarioActual->rol == App\Helpers\Enum\RolesUsuario::Principal)
                <div class="form-group">
                    {!! Form::label('rol', 'Rol de usuario: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::select('rol', $roles,
                        (isset($usuario) ? $usuario->rol : App\Helpers\Enum\RolesUsuario::Principal)
                        , ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('estado', 'Estado: ', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::select('estado', $estados,
                        (isset($usuario) ? $usuario->estado : App\Helpers\Enum\EstadosUsuario::Activo)
                        , ['class' => 'form-control']) !!}
                    </div>
                </div>
                @else
                {!! Form::hidden('rol', (isset($usuario) ? $usuario->rol : App\Helpers\Enum\RolesUsuario::Secundario)) !!} 
                {!! Form::hidden('estado', (isset($usuario) ? $usuario->estado : App\Helpers\Enum\EstadosUsuario::Activo)) !!}    
                @endif
                {!! Form::hidden('id') !!}
            </div>
            <div class="box-footer">    
                <span>(*) Campos obligatorios</span>
                <button id="btn-guardar" type="submit" class="btn btn-success pull-right" >{!! ((isset($modo) && $modo == "registrar") ? "Registrar" : "Guardar") !!}</button>
                <a href="{{ route('usuarios') }}" type="button" class="btn btn-default pull-right" >Cancelar</a>
            </div>
        </div>
    </div>
</div>
