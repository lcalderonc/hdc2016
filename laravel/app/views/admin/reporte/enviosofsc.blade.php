<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('js/psi.js') }}

    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )

    @include( 'admin.reporte.js.enviosofsc_ajax' )
    @include( 'admin.reporte.js.enviosofsc' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Reporte de Envíos OFSC
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Reporte</a></li>
                        <li class="active">Reporte de Envíos OFSC</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row form-group">
                        <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="col-sm-1 control-label">Inicio</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="fecha_recepcion_ini" name="fecha_recepcion_ini" readonly="readonly"/>
                                    </div>
                                    <label class="col-sm-1 control-label">Final</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="fecha_recepcion_fin" name="fecha_recepcion_fin" readonly="readonly"/>
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="button" id="mostrar" class="btn btn-primary">Mostrar</button>
                                    </div>                                        
                                </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- Inicia contenido -->
                            <div class="box">
                             
                                <div class="box-body table-responsive">
                                    <table id="t_enviosofs" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Accion</th>
                                                <th>FechaCreacion</th>
                                                <th>Usuario</th>
                                                <th>Enviado</th>
                                                <th>Respuesta</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                          
                                            </tr>
                                        </thead>
                                        <tbody id="tb_enviosofs">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Accion</th>
                                                <th>FechaCreacion</th>
                                                <th>Usuario</th>
                                                <th>Enviado</th>
                                                <th>Respuesta</th>                                                
                                                <th></th>                                                
                                                <th></th>                                                
                                                <th></th>                                                
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                            <!-- Finaliza contenido -->
                        </div>
                    </div>

                    
                </section><!-- /.content -->

                <!-- Modal -->
                <div class="modal fade" id="ItemPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Resumen Envíos OFSC</h4>
                      </div>
                      <div class="modal-body">
                        <div id="resultadoOfsc" style="word-wrap: break-word;padding: 5px;background-color: #D0FED9;border:1px solid #000;"></div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>         

@stop
