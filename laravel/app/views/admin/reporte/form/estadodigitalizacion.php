<!-- /.modal -->
<div class="modal fade" id="estadosDigitalizacionModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="nav-tabs-custom">
          <ul class="nav nav-tabs logo modal-header" id="tap_digitalizacion_modal">
              <li class="logo active">
                  <a href="#tab_1" data-toggle="tab">
                      <button class="btn btn-primary btn-sm"><i class="fa fa-edit fa-lg"></i> </button>
                      GESTIÓN
                  </a>
              </li>
              <li class="logo">
                  <a href="#tab_2" data-toggle="tab">
                      <button class="btn btn-primary btn-sm"><i class="fa fa-search-plus fa-lg"></i> </button>
                      MOVIMIENTOS
                  </a>
              </li>
              <li class="pull-right">
                  <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                      <i class="fa fa-close"></i>
                  </button>
              </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
              <div class="row form-group">
                <div class="col-sm-12">
                  <form id="form_digitalizacion" name="form_digitalizacion" action="" method="post">
                    <div class="form-group">
                      <label class="control-label">Motivo:
                      </label>
                      <select class="form-control" name="slct_ed_motivo_id" id="slct_ed_motivo_id">
                      </select>
                    </div>
                    <div class="form-group">
                      <label class="control-label">Observación
                          <a id="error_observacion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Observación">
                              <i class="fa fa-exclamation"></i>
                          </a>
                      </label>
                      <input type="text" class="form-control" placeholder="Ingrese Observación" name="txt_observacion" id="txt_observacion">
                    </div>
                  </form>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
              </div>
            </div><!-- /.tab-pane -->

            <div class="tab-pane" id="tab_2">
              <div class="row form-group">
                <div class="col-sm-12">
                  <table id="t_gestiones" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                          <th>ID</th>
                          <th>Motivo</th>
                          <th>F. Movimiento</th>
                          <th> Observación </th>
                          <th>Usuario</th>
                      </tr>
                    </thead>
                    <tbody id="tb_gestiones">
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>ID</th>
                        <th>Motivo</th>
                        <th>F. Movimiento</th>
                        <th> Observación </th>
                        <th>Usuario</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div><!-- /.tab-content -->
      </div><!-- nav-tabs-custom -->
      
    </div>
  </div>
</div>
<!-- /.modal -->