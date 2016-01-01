<!-- /.modal -->
<div class="modal fade" id="trobaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_trobas" name="form_trobas" action="" method="post">
          <div class="row form-group">
            <div class="col-sm-12">
              <div class="col-sm-4">
                <label class="control-label">Zonal:
                    <a id="error_zonal" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Zonal">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_zonal_id" id="slct_zonal_id">
                </select>
              </div>
              <div class="col-sm-4">
                <label class="control-label">Nodo:
                  <a id="error_nodo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Nodo">
                      <i class="fa fa-exclamation"></i>
                  </a>
                </label>
                <select class="form-control" name="slct_nodo_id" id="slct_nodo_id">
                </select>
              </div>
              <div class="col-sm-4">
                <label class="control-label">Troba:
                    <a id="error_troba" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Troba">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_troba_id" id="slct_troba_id">
                </select>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="col-sm-4">
                <label class="control-label">Contrata Reparto:
                    <a id="error_empresa_id" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Empresa">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_empresa_id" id="slct_empresa_id">
                </select>
              </div>
              <div class="col-sm-4">
                <label class="control-label">Contrata Zona:
                    <a id="error_contrata_zona" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Contrata Zona">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <select class="form-control" name="slct_contrata_zona" id="slct_contrata_zona">
                </select>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="col-sm-4">
                <label class="control-label">Cantidad de Clientes
                    <a id="error_can_clientes" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese cantidad de clientes">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <input type="text" class="form-control" placeholder="Ingrese cantidad de clientes" name="txt_can_clientes" id="txt_can_clientes">
              </div>
              <div class="col-sm-4">
                <label class="control-label">Digitalizacion
                    <a id="error_digitalizacion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese digitalizacion">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <input type="text" class="form-control" placeholder="Ingrese digitalizacion" name="txt_digitalizacion" id="txt_digitalizacion">
              </div>
              <div class="col-sm-4">
                  <label class="control-label">Estado:
                  </label>
                  <select class="form-control" name="slct_est_seguim" id="slct_est_seguim">
                      <option value='I'>Inactivo</option>
                      <option value='A' selected>Activo</option>
                  </select>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="col-sm-4">
                <label class="control-label">Fecha Inicio de reparto (Decos)
                    <a id="error_fecha_inicio" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese fecha de inicio">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <input type="text" class="form-control" placeholder="YYYY-MM-DD" name="txt_fecha_inicio" id="txt_fecha_inicio" onfocus="blur()">
              </div>
              <div class="col-sm-4">
                <label class="control-label">Fecha Fin de reparto (APAGON)
                    <a id="error_fecha_fin" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese fecha fin de reparto">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <input type="text" class="form-control" placeholder="YYYY-MM-DD" name="txt_fecha_fin" id="txt_fecha_fin" onfocus="blur()">
              </div>
              <!-- <div class="col-sm-4">
                <label class="control-label">Fecha de Planificacion
                    <a id="error_fecha_planificacion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese fecha de planificacion">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <input type="text" class="form-control" placeholder="YYYY-MM-DD" name="txt_fecha_planificacion" id="txt_fecha_planificacion" onfocus="blur()">
              </div> -->
            </div>
            <div class="col-sm-12">
              <div class="col-sm-12">
                  <label class="control-label">Observacion
                    <a id="error_obs" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese observacion">
                        <i class="fa fa-exclamation"></i>
                    </a>
                </label>
                <input type="text" class="form-control" placeholder="Ingrese observacion" name="txt_obs" id="txt_obs">
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