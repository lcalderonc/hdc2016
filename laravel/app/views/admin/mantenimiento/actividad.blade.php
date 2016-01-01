<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
@parent
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
@include( 'admin.js.slct_global_ajax' )
@include( 'admin.js.slct_global' )
@include( 'admin.mantenimiento.js.actividad_ajax' )
@include( 'admin.mantenimiento.js.actividad' )
@stop
        <!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Mantenimiento de Actividades
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">Mantenimientos</a></li>
        <li class="active">Mantenimiento de Actividades</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <!-- Inicia contenido -->
            <div>
                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active" role="tab" data-toggle="tab">
                        <a href="#tb_actividades">Actividades</a>
                    </li>
                    <li role="presentation" role="tab" data-toggle="tab">
                        <a href="#tb_actividadesTipo">Tipo Actividades</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="tb_actividades">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Filtros</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="t_actividades" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th class="editarG"> [ ] </th>
                                </tr>
                                </thead>
                                <tbody id="tb_actividad">

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th class="editarG"> [ ] </th>
                                </tr>
                                </tfoot>
                            </table>
                            <a class='btn btn-primary btn-sm' class="btn btn-primary nuevo"
                               data-toggle="modal" data-target="#actividadModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
                <div role="tabpanel" class="tab-pane fade" id="tb_actividadesTipo">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Filtros</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="t_actividadesTipo" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Label</th>
                                        <th>Sla</th>
                                        <th>Duracion</th>
                                        <th>Estado</th>
                                        <th class="editarG"> [ ] </th>
                                    </tr>
                                </thead>
                                <tbody id="tb_actividadTipo">

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Label</th>
                                        <th>Sla</th>
                                        <th>Duracion</th>
                                        <th>Estado</th>
                                        <th class="editarG"> [ ] </th>
                                    </tr>
                                </tfoot>
                            </table>
                            <a class='btn btn-primary btn-sm' class="btn btn-primary nuevo"
                               data-toggle="modal" data-target="#actividadTipoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>
            <!-- Finaliza contenido -->
        </div>
    </div>

</section><!-- /.content -->
@stop

@section('formulario')
    @include( 'admin.mantenimiento.form.actividad' )
    @include( 'admin.mantenimiento.form.actividadTipo' )
@stop
