<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
@parent
{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
{{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
{{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}    
{{ HTML::script('https://maps.googleapis.com/maps/api/js?libraries=places,geometry,drawing') }}
{{ HTML::script('js/geo/geo.functions.js') }}
{{ HTML::script('js/geo/markerwithlabel.js') }}
{{ HTML::script('js/utils.js') }}
{{ HTML::script('js/psi.js') }}
{{ HTML::script('js/psigeo.js') }}

@include( 'admin.datos.js.updatexy' )

@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->
<style>
    #update_map, #update_sv {
        width: 100%;
        height: 550px;
    }
    #target {
        width: 345px;
    }
    .controls {
        margin-top: 16px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 250px;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    .pac-container {
        font-family: Roboto;
    }

    #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
    }

    #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }
</style>
<section class="content-header">
    <h1>
        Actualizar coordenadas
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

            <form name="form_Personalizado" id="form_Personalizado" method="POST" action="">

                <div class="col-sm-12">
                    <div class="col-sm-2">
                        <select class="form-control" name="slct_criterio" id="slct_criterio">
                            <option value="gd.averia">Código Actuación</option>
                            <option value="g.id">ID</option>
                            <option value="gd.telefono">Teléfono</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="txt_buscar" id="txt_buscar">
                    </div>
                    <div class="col-sm-2">
                        <a class="btn btn-primary btn-sm" id="btn_personalizado">
                            <i class="fa fa-search fa-lg"></i> Buscar
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <label>X</label>
                        <input type="text" id="txt_upd_x" name="txt_upd_x" readonly="true">

                        <label>Y</label>
                        <input type="text" id="txt_upd_y" name="txt_upd_y" readonly="true">

                        <input type="hidden" id="txt_chk_actu" name="txt_chk_actu" readonly="true">
                        <input type="hidden" id="txt_chk_tipo" name="txt_chk_tipo" readonly="true">
                        <a onclick="actualizarUbicacion()" class="btn btn-danger"><i class="fa fa-save fa-lg"></i> Actualizar</a>
                        <a onclick="limpiarUbicacion()" class="btn btn-warning"><i class="fa fa-eraser fa-lg"></i> Limpiar mapa</a>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-2">
                        <label>FFTT</label>
                        <input class="form-control" type="text" name="txt_data_fftt" id="txt_data_fftt" readonly="true" disabled="true">
                    </div>
                    <div class="col-sm-2">
                        <label>MDF/NODO</label>
                        <select class="form-control" id="mdf_nodo" name="mdf_nodo" onchange="getTroba()">
                            <option value="">.:: MDF/NODO ::.</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="catv">TROBA</label>
                        <select class="form-control catv" id="troba" name="troba" onchange="getAmplificador()">
                            <option value="">.:: TROBA ::.</option>
                        </select>

                        <label class="stb">TIPO RED</label>
                        <select class="form-control stb" id="tipo_red" name="tipo_red" onchange="getCableArmario()">
                            <option value="">.:: TIPO RED ::.</option>
                            <option value="cable">Directa</option>
                            <option value="armario">Flexible</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="catv">AMPLIFICADOR</label>
                        <select class="form-control catv" id="amp" name="amp" onchange="getTap()">
                            <option value="">.:: AMPLIFICADOR ::.</option>
                        </select>

                        <label class="stb">CABLE/ARMARIO</label>
                        <select class="form-control stb" id="cable_armario" name="cable_armario" onchange="getTerminal()">
                            <option value="">.:: CABLE/ARMARIO ::.</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="catv">TAP</label>
                        <select class="form-control catv" id="tap" name="tap" onchange="getTapCoord()">
                            <option value="">.:: TAP ::.</option>
                        </select>

                        <label class="stb">TERMINAL</label>
                        <select class="form-control stb" id="terminal" name="terminal" onchange="getTerminalCoord()">
                            <option value="">.:: TERMINAL ::.</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-2">
                        <label>Dirección</label>
                        <input class="form-control" type="text" name="txt_dir_inst" id="txt_dir_inst" readonly="true" disabled="true">
                    </div>
                    <div class="col-sm-2">
                        <label>Distrito</label>
                        <select class="form-control" name="calle_distrito" id="calle_distrito">
                            <option value="150100"> - Todo Lima - </option>
                            <option value="150101">Lima Cercado</option>
                            <option value="150102">Ancon</option>
                            <option value="150103">Ate</option>
                            <option value="150104">Barranco</option>
                            <option value="150105">Breña</option>
                            <option value="150106">Carabayllo</option>
                            <option value="150107">Chaclacayo</option>
                            <option value="150108">Chorrillos</option>
                            <option value="150109">Cieneguilla</option>
                            <option value="150110">Comas</option>
                            <option value="150111">El Agustino</option>
                            <option value="150112">Independencia</option>
                            <option value="150113">Jesus María</option>
                            <option value="150114">La Molina</option>
                            <option value="150115">La Victoria</option>
                            <option value="150116">Lince</option>
                            <option value="150117">Los Olivos</option>
                            <option value="150118">Lurigancho</option>
                            <option value="150119">Lurin</option>
                            <option value="150120">Magdalena del Mar</option>
                            <option value="150121">Pueblo Libre</option>
                            <option value="150122">Miraflores</option>
                            <option value="150123">Pachacamac</option>
                            <option value="150124">Pucusana</option>
                            <option value="150125">Puente Piedra</option>
                            <option value="150126">Punta Hermosa</option>
                            <option value="150127">Punta Negra</option>
                            <option value="150128">Rimac</option>
                            <option value="150129">San Bartolo</option>
                            <option value="150130">San Borja</option>
                            <option value="150131">San Isidro</option>
                            <option value="150132">San Juan De Lurigancho</option>
                            <option value="150133">San Juan de Miraflores</option>
                            <option value="150134">San Luis</option>
                            <option value="150135">San Martin de Porres</option>
                            <option value="150136">San Miguel</option>
                            <option value="150137">Santa Anita</option>
                            <option value="150138">Santa Maria del Mar</option>
                            <option value="150139">Santa Rosa</option>
                            <option value="150140">Santiago de Surco</option>
                            <option value="150141">Surquillo</option>
                            <option value="150142">Villa el Salvador</option>
                            <option value="150143">Villa Maria del Triunfo</option>
                            <option value="070100"> - Todo Callao - </option>
                            <option value="070101">Callao</option>
                            <option value="070102">Bellavista</option>
                            <option value="070103">Carmen de la Legua Reynoso</option>
                            <option value="070104">La Perla</option>
                            <option value="070105">La Punta</option>
                            <option value="070106">Ventanilla</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label>Calle / Av. / Jr. </label>
                        <input class="form-control" type="text" id="txt_calle_nombre" name="txt_calle_nombre">
                    </div>
                    <div class="col-sm-2">
                        <label>Número</label>
                        <input class="form-control" type="text" id="txt_calle_numero" name="txt_calle_numero">
                    </div>
                    <div class="col-sm-2">
                        <label>&nbsp;</label>
                        <input class="form-control" type="button" id="btn_calle" name="btn_calle" value="buscar" onclick="getBuscarCalle()">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-default" id="btn_show_polygon" onclick="upd_show_polygon()"><i class="fa fa-cube"></i> Polygon</button>
                    </div>
                </div>
            </form>                                

            <!-- Finaliza contenido -->
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <input id="pac-input" class="controls" type="text" placeholder="Search Box">
            <div id="update_map"></div>
        </div>
        <div class="col-sm-6">
            <div id="update_sv"></div>
        </div>
    </div>

</section><!-- /.content -->
@stop
