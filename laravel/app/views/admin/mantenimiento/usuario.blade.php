<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('js/psi.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.mantenimiento.js.usuario_ajax' )
    @include( 'admin.mantenimiento.js.usuario' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Mantenimiento de Usuarios
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Mantenimientos</a></li>
                        <li class="active">Mantenimiento de Usuarios</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- Inicia contenido -->
                            <div class="box">

                                <div class="box-body table-responsive">
                                    <table id="t_usuarios" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Apellidos</th>
                                                <th>Nombres</th>
                                                <th>Usuario</th>
                                                <th>DNI</th>
                                                <th>Perfil</th>
                                                <th>Empresa</th>
                                                <th>Estado</th>
                                                <th class="editarG"> [ ] </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_usuarios">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Apellidos</th>
                                                <th>Nombres</th>
                                                <th>Usuario</th>
                                                <th>DNI</th>
                                                <th>Perfil</th>
                                                <th>Empresa</th>
                                                <th>Estado</th>
                                                <th class="editarG"> [ ] </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <a class='btn btn-primary btn-sm' id="nuevo"
                                    data-toggle="modal" data-target="#usuarioModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <!-- Finaliza contenido -->
                        </div>
                    </div>

                </section><!-- /.content -->
@stop
@section('formulario')
     @include( 'admin.mantenimiento.form.usuario' ) 
@stop