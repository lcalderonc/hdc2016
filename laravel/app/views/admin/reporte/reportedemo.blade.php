<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )

    @include( 'admin.reporte.js.reportedemo' )
    @include( 'admin.reporte.js.reportedemopag' )
    @include( 'admin.reporte.js.reportedemo_ajax' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Nros Agendamientos por Código Actuación
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
                        <form id="form_reporte_demo" name="form_reporte_demo" method="POST">
                        <div class="col-sm-12">
                            <h3>Filtro(s):</h3>
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
                                <button type="button" id="mostrar" class="btn btn-primary">Mostrar</button>
                                <button type="button" id="mostrarpag" class="btn btn-primary">Mostrar Pag Ajax</button>
                            </div>
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

                            <table id="t_reporte_pag" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style='width:70px !important;'>Cod Actu</th>
                                        <th style='width:90px !important;'>F. Registro</th>
                                        <th style='width:100px !important;'>F. Psi</th>
                                        <th style='width:100px !important;'>Total Gestión</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Cod Actu</th>
                                        <th>F. Registro</th>
                                        <th>F. Psi</th>
                                        <th>Total Gestión</th>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                        </div>
                    </div>                    
                </section><!-- /.content -->
@stop
