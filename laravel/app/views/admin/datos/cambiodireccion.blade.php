<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('http://maps.google.com/maps/api/js?sensor=false&libraries=places') }}
    {{ HTML::script('js/psi.js') }}
    {{ HTML::script('js/psigeo.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.datos.js.cambio_direccion_ajax' )
    @include( 'admin.datos.js.cambio_direccion' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Registro de cambios
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Datos</a></li>
                        <li class="active">Cambios de direcciones</li>
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
                                    <table id="t_cambios_direcciones" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Cod.Actu.</th>
                                                <th>Cliente</th>
                                                <th>Usuario</th>
                                                <th>Perfil</th>
                                                <th>Estado dirección</th>
                                                <th>Fecha registro</th>
                                                <th>Estado</th>
                                                <th> [ ] </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_cambios_direcciones">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Cod.Actu.</th>
                                                <th>Cliente</th>
                                                <th>Usuario</th>
                                                <th>Perfil</th>
                                                <th>Estado dirección</th>
                                                <th>Fecha registro</th>
                                                <th>Estado</th>
                                                <th> [ ] </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <a class='btn btn-primary btn-sm' class="btn btn-primary" 
                                    data-toggle="modal" data-target="#cambioDireccionModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <!-- Finaliza contenido -->
                        </div>
                    </div>

                </section><!-- /.content -->
@stop
@section('formulario')
     @include( 'admin.datos.form.modal_cambio_direccion' ) 
@stop