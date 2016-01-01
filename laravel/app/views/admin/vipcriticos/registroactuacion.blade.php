<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent

@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<!-- Content Header (Page header) -->

<section class="content-header">
    <h1>
        Registro de actuación
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">VIP Críticos</a></li>
        <li class="active">Registro de actuación</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <form name="frm_criticos" id="frm_criticos" action="" method="POST">
        <div class="box box-default color-palette-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-tag"></i> Datos</h3>
            </div>
            <div class="box-body">

                <div class="row form-group">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Tipo de cliente
                        </div>
                        <div class="col-sm-4">
                            <select class="form-control" id="tipo_actividad" name="tipo_actividad">
                                <option value="">Nivel Alto Directivo</option>
                                <option value="">Nivel Directivo</option>
                                <option value="">Cliente Alto Impacto</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            &nbsp;
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Tipo de actuación
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="tipo_actividad" name="tipo_actividad">
                                <option value="">AVERIA</option>
                                <option value="">PROVISION</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Referido por
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="255" name="referidopor" id="referidopor">
                        </div>
                        <div class="col-sm-2">
                            Asunto de correo
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="255" name="subcorreo" id="subcorreo">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Nombre Cliente
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="nomcliente" id="nomcliente">
                        </div>
                        <div class="col-sm-2">
                            Telefono Cliente
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="telefono" id="telefono">
                        </div>
                        <div class="col-sm-2">
                            Código Cliente
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="codcliente" id="codcliente">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Aver/Req/Pet
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="codactu" id="codactu">
                        </div>
                        <div class="col-sm-2">
                            DNI/RUC
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="dniruc" id="dniruc">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Nombre Contacto
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="nom_contacto" id="nom_contacto">
                        </div>
                        <div class="col-sm-2">
                            Telefono Contacto
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="tel_contacto" id="tel_contacto">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            LIDER TDP
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="nom_contacto" id="lider_tdp">
                        </div>
                        <div class="col-sm-2">
                             GESTOR HDEC
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="tel_contacto" id="gestor_hdec">
                        </div>
                        <div class="col-sm-2">
                             GESTOR TDP
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="tel_contacto" id="gestor_tdp">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            EECC
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="slct_eecc" name="slct_eecc">
                                <option id=""> - Seleccione - </option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            Tecnico EECC
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="tecnico" id="tecnico">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Problemas
                        </div>
                        <div class="col-sm-6">
                            <select id="problemas" name="problemas"  class="form-control">
                                <option value=""> - Seleccione - </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Observaciones
                        </div>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                        </div>
                    </div>
                </div>            
            </div><!-- /.box-body -->
        </div>
        <div class="box box-default color-palette-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-calendar"></i> Agendamiento</h3>
            </div>
            <div class="box-body">            
                <div class="row form-group">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            ¿Agendar actuación?
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" id="agendar" name="agendar">
                                <option value="no">NO</option>
                                <option value="si">SI</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Fecha búsqueda
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input class="form-control" type="text" value="" maxlength="11" name="fec_busqueda" id="fec_busqueda">
                                <a href="javascript:void(0)" target="_blank" class="btn btn-default input-group-addon">
                                    <i class="fa fa-calendar"> Seleccionar cupo</i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            Fecha agenda
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="fec_agenda" id="fec_agenda">
                        </div>
                        <div class="col-sm-2">
                            Turno agenda
                        </div>
                        <div class="col-sm-2">
                            <input class="form-control" type="text" value="" maxlength="11" name="turno_agenda" id="turno_agenda">
                        </div>
                    </div>
                </div>            
            </div><!-- /.box-body -->
        </div>
        <div class="box box-default color-palette-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-folder-open-o"></i> Archivo</h3>
            </div>
            <div class="box-body">            
                <div class="row form-group">
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <input class="form-control" type="file" value="" maxlength="11" name="archivo" id="archivo">
                        </div>
                    </div>
                </div>

            </div><!-- /.box-body -->
        </div>

        <input class="btn btn-block btn-danger" type="submit" value="Grabar" maxlength="11" name="archivo" id="archivo">
    </form>
</section><!-- /.content -->

@stop
