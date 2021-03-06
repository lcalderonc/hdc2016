<!-- /.modal -->
<div class="modal fade" id="quiebreModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">New message</h4>
            </div>
            <div class="modal-body">
                <form id="form_quiebres" name="form_quiebres" action="" method="post">
                    <input type="hidden" name="txt_token" id="txt_token" value="<?php echo Session::get('s_token'); ?>" />
                    <div class="form-group">
                        <label class="control-label">Nombre
                            <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Apocope
                            <a id="error_apocope" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Apocope">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="Ingrese Apocope" name="txt_apocope" id="txt_apocope">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Grupo quiebre:
                            <a id="error_quiebregrupos" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione ]GRupo">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <select class="form-control" name="slct_quiebregrupos" id="slct_quiebregrupos">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Motivo:
                            <a id="error_motivo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Motivo(s)">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <select class="form-control" name="slct_motivo[]" id="slct_motivo" multiple>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Estado:
                        </label>
                        <select class="form-control" name="slct_estado" id="slct_estado">
                            <option value='0'>Inactivo</option>
                            <option value='1' selected>Activo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Officetrack:
                        </label>
                        <select class="form-control" multiple="multiple" name="slct_actividad[]" id="slct_actividad">
                        </select>
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
