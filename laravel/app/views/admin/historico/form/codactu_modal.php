<style>
    #map_loc_street, #map_loc_classic,
    #codactu_modal_mapObjectMulti1,
    #codactu_modal_mapObjectMulti2,
    #codactu_modal_mapObjectMulti3 {
        width: 100%;
        height: 300px;
        margin: 0px;
        padding: 0px
      }
</style>
<!-- /.modal -->
<div class="modal fade" id="codactuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header logo">
                <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title">Código de Actuación</h4>
            </div>
            <div class="modal-body"  style="overflow: auto;height:700px;">
                <table id="t_codactu" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Cod Actu</th>
                            <th>F. Registro</th>
                            <th>Tipo</th>
                            <th>Quiebre</th>
                            <th>Empresa</th>
                            <th>Fecha Agenda</th>
                            <th>Tecnico</th>
                            <th>Estado</th>
                            <th style="width: 125px;"> [ ] </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Cod Actu</th>
                            <th>F. Registro</th>
                            <th>Tipo</th>
                            <th>Quiebre</th>
                            <th>Empresa</th>
                            <th>Fecha Agenda</th>
                            <th>Tecnico</th>
                            <th>Estado</th>
                            <th style="width: 125px;"> [ ] </th>
                        </tr>
                    </tfoot>
                </table>
                <div>
                    <button class="btn btn-primary pull-left" id="mapActuTec" style="margin-right: 5px; display: hidden">
                        <i class="fa fa-globe"></i> Mapa Actu/Inicio
                    </button> 
                    <p>&nbsp;</p>
                    <div id="codactu_modal_mapObjectMulti3"></div>
                    <button class="btn btn-primary pull-left" id="mapIniActu" style="margin-right: 5px; display: hidden">
                        <i class="fa fa-map-marker"></i> Street View Actu
                    </button>
                    <p>&nbsp;</p>
                    <div id="codactu_modal_mapObjectMulti1"></div>
                    <button class="btn btn-primary pull-left" id="mapIniTec" style="margin-right: 5px; display: hidden">
                        <i class="fa fa-check-square"></i> Street View Inicio
                    </button>
                    <p>&nbsp;</p>
                    <div id="codactu_modal_mapObjectMulti2"></div>
                </div>
                <div id="tecnicoprogramado_tareas"></div>
            </div>            
            <div class="modal-footer">
                <button type="button" id="btn_close_codactu_modal" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
