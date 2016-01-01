<!-- /.modal -->
<div class="modal fade" id="horarioModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New horario</h4>
      </div>
      <div class="modal-body">
        <form id="form_horario" name="form_horario" action="" method="post">
        <div class="form-group">
            <label class="control-label">Tipo de Horario:
              <a id="error_thorario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione GRupo">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <select class="form-control" name="slct_thorario" id="slct_thorario">
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Horario
                <a id="error_horario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Horario">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control" placeholder="Ingrese Horario" name="txt_horario" id="txt_horario">
          </div>
          <div class="form-group">
            <label class="control-label">Hora de Inicio
                <a id="error_hora_inicio" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Hora de Inicio">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <div class="bootstrap-timepicker">
            <input type="text" class="form-control timepicker" placeholder="Ingrese Hora de Inicio" name="txt_hora_inicio" id="txt_hora_inicio">
            </div>
            </div>
          <div class="form-group">
            <label class="control-label">Hora de Fin
                <a id="error_hora_fin" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Hora de Fin">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <div class="bootstrap-timepicker">
            <input type="text" class="form-control timepicker" placeholder="Ingrese Hora de Fin" name="txt_hora_fin" id="txt_hora_fin">
          </div>
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
<script type="text/javascript">
            
        </script>
<!-- /.modal -->
