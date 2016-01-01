<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
@parent
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
{{ HTML::script('lib/backbone/js/json2.js') }}
{{ HTML::script('lib/backbone/js/underscore.min.js') }}
{{ HTML::script('lib/backbone/js/backbone.min.js') }}
{{ HTML::script('lib/backbone/js/handlebars.js') }}
@include( 'admin.js.slct_global_ajax' )
@include( 'admin.js.slct_global' )
@include( 'admin.mantenimiento.js.validacion_ajax' )
@include( 'admin.mantenimiento.js.validacion' )
@stop
        <!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
    <style>
        .edit-col,.edit-row{
            padding: 5px;
            text-align: center;
            width: 30px;
        }
    </style>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Mantenimiento de Validaciones
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Mantenimientos</a></li>
            <li class="active">Mantenimiento de Validaciones</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <!-- Inicia contenido -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Filtros</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="t_validacion" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th> [ ] </th>
                            </tr>
                            </thead>
                            <tbody id="tb_validacion">
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Nombre</th>
                                <th> [ ] </th>
                            </tr>
                            </tfoot>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                <!-- Finaliza contenido -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <!-- Inicia contenido -->
                <div class="box" id="valoresConfiguracion">
                    <div class="box-header">
                        <h3 class="box-title">Valores de <span id="nombreTabla"></span></h3>
                        <input type="hidden" id="idTabla">
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="t_validacionDetalle" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th> Valores </th>
                                <th> Visible </th>
                            </tr>
                            </thead>
                            <tbody id="tb_validacionDetalle">
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Nombre</th>
                                <th> Valores </th>
                                <th> Visible </th>
                            </tr>
                            </tfoot>
                        </table>
                        <button class='btn btn-primary btn-sm nuevo' class="btn btn-primary" id="btnGuardar"><i class="fa fa-archive fa-lg"></i>&nbsp;Guardar</button>
                        <button class='btn btn-primary btn-sm' class="btn btn-primary" id="btnActualizar"><i class="fa fa-th-list fa-lg"></i>&nbsp;Cargar</button>
                        <button class='btn btn-primary btn-sm' class="btn btn-primary" id="btnRefrescar"><i class="fa fa-refresh fa-lg"></i>&nbsp;Refrescar</button>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                <!-- Finaliza contenido -->
            </div>
        </div>
        <form id="formValidaciones">
            <div class="row" id="resultados"></div>
            <div class="row" id="resultadosVacios"></div>
        </form>
    </section><!-- /.content -->
@stop
