<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    
    {{ HTML::style('lib/backbone/css/asistenciaTecnicos.css') }}
    {{ HTML::style('lib/backbone/css/tecnico.css') }}
    {{ HTML::script('lib/backbone/js/json2.js') }}
    {{ HTML::script('lib/backbone/js/underscore.min.js') }}
    {{ HTML::script('lib/backbone/js/backbone.min.js') }}
    {{ HTML::script('lib/backbone/js/handlebars.js') }}
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.reporte.js.estadosoffice_ajax' )
    @include( 'admin.reporte.js.estadosoffice' )
    @include( 'admin.reporte.js.appTecOff' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Estado Officetrack
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Estado Officetrack</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form name="form_reporte" id="form_reporte" method="post" action="reporte/estadosot" enctype="multipart/form-data">
                        <div class="row form-group">
                            <div class="col-sm-4">
                                <label>Empresa:</label>
                                <select class="form-control" name="slct_empresa" id="slct_empresa">
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>Celula:</label>
                                <select class="form-control" name="slct_celula" id="slct_celula">
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>Estado:</label>
                                <select class="form-control" name="slct_estado[]" id="slct_estado" multiple>
                                    <option value="">.::Seleccione::.</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-4">
                                <fieldset>
                                    <legend>Fecha Agendamiento</legend>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Desde</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="fecha_agendamiento" name="fecha_agendamiento" onfocus="blur()"/>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-8">
                                <fieldset>
                                    <legend>Fecha Recepcion Officetrack</legend>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Inicio</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="fecha_recepcion_ini" name="fecha_recepcion_ini" onfocus="blur()"/>
                                        </div>
                                        <label class="col-sm-1 control-label">Final</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="fecha_recepcion_fin" name="fecha_recepcion_fin" onfocus="blur()"/>
                                        </div>
                                    </div>

                                </fieldset>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    <input type="button" id="mostrar" class="form-control btn btn-primary" value="Mostrar">
                                </div>
                                <div class="col-sm-4">
                                    <input type="button" id="reiniciarFiltros" class="form-control btn btn-danger" value="Reiniciar Filtros">
                                </div>
                            </div>
                        </div>


                        <span>
                            <a href="#" id="ExportExcelPendientes" class="btn btn-success">
                                <i class="fa fa-file-excel-o"></i> Exportar
                            </a>
                        </span>
                        <!--        lista tecnicos pendientes-->
                        <div id="ListaTecnicosPendientes" class="table-responsive">
                            <table id="table-tec" class="table table-bordered table-striped">
                                <tr class="cabecera">
                                    <th>Carnet</th>
                                    <th>Tecnico</th>
                                    <th colspan="3">cantidad</th>
                                    <th>Ver en Mapa</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th> < H</th>
                                    <th>H</th>
                                    <th>  H > </th>
                                    <th></th>
                                </tr>
                            </table>
                        </div>
                        <!--        fin lista tecnicos pendientes-->
                        <br/>
                        <hr/>
                        <br/>
                        <span>
                            <a href="#" id="ExportExcelOfficetrack" class="btn btn-success">
                                <i class="fa fa-file-excel-o"></i> Exportar
                            </a>
                        </span>
                <!--        lista tecnicos estados officetrack-->
                        <div id="ListaTecnicosOfficetrack" class="table-responsive">
                            <table id="table-tec" class="table table-bordered table-striped">
                                <tr class="cabecera">
                                    <td colspan="3">DATOS TECNICO</td>
                                    <td colspan="3">PASOS</td>
                                    <td colspan="8">Estados Officetrack</td>
                                </tr>
                                <tr class="cabecera">
                                    <th rowspan="2" >Carnet</th>
                                    <th rowspan="2" colspan="2" >Tecnico</th>
                                    <th rowspan="2" >Inicio</th>
                                    <th rowspan="2" >Supervicion</th>
                                    <th rowspan="2" >Cerrado</th>
                                    <th class="estado-ot">Atendido</th>
                                    <th class="estado-ot">En procesos</th>
                                    <th class="estado-ot">Inefectiva</th>
                                    <th class="estado-ot">No deja</th>
                                    <th class="estado-ot">No desea</th>
                                    <th class="estado-ot">Ausente</th>
                                    <th class="estado-ot">Otros</th>
                                    <th class="estado-ot">Transferido</th>
                                </tr>
                            </table>
                        </div>
                    </form>
                </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.reporte.form.templates' ) 
@stop 