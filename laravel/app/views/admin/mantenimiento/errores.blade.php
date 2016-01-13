<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}

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
    <div class="row form-group">
        <form id="form_errores" name="form_reporte_demo" method="POST">
            <div class="col-sm-12">
                <h3>Filtro(s):</h3>
            </div>
            <div class="col-sm-12">
                <div class="col-sm-3"> 
                    <label>Fecha:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" name="fecha_agenda" id="fecha_agenda" value="<?php echo date("Y-m-d") . " - " . date("Y-m-d"); ?>" />
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row form-group">
        <div class="col-sm-12">
            <div class="col-sm-3">
                <button type="button" onclick="" id="mostrar" class="btn btn-primary">Mostrar</button>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-xs-12">
            <!-- Inicia contenido -->
            <div class="box-body table-responsive">
                <table id="t_errores" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Usuario</th>
                            <th>Codigo</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Comentario</th>
                            <th class="editarG"> Estado </th>
                            <th class="editarG"> [ ] </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Usuario</th>
                            <th>Codigo</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Comentario</th>
                            <th class="editarG"> Estado </th>
                            <th class="editarG"> [ ] </th>
                        </tr>
                    </tfoot>
                </table>

            </div><!-- /.box-body -->
            <!-- Finaliza contenido -->
        </div>
    </div>

</section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.error' )
     @include( 'admin.mantenimiento.form.errorComentario' )
@stop
