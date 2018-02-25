<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>English at home</title>
    <style>
      * {
        margin:0;
        padding:0;
      }
      * {
        font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;
      }
      img {
        max-width:100%;
      }
      .collapse {
        padding-right:15px;
        padding:0;
      }
      body {
        -webkit-font-smoothing:antialiased;
        -webkit-text-size-adjust:none;
        width:100%!important;
        height: 100%;
      }
      a {
        color:#aaaaaa;
        font-size:12px;
      }
      .bt {
        padding-top:10px;
      }
      .btn-inscripcion{
        color: #fff;
        background-color: #286090;
        border-color: #122b40;
        text-decoration: none;
        outline-offset: -2px;
        border-radius: 6px;
        margin: 2px 0 2px 4px;
        border: 1px solid transparent;
        padding: 2px 6px;
        font-weight: 400;
        font-size: 12px;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer !important;
      }
      p.callout {
        padding:9px;
        font-size:12px;
      }
      p.text {
        padding-left:5px;
        font-size:12px;
      }
      p.left {
        padding:5px;
        font-size:12px;
        text-align:left;
      }
      .prod {
        margin:0;
        padding:0;
        color:#aaaaaa;
      }
      .callout a {
        font-weight:bold;
        color: #aaaaaa;
      }
      table.head-wrap {
        width:100%;
      }
      .header.container table td.logo {
        padding:15px;
      }
      .header.container table td.label {
        padding:15px;
        padding-left: 0px;
      }
      table.body-wrap {
        width: 100%;
      }
      table.footer-wrap {
        width:100%;
        background-color: #f5f5f5;
        height: 50px;
      }
      table.footer-wrap2 {
        width: 100%;
      }
      h1,h2,h3,h4,h5,h6 {
        font-family:"Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
        line-height:1.1;
        margin-bottom:5px;
        color:#000;
      }
      h1 small,h2 small,h3 small,h4 small,h5 small,h6 small {
        font-size:60%;
        color:#6f6f6f;
        line-height:0;
        text-transform:none;
      }
      h1 {
        font-weight:200;
        font-size:18px;
        padding:20px;
        letter-spacing:3px;
        font-weight:300;
      }
      h2 {
        font-weight:200;
        font-size:37px;
      }
      h3 {
        font-weight:500;
        font-size:27px;
      }
      h4 {
        font-weight:500;
        font-size:23px;
      }
      h5 {
        font-weight:900;
        font-size:13px;
        color:#c2a67e;
      }
      h6 {
        font-weight:900;
        font-size:14px;
        text-transform:uppercase;
        color:#444;

      }
      h7 {
        font-weight:900;
        font-size:14px;
        text-transform:uppercase;
        color:#444;
        padding:5px;
      }
      .collapse {
        margin:0!important;
      }
      p,ul {
        margin-bottom:2px;
        font-weight:normal;
        font-size:12px;
        line-height:1.3;
      }
      p.lead {
        font-size:13px;
      }
      p.last {
        margin-bottom:0px;
      }
      ul li {
        margin-left:5px;
        list-style-position: inside;
      }
      .container {
        display:block!important;
        max-width:600px!important;
        margin:0 auto!important;
        clear:both!important;
      }
      .content {
        padding:5px;
        max-width:600px;
        margin:0 auto;
        display: block;
      }
      .content table {
        width: 100%;
      }
      .column {
        width:300px;
        float:left;
      }
      .column tr td {
        padding:5px;
      }
      .column-wrap {
        padding:0!important;
        margin:0 auto;
        max-width:600px!important;
      }
      .column table {
        width:100%;
      }
      .social .column {
        width:280px;
        min-width:279px;
        float:left;
      }
      .column3 {
        width:300px;
        float:left;
      }
      .column3 tr td {
        padding:1px;
      }
      .column3-wrap {
        padding:0!important;
        margin:0 auto;
        max-width:600px!important;
      }
      .column3 table {
        width:100%;
      }
      .column2 {
        width:240px;
        float:left;
      }
      .column2 tr td {
        padding:5px;
      }
      .column2-wrap {
        padding:0!important;
        margin:0 auto;
        max-width:600px!important;
      }
      .column2 table {
        width:100%;
      }
      .social .column {
        width:280px;
        min-width:279px;
        float: left;
      }
      .prod {
        width:200px;
        float:left;
      }
      .prod tr td {
        padding:5px;
      }
      .prod-wrap {
        padding:0!important;
        margin:0 auto;
        max-width:600px!important;
      }
      .prod table {
        width:100%;
      }
      .prod .column {
        width:200px;
        min-width:200px;
        float: left;
      }
      .clear {
        display:block;
        clear: both;
      }
      @media only screen and (max-width:600px) {
        a[class="btn"] {
          display:block!important;
          margin-bottom:10px!important;
          background-image:none!important;
          margin-right:0!important;
        }
        div[class="column"] {
          width:auto!important;
          float:none!important;
        }
        div[class="column2"] {
          width:auto!important;
          float:none!important;
        }
        div[class="column3"] {
          width:auto!important;
          float:none!important;
        }
        table[class="top"] {
          width:auto!important;
          float:none!important;
        }
        .prod {
          width:150px;
          float:left;
        }
        table.social div[class="column"] {
          width: auto!important;
        }
      }
    </style>
    <style>
      /*-----------------------------ESTILOS PROPIOS-------------------------------*/
      body {
        color: rgb(102,102,102);
      }
      .header .content {
        padding: 15px;
      }
      .header span {
        color: rgb(255,255,255);
        font-size: 10px;
        line-height: 15px;
      }
      .center {
        text-align: center;
      }
      h2 {
        color: #084C9E;
        font-weight: 700;
        font-size: 30px;
      }  
      .justify {
        padding: 5px;
        font-size: 12px;
        text-align: justify;
        line-height: 1.3;
        clear: both;
      }    
      .sub-title {
        color: #084C9E;
        font-size: 20px;
        font-weight: 500;
        line-height: 1.3;
        text-align: left;
      }
      .contenido {
        padding: 0 10px;
      }

      .sec-curso-incluye .contenido ul {
        margin-left: 25px;
        text-align: left;
        font-size: 12px;
        line-height: 1.3;
      }
      .sec-curso-incluye .contenido ul li {
        margin-left: 0;
        padding-left: 5px;
        list-style-position: initial;
      }

      .sec-inversion table {
        padding: 0 10px;
        font-size: 12px !important;
        line-height: 1.3 !important;
        width: 80% !important;
        border-collapse: collapse;
      }
      .sec-inversion table tr{
        height:17px;
      }
      .sec-inversion table tr td{
        padding:2px 3px;
        vertical-align:bottom;
        border:1px solid rgb(0,0,0);
        font-weight:bold;
        text-align:center
      }
      .sec-inversion table tr.fila-impar td{ 
        background-color:#FFF; 
        color:#000;
      }
      .sec-inversion table tr.fila-par td { 
        background-color:rgb(255,0,0);
        color:rgb(255,255,255); 
      }
      .sec-inversion table tr.fila-cabecera td{
        background-color:rgb(17,85,204); 
        color:rgb(255,255,255);
      }
      .sec-inversion table tr.fila-total td{
        background-color:rgb(28,69,135) !important; 
        color:rgb(255,255,255) !important;
      }      
      .sec-inversion-cuotas p{        
        padding:5px;
        font-size:12px;
        text-align:left;
        margin: 20px 15px;
      }
      .sec-inversion-cuotas p span {
        font-size: 12px;
        color: #FFF;
        background-color: rgb(28,69,135);
        font-weight: inherit;
        padding: 5px 100px 5px 5px;        
        line-height: 1.3;
        text-align: left;
      }

      .sec-proceso-inscripcion ol {
        margin-left: 25px;
        text-align: left;
        font-size: 12px;
        line-height: 1.3;
      }
      .sec-proceso-inscripcion ol li {
        padding-left: 5px;
      }
      .sec-proceso-inscripcion table.sec-cuentas-bancarias {
        font-size: 12px;
        line-height: 1.3;
      }
      .sec-proceso-inscripcion table.sec-cuentas-bancarias td {
        vertical-align: top;
        padding: 10px 0;
      }
      .sec-proceso-inscripcion table.sec-cuentas-bancarias img {
        border-radius: 10px;
      }

      .sec-mensaje-guia .container{
        padding: 15px;
        text-align: center;
      }
      .sec-mensaje-guia a {
        color: #FFF;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        line-height: 15px;
      }

      .sec-mensaje-notas-adicionales .sub-title {
        color: #FFF;
      }
      .sec-mensaje-notas-adicionales .container {
        color: #FFF;
        padding: 10px;
      }
      .sec-notas-adicionales ul {
        color: #FFF;
        margin-left: 25px;
        text-align: left;
        font-size: 11px;
        line-height: 1.3;
      }
      .sec-notas-adicionales ul li {
        margin-left: 0;
        padding-left: 5px;
        list-style-position: initial;
      }

      .footer-wrap p{
        font-size: 10px;
        line-height: 1.3;
      }
    </style>
  </head>
  <body bgcolor="#FFFFFF" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
    <table>
      <tr>
        <td>
          {!! $textoIntroductorio !!}
        </td>
      </tr>
    </table>
    <table class="head-wrap" bgcolor="#084C9E">
      <tr>
        <td>
        </td>
        <td class="header container">
          <div class="content">
            <table bgcolor="#084C9E" class="">
              <tr>
                <td align="center">
                  <span>Fijo: {{ App\Models\VariableSistema::obtenerXLlave("telefonosEmpresa") }} - Cel: {{ App\Models\VariableSistema::obtenerXLlave("celularesEmpresa") }}</span>
                </td>				
                <td align="center"></td>
                <td align="center">
                  <span>Dirección: {{ App\Models\VariableSistema::obtenerXLlave("direccionEmpresa") }}</span>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <td>
        </td>
      </tr>
    </table>
    <table class="body-wrap">
      <tr>
        <td>
        </td>
        <td class="container" bgcolor="#FFFFFF">
          <div class="column-wrap">
            <div>
              <table width="100%">
                <tr>
                  <td class="center">
                    <p>
                      <img src="{{ asset("assets/eah/img/logo.png") }}" width="150"/>
                    </p>
                  </td>
                </tr>
              </table>
            </div>
            <div>
              <table width="100%">
                <tr>
                  <td class="center">
                    <p><a href="{{ App\Models\VariableSistema::obtenerXLlave("urlSitioWebEmpresa") }}">{{ App\Models\VariableSistema::obtenerXLlave("urlSitioWebEmpresa") }}</a></p>
                    <h2>{!! $curso !!} in - house</h2>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="content">
            <table>
              <tr>
                <td align="center">
                  <p>
                    @if(isset($imagenCurso))
                    <img src="{{ $imagenCurso }}"/>
                    @else
                    <img src="{{ asset("assets/eah/img/curso-fondo-defecto.jpg") }}"/>
                    @endif
                  </p>
                  <div class="sec-nuestra-empresa">
                    <p class="left">
                      <span class="sub-title"><strong>Nuestra empresa</strong></span>
                    </p>
                    <div class="justify contenido">					
                      {!! App\Models\VariableSistema::obtenerXLlave("nuestraEmpresaCorreoCotizacion") !!}
                    </div>
                  </div>
                  <div class="sec-datos-curso">
                    <p class="left">
                      <span class="sub-title"><strong>{!! $curso !!}</strong></span> 
                    </p>                  
                    <div class="justify contenido">					
                      {!! $descripcionCurso !!}
                    </div>
                  </div>
                  <div class="sec-inversion">
                    <p class="left">
                      <span class="sub-title"><strong>Módulos</strong></span>
                    </p>  				
                    {!! $modulos !!}                  
                  </div>
                  <div class="sec-nuestra-metodologia">
                    <p class="left">
                      <span class="sub-title"><strong>Nuestra metodología</strong></span>
                    </p>  
                    <div class="justify contenido">					
                      {!! $metodologia !!}
                    </div>                  
                  </div>
                  <div class="sec-curso-incluye">
                    <p class="left">					
                      <span class="sub-title"><strong>Nuestro curso incluye</strong></span>
                    </p>
                    <div class="contenido">
                      {!! $cursoIncluye !!}
                    </div>
                  </div>
                  <div class="sec-inversion">
                    <p class="left">					
                      <span class="sub-title"><strong>Inversión</strong></span>
                    </p>	
                    {!! $inversion !!} 	     
                  </div>     
                  @if($incluirInversionCuotas == 1)
                  <div class="sec-inversion sec-inversion-cuotas">
                    {!! $inversionCuotas !!}      
                  </div>
                  @endif
                  <div class="sec-proceso-inscripcion">
                    <p class="left">					
                      <span class="sub-title"><strong>Proceso de inscripción</strong></span>						
                    </p>
                    <ol>
                      <li>Completar la ficha del alumno en el siguiente link: <a href="{{ $urlInscripcion }}" type="button" class="btn-inscripcion">Ficha de inscripción</a></li>                      
                      {!! preg_replace("/<\\/?ul(\\s+.*?>|>)/s", "", App\Models\VariableSistema::obtenerXLlave("procesoInscripcionCorreoCotizacion")) !!}
                    </ol>
                    <p>
                      <a href="{{ $urlInscripcion }}" type="button" class="btn-inscripcion" style="font-size: 20px">Click aquí para acceder a la ficha de inscripción</a>
                    </p>
                    <table class="sec-cuentas-bancarias">
                      <tr>
                        <td class="center">
                          <img src="{{ asset("assets/eah/img/bcp.jpg") }}" width="110"/><br/>
                          Ahorro Soles: {{ App\Models\VariableSistema::obtenerXLlave("cuentaSolesBcp") }}
                        </td>
                        <td class="center">
                          <img src="{{ asset("assets/eah/img/interbank.jpg") }}" width="110"/><br/>
                          Ahorros Soles : {{ App\Models\VariableSistema::obtenerXLlave("cuentaSolesInterbank") }}
                        </td>
                      </tr>
                      <tr>
                        <td class="center">
                          <img src="{{ asset("assets/eah/img/scotiabank.jpg") }}" width="110"/><br/> 
                          Ahorros Soles: {{ App\Models\VariableSistema::obtenerXLlave("cuentaSolesScotiabank") }}
                        </td>
                        <td class="center">
                          <img src="{{ asset("assets/eah/img/banco-continental.jpg") }}" width="110"/><br/> 
                          Ahorro Soles: {{ App\Models\VariableSistema::obtenerXLlave("cuentaSolesBancoContinental") }}
                        </td>
                      </tr>
                    </table>
                    <div class="justify contenido">	
                      {{ App\Models\VariableSistema::obtenerXLlave("representanteLegalCuentasBancariasCorreoCotizacion") }}
                    </div>
                  </div>
                  <table class="sec-mensaje-guia" bgcolor="#084C9E">
                    <tbody>
                      <tr>
                        <td></td>
                        <td class="container">
                          <a href="{{ App\Models\VariableSistema::obtenerXLlave("urlGuiaAlumnoCorreoCotizacion") }}">{{ App\Models\VariableSistema::obtenerXLlave("recomendacionGuiaAlumnoCorreoCotizacion") }}</a>
                        </td>
                        <td>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table class="sec-mensaje-notas-adicionales" bgcolor="#3F3F3F">
                    <tbody>
                      <tr>
                        <td></td>
                        <td class="container">
                          <p class="justify">	
                            <span class="sub-title">Notas adicionales</span><br/>
                          </p>
                          <div class="sec-notas-adicionales">
                            {!! $notasAdicionales !!} 
                          </div>
                        </td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </table>
          </div>
          <div class="clear"></div>
          </div>
        </td>
        <td>
        </td>
      </tr>
    </table>
    <table class="footer-wrap">
      <tr>
        <td>
        </td>
        <td class="container">
          <div class="content">
            <table>
              <tr>
                <td align="center">
                  <p>		
                    Llámenos:   Telf. {{ App\Models\VariableSistema::obtenerXLlave("telefonosEmpresa") }}<br/>
                    Cel o Whatsapp: {{ App\Models\VariableSistema::obtenerXLlave("celularesEmpresa") }}
                  </p>
                </td>
                <td align="center">
                  <p>
                    También puede visitarnos en nuestra oficina:<br/>
                    {{ App\Models\VariableSistema::obtenerXLlave("direccionEmpresa") }}<br/>
                    {{ App\Models\VariableSistema::obtenerXLlave("referenciaDireccionEmpresa") }}<br/>
                    <a href="{{ App\Models\VariableSistema::obtenerXLlave("urlGoogleMapsDireccionEmpresa") }}">
                      <img src="{{ asset("assets/eah/img/boton-ver-mapa.png")}}" width="100"/>
                    </a>
                  </p>
                </td>
              </tr>
            </table>
          </div>
        </td>
        <td>
        </td>
      </tr>
    </table>
  </body>
</html>