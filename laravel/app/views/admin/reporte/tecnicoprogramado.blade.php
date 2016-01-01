<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/input-mask/js/jquery.inputmask.js') }}
    {{ HTML::script('lib/input-mask/js/jquery.inputmask.date.extensions.js') }}
    {{ HTML::script('https://maps.googleapis.com/maps/api/js?libraries=places,geometry,drawing') }}
    {{ HTML::script('js/geo/geo.functions.js') }}
    {{ HTML::script('js/geo/markerwithlabel.js') }}
    {{ HTML::script('js/utils.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )

    @include( 'admin.js.horarios_ajax' )
    @include( 'admin.historico.js.bandeja_modal' )
    @include( 'admin.historico.js.codactu_modal' )
    @include( 'admin.historico.js.officetrack_modal' )
    @include( 'admin.reporte.js.tecnicoprogramado' )

    @include( 'admin.historico.js.officetrack_ajax' )
    @include( 'admin.historico.js.bandeja_ajax' )
    @include( 'admin.reporte.js.tecnicoprogramado_ajax' )
    @include( 'admin.js.tareas' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
            <style type="text/css">
            .markerLabel {
                color: black;
                background-color: white;
                font-family: "Lucida Grande", "Arial", sans-serif;
                font-size: 10px;
                font-weight: bold;
                text-align: center;
                max-width: 110px;     
                border: 1px solid black;
                white-space: nowrap;
            }
            </style>
                <section class="content-header">
                    <h1>
                        Reporte de Tecnicos programados
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte Tecnicos Progrados</li>
                        <li class="active"><input type="button" value="-" class="show_hide_filtros"></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content" id="filtros">
                    <div class="row form-group">
                        <form id="form_tecnico_programado" name="form_tecnico_programado" method="POST" action="reporte/tecnicoprogramadoexcel" enctype="multipart/form-data">
                        <div class="col-sm-12">
                            <h3>Filtro(s):</h3>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-sm-3">
                                <label class="control-label">Empresa</label>
                                <select class="form-control" name="slct_empresa[]" id="slct_empresa" multiple>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">Zonal</label>
                                <select class="form-control" name="slct_zonal[]" id="slct_zonal" multiple>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">Grupo Quiebre</label>
                                <select class="form-control" name="slct_grupo_quiebre[]" id="slct_grupo_quiebre" multiple>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-sm-3"> 
                                <label>Agenda:</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" name="fecha_agenda" id="fecha_agenda" value="<?php echo date("Y-m-d")." - ".date("Y-m-d"); ?>" />
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-3">
                                <button type="button" id="mostrartecnicos" class="btn btn-primary">Mostrar Tecnicos Programados</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row form-group">
                        <div class="col-sm-2">
                            <select id="slct_visualiza" onchange="CambiaVisibilidad(this.value);">
                                <option value="">Todo</option>
                                <option value="2" selected>Primer Bloque</option>
                                <option value="1">Segundo Bloque</option>
                            </select>
                            <select name="slct_quiebre" id="slct_quiebre" style="display: none;"></select>
                        </div>
                    </div>
                    <br>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="box-body table-responsive">
                            <table id="t_reporte" class="table table-bordered table-striped">
                                <thead>
                                    <tr><td>#</td></tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot><tr><td>#</td></tr></tfoot>
                            </table>
                            </div>
                        </div>
                    </div>                    
                </section><!-- /.content -->
                <section class="content">
                    <!-- Filtros mapa -->
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <select name="slct_tecnico_mapa" id="slct_tecnico_mapa" multiple="multiple"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" class="btn btn-default" id="btn_solo_tec" onclick="mostrarTecAgd('tec')">
                                    <i class="fa fa-users"></i> Sólo tecnicos
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" class="btn btn-default" id="btn_solo_agd" onclick="mostrarTecAgd('agd')">
                                    <i class="fa fa-calendar"></i> Sólo agendas
                                </a>
                            </div>                            
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" class="btn btn-default" id="btn_ruta_agd" onclick="mostrarPathAgd()">
                                    <i class="fa fa-map-marker"></i> Rutas órdenes
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" class="btn btn-default" id="btn_ini_agd" onclick="mostrarIniAgd()">
                                    <i class="fa fa-flag-o"></i> Inicio agendas
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" class="btn btn-default" id="btn_tec_agd" onclick="mostrarRutaTecAgd()">
                                    <i class="fa fa-arrows-h"></i> Tec. / Agd.
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" class="btn btn-danger" id="btn_clear_map" onclick="clearMapTecPrg()">
                                    <i class="fa fa-eraser"></i> Limpiar mapa
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="tecprg_map" style="width: 100%; height: 600px"></div>
                </section>
@stop
@section('formulario')
     @include( 'admin.historico.form.bandeja_modal' )
     @include( 'admin.historico.form.codactu_modal' )
     @include( 'admin.historico.form.officetrack_modal' )
@stop
