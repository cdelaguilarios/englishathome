<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Englis at home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body style="margin: 0; padding: 0;">
    <div style="width: 100%; padding-bottom: 15px; background-color: #f2f2f2">
      <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: white">
        <tr>
          <td style="background-color: #004d9e">&nbsp;</td>
        </tr>
        <tr>
          <td align="center" style="text-align: center; border-bottom: 1px dashed #999 ">
            &nbsp;<br />&nbsp;
            <img src="{{ asset("assets/eah/img/logo.png")}}" alt="English at home" />
            &nbsp;<br />&nbsp;
          </td>
        </tr>
        <tr>
          <td style="padding: 10px; font-size: 14px; text-align: justify">
            &nbsp;<br />
            <p>Estimado(a) {{ $nombreCompletoDestinatario }},</p>
            {!! $mensaje !!}
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center" style="text-align: center; border-top: 1px dashed #999; font-size: 12px; padding: 10px;">
            <p>
              {!! nl2br(App\Models\VariableSistema::obtenerXLlave("telefonosCelularesCorreos")) !!}
            </p>
            <p>
              También puede visitarnos en nuestra oficina:<br/>
              {!! nl2br(App\Models\VariableSistema::obtenerXLlave("direccionCorreos")) !!}<br/>
              <a href="https://www.google.com.pe/maps/place/Loma+De+Las+Cinerarias+235,+Distrito+de+Lima+15039/@-12.1428894,-76.9898311,16z/data=!4m5!3m4!1s0x9105b86bbbc9a4cd:0xc55066c28ade71f2!8m2!3d-12.1467049!4d-76.9835822?hl=es">
                <img src="{{ asset("assets/eah/img/boton-ver-mapa.png")}}" width="100"/>
              </a>			
            </p>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>