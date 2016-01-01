<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
{{ HTML::script('http://maps.google.com/maps/api/js?sensor=false&libraries=places') }}
{{ HTML::script('js/geo/geo.functions.js') }}
@include( 'admin.js.slct_global_ajax' )
@include( 'admin.js.slct_global' )
@include( 'admin.historico.js.registromanual_ajax' )
@include( 'admin.historico.js.registromanual' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->

<section class="content-header">
    <h1>
        Registro manual de Pendiente
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">Hist&oacute;rico</a></li>
        <li class="active">Registro manual</li>
        <li class="active"><input type="button" value="-" class="show_hide_filtros"></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <div class="registro_clientes">
            <div class="row form-group">
                <div class="col-sm-12">
                    <div class="box">
                        <div class="box-header">
                            <div class="col-sm-2">
                                <p id="estado_busqueda" class="alert alert-success" style="display:none"></p>
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label" for="telefonoCliente">Telefono:</label>
                                <input type="text" class="form-control" name="f_telefonoCliente" id="f_telefonoCliente" maxlength="8" />
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label" for="codigoClienteATIS">Cliente (ATIS):</label>
                                <input type="text" class="form-control" name="f_codigoClienteATIS" id="f_codigoClienteATIS" maxlength="12" />
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label" for="codigoServicioCMS">Cod. Servicio:</label>
                                <input type="text" class="form-control" name="f_codigoServicioCMS" id="f_codigoServicioCMS" maxlength="10" />
                            </div>
                            <div class="col-sm-2">
                                <label class="control-label" for="codigoClienteCMS">Cliente (CMS):</label>
                                <input type="text" class="form-control" name="f_codigoClienteCMS" id="f_codigoClienteCMS" maxlength="10" />
                            </div>
                            <div class="col-sm-2">
                                <input class="btn btn-primary btn-sm btn-block" type="button" id="btn_busqueda" name="btn_busqueda" value="Consultar" />
                                <button class="btn btn-default btn-sm btn-block" id="btn_limpiar" type="button" name="btn_limpiar">
                                <span class="glyphicon glyphicon-refresh"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <form name="frm_criticos" id="frm_criticos" action="" method="POST">
            <div class="row form-group">
                <div id="filtros">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Tipo actividad
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="tipo_actividad" name="tipo_actividad">
                                <option value="3">AVERIA</option>
                                <option value="4">PROVISION</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Quiebre
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="slct_quiebre" name="slct_quiebre"></select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Tipo averia
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="slct_tipo_averia" name="slct_tipo_averia">
                                <option value="">Seleccione</option>
                                <option value="rutina-bas-lima">STB</option>
                                <option value="rutina-adsl-pais">ADSL</option>
                                <option value="rutina-catv-pais">CATV</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Edificios
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="slct_edificio" name="slct_edificio">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Averias /Peticion /Motivo Req.
                        </div>
                        <div class="col-sm-4">
                            <div id="valida_codactu" class="form-group has-warning">
                                <label class="control-label" for="inputWarning"><i class="fa fa-warning"></i> Ingrese caracteres alfanuméricos </label>
                                <input class="form-control" pattern="[a-zA-Z0-9]+" onfocus="escondeBotton();" onblur="validaTexto(this.value);" type="text" size="12" maxlength="255" placeholder="Averia/Código Req" value="" id="averia" name="averia">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            Telefono
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="11" name="telefono" id="telefono">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Direccion
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="255" name="direccion" id="direccion">
                        </div>
                        <div class="col-sm-1">
                            Cliente (ATIS):
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="255" name="codclie" id="codclie">
                        </div>
                        <div class="col-sm-1">
                            Cod. Servicio:
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="255" name="codservcms" id="codservcms">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Nombre de Contacto
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="255" name="cr_nombre" id="cr_nombre">
                        </div>
                        <div class="col-sm-2">
                            Telefono de contacto
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="11" name="cr_telefono" id="cr_telefono">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Celular de contacto
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="11" name="cr_celular" id="cr_celular">
                        </div>
                        <div class="col-sm-2">
                            Observacion
                        </div>
                        <div class="col-sm-4">
                            <textarea class="form-control" maxlength="255" value="" id="cr_observacion" name="cr_observacion"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Segmento
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="segmento" name="segmento">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Zonal
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="zonal" name="zonal">
                                <option value="">-Seleccione-</option>
                            </select>
                            <input class="form-control" type="hidden" name="zonal_id" id="zonal_id">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            MDF/Nodo
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="mdf" name="mdf">
                                <option value="" selected="">-Seleccione-</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Distrito
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="distrito" name="distrito">
                                <option value="" selected="">-Seleccione-</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12" id="fftt_catv">
                        <div class="col-sm-2">
                            Troba
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="troba" name="troba">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Amplificador
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="amplificador" name="amplificador">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Tap
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="tap" name="tap">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12" id="fftt_stb">
                        <div class="col-sm-2">
                            Armario/Cable
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="cable" name="cable">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Terminal
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="terminal" name="terminal">
                                <option value="">-Seleccione-</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Movistar uno
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="movistar_uno" name="movistar_uno">
                                <option value="NO" selected="">NO</option>
                                <option value="MOVISTAR UNO">SI</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            EECC
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value=""  name="eecc" id="eecc" readonly="true">
                            <input class="form-control" type="hidden" value="" name="empresa_id" id="empresa_id" readonly="true">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Lejano
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="11" name="lejano" id="lejano" readonly="true">
                        </div>
                        <div class="col-sm-2">
                            Microzona
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="11" name="microzona" id="microzona" readonly="true">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Longitud (x)
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="4" name="x" id="x" readonly="">
                        </div>
                        <div class="col-sm-2">
                            Latitud (y)
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="4" name="y" id="y" readonly="">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-4">

                        </div>
                        <div class="col-sm-4">
                            <input type="button" class="form-control btn-danger" value="Registrar" id="btn_limpiar_todo" name="btn_limpiar_todo">
                        </div>
                        <div class="col-sm-4">

                        </div>
                    </div>
                </div>
                <div class="col-sm-6" style="padding: 10px">
                    <div class="col-sm-12">
                        <input class="form-control" type="text" maxlength="100" id="address" placeholder="Dirección" style="width:250px;" />
                        <div id="map_canvas" style="width: 100%; height: 500px; text-align: center; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"><div class="gm-style" style="position: absolute; left: 0px; top: 0px; overflow: hidden; width: 100%; height: 100%; z-index: 0;"><div style="position: absolute; left: 0px; top: 0px; overflow: hidden; width: 100%; height: 100%; z-index: 0; cursor: url(http://maps.gstatic.com/mapfiles/openhand_8_8.cur) 8 8, default;"><div style="position: absolute; left: 0px; top: 0px; z-index: 1; width: 100%; transform-origin: 0px 0px 0px; transform: matrix(1, 0, 0, 1, 0, 0);"><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 100; width: 100%;"><div style="position: absolute; left: 0px; top: 0px; z-index: 0;"><div aria-hidden="true" style="position: absolute; left: 0px; top: 0px; z-index: 1; visibility: inherit;"><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 116px; top: -202px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 116px; top: 54px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: -140px; top: -202px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: -140px; top: 54px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 372px; top: -202px;"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 372px; top: 54px;"></div></div></div></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 101; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 102; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 103; width: 100%;"><div style="position: absolute; left: 0px; top: 0px; z-index: -1;"><div aria-hidden="true" style="position: absolute; left: 0px; top: 0px; z-index: 1; visibility: inherit;"><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 116px; top: -202px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 116px; top: 54px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: -140px; top: -202px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: -140px; top: 54px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 372px; top: -202px;"></div><div style="width: 256px; height: 256px; overflow: hidden; transform: translateZ(0px); position: absolute; left: 372px; top: 54px;"></div></div></div></div><div style="position: absolute; z-index: 0; left: 0px; top: 0px;"><div style="overflow: hidden; width: 450px; height: 150px;"><img src="https://maps.googleapis.com/maps/api/js/StaticMapService.GetMapImage?1m2&amp;1i299660&amp;2i559562&amp;2e1&amp;3u12&amp;4m2&amp;1u450&amp;2u150&amp;5m5&amp;1e0&amp;5ses-419&amp;6sus&amp;10b1&amp;12b1&amp;token=9669" style="width: 450px; height: 150px;"></div></div><div style="position: absolute; left: 0px; top: 0px; z-index: 0;"><div aria-hidden="true" style="position: absolute; left: 0px; top: 0px; z-index: 1; visibility: inherit;"><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 116px; top: -202px; opacity: 1; transition: opacity 200ms ease-out; -webkit-transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/vt?pb=!1m4!1m3!1i12!2i1171!3i2185!2m3!1e0!2sm!3i295236412!3m9!2ses-419!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0" draggable="false" style="width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; transform: translateZ(0px) translateZ(0px);"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 116px; top: 54px; opacity: 1; transition: opacity 200ms ease-out; -webkit-transition: opacity 200ms ease-out;"><img src="https://mts1.googleapis.com/vt?pb=!1m4!1m3!1i12!2i1171!3i2186!2m3!1e0!2sm!3i295236412!3m9!2ses-419!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0" draggable="false" style="width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; transform: translateZ(0px) translateZ(0px);"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: -140px; top: -202px; opacity: 1; transition: opacity 200ms ease-out; -webkit-transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/vt?pb=!1m4!1m3!1i12!2i1170!3i2185!2m3!1e0!2sm!3i295236412!3m9!2ses-419!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0" draggable="false" style="width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; transform: translateZ(0px) translateZ(0px);"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: -140px; top: 54px; opacity: 1; transition: opacity 200ms ease-out; -webkit-transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/vt?pb=!1m4!1m3!1i12!2i1170!3i2186!2m3!1e0!2sm!3i295236412!3m9!2ses-419!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0" draggable="false" style="width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; transform: translateZ(0px) translateZ(0px);"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 372px; top: -202px; opacity: 1; transition: opacity 200ms ease-out; -webkit-transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/vt?pb=!1m4!1m3!1i12!2i1172!3i2185!2m3!1e0!2sm!3i295236412!3m9!2ses-419!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0" draggable="false" style="width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; transform: translateZ(0px) translateZ(0px);"></div><div style="width: 256px; height: 256px; transform: translateZ(0px); position: absolute; left: 372px; top: 54px; opacity: 1; transition: opacity 200ms ease-out; -webkit-transition: opacity 200ms ease-out;"><img src="https://mts0.googleapis.com/vt?pb=!1m4!1m3!1i12!2i1172!3i2186!2m3!1e0!2sm!3i295236412!3m9!2ses-419!3sUS!5e18!12m1!1e47!12m3!1e37!2m1!1ssmartmaps!4e0" draggable="false" style="width: 256px; height: 256px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; transform: translateZ(0px) translateZ(0px);"></div></div></div></div><div style="position: absolute; left: 0px; top: 0px; z-index: 2; width: 100%; height: 100%;"></div><div style="position: absolute; left: 0px; top: 0px; z-index: 3; width: 100%; transform-origin: 0px 0px 0px; transform: matrix(1, 0, 0, 1, 0, 0);"><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 104; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 105; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 106; width: 100%;"></div><div style="transform: translateZ(0px); position: absolute; left: 0px; top: 0px; z-index: 107; width: 100%;"></div></div></div><div style="margin-left: 5px; margin-right: 5px; z-index: 1000000; position: absolute; left: 0px; bottom: 0px;"><a target="_blank" href="https://maps.google.com/maps?ll=-12.046374,-77.042793&amp;z=12&amp;t=m&amp;hl=es-419&amp;gl=US&amp;mapclient=apiv3" title="Haz clic para ver esta área en Google Maps" style="position: static; overflow: visible; float: none; display: inline;"><div style="width: 62px; height: 26px; cursor: pointer;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/google_white2.png" draggable="false" style="position: absolute; left: 0px; top: 0px; width: 62px; height: 26px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div></a></div><div class="gmnoprint" style="z-index: 1000001; position: absolute; right: 259px; bottom: 0px; width: 82px;"><div draggable="false" class="gm-style-cc" style="-webkit-user-select: none;"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="width: auto; height: 100%; margin-left: 1px; background-color: rgb(245, 245, 245);"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right;"><a style="color: rgb(68, 68, 68); text-decoration: none; cursor: pointer;">Datos del mapa</a><span style="display: none;">Datos del mapa ©2015 Google</span></div></div></div><div style="padding: 15px 21px; border: 1px solid rgb(171, 171, 171); font-family: Roboto, Arial, sans-serif; color: rgb(34, 34, 34); -webkit-box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 16px; box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 16px; z-index: 10000002; display: none; width: 256px; height: 108px; position: absolute; left: 75px; top: 5px; background-color: white;"><div style="padding: 0px 0px 10px; font-size: 16px;">Datos del mapa</div><div style="font-size: 13px;">Datos del mapa ©2015 Google</div><div style="width: 13px; height: 13px; overflow: hidden; position: absolute; opacity: 0.7; right: 12px; top: 12px; z-index: 10000; cursor: pointer;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/mapcnt6.png" draggable="false" style="position: absolute; left: -2px; top: -336px; width: 59px; height: 492px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div></div><div class="gmnoscreen" style="position: absolute; right: 0px; bottom: 0px;"><div style="font-family: Roboto, Arial, sans-serif; font-size: 11px; color: rgb(68, 68, 68); direction: ltr; text-align: right; background-color: rgb(245, 245, 245);">Datos del mapa ©2015 Google</div></div><div class="gmnoprint gm-style-cc" draggable="false" style="z-index: 1000001; position: absolute; -webkit-user-select: none; right: 139px; bottom: 0px;"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="width: auto; height: 100%; margin-left: 1px; background-color: rgb(245, 245, 245);"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right;"><a href="https://www.google.com/intl/es-419_US/help/terms_maps.html" target="_blank" style="text-decoration: none; cursor: pointer; color: rgb(68, 68, 68);">Condiciones del servicio</a></div></div><input type="text" maxlength="100" id="address" placeholder="Dirección" style="width: 250px; z-index: 0; position: absolute; left: 42px; top: 0px;" autocomplete="off"><div draggable="false" class="gm-style-cc" style="-webkit-user-select: none; position: absolute; right: 0px; bottom: 0px;"><div style="opacity: 0.7; width: 100%; height: 100%; position: absolute;"><div style="width: 1px;"></div><div style="width: auto; height: 100%; margin-left: 1px; background-color: rgb(245, 245, 245);"></div></div><div style="position: relative; padding-right: 6px; padding-left: 6px; font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); white-space: nowrap; direction: ltr; text-align: right;"><a target="_new" title="Informar a Google errores en las imágenes o el mapa de carreteras." href="https://www.google.com/maps/@-12.046374,-77.0427934,12z/data=!10m1!1e1!12b1?source=apiv3&amp;rapsrc=apiv3" style="font-family: Roboto, Arial, sans-serif; font-size: 10px; color: rgb(68, 68, 68); text-decoration: none; position: relative;">Informar un error en el mapa</a></div></div><div class="gmnoprint" draggable="false" controlwidth="32" controlheight="84" style="margin: 5px; -webkit-user-select: none; position: absolute; left: 0px; top: 0px;"><div controlwidth="32" controlheight="40" style="cursor: url(http://maps.gstatic.com/mapfiles/openhand_8_8.cur) 8 8, default; position: absolute; left: 0px; top: 0px;"><div aria-label="Control del hombrecito naranja en Street View" style="width: 32px; height: 40px; overflow: hidden; position: absolute; left: 0px; top: 0px;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/cb_scout2.png" draggable="false" style="position: absolute; left: -9px; top: -102px; width: 1028px; height: 214px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div><div aria-label="El hombrecito naranja está inhabilitado." style="width: 32px; height: 40px; overflow: hidden; position: absolute; left: 0px; top: 0px; visibility: hidden;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/cb_scout2.png" draggable="false" style="position: absolute; left: -107px; top: -102px; width: 1028px; height: 214px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div><div aria-label="El hombrecito naranja está en la parte superior del mapa." style="width: 32px; height: 40px; overflow: hidden; position: absolute; left: 0px; top: 0px; visibility: hidden;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/cb_scout2.png" draggable="false" style="position: absolute; left: -58px; top: -102px; width: 1028px; height: 214px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div><div aria-label="Control del hombrecito naranja en Street View" style="width: 32px; height: 40px; overflow: hidden; position: absolute; left: 0px; top: 0px; visibility: hidden;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/cb_scout2.png" draggable="false" style="position: absolute; left: -205px; top: -102px; width: 1028px; height: 214px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div></div><div class="gmnoprint" controlwidth="0" controlheight="0" style="opacity: 0.6; display: none; position: absolute;"><div title="Rotar mapa 90 grados" style="width: 22px; height: 22px; overflow: hidden; position: absolute; cursor: pointer;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/mapcnt6.png" draggable="false" style="position: absolute; left: -38px; top: -360px; width: 59px; height: 492px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div></div><div class="gmnoprint" controlwidth="20" controlheight="39" style="position: absolute; left: 6px; top: 45px;"><div style="width: 20px; height: 39px; overflow: hidden; position: absolute;"><img src="https://maps.gstatic.com/mapfiles/api-3/images/mapcnt6.png" draggable="false" style="position: absolute; left: -39px; top: -401px; width: 59px; height: 492px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;"></div><div title="Acercar" style="position: absolute; left: 0px; top: 2px; width: 20px; height: 17px; cursor: pointer;"></div><div title="Alejar" style="position: absolute; left: 0px; top: 19px; width: 20px; height: 17px; cursor: pointer;"></div></div></div><div class="gmnoprint" style="margin: 5px; z-index: 0; position: absolute; cursor: pointer; right: 0px; top: 0px;"><div class="gm-style-mtc" style="float: left;"><div draggable="false" title="Mostrar mapa de calles" style="direction: ltr; overflow: hidden; text-align: center; position: relative; color: rgb(0, 0, 0); font-family: Roboto, Arial, sans-serif; -webkit-user-select: none; font-size: 11px; padding: 1px 6px; border-bottom-left-radius: 2px; border-top-left-radius: 2px; -webkit-background-clip: padding-box; border: 1px solid rgba(0, 0, 0, 0.14902); -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; min-width: 30px; font-weight: 500; background-color: rgb(255, 255, 255); background-clip: padding-box;">Mapa</div><div style="z-index: -1; padding-top: 2px; -webkit-background-clip: padding-box; border-width: 0px 1px 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.14902); border-bottom-color: rgba(0, 0, 0, 0.14902); border-left-color: rgba(0, 0, 0, 0.14902); -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; position: absolute; left: 0px; top: 17px; text-align: left; display: none; background-color: white; background-clip: padding-box;"><div draggable="false" title="Mostrar mapa de calles con relieve" style="color: rgb(0, 0, 0); font-family: Roboto, Arial, sans-serif; -webkit-user-select: none; font-size: 11px; padding: 3px 8px 3px 3px; direction: ltr; text-align: left; white-space: nowrap; background-color: rgb(255, 255, 255);"><span role="checkbox" style="box-sizing: border-box; position: relative; line-height: 0; font-size: 0px; margin: 0px 5px 0px 0px; display: inline-block; border: 1px solid rgb(198, 198, 198); border-radius: 1px; width: 13px; height: 13px; vertical-align: middle; background-color: rgb(255, 255, 255);"><div style="position: absolute; left: 1px; top: -2px; width: 13px; height: 11px; overflow: hidden; display: none;"><img src="https://maps.gstatic.com/mapfiles/mv/imgs8.png" draggable="false" style="position: absolute; left: -52px; top: -44px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; width: 68px; height: 67px;"></div></span><label style="vertical-align: middle; cursor: pointer;">Relieve</label></div></div></div><div class="gm-style-mtc" style="float: left;"><div draggable="false" title="Mostrar imágenes satelitales" style="direction: ltr; overflow: hidden; text-align: center; position: relative; color: rgb(86, 86, 86); font-family: Roboto, Arial, sans-serif; -webkit-user-select: none; font-size: 11px; padding: 1px 6px; border-bottom-right-radius: 2px; border-top-right-radius: 2px; -webkit-background-clip: padding-box; border-width: 1px 1px 1px 0px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-top-color: rgba(0, 0, 0, 0.14902); border-right-color: rgba(0, 0, 0, 0.14902); border-bottom-color: rgba(0, 0, 0, 0.14902); -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; min-width: 38px; background-color: rgb(255, 255, 255); background-clip: padding-box;">Satélite</div><div style="z-index: -1; padding-top: 2px; -webkit-background-clip: padding-box; border-width: 0px 1px 1px; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.14902); border-bottom-color: rgba(0, 0, 0, 0.14902); border-left-color: rgba(0, 0, 0, 0.14902); -webkit-box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; box-shadow: rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px; position: absolute; right: 0px; top: 17px; text-align: left; display: none; background-color: white; background-clip: padding-box;"><div draggable="false" title="Acerca para mostrar la vista de 45 grados" style="color: rgb(184, 184, 184); font-family: Roboto, Arial, sans-serif; -webkit-user-select: none; font-size: 11px; padding: 3px 8px 3px 3px; direction: ltr; text-align: left; white-space: nowrap; display: none; background-color: rgb(255, 255, 255);"><span role="checkbox" style="box-sizing: border-box; position: relative; line-height: 0; font-size: 0px; margin: 0px 5px 0px 0px; display: inline-block; border: 1px solid rgb(241, 241, 241); border-radius: 1px; width: 13px; height: 13px; vertical-align: middle; background-color: rgb(255, 255, 255);"><div style="position: absolute; left: 1px; top: -2px; width: 13px; height: 11px; overflow: hidden; display: none;"><img src="https://maps.gstatic.com/mapfiles/mv/imgs8.png" draggable="false" style="position: absolute; left: -52px; top: -44px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; width: 68px; height: 67px;"></div></span><label style="vertical-align: middle; cursor: pointer;">45°</label></div><div draggable="false" title="Mostrar imágenes con nombres de calles" style="color: rgb(0, 0, 0); font-family: Roboto, Arial, sans-serif; -webkit-user-select: none; font-size: 11px; padding: 3px 8px 3px 3px; direction: ltr; text-align: left; white-space: nowrap; background-color: rgb(255, 255, 255);"><span role="checkbox" style="box-sizing: border-box; position: relative; line-height: 0; font-size: 0px; margin: 0px 5px 0px 0px; display: inline-block; border: 1px solid rgb(198, 198, 198); border-radius: 1px; width: 13px; height: 13px; vertical-align: middle; background-color: rgb(255, 255, 255);"><div style="position: absolute; left: 1px; top: -2px; width: 13px; height: 11px; overflow: hidden;"><img src="https://maps.gstatic.com/mapfiles/mv/imgs8.png" draggable="false" style="position: absolute; left: -52px; top: -44px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px; width: 68px; height: 67px;"></div></span><label style="vertical-align: middle; cursor: pointer;">Etiquetas</label></div></div></div></div></div></div>
                    
                    </div>
                </div>
                <div class="col-sm-6" style="padding: 10px">
                    <div class="col-sm-12" id="streetview" style="width: 100%; height: 500px">
                    </div>
                </div>
            </div>
        </form>
    </div>

</section><!-- /.content -->
@include( 'admin.historico.js.maps' )
@stop

@section('formulario')
    @include( 'admin.historico.form.registro_manual_modal' )
@stop
