<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

@include( 'admin.mantenimiento.js.wsenvios_ajax' )
@include( 'admin.mantenimiento.js.wsenvios' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Reporte WS Envios
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">Mantenimientos</a></li>
        <li class="active">Reporte de WS Envios</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <!-- Inicia contenido -->
            <div class="box">
                
                <div class="box-body table-responsive">
                    <table id="t_wsenvios" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Operacion</th>
                                <th>N° Tareas</th>
                                <th>Cod. Empleado</th>
                                <th>Duracion</th>
                                <th>Distrito</th>
                                <th>Tipo</th>
                                <th>Response</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Fecha</th>
                                <th>Operacion</th>
                                <th>N° Tareas</th>
                                <th>Cod. Empleado</th>
                                <th>Duracion</th>
                                <th>Distrito</th>
                                <th>Tipo</th>
                                <th>Response</th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
            <!-- Finaliza contenido -->
        </div>
    </div>

</section><!-- /.content -->
@stop
