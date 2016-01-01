<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.mantenimiento.js.troba_ajax' )
    @include( 'admin.mantenimiento.js.troba' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Mantenimiento de Trobas Digitalizadas
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Mantenimientos</a></li>
                        <li class="active">Mantenimiento de Trobas Digitalizadas</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- Inicia contenido -->
                            <div class="box">
                                <!-- <div class="box-header">
                                    <h3 class="box-title">Filtros</h3>
                                </div>/.box-header -->
                                <div class="col-sm-12">
                                  <div class="col-sm-2">
                                    <label class="control-label">Zonal:
                                        <a id="error_zonal" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Zonal">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <select class="form-control" name="slct_zonal" id="slct_zonal">
                                    </select>
                                  </div>
                                  <div class="col-sm-2">
                                    <label class="control-label">Nodo:
                                      <a id="error_nodo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Nodo">
                                          <i class="fa fa-exclamation"></i>
                                      </a>
                                    </label>
                                    <select class="form-control" name="slct_nodo" id="slct_nodo">
                                    </select>
                                  </div>
                                  <div class="col-sm-2">
                                    <label class="control-label">Troba:
                                        <a id="error_troba" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Troba">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <select class="form-control" name="slct_troba" id="slct_troba">
                                    </select>
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="col-sm-2">
                                    <a class='btn btn-primary btn-sm' class="btn btn-primary" id="nuevo"
                                        data-toggle="modal" data-backdrop="static" data-target="#trobaModal" data-titulo="Nuevo">
                                        <i class="fa fa-plus fa-lg"> </i>
                                        &nbsp;Nuevo
                                    </a>
                                  </div>
                                </div>
                                <div class="box-body table-responsive">
                                    <table id="t_trobas" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Zonal</th>
                                                <th>Nodo</th>
                                                <th>Troba</th>
                                                <th>Contrata Reparto</th>
                                                <th>Contrata Zona</th>
                                                <th>clientes</th>
                                                <th>Fecha Inicio Reparto</th>
                                                <th>Fecha Fin (Apagon)</th>
                                                <!-- <th>Fecha planificacion</th> -->
                                                <th>Digitalizacion</th>
                                                <th>Observacion</th>
                                                <th>Estado</th>
                                                <th class="editarG"> [ ] </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_trobas">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Zonal</th>
                                                <th>Nodo</th>
                                                <th>Troba</th>
                                                <th>Contrata Reparto</th>
                                                <th>Contrata Zona</th>
                                                <th>clientes</th>
                                                <th>Fecha Inicio Reparto</th>
                                                <th>Fecha Fin (Apagon)</th>
                                                <!-- <th>Fecha planificacion</th> -->
                                                <th>Digitalizacion</th>
                                                <th>Observacion</th>
                                                <th>Estado</th>
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

@section('formulario')
     @include( 'admin.mantenimiento.form.troba' )
@stop