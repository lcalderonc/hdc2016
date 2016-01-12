<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
 {{ HTML::style('lib/iCheck/all.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
{{ HTML::script('http://maps.google.com/maps/api/js?sensor=false&libraries=drawing') }}
{{ HTML::script('js/geo/geo.functions.js') }}
{{ HTML::script('js/utils.js') }}
{{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}

{{ HTML::script('lib/input-mask/js/jquery.inputmask.js') }}
{{ HTML::script('lib/input-mask/js/jquery.inputmask.date.extensions.js') }}

@include( 'admin.historico.js.bandeja_ajax' )
@include( 'admin.historico.js.visorgps_ajax' )
@include( 'admin.georeferencia.js.planning_ajax' )
@include( 'admin.georeferencia.js.planning' )

@include( 'admin.js.slct_global_ajax' )
@include( 'admin.js.slct_global' )
@include( 'admin.js.horarios_ajax' )
@include( 'admin.historico.js.bandeja_modal' )

@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style>
    .left {float: left;}
    .right {float: right;}

    .clearer {
        clear: both;
        display: block;
        font-size: 0;
        height: 0;
        line-height: 0;
    }

    /*
            Misc
    ------------------------------------------------------------------- */

    .hidden {display: none;}


    /*
            Example specifics
    ------------------------------------------------------------------- */

    /* Layout */

    #center-wrapper {
        margin: 0 auto;
        width: 920px;
    }


    /* Content & sidebar */

    #mymap,#sidebar {
        text-align: center;
        border: 1px solid;
        height: 600px;
    }

    #sidebar {
        text-align: left;
        background-color: #DEF;
        border-color: #BCD;
        display: none;
        overflow: auto;
    }
    #mymap {
        background-color: #EFE;
        border-color: #CDC;
        width: 98%;
    }

    .use-sidebar #mymap {width: 69%;}
    .use-sidebar #sidebar {
        display: block;
        width: 27%;
    }

    .sidebar-at-left #sidebar {margin-right: 1%;}
    .sidebar-at-right #sidebar {margin-left: 1%;}

    .sidebar-at-left #mymap, .use-sidebar.sidebar-at-right #sidebar, .sidebar-at-right #separator {float: right;}
    .sidebar-at-right #mymap, .use-sidebar.sidebar-at-left #sidebar, .sidebar-at-left #separator {float: left;}

    #separator {
        background-color: #EEE;
        border: 1px solid #CCC;
        display: block;
        outline: none;
        width: 1%;
    }
    .use-sidebar #separator {
        background: url('img/separator/vertical-separator.gif') repeat-y center top;
        border-color: #FFF;
    }
    #separator:hover {
        border-color: #ABC;
        background: #DEF;
    }
    
</style>

<!-- Content Header (Page header) -->
<!--
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
-->

<div class="box box-default">
    <div class="box-header with-border">
        <div class="box-title">Geo Planificaci&oacute;n</div>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
        <form name="form_visorgps" id="form_visorgps" method="post" action="">
        <div class="row">

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Mapa</h3>
                        <div class="box-tools pull-right">
                           <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body"  style="min-height: 135px; max-height: 500px;">
                        <div class="col-sm-12">
                            <div class="col-sm-4">
                             <select class="form-control" name="slct_tipo[]" id="slct_tipo" onchange="listtiponodo()">
                                <option value="0" selected>.:: Seleccione ::.</option>
                                <option value="mdf">MDF</option>
                                <option value="nodo">Nodo</option>
                             </select>
                            </div>
                            <div class="col-sm-4" id="divselect">
                             <select class="form-control" name="slct_nodo[]" id="slct_nodo" onchange="" ></select>
                            </div>
                            <div class="col-sm-4">
                              <input type="button" class="form-control btn-danger" value="Trazar Poligono" name="btn_trazar" id="btn_trazar">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-sm-4">
                              <label>&nbsp;</label>
                              <button type="button" class="btn btn-default" style="width: 170px;" id="show_traffic" name="show_traffic">Mostrar tr&aacute;fico</button>
                           </div>
                            <div class="col-sm-4">
                                <label>&nbsp;</label>
                              <input type="button" class="form-control btn-danger" value="Limpiar mapa" id="btn_limpiar_todo" name="btn_limpiar_todo">
                           </div>
                           <div class="col-sm-4">
                            <label>&nbsp;</label>
                              <input type="button" class="form-control btn-danger" value="Limpiar TMP" id="btn_limpiar_tmp" name="btn_limpiar_tmp" >
                           </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
                
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Subir Archivo</h3>
                        <div class="box-tools pull-right">
                            <!--<button type="button" class="btn btn-box-tool" data-widget="collapse" id="btnupfile"><i class="fa fa-minus"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                    <div class="col-sm-8">
                        <input type="file" class="form-control" id="txt_file_plan" name="txt_file_plan">
                    </div>
                    <div class="col-sm-4">
                        <button type="button" class="form-control btn-danger" id="btn_file_plan" name="btn_file_plan" onclick="uploadGeoPlan()">Subir archivo</button>                        
                    </div>
                </div>
                        <div class="col-sm-12"> 
                            <div class="col-sm-8">
                        <a class="fa fa-check text-info"> Asignadas </a>
                        <label class="nro_asg_file"></label>
                    
                        <a class="fa fa-check text-info"> Temporales </a>
                        <label class="nro_tmp_file"></label>
                    
                        <a class="fa fa-check text-info"> No encontradas </a>
                        <label class="planNot"></label>
                        
                        <label class="no_tmp_file">
                            <a href="" class="downNotFound">Download</a>
                        </label>
                    </div>
                        </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
            </div><!-- /.col --> 


            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Búsqueda</h3>
                        <div class="box-tools pull-right">
                           <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
                        </div>
                    </div>
                    <div class="box-body" style="min-height: 200px; max-height: 500px;">
                        <div class="col-sm-12">
                           <div class="col-sm-4">
                              <label>Zonal:</label>
                              <select class="form-control" name="slct_zonal[]" id="slct_zonal" multiple="multiple">
                                  <option selected="true" value="8">LIM</option>
                              </select>
                           </div>
                           <div class="col-sm-4">
                              <label>Actividad:</label>
                              <select class="form-control" name="slct_actividad[]" id="slct_actividad" multiple="multiple"></select>
                           </div>
                           <div class="col-sm-4">
                              <label>Estado:</label>
                              <select class="form-control" name="slct_estado[]" id="slct_estado" multiple="multiple">
                                 <option value="-1">Temporal</option>
                              </select>
                           </div>
                           <div class="col-sm-4">
                              <label>Quiebre:</label>
                              <select class="form-control" name="slct_quiebre[]" id="slct_quiebre" multiple="multiple"></select>
                           </div>
                           <div class="col-sm-4">
                             <label>Empresa:</label>
                             <select class="form-control" name="slct_empresa[]" id="slct_empresa" onchange="listaCelula()"></select>
                            </div>
                            <div class="col-sm-4">
                             <label>Celula:</label>
                             <select class="form-control" name="slct_celula[]" id="slct_celula"></select>
                            </div>
                            <div class="col-sm-6" style="align:right">
                              <label>Fechas Agenda:</label>
                        <!--<input type="text" class="form-control" name="fecha_agenda" id="fecha_agenda" placeholder="Fecha agenda">-->
                              <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" name="fecha_agenda" id="fecha_agenda"/>
                                                <div class="input-group-addon" onclick="cleandate()" style="cursor: pointer">
                                                    <i class="fa fa-rotate-left" ></i>
                                                </div>
                                                
                              </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer" style="min-height:50px">
                        <div class="col-sm-12">
                        <div class="col-sm-4">
                        <!--<button type="submit" class="form-control btn-danger" name="btn_buscar" id="btn_buscar">Buscar</button>-->
                        <input type="button" class="form-control" value="Buscar" name="btn_buscar" id="btn_buscar"> 
                        </div>
                        </div>
                    </div>
                </div><!-- /.box -->
            </div><!-- /.col -->

        </div>

        </form>
    </div><!-- /.box-body -->
</div><!-- /.box -->

<section>

</section>

<!-- Main content -->
<section class="content">              
    <div id="divresultadopla"></div>

    <div class="use-sidebar sidebar-at-left" id="main">

        <div id="mymap">Content</div>

        <div id="sidebar">
            <button type="button" class="btn btn-default" name="btn_show_tec" id="btn_show_tec">
                <i class="fa fa-users"></i> Tec.
            </button>
            <button type="button" class="btn btn-default" name="btn_show_pdt" id="btn_show_pdt">
                <i class="fa fa-clock-o"></i> Pdte.
            </button>
            <button type="button" class="btn btn-default" name="btn_show_coo" id="btn_show_coo">
                <i class="fa fa-phone"></i> Coord.
            </button>
            <!--
            <button type="button" class="btn btn-default" name="btn_show_tmp" id="btn_show_tmp">
                <i class="fa fa-warning"></i> Temp.
            </button>
            -->
            <div id="tec-list"></div>
        </div>

        <a href="#" id="separator"></a>

        <div class="clearer">&nbsp;</div>

    </div>

</section><!-- /.content -->

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" id="btnPlanModal" data-backdrop="" style="display: none">
    Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id='btnclosepla'><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Planificaci&oacute;n</h4>
            </div>
            <div class="modal-body" id="contentPlanModal"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@stop
@section('formulario')
     @include( 'admin.historico.form.bandeja_modal' )
@stop