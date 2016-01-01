<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.reporte.js.tecnicosoffice_ajax' )
    @include( 'admin.reporte.js.tecnicosoffice' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Asistencia Tecnicos
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Asistencia Tecnico</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form name="form_reporte" id="form_reporte" method="post" action="reporte/tecnicoofficetrack" enctype="multipart/form-data">
                        <div class="row form-group">

                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label class="control-label">Empresa</label>
                                    <select class="form-control" name="slct_empresas" id="slct_empresa">
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label">Celula</label>
                                    <select class="form-control" name="slct_celulas" id="slct_celula">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <label class="control-label">Tecnicos Officetrack</label>
                                    <select class="form-control" multiple="multiple" name="slct_tecnicos[]" id="slct_tecnico">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label class="control-label">Tipo de Reportes</label>
                                    
                                    <select class="form-control" name="slct_reporte" id="slct_reporte">
                                        <option value='' style="display:none">.:Seleccione:.</option>
                                        <option value='1'>Por dia</option>
                                        <option value='2'>Por rango de fechas</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label class="control-label">Fecha de Asistencia</label>
                                    <input type="text" class="form-control" placeholder="" id="fecha" name="fecha" onfocus="blur()"/>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <!-- <button type="button" id="mostrarAsistencia" class="btn btn-primary">Mostrar Asistencias</button> -->
                                    <input type="button" class="form-control btn-primary" value="Mostrar Asistencias" id="mostrarAsistencia">
                                </div>
                                <div class="col-sm-3">
                                    <!-- <button type="button" id="reiniciarFiltros" class="btn btn-primary">Reiniciar Filtros</button> -->
                                    <input type="button" class="form-control btn-primary" value="Reiniciar Filtros" id="reiniciarFiltros">
                                </div>
                                <div class="col-sm-3">
                                    <span>
                                        <a href="#" id="ExportExcel" class="btn btn-success">
                                            <i class="fa fa-file-excel-o"></i> Exportar
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-xs-12">
                                <!-- Inicia contenido -->
                                <div class="box">
                                    <!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="t_reporte" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tecnicos</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                                <!-- Finaliza contenido -->
                            </div>
                        </div>
                    </form>
                </section><!-- /.content -->
@stop
<!-- 
@section('formulario')
     @include( 'admin.mantenimiento.form.usuario' ) 
@stop -->