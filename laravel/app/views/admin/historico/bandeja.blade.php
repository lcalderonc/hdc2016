<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::style('lib/iCheck/all.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    
    
    {{ HTML::script('https://maps.googleapis.com/maps/api/js?libraries=places,geometry,drawing') }}
    {{ HTML::script('js/geo/geo.functions.js') }}
    {{ HTML::script('js/geo/markerwithlabel.js') }}
    {{ HTML::script('js/utils.js') }}
    
    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.js.horarios_ajax' )
    @include( 'admin.historico.js.usegestion_ajax' )
    @include( 'admin.historico.js.bandeja_modal' )
    
    @include( 'admin.historico.js.officetrack_modal' )

    @include( 'admin.historico.js.ofsc_ajax' )
    
    @include( 'admin.historico.js.bandeja_ajax' )
    @include( 'admin.historico.js.bandeja' )
    @include( 'admin.js.tareas' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Bandeja de Gestión
                        <small> </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                        <li><a href="#">Historico</a></li>
                        <li class="active">Bandeja de Gestión</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- Inicia contenido -->
                            <div class="box">
                                <form name="form_Personalizado" id="form_Personalizado" method="POST" action="">
                                <div class="form-group">
                                    <div class="col-sm-5">
                                        <div class="col-sm-3">
                                        <label>Buscar por:</label>
                                        <input type="hidden" name="bandeja" id="bandeja" value="1">
                                        </div>
                                        <div class="col-sm-4">
                                        <select class="" name="slct_tipo" id="slct_tipo">
                                            <option value="">.::Seleccione::.</option>
                                            <option value="gd.averia">Código Actuación</option>
                                            <option value="g.id">ID</option>
                                            <option value="gd.telefono">Teléfono</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-4">
                                        <input type="text" name="txt_buscar" id="txt_buscar">
                                        </div>
                                        <div class="col-sm-1">
                                        <a class="btn btn-primary btn-sm" id="btn_personalizado">
                                            <i class="fa fa-search fa-lg"></i> 
                                        </a>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                <form name="form_General" id="form_General" method="POST" action="reporte/bandejaexcel">
                                <div class="row form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                        <label>Actividad:</label>
                                        <input type="hidden" name="bandeja" id="bandeja" value="1">
                                        <input type="hidden" name="usuario" id="usuario" value="1">
                                        <select class="form-control" name="slct_actividad[]" id="slct_actividad" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Estado:</label>
                                        <select class="form-control" name="slct_estado[]" id="slct_estado" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Quiebre:</label>
                                        <select class="form-control" name="slct_quiebre[]" id="slct_quiebre" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Empresa:</label>
                                        <select class="form-control" name="slct_empresa[]" id="slct_empresa" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Celula:</label>
                                        <select class="form-control" name="slct_celula[]" id="slct_celula" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Tecnico:</label>
                                        <select class="form-control" name="slct_tecnico[]" id="slct_tecnico" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="col-sm-2">
                                        <label>Est. Legado:</label>
                                        <select class="form-control" name="slct_legado" id="slct_legado">
                                            <option value="">.::Todo::.</option>
                                            <option value="1">Pendiente</option>
                                            <option value="0">No Pendiente</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>OT Est. Transmisión:</label>
                                        <select class="form-control" name="slct_transmision[]" id="slct_transmision" multiple onchange="validarImagenExp();">
                                            <option value="1">Envio</option>
                                            <option value="0001-Inicio">Inicio</option>
                                            <option value="0002-Supervicion">Supervición</option>
                                            <option value="0003-Cierre">Cierre</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>OT Cierre Estados:</label>
                                        <select class="form-control" name="slct_cierre_estado[]" id="slct_cierre_estado" multiple>
                                            <option value="Atendido">Atendido</option>
                                            <option value="Ausente">Ausente</option>
                                            <option value="En procesos">En procesos</option>
                                            <option value="Inefectiva">Inefectiva</option>
                                            <option value="No deja">No deja</option>
                                            <option value="No desea">No desea</option>
                                            <option value="Otros">Otros</option>
                                            <option value="Transferido">Transferido</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Coordinado:</label>
                                        <select class="form-control" name="slct_coordinado" id="slct_coordinado">
                                            <option value="">.::Todo::.</option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-3"> 
                                        <label>Agenda:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" name="fecha_agenda" id="fecha_agenda"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                        <br><br>
                                        <a class="btn btn-primary btn-sm" id="btn_general">
                                            <i class="fa fa-search fa-lg"></i> 
                                        </a>
                                        <a class="btn btn-primary btn-sm" id="btn_vista_detalle" onclick="vistadetalleG('detalle',this.id);" >
                                            <i class="fa fa-expand fa-lg"></i>
                                        </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 detalle" style="display:none">
                                        <div class="col-sm-3">
                                        <label>Zonal:</label>
                                        <select class="form-control" name="slct_zonal[]" id="slct_zonal" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-4">
                                        <label>Nodo:</label>
                                        <select class="form-control" name="slct_nodo[]" id="slct_nodo" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-2">
                                        <label>Troba:</label>
                                        <select class="form-control" name="slct_troba[]" id="slct_troba" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                        <div class="col-sm-3">
                                        <label>MDF:</label>
                                        <select class="form-control" name="slct_mdf[]" id="slct_mdf" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 detalle" style="display:none">
                                        <div class="col-sm-3" style="display:none">
                                        <label>Tipo Actividad Ofsc:</label>
                                        <select class="form-control" name="slct_actividad_tipo[]" id="slct_actividad_tipo" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div> 
                                        <div class="col-sm-3">
                                        <label>Estado Ofsc:</label>
                                        <select class="form-control" name="slct_estado_ofsc[]" id="slct_estado_ofsc" multiple>
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                <div class="box-body" style="min-height:250px">
                                    <table id="t_bandeja" class="table table-bordered table-striped col-sm-12 responsive" width="100%">
                                        <thead>
                                            <tr>
                                                <th style='width:55px !important;' >Id</th>
                                                <th style='width:70px !important;'>Cod Actu</th>
                                                <th style='width:90px !important;'>F. Registro</th>
                                                <th style='width:100px !important;'>Tipo</th>
                                                <th>Quiebre</th>
                                                <th>Empresa</th>
                                                <th style='width:40px !important;'>MDF</th>
                                                <th style='width:90px !important;'>Fecha Agenda</th>
                                                <th style='width:200px !important;'>Tecnico</th>
                                                <th>Estado Ofsc</th>
                                                <th>Estado</th>
                                                <th style="width: 100px !important;"> 
                                                    <a onclick="descargarReporte();" class="btn btn-success"><i class="fa fa-download fa-lg"></i></a>
                                                    <a onclick="descargarReporteImagen();" class="expImage btn btn-success"><i class="fa fa-picture-o fa-lg"></i></a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tb_bandeja">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Id</th>
                                                <th>Cod Actu</th>
                                                <th>F. Registro</th>
                                                <th>Tipo</th>
                                                <th>Quiebre</th>
                                                <th>Empresa</th>
                                                <th>MDF</th>
                                                <th>Fecha Agenda</th>
                                                <th>Tecnico</th>
                                                <th>Estado Ofsc</th>
                                                <th>Estado</th>
                                                <th> 
                                                    <a onclick="descargarReporte();" class="btn btn-success"><i class="fa fa-download fa-lg"></i></a> 
                                                </th>
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
     @include( 'admin.historico.form.bandeja_modal' )
     @include( 'admin.historico.form.officetrack_modal' )
@stop
