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
        Bandeja de actuación
        <small> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
        <li><a href="#">VIP Críticos</a></li>
        <li class="active">Bandeja de actuación</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <form name="frm_bandeja_actuacion" id="frm_bandeja_actuacion" action="" method="POST">
        <div class="box box-default color-palette-box">
            <div class="box-body">
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
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="box-body table-responsive">
                            <table id="t_bandeja_actuacion" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>C. Atención</th>
                                        <th>Tipo Cliente</th>
                                        <th>Referido Por</th>
                                        <th>Fecha Reg</th>
                                        <th>Fecha Agenda</th>
                                        <th>Horario</th>
                                        <th>Gestor HDC</th>
                                        <th>Gestor TDP</th>
                                        <th>Tecnico</th>
                                        <th>EECC</th>
                                        <th>Asunto Correo</th>
                                        <th>Lider TDP</th>
                                        <th>Observaciones</th>
                                        <th style="width: 125px;"> 
                                            <a onclick="#" class="btn btn-success"><i class="fa fa-download fa-lg"></i></a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Estado</th>
                                        <th>C. Atención</th>
                                        <th>Tipo Cliente</th>
                                        <th>Referido Por</th>
                                        <th>Fecha Reg</th>
                                        <th>Fecha Agenda</th>
                                        <th>Horario</th>
                                        <th>Gestor HDC</th>
                                        <th>Gestor TDP</th>
                                        <th>Tecnico</th>
                                        <th>EECC</th>
                                        <th>Asunto Correo</th>
                                        <th>Lider TDP</th>
                                        <th>Observaciones</th>
                                        <th> 
                                            <a onclick="#" class="btn btn-success"><i class="fa fa-download fa-lg"></i></a> 
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div><!-- /.box-body -->
        </div>
    </form>
</section><!-- /.content -->

@stop
