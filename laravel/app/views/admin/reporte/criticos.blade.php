<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.reporte.js.critico' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Criticos
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Critico</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form name="form_critico" id="form_critico" method="post" action="reporte/critico" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- Inicia contenido -->
                                <div class="box">
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th colspan='2'>Reporte de Pendientes</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="success">Averias:</td>
                                                    <td>
                                                        <a href="#" id="averias" name="Averias" onclick="descargaAverias();">[ Descargar ]</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="success">Provision:</td>
                                                    <td>
                                                        <a href="#" id="provision" name="provision" onclick="descargaProvision();">[ Descargar ]</a>
                                                        </td >
                                                </tr>
                                                <tr>
                                                    <td colspan='2'><a href="#" id="reg_link">[ Ver ultima actualizacion ]</a></td >
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                                <!-- Finaliza contenido -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- Inicia contenido -->
                                <div class="box">
                                    <div class="box-body table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <th colspan='2'>Reporte de historico</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="success">Actividad:</td >
                                                    <td>
                                                        <select class="form-control" name="actividad" id="actividad">
                                                            <option value='' style="display:none">.:Seleccione:.</option>
                                                            <option value='1'>Averia</option>
                                                            <option value='2'>Provision</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="success">Fecha:</td >
                                                    <td>
                                                        <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="fecha" name="fecha" onfocus="blur()"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan='2'>
                                                        <button type="button" class="btn btn-primary btn-sm" id="generar_critico" name="generar_critico">
                                                            Reporte
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                                <!-- Finaliza contenido -->
                            </div>
                        </div>
                    </form>
                </section><!-- /.content -->
@stop
<!-- 
@section('formulario')
     @include( 'admin.mantenimiento.form.usuario' ) 
@stop -->
