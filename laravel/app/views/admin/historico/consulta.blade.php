<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/bootstrap-spinner/bootstrap-spinner.js') }}
    {{ HTML::script('http://maps.google.com/maps/api/js?sensor=false&libraries=places') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.js.horarios_ajax' )
    <!-- modal para a bandeja -->
    @include( 'admin.historico.js.consulta_bandeja_modal' )
    @include( 'admin.historico.js.bandeja_ajax' )
    @include( 'admin.historico.js.bandeja' )

    @include( 'admin.historico.js.consulta_ajax' )
    @include( 'admin.historico.js.consulta' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Consulta Hist&oacute;rica
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Hist&oacute;rico</a></li>
                        <li class="active">Consulta Hist&oacute;rica</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-5">
                                <fieldset>
                                    <legend>Speedy / Básica</legend>
                                        <div class="col-sm-6">
                                            <label class="control-label" for="telefonoCliente">Telefono:</label>
                                            <input type="text" class="form-control" name="telefonoCliente" id="telefonoCliente" maxlength="8" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="control-label" for="codigoClienteATIS">Cod. Cliente (ATIS):</label>
                                            <input type="text" class="form-control" name="codigoClienteATIS" id="codigoClienteATIS" maxlength="12" />
                                        </div>
                                </fieldset>
                            </div>

                            <div class="col-sm-5">
                                <fieldset>
                                    <legend>CATV</legend>
                                        <div class="col-sm-6">
                                            <label class="control-label" for="codigoServicioCMS">Cod. Servicio:</label>
                                            <input type="text" class="form-control" name="codigoServicioCMS" id="codigoServicioCMS" maxlength="10" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="control-label" for="codigoClienteCMS">Cod. Cliente (CMS):</label>
                                            <input type="text" class="form-control" name="codigoClienteCMS" id="codigoClienteCMS" maxlength="10" />
                                        </div>
                                </fieldset>
                            </div>
                            
                            <div class="col-sm-2">
                                <input class="btn btn-primary btn-sm btn-block" type="button" id="btn_historico" name="btn_historico" value="Consultar Historico" />
                                <button class="btn btn-default btn-sm btn-block" id="btn_limpiar" name="btn_limpiar"><span class="glyphicon glyphicon-refresh"></span></button>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <p id="critico" class="alert alert-danger" style="display:none"></p>
                                <p id="nocritico" class="alert alert-danger" style="display:none"></p>
                                <p id="critico2" class="alert alert-danger" style="display:none"></p>
                            </div>
                        </div>
                        <div id="datos_cliente" class="col-sm-12">
                            <div class="bg-success col-sm-1">
                                Inscripci&oacute;n
                            </div>
                            <div class="col-sm-2">
                                <span id="inscripcion">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-1">
                                Tel&eacute;fono
                            </div>
                            <div class="col-sm-2">
                                <span id="telefono">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-2">
                                Cod. Cli. ATIS
                            </div>
                            <div class="col-sm-1">
                                <span id="codcli">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-2">
                                Cod. Cli. CMS
                            </div>
                            <div class="col-sm-1">
                                <span id="codclicms">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="bg-success col-sm-2">
                                Cod. Ser. CMS
                            </div>
                            <div class="col-sm-2">
                                <span id="codsercms">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-2">
                                Paquete
                            </div>
                            <div class="col-sm-2">
                                <span id="paquete">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-2">
                                Segmento
                            </div>
                            <div class="col-sm-2">
                                <span id="segmentos">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="bg-success col-sm-1">
                                Direcci&oacute;n
                            </div>
                            <div class="col-sm-2">
                                <span id="direccion">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-1">
                                Nombre
                            </div>
                            <div class="col-sm-2">
                                <span id="nombre">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-1">
                                Paterno
                            </div>
                            <div class="col-sm-2">
                                <span id="paterno">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-1">
                                Materno
                            </div>
                            <div class="col-sm-2">
                                <span id="materno">&nbsp;</span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="bg-success col-sm-2">
                                Modalidad Speedy
                            </div>
                            <div class="col-sm-2">
                                <span id="modalidad">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-1">
                                Velocidad
                            </div>
                            <div class="col-sm-2">
                                <span id="velocidad">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-1">
                                Tasa
                            </div>
                            <div class="col-sm-1">
                                <span id="tasa">&nbsp;</span>
                            </div>
                            <div class="bg-success col-sm-2">
                                Tecnolog&iacute;a
                            </div>
                            <div class="col-sm-1">
                                <span id="tecnologia">&nbsp;</span>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div>
                                <ul id="myTab" class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#t_averias">Averías</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_provision">Provisión</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_llamadas">Llamadas</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_criticos">Criticos</a>
                                    </li>
                                </ul>

                            </div>
                            <div class="tab-content">

                                <div class="tab-pane fade active in" id="t_averias">
                                    @include( 'admin.historico.tab.averias' )
                                </div>
                                <div class="tab-pane fade" id="t_provision">
                                    @include( 'admin.historico.tab.provision' )
                                </div>
                                <div class="tab-pane fade" id="t_llamadas">
                                    @include( 'admin.historico.tab.llamadas' )
                                </div>
                                <div class="tab-pane fade" id="t_criticos">
                                    @include( 'admin.historico.tab.criticos' )
                                </div>

                            </div>
                        </div>
                    </div>

                </section><!-- /.content -->

@stop

@section('formulario')
    @include( 'admin.historico.form.consulta_servicios_modal' ) 
    @include( 'admin.historico.form.bandeja_modal' )
@stop