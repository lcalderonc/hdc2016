<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

@include( 'admin.mantenimiento.js.errores_ajax' )
@include( 'admin.mantenimiento.js.errores' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Mantenimiento de Errores
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">Mantenimientos</a></li>
        <li class="active">Mantenimiento de Errores</li>
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
                    <table id="t_errores" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Codigo</th>
                                <th>Archivo</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th class="editarG"> [ ] </th>
                            </tr>
                        </thead>
                        <tbody id="tb_errores">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Usuario</th>
                                <th>Codigo</th>
                                <th>Archivo</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th class="editarG"> [ ] </th>
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
