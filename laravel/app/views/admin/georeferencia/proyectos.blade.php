<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
{{ HTML::script('http://maps.google.com/maps/api/js?sensor=false&libraries=drawing') }}
{{ HTML::script('js/psigeo.js') }}
{{ HTML::script('js/psi.js') }}

@include( 'admin.georeferencia.js.proyectos' )

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
        <div class="box-title">Buscar elemento</div>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
        <form name="form_visorgps" id="form_visorgps" method="post" action="">
            <div class="row form-group">
                <div class="col-sm-12">
                    <div class="col-sm-2">
                        <select name="geo_elemento" id="geo_elemento" class="form-control">
                            <option value="">-Seleccione-</option>
                            <option value="1_punto_amplificador">Amplificador</option>
                            <option value="13_poligono_armariopoligono">Armario (area)</option>
                            <option value="14_punto_armariopunto">Armario (punto)</option>
                            <option value="18_punto_clienteanpunto">Cliente (altas nuevas)</option>
                            <option value="4_poligono_distrito">Distrito (area)</option>
                            <option value="16_punto_distritopunto">Distrito (punto)</option>
                            <option value="5_poligono_mdf">Mdf</option>
                            <option value="17_punto_mdfpunto">Mdf (punto)</option>
                            <option value="15_poligono_nodopoligono">Nodo (area)</option>
                            <option value="7_poligono_provincia">Provincia</option>
                            <option value="8_punto_tap">Tap</option>
                            <option value="9_punto_terminald">Terminal (TRD)</option>
                            <option value="10_punto_terminalf">Terminal (TRF)</option>
                            <option value="11_poligono_troba">Troba</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    
                </div>
                <div class="col-sm-12">
                    
                </div>
            </div>


        </form>
    </div><!-- /.box-body -->
</div><!-- /.box -->

<section>

</section>

<!-- Main content -->
<section class="content">              


    <div class="use-sidebar sidebar-at-left" id="main">

        <div id="mymap">Content</div>

        <div id="sidebar">
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Planificaci&oacute;n</h4>
            </div>
            <div class="modal-body" id="contentPlanModal"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@stop
@section('formulario')
     @include( 'admin.historico.form.bandeja_modal' )
@stop