<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.reporte.js.cruce' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Cruce Finalizado
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Cruce Finalizado</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form name="form_cruce" id="form_cruce" method="post" action="reporte/cruce" enctype="multipart/form-data">
                        <fieldset>
                            <legend>
                                <label>
                                    Reporte de Cruce Finalizado - Officetrack VS Liquidados
                                </label>
                            </legend>
                            <div class="row form-group" id="div_fecha">
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="fecha" name="fecha" onfocus="blur()"/>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary" id="generar_cruce" name="generar_cruce">
                                    Reporte
                                </button>
                            </div>
                        </div>
                    </form>
                </section><!-- /.content -->
@stop
<!-- 
@section('formulario')
     @include( 'admin.mantenimiento.form.usuario' ) 
@stop -->