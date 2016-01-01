<!-- /.modal -->
<div class="modal fade" id="estadomotivosubmotivoModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_estadomotivosubmotivos" name="form_estadomotivosubmotivos" action="" method="post">

          <div class="row form-group">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <label class="control-label">Motivo:
                </label>
                <select class="form-control" name="slct_motivo" id="slct_motivo">
                </select>
              </div>
              <div class="col-sm-6">
                <label class="control-label">Submotivo:
                  <a id="error_horario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Horario">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_submotivo" id="slct_submotivo">
                </select>
              </div>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <label class="control-label">Estados:
                  <a id="error_horario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Horario">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_estados" id="slct_estados">
                </select>
              </div>
              <div class="col-sm-6">
              <label class="control-label">Descripción de la acción:
                  <a id="error_horario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Horario">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_descripcion" id="slct_descripcion">
                    <option value="1">Agendamiento con técnico</option>
                    <option value="2">Agendamiento sin técnico</option>
                    <option value="3">Asignación automática</option>
                    <option value="9">Inhabilitar gestión</option>
                    <option value="0">Sin asignación</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-sm-12">
              <div class="col-sm-6">
                <label class="control-label">Estado:
                </label>
                <select class="form-control" name="slct_estado4" id="slct_estado4">
                    <option value='0'>Inactivo</option>
                    <option value='1' selected>Activo</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->