<!-- /.modal -->
<style>
    #bandeja_modal_mapObjectMulti1,
    #bandeja_modal_mapObjectMulti2,
    #bandeja_modal_mapObjectMulti3 {
        margin: 0px;
        padding: 0px
      }
</style>
<div class="modal fade" id="bandejaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">

            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs logo modal-header">
                    <li class="logo tab_0">
                        <a href="#tab_0" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-cloud fa-lg"></i> </button>
                            TOA
                        </a>
                    </li>
                    <li class="logo tab_1 active">
                        <a href="#tab_1" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-edit fa-lg"></i> </button>
                            GESTIÓN
                        </a>
                    </li>
                    <li class="logo tab_2">
                        <a href="#tab_2" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-search-plus fa-lg"></i> </button>
                            MOVIMIENTOS
                        </a>
                    </li>
                    <li class="logo tab_3">
                        <a href="#tab_3" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-list-ul fa-lg"></i> </button>
                            OBSERVACIÓN
                        </a>
                    </li>
                    <li class="logo tab_4">
                        <a href="#tab_4" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-tasks fa-lg"></i> </button>
                            DETALLE
                        </a>
                    </li>
                    <li class="logo tab_5">
                        <a href="#tab_5" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-comments fa-lg"></i> </button>
                            MENSAJE
                        </a>
                    </li>
                    <li class="logo tab_6" id="ot_resend">
                        <a href="#tab_6" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-mobile fa-lg"></i> </button>
                            OT
                        </a>
                    </li>
                    <li class="logo tab_7">
                        <a href="#tab_7" data-toggle="tab">
                            <button class="btn btn-primary btn-sm"><i class="fa fa-cloud fa-lg"></i> </button>
                            Actualizar
                        </a>
                    </li>
                    <li class="pull-right">
                        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                            <i class="fa fa-close"></i>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab_0">
                        <form id="form_bandeja_toa" name="form_bandeja_toa" action="" method="post" style="overflow: auto;height:500px;">
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Código Actuación:</label>
                                        <input type="text" class="form-control" id="txt_codactu_toa_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Quiebre:</label>
                                        <input type="text" class="form-control" id="txt_quiebre_toa_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Estado:</label>
                                        <input type="text" class="form-control" id="txt_estado_toa_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Empresa:</label>
                                        <input type="hidden" class="form-control" id="txt_empresa_toa_modal" name="txt_empresa_toa_modal" readonly>
                                        <select class="form-control" id="slct_empresa_toa_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-5">
                                        <label>Seleccione Envio:</label>
                                        <select class="form-control" id="slct_agdsla" name="slct_agdsla" onchange="SlaF(this.value);">
                                            <option value="">.::Seleccione::.</option>
                                            <option value="agenda">Agenda</option>
                                            <option value="sla">SLA</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        &nbsp;
                                    </div>
                                    <div class="col-sm-3">
                                        <br>
                                        <button type="button" id="btn_iniciar_ofsc_modal" name="btn_iniciar_ofsc_modal" class="btn btn-success">
                                            <i class='fa fa-check fa-lg'></i>Iniciar Ofsc
                                        </button>
                                        <button type="button" id="btn_completar_ofsc_modal" name="btn_completar_ofsc_modal" class="btn bg-navy">
                                            <i class='fa fa-save fa-lg'></i>Completar Ofsc
                                        </button>
                                        <br><br>
                                        <button type="button" id="btn_cancelar_ofsc_modal" name="btn_cancelar_ofsc_modal" class="btn btn-danger">
                                            <i class='fa fa-remove fa-lg'></i>Cancelar Ofsc
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-5 fecage" style="display:none">
                                        <label>Seleccione Fecha(s):</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" name="txt_fecha_agenda_toa_modal" id="txt_fecha_agenda_toa_modal"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 slaini" style="display:none">
                                        <label>Fecha Inicio SLA:</label>
                                        <input type="text" class="form-control" id="txt_slaini" onchange="CargarFechaAgenda(this.value);">
                                    </div>
                                    <div class="col-sm-3">
                                        <br>
                                        <button type="button" id="btn_obtener_capacity_modal" name="btn_obtener_capacity_modal" class="btn btn-primary">
                                            <i class='fa fa-cloud-download fa-lg'></i>Obtener Capacidad Horaria
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="box-body table-responsive">
                                        <br><br>
                                        <table id="t_capacidad" class="table table-bordered table-striped">
                                            <thead>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" id="btn_close_modal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btn_ofsc_modal" name="btn_ofsc_modal" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                    <div class="tab-pane active" id="tab_1">
                        <div id="form" style="display: none">
                            <form id="form_bandeja" name="form_bandeja" action="" method="post" style="overflow: auto;height:500px;">
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Código Actuación:</label>
                                        <input type="text" class="form-control" id="txt_codactu_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Quiebre:</label>
                                        <input type="text" class="form-control" id="txt_quiebre2_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Estado:</label>
                                        <input type="text" class="form-control" id="txt_estado_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Empresa:</label>
                                        <input type="hidden" class="form-control" id="txt_empresa_modal" name="txt_empresa_modal" readonly>
                                        <select class="form-control" id="slct_empresa_modal" data-evento="1">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Motivo:</label>
                                        <select class="form-control" id="slct_motivo_modal" name="slct_motivo_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Submotivo:</label>
                                        <select class="form-control" id="slct_submotivo_modal" name="slct_submotivo_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Estado:</label>
                                        <select class="form-control" id="slct_estado_modal" name="slct_estado_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="H0 col-sm-3">
                                        <label>Tipo Horario:</label>
                                        <select class="form-control" id="slct_horario_tipo_modal" name="slct_horario_tipo_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                </div>
                                <!--div class="col-sm-12 H0">
                                  <div class="col-sm-4">
                                  &nbsp;
                                  </div>
                                  <div class="col-sm-4">
                                  <label>Tipo Horario:</label>
                                  <select class="form-control" id="slct_horario_tipo_modal" name="slct_horario_tipo_modal">
                                      <option value="">.::Seleccione::.</option>
                                  </select>
                                  </div>
                                  <div class="col-sm-4">
                                  &nbsp;
                                  </div>
                                </div-->
                                <div class="col-sm-12">
                                    <div class="col-sm-4">
                                        <label>Coordino con Cliente:</label>
                                        <select class="form-control" id="slct_coordinado2_modal" name="slct_coordinado2_modal">
                                            <option value="">.::Seleccione::.</option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-8">
                                        <label>Observación:</label>
                                        <textarea rows="3" class="form-control" id="txt_observacion2_modal" name="txt_observacion2_modal"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 L0">
                                    <h3>Liquidados:</h3>
                                    <b>Fecha Agenda:</b> <span id="span_fecha_agenda"></span>
                                    <label>Cumplió Agenda?:
                                    |   <select id="slct_cumplimiento_modal" name="slct_cumplimiento_modal">
                                            <option value="">--Seleccione--</option>
                                            <option value="1">Si cumple</option>
                                            <option value="0">No cumple</option>
                                            <option value="2">Sin responsabilidad del Técnico</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="col-sm-12 L0">
                                    <div class="col-sm-4">
                                        <label>Contacto:</label>
                                        <select class="form-control L1" id="slct_contacto_modal" name="slct_contacto_modal">
                                            <option value="">.::Seleccione::.</option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Pruebas:</label>
                                        <select class="form-control L1" id="slct_pruebas_modal" name="slct_pruebas_modal">
                                            <option value="">.::Seleccione::.</option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Fecha Consolidación:</label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right L1" name="fecha_consolidacion" id="fecha_consolidacion"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 L0">
                                    <div class="col-sm-4">
                                        <label>Feedback:</label>
                                        <select class="form-control L1" id="slct_feedback_modal" name="slct_feedback_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Solución:</label>
                                        <select class="form-control L1" id="slct_solucion_modal" name="slct_solucion_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Penalizable: <input type="checkbox" class="L1" id="chk_penalizable_modal" name="chk_penalizable_modal" value="1"></label>
                                        <textarea rows="2" class="form-control L1" id="txt_penalizable_obs_modal" name="txt_penalizable_obs_modal"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 T0">
                                    <div class="col-sm-4">
                                        <label>Celula:</label>
                                        <select class="form-control T1" id="slct_celula_modal" name="slct_celula_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Tecnico:</label><label class="pull-right">Tec. Entregado:<input type="checkbox" class="T1" id="chk_flag_tecnico_modal" name="chk_flag_tecnico_modal" value="1"></label>
                                        <select class="form-control T1" id="slct_tecnico_modal" name="slct_tecnico_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Officetrack:</label>
                                        <input type="text" class="form-control T1" id="txt_officetrack_modal" name="txt_officetrack_modal" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-12 C0">
                                    <div id="htmlcomponente" class="col-sm-5">
                                        <div ></div>
                                    </div>
                                    <div id="htmlselectcomponente" class="col-sm-5">
                                        <label>Componentes:</label>
                                        <select class="form-control" id="slct_componente_modal">
                                            <option value="">.::Seleccione::.</option>
                                        </select>
                                        &nbsp;&nbsp;
                                        <button type="button" onclick="adicionarComponente()" class="btn btn-success btn-sm">
                                            <i class="fa fa-plus fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-12 H0">
                                    <div class="col-sm-12">
                                        <label><h3>Horario</h3></label>
                                        <div id="html" class="box-body table-responsive"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12 H0">
                                    <button type="button" onclick="VerMapa();" class="btn btn-default">
                                        <span>Ver mapa</span><i class="caret"></i>
                                    </button>
                                </div>
                                <div class="col-sm-12 map">
                                    <div class="col-sm-3">
                                        <label><b>X:</b></label>
                                        <input type="text" class="form-control" id="txt_x_modal" name="txt_x_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label><b>Y:</b></label>
                                        <input type="text" class="form-control" id="txt_y_modal" name="txt_y_modal" readonly>
                                    </div>
                                    <div class="col-sm-4">
                                        <label><b>Dirección:</b></label>
                                        <textarea class="form-control" id="txt_direccion_instalacion2_modal" rows="2" readonly>
                                        </textarea>
                                    </div>
                                    <div class="col-sm-2">
                                        <label><b>&nbsp;</b></label>
                                        <input type="button" id="btn_savexy_modal" name="btn_savexy_modal" value="Actualizar XY" class="form-control">
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="map_canvas" style="width: 100%; height: 280px; text-align: center; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="street_canvas" style="width: 100%; height: 280px; text-align: center; position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                        <div id="mapas">
                            <br>
                            <div id="bandeja_modal_mapObjectMulti3"></div>
                            <br>
                            <div id="bandeja_modal_mapObjectMulti1"></div>
                            <br>
                            <div id="bandeja_modal_mapObjectMulti2"></div>
                            <div id="bandeja_tareas"></div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-8" style="text-align: left;">
                                    <button class="btn btn-primary pull-left" id="ubicacion" style="margin-right: 5px; display: hidden"><i class="fa fa-map-marker"></i> Ubicación</button>
                                </div>
                                <div class="col-xs-6 col-md-4">
                                    <button type="button" id="btn_close_modal" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" id="btn_gestion_modal" name="btn_gestion_modal" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                            <!-- <button type="button" id="btn_close_modal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btn_obsevacion_modal" name="btn_obsevacion_modal" class="btn btn-primary">Guardar</button> -->
                        </div>

                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">

                        <div class="row form-group" style="overflow: auto;height:500px;">
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    <label>Código Actuación:</label>
                                    <input type="text" class="form-control" id="txt_codactu_m_modal" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label>Actividad:</label>
                                    <input type="text" class="form-control" id="txt_actividad_m_modal" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label>Quiebre:</label>
                                    <input type="text" class="form-control" id="txt_quiebre_m_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table id="t_movimiento" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>F. Movimiento</th>
                                            <th>Empresa</th>
                                            <th>Cliente</th>
                                            <th>Fecha Agenda</th>
                                            <th>Celula</th>
                                            <th>Tecnico</th>
                                            <th>Estado</th>
                                            <th>Usuario</th>
                                            <th> Observación </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb_movimiento">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>F. Movimiento</th>
                                            <th>Empresa</th>
                                            <th>Cliente</th>
                                            <th>Fecha Agenda</th>
                                            <th>Celula</th>
                                            <th>Tecnico</th>
                                            <th>Estado</th>
                                            <th>Usuario</th>
                                            <th> Observación </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_close_modal" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>

                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">

                        <form id="form_observacion" name="form_observacion" action="" method="post" style="overflow: auto;height:500px;">
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <label>Código Actuación:</label>
                                        <input type="text" class="form-control" id="txt_codactu_o_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Actividad:</label>
                                        <input type="text" class="form-control" id="txt_actividad_o_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Quiebre:</label>
                                        <input type="text" class="form-control" id="txt_quiebre_o_modal" readonly>
                                    </div>
                                    <div class="col-sm-3">
                                    <label>Tipo Observaci&oacute;n</label>
                                    <select class="form-control" id="slct_obs_tipo" name="slct_obs_tipo">
                                        <option value="1">Contestó</option>
                                        <option value="2">No Contestó</option>
                                        <option value="3">Visita</option>
                                        <option value="4">Otros</option>
                                        <option value="5">No cumple Agenda</option>
                                        <option value="6">Cumple Agenda</option>
                                        <option value="7">Falta datos</option>
                                    </select>
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
                        <div class="modal-footer">
                            <button type="button" id="btn_close_modal" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btn_obsevacion_modal" name="btn_obsevacion_modal" class="btn btn-primary">Guardar</button>
                        </div>

                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4">

                        <div class="row form-group" style="overflow: auto;height:500px;">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Tipo avería</label>
                                    <input type="text" class="form-control" id="txt_d_tipo_averia_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Horas avería</label>
                                    <input type="text" class="form-control" id="txt_d_horas_averia_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Fecha reporte</label>
                                    <input type="text" class="form-control" id="txt_d_fecha_reporte" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Fecha registro</label>
                                    <input type="text" class="form-control" id="txt_d_fecha_registro_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Ciudad</label>
                                    <input type="text" class="form-control" id="txt_d_ciudad_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Avería</label>
                                    <input type="text" class="form-control" id="txt_d_codactu_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>MDF</label>
                                    <input type="text" class="form-control" id="txt_d_mdf_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Segmento</label>
                                    <input type="text" class="form-control" id="txt_d_segmento_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Inscrición</label>
                                    <input type="text" class="form-control" id="txt_d_inscripcion_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Fono 1</label>
                                    <input type="text" class="form-control" id="txt_d_fono1_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Teléfono</label>
                                    <input type="text" class="form-control" id="txt_d_telefono_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Área</label>
                                    <input type="text" class="form-control" id="txt_d_area_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Cliente</label>
                                    <input type="text" class="form-control" id="txt_d_nombre_cliente_modal" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <label>Dirección</label>
                                    <input type="text" class="form-control" id="txt_d_direccion_instalacion_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>WU última agenda</label>
                                    <input type="text" class="form-control" id="txt_d_wu_fecha_ult_agenda_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Cod. Distrito</label>
                                    <input type="text" class="form-control" id="txt_d_codigo_distrito_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Ord. Trabajo</label>
                                    <input type="text" class="form-control" id="txt_d_orden_trabajo_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Vel. ADSL</label>
                                    <input type="text" class="form-control" id="txt_d_veloc_adsl_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Zonal</label>
                                    <input type="text" class="form-control" id="txt_d_zonal_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Clase_Serv_Catv</label>
                                    <input type="text" class="form-control" id="txt_d_clase_servicio_catv_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Codmotivo_req_catv:</label>
                                    <input type="text" class="form-control" id="txt_d_codmotivo_req_catv_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Llave</label>
                                    <input type="text" class="form-control" id="txt_d_llave_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Quiebre</label>
                                    <input type="text" class="form-control" id="txt_d_quiebre_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Tot. Averías cable</label>
                                    <input type="text" class="form-control" id="txt_d_total_averias_cable_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Tot. Averías cobre</label>
                                    <input type="text" class="form-control" id="txt_d_total_averias_cobre_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Total averías</label>
                                    <input type="text" class="form-control" id="txt_d_total_averias_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Distrito</label>
                                    <input type="text" class="form-control" id="txt_d_distrito_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Fonos Contacto</label>
                                    <input type="text" class="form-control" id="txt_d_fonos_contacto_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Contrata</label>
                                    <input type="text" class="form-control" id="txt_d_contrata_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Lejano</label>
                                    <input type="text" class="form-control" id="txt_d_lejano_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>EEECC zona</label>
                                    <input type="text" class="form-control" id="txt_d_eecc_zona_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    <label>FFTT</label>
                                    <input type="text" class="form-control" id="txt_d_fftt_modal" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label>Dir.Terminal</label>
                                    <input type="text" class="form-control" id="txt_d_dir_terminal_modal" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label>Paquete</label>
                                    <input type="text" class="form-control" id="txt_d_paquete_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Zona Movistar 1</label>
                                    <input type="text" class="form-control" id="txt_d_zona_movistar_uno_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Data Multiproducto</label>
                                    <input type="text" class="form-control" id="txt_d_data_multiproducto_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Averia m1</label>
                                    <input type="text" class="form-control" id="txt_d_averia_m1_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Fec Data Fuente</label>
                                    <input type="text" class="form-control" id="txt_d_fecha_data_fuente_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Tel. Codclientecms</label>
                                    <input type="text" class="form-control" id="txt_d_telefono_codclientecms_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Rango de Días</label>
                                    <input type="text" class="form-control" id="txt_d_rango_dias_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Area2</label>
                                    <input type="text" class="form-control" id="txt_d_area2_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Tot_Llam_Tecnicas</label>
                                    <input type="text" class="form-control" id="txt_d_total_llamadas_tecnicas_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Tot_Llam_Seguimie</label>
                                    <input type="text" class="form-control" id="txt_d_total_llamadas_seguimiento_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Estado Legado</label>
                                    <input type="text" class="form-control" id="txt_d_estado_legado_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Fecha Liq. Legado</label>
                                    <input type="text" class="form-control" id="txt_d_fec_liq_legado_modal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Contrata Legado</label>
                                    <input type="text" class="form-control" id="txt_d_contrata_legado_modal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    SMS1
                                </div>
                                <div class="col-sm-4">
                                    SMS2
                                </div>
                                <div class="col-sm-4">OBS_102</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-4">
                                    <textarea class="form-control" rows="3" id="txt_d_sms1_modal" readonly></textarea>
                                </div>
                                <div class="col-sm-4">
                                    <textarea class="form-control" rows="3" id="txt_d_sms2_modal" readonly></textarea>
                                </div>
                                <div class="col-sm-4">
                                    <textarea class="form-control" rows="3" id="txt_d_observacion_modal" readonly></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tab_5">

                        <div class="row form-group" style="overflow: auto;height:500px;">
                            <div class="col-sm-12">
                                <div class="col-sm-2">
                                    <button class="btn btn-success btn-sm"><i class="fa fa-phone fa-lg"></i></button>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="txt_celular_modal"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <button class="btn btn-success btn-sm"><i class="fa fa-comments fa-lg">SMS1</i></button>
                                </div>
                                <div class="col-sm-4">
                                    <textarea class="form-control" rows="4" id="txt_m_sms1_modal"></textarea>
                                </div>
                                <button type="button" id="btn_mensaje1_modal" onclick="envioMensaje(1);" class="btn btn-primary">Envio SMS1</button>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <button class="btn btn-success btn-sm"><i class="fa fa-comments fa-lg">SMS2</i></button>
                                </div>
                                <div class="col-sm-4">
                                    <textarea class="form-control" rows="4" id="txt_m_sms2_modal"></textarea>
                                </div>
                                <button type="button" id="btn_mensaje2_modal" onclick="envioMensaje(2);" class="btn btn-primary">Envio SMS2</button>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tab_6">
                        <h3>Reenvío a OfficeTrack</h3>
                        <form name="rot_form" id="rot_form" method="post" action="" style="overflow: auto;height:500px;">
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Fecha/hora agenda</label>
                                    <input type="text" class="form-control" name="rot_fh_agenda" id="rot_fh_agenda" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Código actuación</label>
                                    <input type="text" class="form-control" name="rot_codactu" id="rot_codactu" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Fecha registro</label>
                                    <input type="text" class="form-control" name="rot_fecha_registro" id="rot_fecha_registro" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Cliente</label>
                                    <input type="text" class="form-control" name="rot_nombre_cliente" id="rot_nombre_cliente" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Actividad/Req.CATV</label>
                                    <input type="text" class="form-control" name="rot_act_codmotivo_req_catv" id="rot_act_codmotivo_req_catv" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Orden</label>
                                    <input type="text" class="form-control" name="rot_orden_trabajo" id="rot_orden_trabajo" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>FFTT</label>
                                    <input type="text" class="form-control" name="rot_fftt" id="rot_fftt" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Dir. Terminal</label>
                                    <input type="text" class="form-control" name="rot_dir_terminal" id="rot_dir_terminal" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Inscripción</label>
                                    <input type="text" class="form-control" name="rot_inscripcion" id="rot_inscripcion" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>MDF</label>
                                    <input type="text" class="form-control" name="rot_mdf" id="rot_mdf" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Segmento</label>
                                    <input type="text" class="form-control" name="rot_segmento" id="rot_segmento" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Clase Serv. CATV</label>
                                    <input type="text" class="form-control" name="rot_clase_servicio_catv" id="rot_clase_servicio_catv" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Total averías</label>
                                    <input type="text" class="form-control" name="rot_total_averias" id="rot_total_averias" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Zonal</label>
                                    <input type="text" class="form-control" name="rot_zonal" id="rot_zonal" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Llamadas tec. 15 días</label>
                                    <input type="text" class="form-control" name="rot_llamadastec15dias" id="rot_llamadastec15dias" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Quiebre</label>
                                    <input type="text" class="form-control" name="rot_quiebre" id="rot_quiebre" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Lejano</label>
                                    <input type="text" class="form-control" name="rot_lejano" id="rot_lejano" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Distrito</label>
                                    <input type="text" class="form-control" name="rot_distrito" id="rot_distrito" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Averías M1</label>
                                    <input type="text" class="form-control" name="rot_averia_m1" id="rot_averia_m1" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Telf. Cod.Cli.CMS</label>
                                    <input type="text" class="form-control" name="rot_telefono_codclientecms" id="rot_telefono_codclientecms" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Area:</label>
                                    <input type="text" class="form-control" name="rot_area2" id="rot_area2" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>EECC</label>
                                    <input type="text" class="form-control" name="rot_eecc_final" id="rot_eecc_final" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Tarea</label>
                                    <input type="text" class="form-control" name="rot_gestion_id" id="rot_gestion_id" readonly>
                                </div>
                                <div class="col-sm-3">
                                    <label>Estado</label>
                                    <input type="text" class="form-control" name="rot_estado" id="rot_estado" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label>Velocidad</label>
                                    <input type="text" class="form-control" name="rot_velocidad" id="rot_velocidad" readonly>
                                </div>
                                <div class="col-sm-9">
                                    <label>Observación</label>
                                    <textarea class="form-control" name="rot_cr_observacion" id="rot_cr_observacion" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <input type="button" name="rot_reenviar" id="rot_reenviar" style="width: 100%" class="btn btn-primary" value="Reenviar a Office Track" onclick="reenviarOfficeTrack()">
                                </div>
                            </div>
                        </div>
                            <input type="hidden" class="form-control" name="rot_tecnico_id" id="rot_tecnico_id" >
                            <input type="hidden" class="form-control" name="rot_coordinado2" id="rot_coordinado2" >
                        </form>
                    </div>
                    <div class="tab-pane" id="tab_7">
                        <h3>Actualizaci&oacute;n de datos</h3>
                        <form id="form_update_ofsc" name="form_update_ofsc" action="" method="post" style="overflow: auto;height:400px;">
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="col-sm-4">
                                        <label>Código Actuación:</label>
                                        <input type="text" class="form-control" id="txt_codactu_update_toa_modal" readonly>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Quiebre:</label>
                                        <input type="text" class="form-control" id="txt_quiebre_update_toa_modal" readonly>
                                    </div>
                                    <div class="col-sm-4">
                                        <label>Estado:</label>
                                        <input type="text" class="form-control" id="txt_estado_update_toa_modal" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-5">
                                        <label>Dirección:</label>
                                        <textarea class="form-control" id="txt_direccion_update_toa_modal" name="txt_direccion_update_toa_modal"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" id="btn_update_ofsc_modal" name="btn_update_ofsc_modal" class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </div><!-- /.tab-content -->
            </div><!-- nav-tabs-custom -->
        </div>
    </div>
</div>
<!-- /.modal -->
