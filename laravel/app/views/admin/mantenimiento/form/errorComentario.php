<div class="modal fade" id="errorComentarioModal" tabindex="-1" role="dialog" aria-labelledby="errorComentarioModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="errorComentarioModalLabel"><?php echo trans('mantenimiento.escribir_solucion') ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <!--
                    <label class="control-label"><?php //echo trans('mantenimiento.comentario') ?></label>
                    -->
                    <textarea class="form-control resize-none" id="txt_comentario" rows="10" required=""></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnErrorDiligencia" type="button" class="btn btn-danger" data-dismiss="modal"><?php echo trans('mantenimiento.volver_diligencia') ?></button>
                <button id="btnErrorComentario" type="button" class="btn btn-info" data-dismiss="modal"><?php echo trans('mantenimiento.guardar') ?></button>
            </div>
        </div>
    </div>
</div>