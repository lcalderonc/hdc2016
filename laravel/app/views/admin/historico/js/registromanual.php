<script type="text/javascript">
var esCritico = false;
$(document).ready(function() {
    /*variables*/
    var ids= [];//valores que se seleccionen en los select
    var data=0, i;
    $( "#f_telefonoCliente" ).keypress(function() {
        var key = event.keyCode;
        return (key >= 48 && key <= 57);
    });
    $( "#f_codigoClienteATIS" ).keypress(function() {
        var key = event.keyCode;
        return (key >= 48 && key <= 57);
    });
    $( "#f_codigoServicioCMS" ).keypress(function() {
        var key = event.keyCode;
        return (key >= 48 && key <= 57);
    });
    $( "#f_codigoClienteCMS" ).keypress(function() {
        var key = event.keyCode;
        return (key >= 48 && key <= 57);
    });
    /*eventos en objetos*/
    $("#btn_busqueda").click(buscar);
    $("#btn_limpiar").click(limpiar);
    $("#btn_limpiar_todo").click(guardarTodo);
    $("#fftt_catv").css('display','none');
    $("#fftt_stb").css('display','none');

    $('#slct_tipo_averia').change(function() {
        cambiarTipoAveria( $('#slct_tipo_averia').val());
    });
    $('#slct_quiebre').change(function() {
        cambiarQuiebre( $('#slct_quiebre').val());
    });
    $('#zonal').change(function() {
        cambiarZonal( $('#zonal').val());
    });
    $('#mdf').change(function() {
        cambiarMdfNodo( $('#mdf').val());
    });
    /*mdf*/
    $('#troba').change(function() {
        cambiarTroba( $('#troba').val());
    });
    $('#amplificador').change(function() {
        cambiarAmp( $('#amplificador').val());
    });
    $('#tap').change(function() {
        cambiarTap( $('#tap').val());
    });
    /*nodo*/
    $('#cable').change(function() {
        cambiarCable( $('#cable').val());
    });
    $('#terminal').change(function() {
        cambiarTerminal( $('#terminal').val());
    });

    slctGlobalHtml('tipo_actividad','simple');

    slctGlobalHtml('slct_tipo_averia','simple');
    data = { usuario:1 };
    slctGlobal.listarSlct('quiebre', 'slct_quiebre', 'simple',ids,data);

    slctGlobalHtml('slct_edificio', 'simple');

    slctGlobalHtml('segmento','simple');
    ids = [ 'LIM|8' ];
    data = 0;
    slctGlobal.listarSlct('zonal', 'zonal', 'simple',ids,data);
    $("#zonal_id").val('8');

    ids = [];
    slctGlobalHtml('mdf', 'simple');

    data={ coddep:'15',codpro:'01' };
    slctGlobal.listarSlct('ubigeo', 'distrito', 'simple',ids,data);

    slctGlobalHtml('troba','simple');
    slctGlobalHtml('amplificador','simple');
    slctGlobalHtml('tap','simple');
    slctGlobalHtml('cable','simple');//o armario
    slctGlobalHtml('terminal','simple');
    slctGlobalHtml('movistar_uno','simple');
    $(".show_hide_filtros").click(function (){
        var btn_value = $(this).attr("value");

        if ( btn_value=='-' )
        {
            $("#filtros").slideUp();
            $(this).attr("value", "+");
        } else {
            $("#filtros").slideDown();
            $(this).attr("value", "-");
        }
    });

});
cargarDatos=function(estado, data){
    //limpiar data
    if (data.troba) {
        data.troba=String(data.troba).trim();
        if (data.troba.substr(0, 1)==0 && data.troba.length>1)
            data.troba=data.troba.substr(1);
    }
    if (data.amplificador){
        data.amplificador=String(data.amplificador).trim();
        if (data.amplificador.substr(0, 1)==0 && data.amplificador.length>1)
            data.amplificador=data.amplificador.substr(1);
    }
    if (data.empresa_id) {
        data.empresa_id=String(data.empresa_id).trim();
        if (data.empresa_id.substr(0, 1)==0 && data.empresa_id.length>1)
            data.empresa_id=data.empresa_id.substr(1);
    }
    if (data.tap) {
        data.tap=String(data.tap).trim();
        if (data.tap.substr(0, 1)==0 && data.tap.length>1)
            data.tap=data.tap.substr(1);
    }
    if (data.armario) {
        data.armario=String(data.armario).trim();
        if (data.armario.substr(0, 1)==0 && data.armario.length>1)
            data.armario=data.armario.substr(1);
    }
    if (data.mdf) {
        data.mdf=String(data.mdf).trim();
    }
    if (data.segmento) {
        data.segmento=String(data.segmento).trim();
    }
    var tiposerv='',segmento='',zonal='',zonal_id='',tipo_averia='';
    if (estado=='maestro') {
        //estado
        $('#estado_busqueda').html("maestro");
        $('#estado_busqueda').css("display","block");
         //tipo averia
        if (data.tiposerv=='STB') {
            tiposerv='rutina-bas-lima';
        } else if(data.tiposerv=='ADSL'){
            tiposerv='rutina-adsl-pais';
        } else if(data.tiposerv=='HFC'){
            tiposerv='rutina-catv-pais';
        }
        $('#slct_tipo_averia').multiselect('select', tiposerv);
        $('#slct_tipo_averia').multiselect('refresh');
        $('#slct_tipo_averia').trigger('change');
        //tipo actividad,
        $('#tipo_actividad').multiselect('select', data.actividad_id);
        $('#tipo_actividad').multiselect('refresh');
        //quiebre, no se sabe
        //campos input
        $("#telefono").val(data.telefono);
        //$("#averia").val(data.codactu);
        $("#direccion").val(data.tipocalle+" "+data.nomcalle+" "+data.numcalle);
        $("#codclie").val(data.codclie);
        $("#codservcms").val(data.codservcms);
        $("#cr_nombre").val(data.nombre+' '+data.appater+' '+data.apmater);
        $("#cr_telefono").val('');
        $("#cr_celular").val('');
        $("#cr_observacion").val('');
        //segmento
        segmento = data.segmento;
        if (segmento===null || segmento==='' || segmento==undefined) {
            segmento='';
        } else {
            segmento = data.segmento.trim();
        }
        segmento=getSegmento(segmento);
        $('#segmento').multiselect('select', segmento);
        $('#segmento').multiselect('refresh');
        //zonal
        zonal =data.zonal;
        if (zonal==null) {
            zonal='';
            $("#zonal_id").val('');
        } else {
            zonal=getZonal(zonal);
            $('#zonal').multiselect('select', zonal);
            $('#zonal').multiselect('refresh');
            zonal_id=zonal.split('|');
            zonal_id=zonal_id[1];
            $("#zonal_id").val(zonal_id);
            //mdf
            if (data.mdf.trim()!=='') {
                $("#mdf").multiselect('destroy');
                ids = data.mdf;
                tipo_averia=$("#slct_tipo_averia").val();
                zonal=$("#zonal").val().split("|")[0];
                ajax= { tipo:tipo_averia , zonal:zonal};
                Registromanual.cargarMdf(ids,ajax,data);
            }
        }
        //troba
        //amplificador
        //tap
        //buscar en terminal
        if (data.tiposerv=='HFC') {
            var coord_x, coord_y, hayXY, locations=[], image;
            if ( data.xtroba != null && data.ytroba != null) {
                coord_x=Number(data.xtroba);
                coord_y=Number(data.ytroba);
                hayXY='troba';
                locations.push(['Troba', coord_y, coord_x, 'troba.png',data.dir_troba]);
            }
            if ( data.xtab != null && data.ytab != null) {
                coord_x=Number(data.xtab);
                coord_y=Number(data.ytab);
                hayXY='tab';
                locations.push(['Tab', coord_y, coord_x, 'tap.png',data.dir_tab]);
            }
            if ( data.xterminal != null && data.yterminal != null) {
                coord_x=Number(data.xterminal);
                coord_y=Number(data.yterminal);
                hayXY='terminal';
                locations.push(['Terminal', coord_y, coord_x, 'terminal.png',data.dir_term]);
            }
            //la preferencia de coor: terminal, tap, troba
            if (hayXY=='terminal' || hayXY=='tab' || hayXY=='troba') {
                locations.push(['Cliente', coord_y, coord_x, 'male-2.png']);
            }
            
            infowindow = new google.maps.InfoWindow();
            //limpiar cliente
            if (markerCliente.visible) {
                markerCliente.setMap(null);
                markerCliente=[];
            }
            //limpiar estructuras: troba nodo tap
            for (var i = 0, marker; marker = markerEstructura[i]; i++) {
                marker.setMap(null);
            }

            for (i = 0; i < locations.length; i++) {
                
                if (locations.length==i+1) {
                    //el ultimo, marker del cliente
                    //añadir marcador
                    situarMarcador(locations[i][1], locations[i][2], true);

                } else {
                //troba, terminal, tab
                    //añadir marcador
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        draggable: false,
                        icon: 'img/icons/'+locations[i][3],
                        map: map
                    });
                    //informacion de troba terminal, tab
                    infocontent = "Tipo: <strong>"+locations[i][0] +
                             "</strong><br>"+
                             "Direccion: <strong>"+locations[i][4]+
                             "</strong><br><a href=\"javascript:closeInfoWindow()\">Cerrar</a>";
                    doInfoWindow(map, marker, infocontent);
                    markerEstructura.push(marker);
                }
            }
        } else {
            //situar marcador del terminal,o calbe
            if (data.coordy!=='' && data.coordy!=undefined && data.coordy!='0') {
                situarMarcador(data.coordy, data.coordx, true);
            }
        }
        //movistar uno, no se sabe
        //otros input
        $("#eecc").val( data.eecc );
        $("#microzona").val( data.microzona );
        $("#lejano").val( data.lejano );
        $("#empresa_id").val( data.empresa_id );
        

    } else if (estado=='pendientes' || estado=='liquidados') {
        //estado
        $('#estado_busqueda').html(estado);
        $('#estado_busqueda').css("display","block");
        //tipo averia
        tipo_averia=data.cod_negocio;
        if (tipo_averia=='01') {//STB
            $('#slct_tipo_averia').multiselect('select', 'rutina-bas-lima');
            $('#slct_tipo_averia').multiselect('refresh');
            $('#slct_tipo_averia').trigger('change');
        } else if (tipo_averia=='02') {//CATV
            $('#slct_tipo_averia').multiselect('select', 'rutina-catv-pais');
            $('#slct_tipo_averia').multiselect('refresh');
            $('#slct_tipo_averia').trigger('change');
        } else if (tipo_averia=='03') {//ADSL
            $('#slct_tipo_averia').multiselect('select', 'rutina-adsl-pais');
            $('#slct_tipo_averia').multiselect('refresh');
            $('#slct_tipo_averia').trigger('change');
        }
        //tipo actividad,
        $('#tipo_actividad').multiselect('select', data.actividad_id);
        $('#tipo_actividad').multiselect('refresh');
        //quiebre
        $("#telefono").val(data.telefono);
        //$("#averia").val(data.codactu);
        $("#direccion").val(data.dir_instal);
        $("#codclie").val(data.cod_cliente);
        $("#codservcms").val('');
        $("#cr_nombre").val(data.cliente);
        $("#cr_telefono").val(data.telef_contac);
        $("#cr_celular").val(data.celular_contac);
        $("#cr_observacion").val('');
        //segmento
        var seg = data.segmento;
        if (seg.length>1) {
            seg=seg.replace('0','');
        }
        segmento=getSegmento(seg);
        $('#segmento').multiselect('select', segmento);
        $('#segmento').multiselect('refresh');
        //zonal
        zonal =data.zonal;
        if (zonal==null) {
            zonal='';
            $("#zonal_id").val('');
        } else {
            zonal=getZonal(zonal);
            $('#zonal').multiselect('select', zonal);
            $('#zonal').multiselect('refresh');
            zonal_id=zonal.split('|');
            zonal_id=zonal_id[1];
            $("#zonal_id").val(zonal_id);
            //mdf
            if (data.mdf.trim()!=='') {
                $("#mdf").multiselect('destroy');
                ids = data.mdf;
                tipo_averia=$("#slct_tipo_averia").val();
                zonal=$("#zonal").val().split("|")[0];
                ajax= { tipo:tipo_averia , zonal:zonal};
                Registromanual.cargarMdf(ids,ajax,data);
            }
        }
        //troba
        //amplificador
        //tap
        //movistar uno
        //otros input
        $("#eecc").val( data.eecc );
        $("#microzona").val( data.microzona );
        $("#lejano").val( data.lejano );
        $("#empresa_id").val( data.empresa_id );
        //si se recibe x y pintarlo
        if (data.coordx!=='' && data.coordx!=undefined && data.coordx!='0') {
            situarMarcador(data.coordy, data.coordx, true);
        }
        
    }
};
buscar=function(){
    var fono = $("#f_telefonoCliente").val();
    var codATIS = $("#f_codigoClienteATIS").val();
    var codServCMS = $("#f_codigoServicioCMS").val();
    var codCliCMS = $("#f_codigoClienteCMS").val();
    var parametro='';

    if (fono.length>0 && codATIS.length===0 && codServCMS.length===0 && codCliCMS.length===0){
        parametro={telefonoCliente:fono};
    }
    else if (fono.length===0 && codATIS.length>0 && codServCMS.length===0 && codCliCMS.length===0){
        parametro={codigoClienteATIS:codATIS};
    }
    else if (fono.length===0 && codATIS.length===0 && codServCMS.length>0 && codCliCMS.length===0){
        parametro={codigoServicioCMS:codServCMS};
    }
    else if (fono.length===0 && codATIS.length===0 && codServCMS.length===0 && codCliCMS.length>0){
        parametro={codigoClienteCMS:codCliCMS};
    }
    else {
        alert('debe ingresar un valor de busqueda ');
        return false;
    }
    Registromanual.BuscaCliente(parametro);
};
limpiar=function(){
    $('#streetview').html('');
    $('#estado_busqueda').css('display','none');
    $('#estado_busqueda').html('');
    $("#averia").val('');
    $("#valida_codactu").val('');
    $("#telefono").val('');
    $("#cr_nombre").val('');
    $("#cr_telefono").val('');
    $("#cr_observacion").val('');
    $("#cr_celular").val('');
    $("#troba").val('');
    $("#amplificador").val('');
    $("#tap").val('');
    $("#cable").val('');
    $("#terminal").val('');
    $("#direccion").val('');
    $("#codclie").val('');
    $("#codservcms").val('');
    $("#eecc").val('');
    $("#lejano").val('');
    $("#microzona").val('');
    $("#empresa_id").val('');
    $("#x").val('');
    $("#y").val('');
    
    $('#slct_tipo_averia').multiselect('select', '');
    $('#slct_tipo_averia').multiselect('refresh');

    $('#tipo_actividad').multiselect('select', '');
    $('#tipo_actividad').multiselect('refresh');

    $('#slct_quiebre').multiselect('select', '');
    $('#slct_quiebre').multiselect('refresh');

    $('#segmento').multiselect('select', '');
    $('#segmento').multiselect('refresh');

    $("#zonal").val('LIM|8');
    $("#zonal_id").val('8');
    $('#zonal').multiselect('select', '');
    $('#zonal').multiselect('refresh');

    $("#mdf").multiselect('destroy');
    $("#mdf").html("<option value=''>Seleccione</option>");
    slctGlobalHtml('mdf','simple');

    $('#distrito').multiselect('select', '');
    $('#distrito').multiselect('refresh');

    $("#slct_edificio").multiselect('destroy');
    $("#slct_edificio option").remove();
    $("#slct_edificio").append("<option value=''>Seleccione</option>");
    slctGlobalHtml('slct_edificio', 'simple');
    $("#troba").multiselect('destroy');
    $("#troba option").remove();
    $("#troba").append("<option value=''>Seleccione</option>");
    slctGlobalHtml('troba','simple');
    $("#amplificador").multiselect('destroy');
    $("#amplificador option").remove();
    $("#amplificador").append("<option value=''>Seleccione</option>");
    slctGlobalHtml('amplificador','simple');
    $("#tap").multiselect('destroy');
    $("#tap option").remove();
    $("#tap").append("<option value=''>Seleccione</option>");
    slctGlobalHtml('tap','simple');
    $("#cable").multiselect('destroy');
    $("#cable option").remove();
    $("#cable").append("<option value=''>Seleccione</option>");
    slctGlobalHtml('cable','simple');//o armario
    $("#terminal").multiselect('destroy');
    $("#terminal option").remove();
    $("#terminal").append("<option value=''>Seleccione</option>");
    slctGlobalHtml('terminal','simple');
    slctGlobalHtml('movistar_uno','simple');
    $("#f_telefonoCliente").focus();
    //eliminar marcadores de clientes
    if (markerCliente.visible) {
        markerCliente.setMap(null);
        markerCliente=[];
    }
    if (markerHelp.visible) {
        markerHelp.setMap(null);
        markerHelp=[];
    }
    //eliminar marcadores de edificios
    for ( i = 0; i < edificios.length; i++) {
        edificios[i].setMap(null);
    }
    //eliminar marcadores de estructuras: troba nodo tap
    for (var i = 0, marker; marker = markerEstructura[i]; i++) {
        marker.setMap(null);
    }
    initialize(-12.046374,-77.0427934);
};
cambiarQuiebre=function(valor){
    if (valor==16){//quiebre edificios
        var locations=[];
        var x=$('#x').val();
        var y=$('#y').val();
        //limpiar combo
        $("#slct_edificio").multiselect('destroy');
        $("#slct_edificio option").remove();
        //llenar locations con edificios mas cercanos
        //dibujar edificios mas cercanos
        if (x!=='' && y!=='') {//si hay cliente
            Registromanual.Edificios($('#y').val() ,  $('#x').val() , 10);
        } else{
            for ( i = 0; i < edificios.length; i++) {
                edificios[i].setMap(null);
            }
            edificios=[];
            $("#slct_edificio").append("<option value=''>Seleccione</option>");
            slctGlobalHtml('slct_edificio', 'simple');
        }
    } else {
        //eliminar edificios dibujados
        for ( i = 0; i < edificios.length; i++) {
            edificios[i].setMap(null);
        }
        edificios=[];
        $("#slct_edificio").multiselect('destroy');
        $("#slct_edificio option").remove();
        $("#slct_edificio").append("<option value=''>Seleccione</option>");
        slctGlobalHtml('slct_edificio', 'simple');
    }
};
cambiarTipoAveria=function(valor){
    //no debe setearse el valor de zonal si  se encuentra seleccionado
    if (valor!=='') {
        var buscar = valor.indexOf("catv");
        $("#segmento").multiselect('destroy');
        var segmentoNoCatv = ['8','9','A','B','C','D','M'];
        var segmentoCatv = ['VIP','NO VIP'];
        var index;
        //segmento
        $("#segmento option").remove();
        $("#segmento").append("<option value=''>Seleccione</option>");
        if (buscar >= 0) {
            for ( index in segmentoCatv ) {
                if ( segmentoCatv.hasOwnProperty(index) ) {
                    $("#segmento").append("<option value=\"" +
                             index +
                             "\" >" +
                             segmentoCatv[index] +
                             "</option>");
                }
            }
            $("#fftt_stb").hide();
            $("#fftt_catv").show();
        } else {
            for ( index in segmentoNoCatv ) {
                if ( segmentoNoCatv.hasOwnProperty(index) ) {
                    $("#segmento").append("<option value=\"" +
                             index +
                             "\" >" +
                             segmentoNoCatv[index] +
                             "</option>");
                }
            }
            $("#fftt_stb").show();
            $("#fftt_catv").hide();
        }
        slctGlobalHtml('segmento','simple');
        //mdf nodo, si esta seleciconado zonal debera filtrar
        if( $("#zonal").val()!=='' ){
            $("#mdf").multiselect('destroy');
            $("#mdf").html('');
            var tipo_averia, zonal;
            ids = [];
            zonal=$("#zonal").val().split("|")[0];
            data= { tipo:valor , zonal:zonal};
            slctGlobal.listarSlct('mdf', 'mdf', 'simple',ids,data);
            if (markerHelp.visible) {
                markerHelp.setMap(null);
                markerHelp=[];
            }
        }
    } else {
        $("#segmento").multiselect('destroy');
        $("#segmento").html("<option value=''>Seleccione</option>");
        slctGlobalHtml('segmento','simple');

        $("#mdf").multiselect('destroy');
        $("#mdf").html("<option value=''>Seleccione</option>");
        slctGlobalHtml('mdf','simple');

        $("#fftt_stb").hide();
        $("#fftt_catv").hide();
        $('#zonal').multiselect('select', '');
        $('#zonal').multiselect('refresh');
        $("#zonal_id").val('');
    }
    if (markerHelp.visible) {
        markerHelp.setMap(null);
        markerHelp=[];
    }
};
cambiarZonal=function(valor){
    if( $("#slct_tipo_averia").val()!=='' ){
        $("#zonal_id").val(valor.split("|")[1]);
        var ids = [];
        var tipo_averia=$("#slct_tipo_averia").val();
        var zonal=$("#zonal").val().split("|")[0];
        var data= { tipo:tipo_averia , zonal:zonal};
        $("#mdf").multiselect('destroy');
        $("#mdf option").remove();
        slctGlobal.listarSlct('mdf', 'mdf', 'simple',ids,data);
    } else {
        $("#mdf").multiselect('destroy');
        $("#mdf").html("<option value=''>Seleccione</option>");
        slctGlobalHtml('mdf','simple');
    }
    //si se dibujo el marker de ayuda: armario, cable, terminal, troba, amp, tap
    if (markerHelp.visible) {
        markerHelp.setMap(null);
        markerHelp=[];
    }
};
cambiarMdfNodo=function(valor){
    var val;
    var tipo_registro = $("#slct_tipo_averia").val();
    var buscar = tipo_registro.indexOf("catv");
    var ids = [];
    var data;
    $("#eecc").val( "" );
    $("#empresa_id").val( "" );
    $("#lejano").val( "" );
    $("#microzona").val( "" );
    if( $.trim(valor)!=='' ){
        var arrData = valor.split("___");
        val = arrData[0];
        if( $.trim(arrData[1])!=='' ){
            $("#eecc").val( arrData[1].split("   ")[0] );
            $("#empresa_id").val( arrData[1].split("   ")[1] );
        }
        $("#lejano").val( arrData[2] );
        $("#microzona").val( arrData[3] );

        if (buscar >= 0) {//existe catv
            data = {nodo:val};
            $("#troba").multiselect('destroy');
            $("#troba option").remove();
            slctGlobal.listarSlct('lista/troba', 'troba', 'simple',ids,data);
            if (markerHelp.visible) {
                markerHelp.setMap(null);
                markerHelp=[];
            }
        } else {//adsl, stb
            data = {mdf:val};
            $("#cable").multiselect('destroy');
            $("#cable option").remove();
            slctGlobal.listarSlct('lista/cable', 'cable', 'simple',ids,data);
            //si se tiene X,Y de mdf pintar el punto en el mapa y las cajas de texto
            //sin sobreescribir ni en el mapa ni en las cajas
            //markerCliente es el marcador del mapa cargado por la busqueda
            validarXY('mdf');
        }
    } else {
        if (buscar >= 0) {//existe catv
            slctGlobalHtml('troba','simple');
        } else {//adsl, stb
            slctGlobalHtml('cable','simple');//o armario
        }
    }
};
cambiarTroba=function(valor){
    var ids=[];
    var nodo=$('#mdf').val();/**revisar el valor de la troba*/
    nodo = nodo.split("___");
    nodo = nodo[0];
    var data={troba:valor,nodo:nodo};
    $("#amplificador").multiselect('destroy');
    $("#amplificador option").remove();
    slctGlobal.listarSlct('lista/amplificador', 'amplificador', 'simple',ids,data);
    //si se tiene X,Y de troba pintar el punto en el mapa y las cajas de texto
    //sin sobreescribir ni en el mapa ni en las cajas
    validarXY('troba');
};
cambiarAmp=function(valor){
    var ids=[];
    var troba=$('#troba').val();/*revisar*/
    var nodo=$('#mdf').val();/**revisar el valor de la troba*/
    nodo = nodo.split("___");
    nodo = nodo[0];
    var data={amplificador:valor,nodo:nodo,troba:troba};
    $("#tap").multiselect('destroy');
    $("#tap option").remove();
    slctGlobal.listarSlct('lista/tap', 'tap', 'simple',ids,data);
    //si se tiene X,Y de amplificador pintar el punto en el mapa y las cajas de texto
    //sin sobreescribir ni en el mapa ni en las cajas
    validarXY('amplificador');
};
cambiarTap=function(valor){
    //si se tiene X,Y de tap pintar el punto en el mapa y las cajas de texto
    //sin sobreescribir ni en el mapa ni en las cajas
    validarXY('tap');
};
cambiarCable=function(valor){
    var ids=[];
    var mdf=$('#mdf').val();
    if( $.trim(valor)!=='' ){
        mdf = mdf.split("___");
        mdf = mdf[0];
        var data={cable:valor,mdf:mdf};
        $("#terminal").multiselect('destroy');
        $("#terminal option").remove();
        slctGlobal.listarSlct('lista/terminal', 'terminal', 'simple',ids,data);
        //si se tiene X,Y de cable pintar el punto en el mapa y las cajas de texto
        //sin sobreescribir ni en el mapa ni en las cajas
        validarXY('cable');
    }
};
cambiarTerminal=function(valor){
    //si se tiene X,Y de terminal pintar el punto en el mapa y las cajas de texto
    //sin sobreescribir ni en el mapa ni en las cajas
    validarXY('terminal');
};
escondeBotton=function(){
    $("#btn_limpiar_todo").css("display","none");
    $("#valida_codactu").removeClass("has-success").removeClass("has-warning");
    $("#valida_codactu").addClass("has-info");
    $("#valida_codactu label").html('<i class="fa fa-info-circle"></i> Validando Código...');
};
validaTexto=function(valor){
    var RegExPattern = /^[a-zA-Z0-9]*$/;
    if ((valor.match(RegExPattern)) && (valor!=='')) {
        Registromanual.ValidarTexto(valor);
    } else {
        $("#valida_codactu").removeClass("has-info").addClass("has-warning");
        $("#valida_codactu label").html('<i class="fa fa-warning"></i> Ingrese caracteres alfanuméricos ');
        $("#valida_codactu").focus();
    }
};
validarXY=function(select){
    //se tiene que sabr si el marker a sido generado de 
    //markerCliente
    //si se tiene X,Y  pintar el punto en el mapa y las cajas de texto
    //sin sobreescribir ni en el mapa ni en las cajas
    //markerCliente es el marcador del mapa cargado por la busqueda

    //markerHelp.setMap(null);
    var coord_y=$('#'+select+' option:selected').data('coord_y');
    var coord_x=$('#'+select+' option:selected').data('coord_x');
    var direccion=$('#'+select+' option:selected').data('direccion');
    if (coord_x!=null && coord_x!=='' && coord_y!=null && coord_y!=='') {
        if (markerHelp.visible) {
            markerHelp.setMap(null);
            markerHelp=[];
        }

        infowindow = new google.maps.InfoWindow();
        markerHelp = new google.maps.Marker({
            position: new google.maps.LatLng(coord_y, coord_x),
            draggable: false,
            icon: 'img/icons/'+select+'.png',//locations[i][3],
            map: map
        });
        //informacion de troba terminal, tab
        infocontent = "Tipo: <strong>"+select +
                 "</strong><br>"+
                 "Direccion: <strong>"+direccion+
                 "</strong><br><a href=\"javascript:closeInfoWindow()\">Cerrar</a>";
        doInfoWindow(map, markerHelp, infocontent);
        var latLng = markerHelp.getPosition(); // returns LatLng object
        map.setCenter(latLng);
        SetXY(coord_x, coord_y);
        geoStreetView(coord_y,coord_x,'streetview');
    } else {
        //si no hay x, y establecidos
        //reestablecer mapa
        var x=$('#x').val();
        var y=$('#y').val();
        if (x===null || x==='' || y===null || y==='') {
            initialize(-12.046374,-77.0427934);
        }
    }

};
guardarTodo=function(){

    validaTexto($('#averia').val());//cambiar ya que demora consultar el servidor
    if ( $("#tipo_actividad").val()==='' ){
        alert('Seleccione Tipo Actividad');
    }
    else if ( $("#slct_tipo_averia").val()==='' ){
        alert('Seleccione Tipo Averia');
    }
    else if ( $("#slct_quiebre").val()==='' ){
        alert('Seleccione Quiebre');
    }
    else if ( $("#averia").val()==='' ){
        alert('Ingrese Código Actuación');
    }
    else if ( $("#averia").val()!=='' && $("#valida_codactu").attr("class")!='form-group has-success' ){
        alert('Código Actuación no permitido favor de validar');
    }
    else if ( $("#telefono").val()==='' ){
        alert('Ingrese Teléfono/Cod Cliente CMS');
    }
    else if ( $("#cr_nombre").val()==='' ){
        alert('Ingrese Nombre Contacto');
    }
    else if ( $("#cr_telefono").val()==='' ){
        alert('Ingrese Telefono Contacto');
    }
    else if ( $("#cr_observacion").val()==='' ){
        alert('Ingrese Observación');
    }
    else if ( $("#segmento").val()==='' ){
        alert('Seleccione Segmento');
    }
    else if ( $("#zonal").val()==='' ){
        alert('Seleccione Zonal');
    }
    else if ( $("#mdf").val()==='' ){
        alert('Seleccione nodo/mdf');
    }
    else if ( $("#distrito").val()==='' && $("#zonal").val()==='LIM|8' ){
        alert('Seleccione Distrito');
    }
    else if ( $("#x").val()==='' ){
        alert('Seleccione en el mapa una ubicación');
    }
    else{
        Registromanual.Crear();
    }
};
getZonal=function(zonal){
    switch (zonal) {
        case 'ARE':
            zonal = "ARE|1";
            break;
        case 'CHB':
            zonal = "CHB|2";
            break;
        case 'CHY':
            zonal = "CHY|3";
            break;
        case 'CHI':
            zonal = "CHY|3";
            break;
        case 'CUZ':
            zonal = "CUZ|4";
            break;
        case 'HYO':
            zonal = "HYO|5";
            break;
        case 'ICA':
            zonal = "ICA|6";
            break;
        case 'IQU':
            zonal = "IQU|7";
            break;
        case 'LIM':
            zonal = "LIM|8";
            break;
        case 'PIU':
            zonal = "PIU|9";
            break;
        case 'TRU':
            zonal = "TRU|10";
            break;
        /*default:
            zonal = "";*/
    }
    return zonal;
};
getSegmento=function(segmento){
    switch (segmento) {
        case '8':
            segmento = "0";
            break;
        case '9':
            segmento = "1";
            break;
        case 'A':
            segmento = "2";
            break;
        case 'B':
            segmento = "3";
            break;
        case 'C':
            segmento = "4";
            break;
        case 'D':
            segmento = "5";
            break;
        case 'M':
            segmento = "6";
            break;
        /*default:
            segmento = "";*/
    }
    return segmento;
};
HTMLListar=function(datos,id,element){

    var html="<option value=''>.::Seleccione::.</option>";
    $.each(datos,function(index,data){
        var x='';y='';direccion='';
        if (data.nombre==id) {
            if (data.coord_x!=='' && data.coord_x!=null) {
                x=' data-coord_x="'+data.coord_x+'" ';
            }
            if (data.coord_y!=='' && data.coord_y!=null) {
                y=' data-coord_y="'+data.coord_y+'" ';
            }
            if (data.direccion!=='' && data.direccion!=null) {
                direccion=' data-direccion="'+data.direccion+'" ';
            }
            html += "<option selected='selected'"+x+y+direccion+" value=\"" + data.id + "\">" + data.nombre + "</option>";
        }
        else {
            if (data.coord_x!=='' && data.coord_x!=null) {
                x=' data-coord_x="'+data.coord_x+'" ';
            }
            if (data.coord_y!=='' && data.coord_y!=null) {
                y=' data-coord_y="'+data.coord_y+'" ';
            }
            if (data.direccion!=='' && data.direccion!=null) {
                direccion=' data-direccion="'+data.direccion+'" ';
            }
            html += "<option "+x+y+direccion+" value=\"" + data.id + "\">" + data.nombre + "</option>";
        }
    });
    $("#"+element).html(html);

    $("#"+element).multiselect({
        maxHeight: 200,
        buttonContainer: '<div class="btn-group col-xxs-12" />',
        buttonClass: 'btn btn-primary col-xxs-12',
        templates: {
            ul: '<ul class="multiselect-container dropdown-menu col-xxs-12"></ul>',
        },
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonText: function(options, select) { // para multiselect indicar vacio...
            if (options.length === 0) {
                return '.::Seleccione::.';
            }
            else if (options.length > 2) {
                return options.length+' Seleccionados';//More than 3 options selected!
            }
            else {
                 var labels = [];
                 options.each(function() {
                     if ($(this).attr('label') !== undefined) {
                         labels.push($(this).attr('label'));
                     }
                     else {
                         labels.push($(this).html());
                     }
                 });
                 return labels.join(', ') + '';
            }
        }
    });
    validarXY(element);
};

</script>
