<!-- /.modal -->
<div class="modal fade" id="permisoeventosModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">Editar Permisos a Eventos</h4>
      </div>
      <div class="modal-body">
        <form id="form_permisoeventos" name="form_permisoeventos" action="" method="post">
          <div class="form-group">
            <label class="control-label">Nombre
                <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control" name="txt_nombre" id="txt_nombre">
          </div>
          <div class="form-group">
            <label class="control-label">Consulta:
            </label>
            <select class="form-control" multiple="multiple" name="slct_consulta[]" id="slct_consulta">
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Metodo:
            </label>
            <select class="form-control" multiple="multiple" name="slct_metodo[]" id="slct_metodo">
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary nuevo">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->