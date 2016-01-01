<script type="text/javascript">
$(document).ready(function() {
    //estados pre seleccionados
    var ids = [2,3,7,8,13];
    var data = {usuario: 1};
    slctGlobal.listarSlct('empresa','slct_empresa','simple',[],data,0,'#slct_celula','E');
    slctGlobal.listarSlct('celula','slct_celula', 'simple', [],0,   1);
    slctGlobal.listarSlct('estado', 'slct_estado','multiple',ids);
    EstadosOT.ObtenerTecnicosOT();
    $('#fecha_agendamiento').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: true
    });
    $('#fecha_recepcion_ini').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: true
    });
    $('#fecha_recepcion_fin').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: true
    });
    $("#mostrar").click(function(){
        empresa = $("#slct_empresa").val();
        celula = $("#slct_celula").val();
        estados = $("#slct_estado").val();
        fecha_agen = $("#fecha_agendamiento").val();
        recepcion_ini = $("#fecha_recepcion_ini").val();
        recepcion_fin = $("#fecha_recepcion_fin").val();
        if ( validar('Empresa', empresa) && 
             validar('Celula', celula) && 
             validar('Estados', estados) && 
             validar('Fecha de agendamiento', fecha_agen) && 
             validar('Fecha de inicio de recepcion', recepcion_ini) && 
             validar('Fecha de fin de recepcion', recepcion_fin) )
        {
            var tec = _.where(objTecnicosOT, {celula_id: Number(celula)});
            var carnets = _.map(tec , function(item){ return item.carnet; }).join("','");
            EstadosOT.ListarTecnicosOT(empresa,celula,recepcion_ini,recepcion_fin);
            EstadosOT.Pendientes(empresa,celula,estados,fecha_agen,carnets);

        }
    });

    $("#reiniciarFiltros").click(function(){
        $("#slct_empresa").multiselect('deselectAll', false);
        $("#slct_empresa").multiselect('rebuild');
        $("#slct_empresa").multiselect('refresh');
        $("#slct_celula").multiselect('deselectAll', false);
        $("#slct_celula").multiselect('rebuild');
        $("#slct_celula").multiselect('refresh');
        //$("#slct_empresa").val("");
        //$("#slct_empresa").trigger("change");
        $("#fecha_recepcion_ini").val("");
        $("#fecha_recepcion_fin").val("");
        $("#fecha_agendamiento").val("");
    });

    $("#ExportExcelOfficetrack").click(function(){
        empresa = $("#slct_empresa").val();
        celula = $("#slct_celula").val();
        recepcion_ini = $("#fecha_recepcion_ini").val();
        recepcion_fin = $("#fecha_recepcion_fin").val();

        if ( validar('Empresa', empresa) && 
             validar('Celula', celula) && 
             validar('Fecha de inicio de recepcion', recepcion_ini) && 
             validar('Fecha de fin de recepcion', recepcion_fin) )
        {
            $("input[type='hidden']").remove();
            $("#form_reporte").append("<input type='hidden' value='tecnicosot' name='accion'>");
            $("#form_reporte").append("<input type='hidden' value='"+empresa+"' name='empresaId'>");
            $("#form_reporte").append("<input type='hidden' value='"+celula+"' name='celulaId'>");
            $("#form_reporte").append("<input type='hidden' value='"+recepcion_ini+"' name='fechaIni'>");
            $("#form_reporte").append("<input type='hidden' value='"+recepcion_fin+"' name='fechaFin'>");
            $("#form_reporte").append("<input type='hidden' value='1' name='excel'>");
            $("#form_reporte").submit();
        }
    });

    $("#ExportExcelPendientes").click(function(){
        empresa = $("#slct_empresa").val();
        celula = $("#slct_celula").val();
        estados = $("#slct_estado").val();
        fecha_agen = $("#fecha_agendamiento").val();

        if ( validar('Empresa', empresa) && 
             validar('Celula', celula) && 
             validar('Fecha de agendamiento', fecha_agen) &&
             validar('Estados', estados) )
        {
            var tec = _.where(objTecnicosOT, {celula_id: Number(celula)});
            var carnets = _.map(tec , function(item){ return item.carnet; }).join("','");

            $("input[type='hidden']").remove();
            $("#form_reporte").append("<input type='hidden' value='pendientes' name='accion'>");
            $("#form_reporte").append("<input type='hidden' value='"+empresa+"' name='empresaId'>");
            $("#form_reporte").append("<input type='hidden' value='"+celula+"' name='celulaId'>");
            $("#form_reporte").append("<input type='hidden' value='"+estados+"' name='estados[]'>");
            $("#form_reporte").append("<input type='hidden' value='"+fecha_agen+"' name='fecha_agen'>");
            $("#form_reporte").append("<input type='hidden' value='"+carnets+"' name='carnets'>");
            $("#form_reporte").append("<input type='hidden' value='1' name='excel'>");
            $("#form_reporte").submit();
        }
    });
    
});
mostrarTabla=function(data){
    $(tablaOfficetrack + " .row-tec").remove();
    var tecnicos = _.groupBy(data,"cod_tecnico");
    var tr = "";
    //MOSTRAMOS LISTADO DE TECNICOS
    _.each(tecnicos,function(tareas, key, list){
        //                  valor  indice  objeto
        //imprime la fila de acumulado sumatoria
        tr = Templates.trTareas({carnet:key ,
            tec_nombre : tareas[0].nombre_tecnico ,
            cant_paso1 :_.where(tareas,{paso:"0001-Inicio"}).length ,
            cant_paso2: _.where(tareas,{paso:"0002-Supervision"}).length,
            cant_paso3 :_.where(tareas,{paso:"0003-Cierre"}).length,
            cant_atendido :_.where(tareas,{estado:"Atendido"}).length,
            cant_enproceso :_.where(tareas,{estado:"En Proceso"}).length,
            cant_inefectiva :_.where(tareas,{estado:"Inefectiva"}).length,
            cant_nodeja :_.where(tareas,{estado:"No Deja"}).length,
            cant_nodesea :_.where(tareas,{estado:"No Desea"}).length,
            cant_noubica :_.where(tareas,{estado:"Ausente"}).length,
            cant_otros :_.where(tareas,{estado:"Otros"}).length,
            cant_transferidos :_.where(tareas,{estado:"Transferido"}).length
        });
        tr += "<tr class='tec detalle-main-"+key+"' style='display:none'> <td colspan='20'>" +
        "<table></table>" +
        "</td></tr>";

        tareas.forEach(function(value){
           //imprime el detalle oculto
            tr +=Templates.trTareasDetalle({carnet:key ,  grupo : value.paso ,    task_id : value.task_id ,   atc : value.id_atc ,
                recepcion : value.fecha_recepcion ,     agenda : value.fecha_agenda , estado : value.estado , obs:value.observacion  });
        });
        $(tablaOfficetrack + " #table-tec").append(tr);
    });
    verDetalle();
};
mostrarTablaPendientes=function(data){
    $(tablaPendientes + " .row-tec").remove();
    var tecnicos = _.groupBy(data,"carnet");
    var tr = "", estados = "";
    if($("#slct_estado").val()!== null){
        estados = $("#slct_estado").val().join();
    }
    //MOSTRAMOS LISTADO DE TECNICOS
    _.each(tecnicos,function(tareas, key, list){
        tr = "<tr class='row-tec  tec main-"+key+"' key='"+key+"'>" +
        "<td>" + key  + "</td>" +
        "<td>" + tareas[0].tecnico  + "</td>" +
        "<td class='detalle pen-0' key='"+key+"' pendiente='0'>"+ _.where(tareas,{pendiente:0}).length +"</td>" +
        "<td class='detalle pen-1' key='"+key+"' pendiente='1'>"+ _.where(tareas,{pendiente:1}).length +"</td>" +
        "<td class='detalle pen-2' key='"+key+"' pendiente='2'>"+ _.where(tareas,{pendiente:2}).length +"</td>" +
        "<td> <span class='mapa'> " +
        "<a class='ver-mapa' key='todos' href='#'>Todos</a> " +
        "<a class='ver-mapa' key='pasados' href='#'>Pasados</a> " +
        "<a class='ver-mapa' key='hoy' href='#'>Hoy</a> " +
        "<a class='ver-mapa' key='futuros' href='#'>Futuros</a> " +
        "</span> </td>" +
        "</tr>";

        tareas.forEach(function(value){
            var todo =  _.reduce(value, function(memo, num){ return memo + " , "+num; });
            tr += "<tr class='row-tec tareas  sub-"+key+" pen-"+value.pendiente+" pendiente-"+value.pendiente+"' tarea='"+value.id_atc+"'  style='display:none'>" +
                    "<td>" +
                        "<a target='_blank' href='"+OrdenTecnico+key+"/"+value.id+"'>"+value.id_atc+"</a>"+
                    "</td>" +
                    "<td>"+ value.fecha_agenda + "/"+value.horario +"</td>" +
                    "<td colspan='3'>"+ value.estado +"</td>" +
                    "<td>" + todo +" </td>" +
                "</tr>";
        });

        $(tablaPendientes + " #table-tec").append(tr);
    });
    verDetallePendiente();
};
validar=function( input , valor){
    if (valor === "" || valor === null) {
        alert("Seleccione "+input);
        return false;
    }
    return true;
};
verDetalle=function(){
    $(tablaOfficetrack + " .detalle").click(function(){
        var ocultar = $( this ).hasClass( "active" );
        var key,pendiente;
        if (ocultar) {
            $(this).removeClass("active");
            key = $(this).attr("key").trim();
            pendiente = $(this).attr("pendiente").trim();
            $(tablaOfficetrack + " .sub-"+key+".pendiente-"+pendiente).hide("fast");
        } else {
            $(this).addClass("active");
            key = $(this).attr("key").trim();
            pendiente = $(this).attr("pendiente").trim();
            $(tablaOfficetrack + " .sub-"+key+".pendiente-"+pendiente).show("fast");
        }
    });
};
verDetallePendiente=function(){
    $(tablaPendientes + " .detalle").click(function(){
        var ocultar = $( this ).hasClass( "active" );
        var key,pendiente;
        if (ocultar) {
            $(this).removeClass("active");
            key = $(this).attr("key").trim();
            pendiente = $(this).attr("pendiente").trim();

            $(tablaPendientes + " .sub-"+key+".pendiente-"+pendiente).hide("fast");
        } else {
            $(this).addClass("active");
            key = $(this).attr("key").trim();
            pendiente = $(this).attr("pendiente").trim();

            $(tablaPendientes + " .sub-"+key+".pendiente-"+pendiente).show("fast");
        }
    });
    //MOSTRAR MAPA SEGUN GRUPO
    $(".ver-mapa").click(function(){
        var key = $(this).attr("key");
        var carnet = $(this).parent().parent().parent().attr("key");
        var estados = "";
        if($("#slct_estado").val()!== null){
            estados = $("#slct_estado").val().join();
        }
        var sin_fecha = 1;
        var tareas = [];
        var busqueda = "";
        if(key=="todos"){
            busqueda = ".sub-"+carnet;
        }else if(key=="hoy"){
            busqueda = ".sub-"+carnet+".pen-1";
        }else if(key=="pasados"){
            busqueda = ".sub-"+carnet+".pen-0";
        }else if(key=="futuros"){
            busqueda = ".sub-"+carnet+".pen-2";
        }
        $(tablaPendientes + " " +busqueda).each(function(value){
            tareas.push( $(this).attr("tarea") );
        });
        tareas  = tareas.join();
        //key: hoy, pasado, futuro, todo
        window.open(RutaTecnico+carnet+'/'+key,'_blank');
    });
};
</script>