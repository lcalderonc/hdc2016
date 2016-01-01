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
                        Reporte de Digitalización
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Digitalización</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form name="form_critico" id="form_critico" method="post" action="reporte/digitalizacion" enctype="multipart/form-data">
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
