<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::style('lib/bootstrap-fileinput/css/fileinput.min.css') }}
    {{ HTML::script('lib/bootstrap-fileinput/js/fileinput.min.js') }} 
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.reporte.js.movimientoult' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Movimientos
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Movimiento</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form name="form_ult_movimiento" id="form_ult_movimiento" method="post" action="reporte/movimientoult" enctype="multipart/form-data">
                        <fieldset>
                            <legend>
                                <div class="checkbox">
                                    <label id="lbl_fecha">
                                        <input type="checkbox" id="check_fecha" name="check_fecha"> 
                                        Filtrar por fecha
                                    </label>
                                </div>
                            </legend>
                            <div class="row form-group" id="div_fecha">
                                <div class="col-sm-12">
                                    <div class="col-sm-6">
                                        <select class="" name="slct_reporte" id="slct_reporte">
                                            <option value="" style="display:none">.::Seleccione::.</option>
                                            <option value="act">Fecha registro de actuacion</option>
                                            <option value="atc">Fecha Registro ATC</option>
                                            <option value="mov">Fecha Movimiento</option>
                                            <option value="age">Fecha Agenda</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="fecha" name="fecha" onfocus="blur()"/>
                                    </div>
                                    
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="field_averia">
                            <legend>
                                <div class="checkbox">
                                    <label id="lbl_averia">
                                        <input type="checkbox" id="check_averia" name="check_averia"> 
                                        Filtrar masivamente por Código Actuación
                                    </label>
                                </div>
                            </legend>
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Archivo</label>
                                        <input type="file" class="form-control" id="file_averia" name="file_averia" accept="text/plain">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="field_averia">
                            <legend>
                                <div class="checkbox">
                                    <label id="lbl_averia_u">
                                        <input type="checkbox" id="check_averia_u" name="check_averia_u"> 
                                        Filtrar por Código Actuación
                                    </label>
                                </div>
                            </legend>
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Código Actuación</label>
                                        <input type="text" id="txt_cod_actu" name="txt_cod_actu">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <label>Solo Pendientes?</label>
                                <select id="slct_pendiente" name="slct_pendiente">
                                    <option value="0">No</option>
                                    <option value="1">Si</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary" id="generar_movimientos" name="generar_movimientos">
                                    Reporte de Movimiento
                                </button>
                            </div>
                        </div>
                    </form>
                </section><!-- /.content -->
@stop

@section('formulario')
     <!-- @include( 'admin.mantenimiento.form.usuario' )  -->
@stop
