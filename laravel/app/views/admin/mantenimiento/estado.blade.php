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
    @include( 'admin.mantenimiento.js.motivo_ajax' )
    @include( 'admin.mantenimiento.js.motivo' )
    @include( 'admin.mantenimiento.js.submotivo_ajax' )
    @include( 'admin.mantenimiento.js.submotivo' )
    @include( 'admin.mantenimiento.js.estado_ajax' )
    @include( 'admin.mantenimiento.js.estado' )
    @include( 'admin.mantenimiento.js.estadomotivosubmotivo_ajax' )
    @include( 'admin.mantenimiento.js.estadomotivosubmotivo' )
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
                        Mantenimiento de Estados
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Mantenimientos</a></li>
                        <li class="active">Mantenimiento de Estados</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div>
                                <ul id="myTab" class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#t_motivo">Motivo</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_submotivo">Submotivo</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_estado">Estado</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_estadomotivosubmotivo">Matriz PSI</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="t_motivo">
                                    <div class="box">
                                        <div class="box-body table-responsive">
                                            <table id="t_motivos" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_motivos">
                                                    
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
                                            data-toggle="modal" data-target="#motivoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <div class="tab-pane fade" id="t_submotivo">
                                    <div class="box">
                                        <div class="box-body table-responsive">
                                            <table id="t_submotivos" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_submotivos">
                                                    
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
                                            data-toggle="modal" data-target="#submotivoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                        </div><!-- /.box-body -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="t_estado">
                                    <div class="box">
                                        <div class="box-body table-responsive">
                                            <table id="t_estados" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_estados">
                                                    
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
                                            data-toggle="modal" data-target="#estadoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <div class="tab-pane fade" id="t_estadomotivosubmotivo">
                                    <div class="box">
                                        <div class="box-body table-responsive">
                                            <table id="t_estadomotivosubmotivos" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Motivo</th>
                                                        <th>Submotivo</th>
                                                        <th>Estados</th>
                                                        <th>Descripción de la acción</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_estadomotivosubmotivos">
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Motivo</th>
                                                        <th>Submotivo</th>
                                                        <th>Estados</th>
                                                        <th>Descripción de la acción</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <a class='btn btn-primary btn-sm' class="btn btn-primary nuevo"
                                            data-toggle="modal" data-target="#estadomotivosubmotivoModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                            </div>
                        </div>
                    </div>

                </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.motivo' ) 
     @include( 'admin.mantenimiento.form.submotivo' ) 
     @include( 'admin.mantenimiento.form.estado' ) 
     @include( 'admin.mantenimiento.form.estadomotivosubmotivo' ) 
@stop