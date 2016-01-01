<div id="parentModal" style="display: none;">
    <div id="childModal" style="background: #fff;"></div>
    <div id="childModal_nuevo" style="background: #fff;"></div>
</div>

<script id="tarea-tr" type="text/x-handlebars-template">
    <tr key="{{carnet}}" class="row-tec  tec main-{{carnet}}">
        <td>{{carnet}}</td>
        <td colspan="2">{{tec_nombre}}</td>
        <td pendiente="0001-Inicio"         key="{{carnet}}" class="detalle pen-0001-Inicio">       {{cant_paso1}}</td>
        <td pendiente="0002-Supervision"    key="{{carnet}}" class="detalle pen-0002-Supervision">  {{cant_paso2}}</td>
        <td pendiente="0003-Cierre"         key="{{carnet}}" class="detalle pen-0003-Cierre">       {{cant_paso3}}</td>
        <td class="estado-officetrack" grupo="g-atendido">{{cant_atendido}}</td>
        <td class="estado-officetrack"  grupo="g-en-proceso">{{cant_enproceso}}</td>
        <td  class="estado-officetrack" grupo="g-inefectiva">{{cant_inefectiva}}</td>
        <td  class="estado-officetrack" grupo="g-no-deja">{{cant_nodeja}}</td>
        <td  class="estado-officetrack" grupo="g-no-desea">{{cant_nodesea}}</td>
        <td  class="estado-officetrack" grupo="g-no-ubica">{{cant_noubica}}</td>
        <td class="estado-officetrack"  grupo="g-otros">{{cant_otros}}</td>
        <td  class="estado-officetrack" grupo="g-transferidos">{{cant_transferidos}}</td>
    </tr>
</script>

<script id="tarea-tr-detalle" type="text/x-handlebars-template">
    <tr  style='display:none' tarea="{{task_id}}" class="g-{{StringToClass estado}} row-tec tareas  sub-{{carnet}} pen-{{grupo}} pendiente-{{grupo}} g-{{grupo}}">
        <td>{{atc}} </td>
        <td>{{grupo}} </td>
        <td colspan="4" >Recepcion: {{recepcion}}</td>
        <td colspan="3">{{agenda}}</td>
        <td colspan="2">{{estado}}</td>
        <td colspan="4">{{obs}}</td>
    </tr>
</script>

<script id="tareaDetalleVs" type="text/x-handlebars-template">
    /*<tr id="main-{{carnet}}" class="row-tec  tec " carnet="{{carnet}}">
        <td>{{carnet}}</td>
        <td>{{tec_nombre}}</td>
        <td class="AccionGrupo pendiente" key="g-pasado">{{pasado}}</td>
        <td class="AccionGrupo pendiente" key="g-hoy">{{hoy}}</td>
        <td class="AccionGrupo pendiente" key="g-futuro">{{futuro}}</td>

        <td class="AccionGrupo" key="g-p1" class="">  {{cant_paso1}}</td>
        <td class="AccionGrupo" key="g-p2" class="">  {{cant_paso2}}</td>
        <td class="AccionGrupo" key="g-p3" class="">  {{cant_paso3}}</td>

        <td class="AccionGrupo"   key="g-atendido">{{cant_atendido}}</td>
        <td  class="AccionGrupo"  key="g-en-proceso">{{cant_enproceso}}</td>
        <td  class="AccionGrupo" key="g-inefectiva">{{cant_inefectiva}}</td>
        <td  class="AccionGrupo" key="g-no-deja">{{cant_nodeja}}</td>
        <td class="AccionGrupo"  key="g-no-desea">{{cant_nodesea}}</td>
        <td  class="AccionGrupo" key="g-no-ubica">{{cant_noubica}}</td>
        <td class="AccionGrupo"   key="g-otros">{{cant_otros}}</td>
        <td class="AccionGrupo"  key="g-transferidos">{{cant_transferidos}}</td>
    </tr>
    <tr id="main-detalle-{{carnet}}" class="row-tec" style="display: none;">
        <td colspan="20">
            <table id="detalleGrupo" width="100%">
                <tr  class=" cabecera-detalle-grupo row-tec ">
                    <th>atc</th>
                    <th>cod_req</th>
                    <th>estado_webpsi</th>
                    <th >paso</th>
                    <th >estado_ot</th>
                    <th>FechaAgenda</th>
                    <th>FechaOfficetrack</th>
                    <th>observacion</th>
                </tr>
            </table>
        </td>
    </tr>*/
</script>

<script id="detalleGrupo" type="text/x-handlebars-template">
    /*<tr class=" detalle-grupo row-tec  tec detalle-{{carnet}} ">
        <td>{{atc}}</td>
        <td>{{cod_req}}</td>
        <td>{{estado_webpsi}}</td>
        <td >{{paso}}</td>
        <td >{{estado_ot}}</td>
        <td>{{FechaAgenda}}</td>
        <td>{{FechaOfficetrack}}</td>
        <td>{{observacion}}</td>
    </tr>*/
</script>