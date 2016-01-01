<!-- /.modal -->
<div class="modal fade" id="actividadTipoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">New message</h4>
            </div>
            <div class="modal-body">
                <form id="form_actividadesTipo" name="form_actividades" action="" method="post">
                    <div class="form-group">
                        <label class="control-label">Nombre
                            <a id="error_nombreTipo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombreTipo" id="txt_nombreTipo">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Tipo Actividad
                            <a id="error_actividad" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Actividad">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <select class="form-control" name="slct_actividad" id="slct_actividad"></select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Label
                            <a id="error_label" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese un Label">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="Ingrese Label" name="txt_label" id="txt_label">
                    </div>
                    <div class="form-group">
                        <label class="control-label">SLA
                            <a id="error_sla" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese SLA">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="number" class="form-control" placeholder="Ingrese SLA" name="txt_sla" id="txt_sla">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Duracion
                            <a id="error_duracion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Duracion">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="Ingrese Duracion" name="txt_duracion" id="txt_duracion">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Estado:
                        </label>
                        <select class="form-control" name="slct_estado" id="slct_estado">
                            <option value='0'>Inactivo</option>
                            <option value='1' selected>Activo</option>
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