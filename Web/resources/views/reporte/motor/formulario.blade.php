<div class="row">
  <div class="col-sm-12">
    <div class="box box-info">
      <div class="box-header">
        <h3 id="sec-titulo" class="box-title"></h3>
        <div id="sec-men-alerta"></div>
      </div>
      <div id="sec-paso-1" class="box-body" style="display: none">      
        @foreach ($entidades as $id => $datos)
        <div id="sec-entidades" class="col-sm-3">
          <button type="button" class="btn-entidad" rel="{{ $id }}">{!! $datos[4] !!} {{ $datos[0] }}</button>
        </div>
        @endforeach
      </div>
      <div id="sec-paso-2" class="box-body" style="display: none">  
        <div id="sec-campos"></div>
      </div>
      <div id="sec-paso-3" class="box-body" style="display: none">
        <div id="sec-entidades-relacionadas"></div>
        <div id="sec-campos-entidades-relacionadas"></div>
      </div>
      <div id="sec-paso-4" class="box-body" style="display: none">
        <div id="sec-filtro"></div>
      </div>
      <div id="sec-paso-5" class="box-body" style="display: none">
      </div>
      <div class="box-footer">    
        <div class="form-group">
          <div class="col-sm-12">  
            <button id="btn-guardar" type="submit" class="btn btn-success pull-right" style="display:none" >Guardar</button>  
            <button id="btn-siguiente" type="button" class="btn btn-primary pull-right" style="display:none" >Siguiente</button>  
            <button id="btn-anterior" type="button" class="btn btn-default pull-right" style="display:none" >Anterior</button>           
          </div>
        </div>
      </div>
      {{ Form::hidden("entidad") }}
    </div>
  </div>
</div>