<!-- /.modal -->
<div class="modal fade" id="cupoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">New message</h4>
            </div>
            <div class="modal-body">
                <form id="form_cupos" name="form_cupos" action="" method="post">
                    <input type="hidden" name="txt_token" id="txt_token" value="<?php echo Session::get('s_token'); ?>" />
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label">Zonal:
                                    <a id="error_zonal" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Zonal">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <select class="form-control" name="slct_zonal" id="slct_zonal">
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Empresa:
                                    <a id="error_empresa" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Empresa">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <select class="form-control" name="slct_empresa" id="slct_empresa">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label">Grupo quiebre:
                                    <a id="error_quiebregrupos" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione grupo de quiebre">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <select class="form-control" name="slct_quiebregrupos" id="slct_quiebregrupos">
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Dia:
                                    <a id="error_dia" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Dia">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <select class="form-control" name="slct_dia" id="slct_dia">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label">Tipo Horario (m):
                                </label>
                                <select class="form-control" name="slct_horariotipo" id="slct_horariotipo">
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Horario:
                                    <a id="error_horario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Horario">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <select class="form-control" name="slct_horario" id="slct_horario">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label">Capacidad
                                    <a id="error_capacidad" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Capacidad">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <input type="text" class="form-control" placeholder="Ingrese Capacidad" name="txt_capacidad" id="txt_capacidad">
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Estado:
                                </label>
                                <select class="form-control" name="slct_estado" id="slct_estado">
                                    <option value='0'>Inactivo</option>
                                    <option value='1' selected>Activo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->