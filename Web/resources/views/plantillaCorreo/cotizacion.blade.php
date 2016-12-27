<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="border-collapse:collapse;height:2387px;margin:0px;padding:0px;width:791px">
  <tbody>
    <tr>
      <td align="center" valign="top"  style="height:2387px;padding:0px;width:791px;border-top:0px">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
          <tbody>
            @include("plantillaCorreo.util.cabecera")
            <tr>
              <td align="center" valign="top">
                <table border="0" cellpadding="0" cellspacing="0" width="100%"  style="border-collapse:collapse;background-color:rgb(196,202,213);border-top:1px none rgb(248,65,65);border-bottom:0px">
                  <tbody>
                    <tr>
                      <td align="center" valign="top" style="padding:9px 10px">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;background-color:rgb(255,255,255);border-top:0px;border-bottom:0px">
                          <tbody>
                            <tr>
                              <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                  <tbody>
                                    <tr>
                                      <td valign="top">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top" style="padding:9px 18px">
                                                <table align="right" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:rgb(227,0,15);border-collapse:collapse">
                                                  <tbody>
                                                    <tr>
                                                      <td align="left" valign="top" style="padding:18px 18px 0px">       
                                                        <img src="{{ asset("assets/eah/img/fondo-correo-cotizacion.jpg")}}" width="528" style="outline-style:none;outline-width:initial;max-width:1170px;border:0px;height:auto;vertical-align:bottom" tabindex="0"/>    
                                                        <div class="a6S" dir="ltr" style="opacity: 0.01; left: 611.042px; top: 729.819px;">
                                                          <div title="Descargar" role="button" tabindex="0" aria-label="Descargar el archivo adjunto " data-tooltip-class="a1V">
                                                            <div></div>
                                                          </div>
                                                        </div>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" width="528" style="font-family:helvetica;padding:9px 18px;color:rgb(255,255,255);word-break:break-word;font-size:16px;line-height:24px"></td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top">
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                                  <tbody>
                                                    <tr>
                                                      <td style="padding:9px 18px">
                                                        <table border="0" cellpadding="18" cellspacing="0" width="100%" style="border:1px none rgb(238,238,238);border-collapse:collapse;min-width:100%">
                                                          <tbody>
                                                            <tr>
                                                              <td valign="top" style="font-family:helvetica;color:rgb(102,102,102);line-height:16px;word-break:break-word;font-size:16px">
                                                                <div>
                                                                  <div>
                                                                    <div>
                                                                      <div style="border:0pt none"><span style="font-size:24px"><span style="color:rgb(0,76,158)"><strong><span>Nuestra Empresa</span></strong></span></span></div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <div style="padding:0px;margin:0px;border:0px;text-align:center">
                                                                  <div style="padding:0px;margin:0px;border:0px">
                                                                    <div style="padding:0px;margin:0px;border:0px">
                                                                      <div style="text-align:justify">
                                                                        <span style="font-size:12px">Para conocer y obtener mas información acerca de nosotros, visite nuestra pagina web:<br><br><a href="http://englishathome.pe/nosotros-docentes/" style="color:rgb(255,255,255)" target="_blank" ><img align="none" height="36" src="{{ asset("assets/eah/img/boton-visitar-web.png")}}" width="170" class="m_-476653997664451306m_7024033486877547357gmail-CToWUd CToWUd" style="width:170px;height:36px;margin:0px;border:0px;outline:none;text-decoration:none"></a></span><br>
                                                                      </div>
                                                                      <h2 style="font-size:26px;line-height:32.5px;margin:0px;padding:0px;font-weight:normal;letter-spacing:-0.75px;text-align:left;color:rgb(255,255,255)"><span style="font-size:24px"><span style="color:rgb(0,76,158)"><strong><span>{!! nl2br($curso) !!}</span></strong></span></span></h2>
                                                                      <div style="text-align:justify">
                                                                        <span style="font-size:12px"><font color="#666666" face="Helvetica"><span style="line-height:16px">{!! nl2br($descripcionCurso) !!}</span></font></span><br>
                                                                        <h2 style="font-size:26px;line-height:32.5px;margin:0px;padding:0px;font-weight:normal;letter-spacing:-0.75px;text-align:left;color:rgb(255,255,255)"><span style="font-size:24px"><span style="color:rgb(0,76,158)"><strong>Módulos</strong>:</span></span></h2>
                                                                        <img align="none" height="83" src="{{ asset("assets/eah/img/tabla-modulos-horas.png")}}" width="189" style="width:189px;height:83px;margin:0px;border:0px;outline:none">
                                                                        <div style="text-align:left">&nbsp;</div>
                                                                        <span style="font-family:helvetica;font-size:24px;line-height:16px"><span style="color:rgb(0,76,158)"><strong><span><font>Nuestra Metodología</font></span></strong></span></span><br><br><span style="font-size:12px"><span style="font-family:helvetica;line-height:16px">{!! nl2br($metodologia) !!}</span></span><br><br>
                                                                        <strong style="color:rgb(0,76,158);font-family:helvetica;font-size:24px;letter-spacing:-0.75px;text-align:left">Nuestro Curso Incluye</strong><font style="color:rgb(0,76,158);font-size:24px;letter-spacing:-0.75px;text-align:left">&nbsp;:</font>
                                                                      </div>
                                                                      <div style="color:rgb(96,96,96);font-size:15px;line-height:22.5px;text-align:left">
                                                                        <span style="font-size:12px">{!! nl2br($cursoIncluye) !!}</span><br><br><span style="font-size:13px">.</span><strong style="color:rgb(0,76,158);font-family:arial,verdana,sans-serif;font-size:24px;line-height:1.6em">Inversión:</strong><br><br>
                                                                        <table cellspacing="0" cellpadding="0" dir="ltr" style="table-layout:fixed;font-size:13px;font-family:arial,sans,sans-serif">
                                                                          <colgroup>
                                                                            <col width="128">
                                                                            <col width="92">
                                                                            <col width="59">
                                                                            <col width="100">
                                                                            <col width="54">
                                                                            <col width="77">
                                                                          </colgroup>
                                                                          <tbody>
                                                                            <tr style="height:17px">
                                                                              <td style="padding:2px 3px;vertical-align:bottom;border-right:1px solid rgb(0,0,0)"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;background-color:rgb(17,85,204);border-top:1px solid rgb(0,0,0);border-right:1px solid rgb(0,0,0);border-bottom:1px solid rgb(0,0,0);font-weight:bold;color:rgb(255,255,255);text-align:center">Nro De horas</td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;background-color:rgb(17,85,204);border-top:1px solid rgb(0,0,0);border-bottom:1px solid rgb(0,0,0);font-weight:bold;color:rgb(255,255,255);text-align:center;border-right:1px solid rgb(0,0,0)" rowspan="1" colspan="2">Materiales</td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;background-color:rgb(17,85,204);border-top:1px solid rgb(0,0,0);border-right:1px solid rgb(0,0,0);border-bottom:1px solid rgb(0,0,0);font-weight:bold;color:rgb(255,255,255);text-align:center" rowspan="1" colspan="2">Inversión Total </td>
                                                                            </tr>
                                                                            <tr style="height:17px">
                                                                              <td style="padding:2px 3px;vertical-align:bottom;border-right:1px solid rgb(0,0,0)"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;background-color:rgb(255,0,0);border-right:1px solid rgb(0,0,0);border-bottom:1px solid rgb(0,0,0);font-weight:bold;color:rgb(255,255,255);text-align:center">{{ $numeroHorasInversion }}</td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;background-color:rgb(255,0,0);border-bottom:1px solid rgb(0,0,0);font-weight:bold;color:rgb(255,255,255);text-align:center;border-right:1px solid rgb(0,0,0)" rowspan="1" colspan="2">s/.{{ $costoMaterialesIversion }}</td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;background-color:rgb(255,0,0);border-bottom:1px solid rgb(0,0,0);font-weight:bold;color:rgb(255,255,255);text-align:center" rowspan="1" colspan="2">s/.{{ $totalInversion }}</td>
                                                                            </tr>
                                                                            <tr style="height:17px">
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                            </tr>
                                                                            <tr style="height:20px">
                                                                              <td style="padding:2px 3px;vertical-align:bottom;border-bottom:1px solid rgb(0,0,0)"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;border-bottom:1px solid rgb(0,0,0)"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom;border-bottom:1px solid rgb(0,0,0)"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                              <td style="padding:2px 3px;vertical-align:bottom"></td>
                                                                            </tr>
                                                                          </tbody>
                                                                        </table>
                                                                        <br><strong style="line-height:1.4625"><span style="color:rgb(0,76,158);font-family:arial;font-size:24px;vertical-align:baseline;white-space:pre-wrap">Proceso de inscripción </span></strong>
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <span style="font-size:13px">1- Completar la ficha del alumno en el siguiente link:&nbsp;<a href="{{ $urlInscripcion }}" style="color:rgb(255,255,255)" target="_blank"><img align="none" height="24" src="{{ asset("assets/eah/img/boton-inscripcion.gif")}}" width="100" style="width:100px;height:24px;margin:0px;border:0px;outline:none;text-decoration:none"></a></span><br><span style="font-size:13px">2- Realizar la confirmación del abono de la inversión de acuerdo a los términos de la forma de pago especificada.</span><br><span style="font-size:13px">3- Esperar un plazo máximo entre dos y tres días para asignar al profesor y dejar los materiales listos para el inicio de clases.</span><br><br><br><font face="Helvetica"><img align="none" height="26" src="{{ asset("assets/eah/img/bcp.jpg") }}" width="100" style="border:0px;outline:none;height:auto">&nbsp;</font><span style="font-size:12px">Ahorro Soles: 194-34310254-0-21<br><br><img align="none" height="26" src="{{ asset("assets/eah/img/interbank.jpg") }}" width="100" style="border:0px;outline:none;height:auto">&nbsp;Ahorros Soles<strong>&nbsp;:</strong>&nbsp;379-3074633210&nbsp;<br><br><img align="none" height="26" src="{{ asset("assets/eah/img/scotiabank.jpg") }}" width="100" style="border:0px;outline:none;height:auto">&nbsp;Ahorros Soles:&nbsp;068-7415372<br><br><img align="none" height="25" src="{{ asset("assets/eah/img/banco-continental.jpg") }}" width="95" style="width:95px;height:25px;margin:0px;border:0px;outline:none">&nbsp;&nbsp;Ahorro Soles: 001102610200199839<br><br>Cuentas a nombre de G. Fernando Rios H. Representante Legal de Capacidad Empresarial EIRL.</span>
                                                              </td>
                                                            </tr>
                                                          </tbody>
                                                        </table>
                                                      </td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top" align="center" style="padding:0px 18px 18px">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border-radius:3px;background-color:rgb(0,0,128)">
                                                  <tbody>
                                                    <tr>
                                                      <td align="center" valign="middle" style="font-family:arial;font-size:16px;padding:15px"><a title="Es importante que pueda leer nuestra GUÍA DEL ALUMNO y brindarnos su conformidad ¡CLIC AQUÍ!" href="https://drive.google.com/open?id=0B0Fn2_yDU8iHNU5Fd2pnbmdSX2s" style="color:rgb(255,255,255);font-weight:bold;line-height:16px;text-decoration:none;display:block" target="_blank">Es importante que pueda leer nuestra GUÍA DEL ALUMNO y brindarnos su conformidad ¡CLIC AQUÍ!</a></td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top">
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                                  <tbody>
                                                    <tr>
                                                      <td style="padding:9px 18px">
                                                        <table border="0" cellpadding="18" cellspacing="0" width="100%" style="background-color:rgb(64,64,64);border-collapse:collapse;min-width:100%">
                                                          <tbody>
                                                            <tr>
                                                              <td valign="top" style="font-family:helvetica;color:rgb(242,242,242);font-size:14px;text-align:center;word-break:break-word;line-height:21px"></td>
                                                            </tr>
                                                          </tbody>
                                                        </table>
                                                      </td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top">
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                                  <tbody>
                                                    <tr>
                                                      <td style="padding:9px 18px">
                                                        <table border="0" cellpadding="18" cellspacing="0" width="100%" style="background-color:rgb(63,63,63);border:1px none rgb(238,238,238);border-collapse:collapse;min-width:100%">
                                                          <tbody>
                                                            <tr>
                                                              <td valign="top" style="font-family:helvetica;color:rgb(255,255,255);line-height:16px;word-break:break-word;font-size:16px">
                                                                <h2 style="margin:0px;padding:0px;font-size:26px;font-weight:normal;line-height:32.5px;letter-spacing:-0.75px"><span style="font-size:24px">Notas Adicionales</span></h2>
                                                                <span style="font-size:12px">- Las sesiones de clases deben ser de 2 horas como mínimo.</span><br><span style="font-size:12px">- Se pueden cancelar clases hasta con 12 horas de anticipación.</span><br><span style="font-size:12px">- La inversión está basada en un solo participante, cualquier participante<br>&nbsp; adicional pagará solo un 15% de la inversión.</span><br><span style="font-size:12px">- Si desea recibir una factura se le sumará el IGV al monto indicado.</span>
                                                              </td>
                                                            </tr>
                                                          </tbody>
                                                        </table>
                                                      </td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" valign="top" style="padding-bottom:40px">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;border-top:0px;border-bottom:0px">
                  <tbody>
                    <tr>
                      <td align="center" valign="top" style="padding-right:10px;padding-left:10px">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse">
                          <tbody>
                            <tr>
                              <td valign="top" style="padding-top:9px;padding-bottom:9px">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width:100%;border-collapse:collapse">
                                  <tbody>
                                    <tr>
                                      <td valign="top" style="padding-top:9px">
                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:210px;border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top" style="font-family:helvetica;padding:0px 18px 9px;word-break:break-word;color:rgb(128,128,128);font-size:12px;line-height:12px"><span >&nbsp;Llámenos: &nbsp; Telf.&nbsp;<strong>3334306</strong></span><br><span>Cel o Whatsapp: &nbsp;&nbsp;<strong>970883890</strong></span><br>&nbsp;</td>
                                            </tr>
                                          </tbody>
                                        </table>
                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:390px;border-collapse:collapse">
                                          <tbody>
                                            <tr>
                                              <td valign="top" style="font-family:helvetica;padding:0px 18px 9px;word-break:break-word;color:rgb(128,128,128);font-size:12px;line-height:12px">
                                                <div style="text-align:center"><span>También puede visitarnos en nuestra oficina:<br><span style="font-size:14px"><strong>Jr Loma de las Gardenias 235, 1° piso, Surco</strong>,</span><br>Ref. Entre la cdra 33 y 34 de la Av Caminos del Inca.&nbsp;</span></div>
                                                <div style="text-align:center">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href="https://www.google.com.pe/maps/place/Loma+De+Las+Cinerarias+235,+Lima+15039/@-12.1428894,-76.9898311,16.5z/data=!4m5!3m4!1s0x9105b86bbbc9a4cd:0xc55066c28ade71f2!8m2!3d-12.1467049!4d-76.9835822?hl=es" style="color:rgb(128,128,128)" target="_blank"><img align="none" height="34" src="{{ asset("assets/eah/img/boton-ver-mapa.png")}}" width="100" style="border:0px;outline:none;text-decoration:none;height:auto"></a>&nbsp;&nbsp;&nbsp;</div>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>