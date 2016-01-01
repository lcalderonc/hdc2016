<!-- /.modal -->
        <div class="modal fade" id="observacionModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg ">
            <div class="modal-content">
              <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">Observación</h4>
              </div>
              <div class="modal-body">
                <form id="form_observacion" name="form_observacion" action="" method="post">
                  <div class="row form-group">                                    
                    <div class="col-sm-12">
                      <div class="col-sm-4">
                      <label>Código Actuación:</label>
                        <input type="text" class="form-control" id="txt_codactu_o_modal" readonly> 
                      </div>
                      <div class="col-sm-4">
                      <label>Actividad:</label>
                        <input type="text" class="form-control" id="txt_actividad_o_modal" readonly>
                      </div>
                      <div class="col-sm-4">
                      <label>Quiebre:</label>
                        <input type="text" class="form-control" id="txt_quiebre_o_modal" readonly>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="col-sm-8">
                        <label>Observación:</label>
                        <textarea rows="3" class="form-control" id="txt_observacion_o_modal" name="txt_observacion_o_modal"></textarea>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" id="btn_close_modal" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="btn_obsevacion_modal" name="btn_obsevacion_modal" class="btn btn-primary">Guardar</button>
              </div>
            </div>
          </div>
        </div>
        <!-- /.modal -->
