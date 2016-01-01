<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.historico.js.masivo_ajax' )
    @include( 'admin.historico.js.masivo' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Actualizaci&oacute;n de quiebre:
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Hist&oacute;rico</a></li>
                        <li class="active">Carga individual</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label">Quiebre</label>
                                <select class="form-control" name="slct_quiebre" id="slct_quiebre">
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Contrata</label>
                                <select class="form-control" name="slct_empresa" id="slct_empresa">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label id="lbl_masivo">
                                    <input type="radio" name="opcion" id="masivo" value="1" checked>
                                    Masiva
                                </label>
                                <input type="file" class="form-control" id="txt_archivo" name="txt_archivo" accept="text/plain">
                            </div>
                            <div class="col-sm-6">
                                <label id="lbl_individual">
                                    <input type="radio" name="opcion" id="individual" value="2">
                                    Individual
                                </label>
                                <input type="text" class="form-control" id="averia" name="averia"/>
                            </div>
                            
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label>Forzar Actualización: 
                                <input type="checkbox" class="form-control" id="chk_forzar" name="chk_forzar" value="1">
                            </label>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <button type="button" id="btn_generar" name="btn_generar" class="btn btn-primary" onclick="doCarga()">
                                    Generar actualizaci&oacuten
                                </button>
                            </div>
                        </div>
                    </div>
                </section><!-- /.content -->
@stop
<!-- 
@section('formulario')
     @include( 'admin.mantenimiento.form.usuario' ) 
@stop -->