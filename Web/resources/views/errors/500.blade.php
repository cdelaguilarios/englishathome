<!DOCTYPE html>
<html>
  <head>
    <title>English at home - Página no encontrada</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet">

    <style>
      html, body {
        height: 100%;
      }

      body {
        margin: 0;
        padding: 0;
        width: 100%;
        color: #33383E;
        display: table;
        font-weight: 900;
        font-family: "Lato";
      }

      .container {
        text-align: center;
        display: table-cell;
        vertical-align: middle;
      }

      .content {
        text-align: center;
        display: inline-block;
      }

      .title {
        font-size: 72px;
        margin-bottom: 40px;
      }

      .sec-content{        
        width: 50%;
        float: left;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="content">
        <div class="sec-content">
          <div class="title">
            Error 500<br/>
          </div>
          <b>Ha ocurrido un error y tu solicitud no ha sido procesada. Por favor inténtelo nuevamente.</b>
        </div>
        <div class="sec-content">          
          <img src="{{ asset("assets/eah/img/error500.png")}}" width="300"/>
        </div>
      </div>
    </div>
  </body>
</html>
