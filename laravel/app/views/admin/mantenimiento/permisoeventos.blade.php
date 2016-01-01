<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.mantenimiento.js.permisoeventos_ajax' )
    @include( 'admin.mantenimiento.js.permisoeventos' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Permisos a Eventos
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Mantenimiento</a></li>
            <li class="active">Permisos a Eventos</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
        <div class="row form-group">
            <div class="col-sm-12">
<!--                <div>
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
                </div>-->
                <div class="tab-content">
                    <!--<div class="tab-pane fade active in" id="tap_gestionar">-->
                        <div class="box">
                            <div class="row form-group col-sm-12">
                                <!-- Inicia contenido -->
                                <div class="box">
                                    <div class="box-header">
                                        <div class="col-sm-3">
                                            <label>Tipo de Persona:</label>
                                            <select class="form-control" name="slct_tipo_persona" id="slct_tipo_persona">
                                                <option value="">.::Seleccione::.</option>
                                                <option value="1">Usuarios</option>
                                                <option value="2">Tecnicos</option>
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
                                        <table id="t_personas" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Apellido</th>
                                                    <th>Nombre</th>
                                                    <th>DNI</th>
                                                    <th>Tipo de Persona</th>
                                                    <th>Detalle</th>
                                                    <th> [] </th>
                                                </tr>
                                            </thead>
                                            <tbody id="tb_personas">
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Apellido</th>
                                                    <th>Nombre</th>
                                                    <th>DNI</th>
                                                    <th>Tipo de Persona</th>
                                                    <th>Detalle</th>
                                                    <th> [] </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                                <!-- Finaliza contenido -->
                            </div>
                        </div>
                    <!--</div>-->
                </div>
            </div>
        </div>

    </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.permisoeventos' )
@stop