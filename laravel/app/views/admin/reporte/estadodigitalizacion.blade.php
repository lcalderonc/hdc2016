<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('js/psi.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.reporte.js.estadodigitalizacion_ajax' )
    @include( 'admin.reporte.js.estadodigitalizacion' )

@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Estado de Digitalización
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Estado de Digitalización</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
        <div class="row form-group">
            <div class="col-sm-12">
                <div>
                    <ul id="tap_digitalizacion" class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_carga">Cargar</a>
                        </li>
                        <li role="presentation">
                            <a href="#tap_descargar">Descargar</a>
                        </li>
                        <li role="presentation">
                            <a href="#tap_gestionar">Gestionar</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tab_carga">
                        <div class="box">
                            <div class="row form-group col-sm-12">
                                <div class="col-sm-3">
                                    <label>
                                        Cargar Archivo
                                    </label>
                                    <input type="file" class="form-control" id="archivo" name="archivo" accept="text/plain">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tap_descargar">
                        <div class="box">
                            <div class="row form-group col-sm-12">
                            <form name="form_estados" id="form_estados" method="post" action="reporte/estadodigitalizacionexcel" enctype="multipart/form-data">
                                <div class="col-sm-3">
                                    <label>Proyecto:</label>
                                    <select class="form-control" name="slct_proyecto" id="slct_proyecto">
                                        <option value="">.::Seleccione::.</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                   <br>
                                   <button type="button" id="btn_generar" name="btn_generar" class="btn btn-primary">
                                       Descargar
                                   </button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tap_gestionar">
                        <div class="box">
                            <div class="row form-group col-sm-12">
                                <!-- Inicia contenido -->
                                <div class="box">
                                    <div class="box-header">
                                        <div class="col-sm-3">
                                            <label>Proyecto:</label>
                                            <select class="form-control" name="slct_proyecto2" id="slct_proyecto2">
                                                <option value="">.::Seleccione::.</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                           <br>
                                           <button type="button" id="btn_buscar" name="btn_buscar" class="btn btn-danger">
                                               Buscar
                                           </button>
                                        </div>
                                    </div><!-- /.box-header -->
                                    <div class="box-body table-responsive">
                                        <table id="t_estados" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>cliente cms</th>
                                                    <th>servicio cms</th>
                                                    <th>Nombre</th>
                                                    <th>condicion</th>
                                                    <th>Orden</th>
                                                    <th>Motivo</th>
                                                    <th>Fecha de motivo</th>
                                                    <th> [] </th>
                                                </tr>
                                            </thead>
                                            <tbody id="tb_estados">
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>cliente cms</th>
                                                    <th>servicio cms</th>
                                                    <th>Nombre</th>
                                                    <th>condicion</th>
                                                    <th>Orden</th>
                                                    <th>Motivo</th>
                                                    <th>Fecha de motivo</th>
                                                    <th> [] </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                                <!-- Finaliza contenido -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.reporte.form.estadodigitalizacion' ) 
@stop