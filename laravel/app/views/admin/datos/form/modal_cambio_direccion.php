<!-- /.modal -->
    <style>
      #map, #pano {
        width: 100%;
        height: 350px;
      }
    </style>
<div class="modal fade" id="cambioDireccionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">Validar cambio de dirección</h4>
            </div>
            <div class="modal-body">
                <form id="form_cambiodir" name="form_cambiodir" action="" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Datos del cambio</legend>
                        <div class="row form-group">

                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <label class="control-label">Cliente
                                        <a id="error_nombrecliente" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre cliente">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Ingrese Nombre cliente" name="txt_nombrecliente" id="txt_nombrecliente" readonly>
                                </div>
                                <div class="col-sm-12">
                                    <label class="control-label">Dirección
                                        <a id="error_direccion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese dirección">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Ingrese direccion" name="txt_direccion" id="txt_direccion">
                                </div>
                                <div class="col-sm-12">
                                    <label class="control-label">Referencia
                                        <a id="error_referencia" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese referencia">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Ingrese referencia" name="txt_referencia" id="txt_referencia">
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label">Latitud (Y)
                                        <a id="error_latitud" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese latitud">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Ingrese latitud" name="txt_latitud" id="txt_latitud" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label">Longitud (X)
                                        <a id="error_longitud" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese longitud">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Ingrese longitud" name="txt_longitud" id="txt_longitud" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label class="control-label">Validar
                                        <a id="error_validacion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione opción">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <select name="slct_validacion" id="slct_validacion" class="form-control">
                                        <option value="0">Registrado</option>
                                        <option value="1">Aprobado</option>
                                        <option value="2">Observado</option>
                                    </select>
                                </div>

                                <div class="col-sm-12">
                                    <label class="control-label">Observación
                                        <a id="error_observacion" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese observacion">
                                            <i class="fa fa-exclamation"></i>
                                        </a>
                                    </label>
                                    <textarea class="form-control" rows="3" name="txt_observacion" id="txt_observacion"></textarea>
                                </div>
                                <div class="col-sm-12">
                                    <label class="control-label">Mapa</label>
                                    <div id="mapa_cambio"></div>
                                </div>

                                <div class="col-sm-6">
                                    <div id="map"></div>
                                </div>
                                <div class="col-sm-6">
                                    <div id="pano"></div>
                                </div>
                            </div>
                        </div>
                    </fieldset>



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