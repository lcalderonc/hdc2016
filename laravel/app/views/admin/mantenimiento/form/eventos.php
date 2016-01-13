<!-- /.modal -->
<div class="modal fade" id="eventosModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">Editar Consultas</h4>
            </div>
            <div class="modal-body">

                <form id="form_eventos" name="form_eventos" action="" method="post" autocomplete="off">
                    <input type="hidden" name="txt_token" id="txt_token" value="<?php echo Session::get('s_token'); ?>" />   
                    <div class="form-group">
                        <label class="control-label">Evento:
                        </label>
                        <select class="form-control" name="slct_tipoevento" id="slct_tipoevento">
                            <option value='1'>Consulta</option>
                            <option value='2' selected>Metodo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Nombre
                            <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="" name="txt_nombre" id="txt_nombre">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Evento
                            <a id="error_evento" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="" name="txt_evento" id="txt_evento">
                    </div>

                    <div class="form-group">
                        <label class="control-label">id_sql
                            <a id="error_id_sql" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="" name="txt_id_sql" id="txt_id_sql">
                    </div>

                    <div class="form-group">
                        <label class="control-label">id_Where
                            <a id="error_id_where" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="" name="txt_id_where" id="txt_id_where">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Extraer
                            <a id="error_extraer" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="" name="txt_extraer" id="txt_extraer">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Valor Where
                            <a id="error_valor_where" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                <i class="fa fa-exclamation"></i>
                            </a>
                        </label>
                        <input type="text" class="form-control" placeholder="" name="txt_valor_where" id="txt_valor_where">
                    </div>

                    <div class="row form-group">

                        <div class="col-sm-12">
                            <div class="col-sm-3">
                                <label class="control-label">Grupo
                                    <a id="error_grupo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <input type="text" class="form-control" placeholder="" name="txt_grupo" id="txt_grupo">
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label">Orden
                                    <a id="error_apellido" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="">
                                        <i class="fa fa-exclamation"></i>
                                    </a>
                                </label>
                                <input type="text" class="form-control" placeholder="" name="txt_orden" id="txt_orden">
                            </div>

                            <div class="col-sm-3">
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