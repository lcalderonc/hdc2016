<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::style('lib/timepicker/css/bootstrap-timepicker.css') }}
    {{ HTML::script('lib/timepicker/js/bootstrap-timepicker.js') }}
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/backbone/js/json2.js') }}
    {{ HTML::script('lib/backbone/js/underscore.min.js') }}
    {{ HTML::script('lib/backbone/js/backbone.min.js') }}
    {{ HTML::script('lib/backbone/js/handlebars.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.mantenimiento.js.cupo_ajax' )
    @include( 'admin.mantenimiento.js.cupo' )
    @include( 'admin.mantenimiento.js.appCupos' )
    @include( 'admin.mantenimiento.js.thorario_ajax' )
    @include( 'admin.mantenimiento.js.thorario' )
    @include( 'admin.mantenimiento.js.horario_ajax' )
    @include( 'admin.mantenimiento.js.horario' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style>
    .edit-col,.edit-row{
        padding: 5px;
        text-align: center;
        width: 30px;
    }
</style>
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Mantenimiento de Cupos
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Mantenimientos</a></li>
                        <li class="active">Mantenimiento de Cupos</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div>
                                <ul id="myTab" class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#t_tabla">Tabla</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_lthorarios">Tipo de Horarios</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#t_horariotb">Horarios</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="t_tabla">
                                    <div class="box">
                                        <div class="row form-group col-sm-12">
                                            <div class="col-sm-2">
                                                <label class="control-label">Zonal:</label>
                                                <select class="form-control" name="t_slct_zonal[]" id="t_slct_zonal"></select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Empresa:</label>
                                                <select class="form-control" name="t_slct_empresa[]" id="t_slct_empresa"></select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Grupo quiebre:</label>
                                                <select class="form-control" name="t_slct_quiebregrupos[]" id="t_slct_quiebregrupos"></select>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Tipo Horario:</label>
                                                <select class="form-control" name="t_slct_horariotipo[]" id="t_slct_horariotipo"></select>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label"></label>
                                                <input type="button" class="form-control btn btn-danger" id="guardar" name="guardar" value="Guardar">
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-12">
                                            <div class="box-body table-responsive">
                                                <table id="t_cupos2" class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Horario</th>
                                                            <th>[ ]</th>
                                                            <th data-dia="1">
                                                                <table>
                                                                    <tr>
                                                                        <td>Lunes</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                            <th data-dia="2">
                                                                <table>
                                                                    <tr>
                                                                        <td>Martes</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                            <th data-dia="3">
                                                                <table>
                                                                    <tr>
                                                                        <td>Míercoles</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                            <th data-dia="4">
                                                                <table>
                                                                    <tr>
                                                                        <td>Jueves</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                            <th data-dia="5">
                                                                <table>
                                                                    <tr>
                                                                        <td>Viernes</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                            <th data-dia="6">
                                                                <table>
                                                                    <tr>
                                                                        <td>Sábado</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                            <th data-dia="7">
                                                                <table>
                                                                    <tr>
                                                                        <td>Domingo</td>
                                                                        <td onkeypress="return soloNumeros(event)" class="edit-col" contentEditable>0</td>
                                                                    </tr>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tb_cupos2">
                                                        @include( 'admin.mantenimiento.form.templatesCupos' ) 
                                                    </tbody>
                                                </table>
                                            </div>
                                                
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="t_lthorarios">
                                    <div class="box">
                                        <div class="box-body table-responsive">
                                            <table id="t_thorarios" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo Horario</th>
                                        <th>Minutos</th>
                                        <th>Estado</th>
                                        <th class="editarG"> [ ] </th>
                                    </tr>
                                </thead>
                                <tbody id="tb_thorarios">
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Tipo Horario</th>
                                        <th>Estado</th>
                                        <th>Minutos</th>
                                        <th class="editarG"> [ ] </th>
                                    </tr>
                                </tfoot>
                            </table>
                                            <a class='btn btn-primary btn-sm nuevo'
                            data-toggle="modal" data-target="#thorarioModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                                <div class="tab-pane fade" id="t_horariotb">
                                    <div class="box">
                                        <div class="box-body table-responsive">
                                            <table id="t_horarios" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Horario</th>
                                                        <th>Hora Inicio</th>
                                                        <th>Hora Fin</th>
                                                        <th>Tipo Horario</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tb_horarios">
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Horario</th>
                                                        <th>Hora Inicio</th>
                                                        <th>Hora Fin</th>
                                                        <th>Tipo Horario</th>
                                                        <th>Estado</th>
                                                        <th class="editarG"> [ ] </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <a class='btn btn-primary btn-sm nuevo'
                                            data-toggle="modal" data-target="#horarioModal" data-titulo="Nuevo"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                                        </div><!-- /.box-body -->
                                    </div><!-- /.box -->
                                </div>
                            </div>
                        </div>
                    </div>

                </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.cupo' ) 
     @include( 'admin.mantenimiento.form.thorario' )
     @include( 'admin.mantenimiento.form.horario' )
@stop