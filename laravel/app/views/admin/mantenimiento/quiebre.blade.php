<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.mantenimiento.js.quiebre_ajax' )
    @include( 'admin.mantenimiento.js.quiebre' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Mantenimiento de Quiebres
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Mantenimientos</a></li>
                        <li class="active">Mantenimiento de Quiebres</li>
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
                                    <table id="t_quiebres" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Apocope</th>
                                                <th>Grupo</th>
                                                <th>Estado</th>
                                                <th class="editarG"> [ ] </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_quiebres">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Apocope</th>
                                                <th>Grupo</th>
                                                <th>Estado</th>
                                                <th class="editarG"> [ ] </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <a class='btn btn-primary btn-sm' class="btn btn-primary" id="nuevo"
                                    data-toggle="modal" data-target="#quiebreModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <!-- Finaliza contenido -->
                        </div>
                    </div>

                </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.quiebre' )
@stop