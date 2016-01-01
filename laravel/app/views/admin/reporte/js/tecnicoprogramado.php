<script type="text/javascript">
//Crear mapa

    var mapTools = {draw: false};
    var objRepMap;
    var mapRepMarker = {};
    var mapObjects = {};
    var objMarker = {};
    var directionsDisplay = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();

    var controlPest = '';

    $(document).ready(function() {
        $('#fecha_agenda').daterangepicker(
                {
                    format: 'YYYY-MM-DD'
                });

        //Celulas.CargarCelulas(activarTabla);
        $("#mostrartecnicos").click(mostrarReporte);
        var ids = []; //para seleccionar un id
        var data = {usuario: 1};
        ids = [1, 5];
        slctGlobal.listarSlct('empresa', 'slct_empresa', 'multiple', ids, data);
        ids = ['LIM|8'];
        slctGlobal.listarSlct('zonal', 'slct_zonal', 'multiple', ids, data);
        //ids = [7];
        data = {usuario_id: 1};
        slctGlobal.listarSlct('quiebregrupo', 'slct_grupo_quiebre', 'multiple', '7',data);

        //Lista de quiebres por usuario
        Tecnicos.listaQuiebre();

        slctGlobalHtml('slct_order', 'simple');

        $("#t_reporte").dataTable();

        //Mapa de ordenes y tecnicos
        objRepMap = doObjMap("tecprg_map", objMapProps, mapTools);

        $("#slct_tecnico_mapa").multiselect({
            onChange: function(option, checked, select) {
                //console.log(option +" / "+ $(option).val() +" / "+ checked +" / "+ select);
                tecOrdMap(option, $(option).val(), checked);
            },
            enableFiltering: true,
            includeSelectAllOption: true,
            nonSelectedText: 'Seleccione Tecnico'
        });

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

    eventcodactumodal = function() {
        controlPest = 'ACTIVADO';
        $("#codactuModal #btn_close_codactu_modal").click();
    };

    mostrarReporte = function() {

        if ($.trim($("#slct_empresa").val()) === '') {
            alert('Seleccione Empresa para filtro Horario');
        }
        else if ($.trim($("#slct_zonal").val()) === '') {
            alert('Seleccione Zonal');
        }
        else if ($.trim($("#slct_grupo_quiebre").val()) === '') {
            alert('Seleccione Grupo Quiebre');
        }
        else {
            Tecnicos.tecnicoProgramado(tecnicoProgramadoHTML);
        }
    };

    descargarReporte = function() {
        if ($('#t_reporte tbody').html() !== '') {
            $("#form_tecnico_programado").submit();
        }
    };

    CambiaVisibilidad=function(val){
        $(".bloque1,.bloque2").css("display","");
        if(val!==''){
        $(".bloque"+val).css("display","none");
        }
    };

    tecnicoProgramadoHTML = function(datos, order, fechacabecera, horacabecera, iconos) {
        $('#t_reporte').dataTable().fnDestroy();
        $('#t_reporte2').dataTable().fnDestroy();

        if (Object.size(mapRepMarker) > 0)
        {
            clearMapTecPrg();
        }

        //Objetos del mapa en blanco
        mapRepMarker = {};
        mapObjects = {};
        objMarker = {};

        //Eliminar lista de tecnicos
        $("#slct_tecnico_mapa").empty();
        $("#slct_tecnico_mapa").multiselect('rebuild');
        $("#slct_tecnico_mapa").multiselect('refresh');
        /*var modelo="<table class='table table-striped table-border'>"+
                    "<tr>"+
                    "   <th colspan='2' style='text-align:center'>Leyenda</th>"+
                    "</tr>"+
                    "<tr>"+
                    "   <td>CodActu</td>"+
                    "   <td>Nodo(s)</td>"+
                    "</tr>"+
                    "<tr>"+
                    "   <td>N° Obs</td>"+
                    "   <td>Est. Legado</td>"+
                    "</tr>"+
                    "<tr>"+
                    "   <td colspan='2' style='text-align:center'>Est. Final Officetrack</td>"+
                    "</tr>"+
                    "<tr>"+
                    "   <td>CodActu</td>"+
                    "   <td>N° HD</td>"+
                    "</tr>"+
                    "<tr>"+
                    "   <td>N° SD</td>"+
                    "   <td>N° Pu</td>"+
                    "</tr>"+
                    "</table>";
        $("#modelo").html(modelo);*/
        var htmlcabf = "<tr><td colspan='5'>&nbsp;</td>";

        var htmlcab = "<tr><th>Grupo Quiebre</th><th>Quibre</th><th>Célula</th><th>Técnico</th><th>Última Posición</th>";
        objMarker.tecnico = {};
        objMarker.ordenes = {};
        var zIndexRep = 0;
        var repMarker;
        var bounds = new google.maps.LatLngBounds();

        for (var i = 0; i < fechacabecera.length; i++) {
            for (var j = 0; j < horacabecera.length; j++) {
                htmlcab += "<th>" + horacabecera[j] + "</th>";
            }
            htmlcabf += "<th style='text-align:center' colspan='" + j + "'>" + fechacabecera[i] + "</th>";
        }
        htmlcabf += "<th><a onClick='descargarReporte();' class='btn btn-success'><i class='fa fa-download fa-lg'></li></a></th>";
        htmlcab += "<th>TOTAL</th>";

        htmlcab += "</tr>";
        htmlcabf += "</tr>";

        var html = "";
        var html2="";

        $("#t_reporte thead,#t_reporte tfoot").html(htmlcabf + htmlcab);

        var contadordet = 0;
        var detalle = "";
        var iconx = 0;
        var detalle2="";

        $.each(datos, function(index, data) {
            contadordet++;
            html += "<tr>";

            html += "<td>" + data.grupo_quiebre + "</td>";
            html += "<td>" + data.quiebre + "</td>";
            html += "<td>" + data.celula + "</td>";
            html += "<td>" + data.tecnico + "</td>";
            html += "<td>" + $.trim(data.ultima) + "</td>";

            for (var i = 1; i <= fechacabecera.length; i++) {
                for (var j = 1; j <= horacabecera.length; j++) {
                    ////////////////////////////////////////////////Primera Tabla//////////////////////////////////////////////
                    if (data["h" + j + "_" + i] == null || data["h" + j + "_" + i] === '' || data["h" + j + "_" + i] == 0) {
                        data["h" + j + "_" + i] = "<b><font color='#1BE137'>" + 0 + "</font></b>";
                    }
                    else if (data["h" + j + "_" + i] * 1 == 1) {
                        data["h" + j + "_" + i] = "<b id='tdb" + contadordet + "_" + i + "_" + j + "' data-codactu='" + data["a" + j + "_" + i] + "'>" + data["h" + j + "_" + i] + "</b>";
                    }
                    else {
                        data["h" + j + "_" + i] = "<b id='tdb" + contadordet + "_" + i + "_" + j + "' data-codactu='" + data["a" + j + "_" + i] + "'><font color='#E9102F'>" + data["h" + j + "_" + i] + "</font></b>";
                    }

                    if (data["s" + j + "_" + i] == null || data["s" + j + "_" + i] == 0) {
                        data["s" + j + "_" + i] = 0;
                    }
                    else {
                        data["s" + j + "_" + i] = "<b><font color='#478FD2'>" + data["s" + j + "_" + i] + "</font></b>";
                    }

                    if (data["mdf" + j + "_" + i] == null || $.trim(data["mdf" + j + "_" + i]) === '') {
                        data["mdf" + j + "_" + i] = '';
                    }

                    if (data["legado" + j + "_" + i] == null || $.trim(data["legado" + j + "_" + i]) === '') {
                        data["legado" + j + "_" + i] = '';
                    }

                    if (data["esfiot" + j + "_" + i] == null || $.trim(data["esfiot" + j + "_" + i]) === '') {
                        data["esfiot" + j + "_" + i] = '';
                    }

                    detalle = data["legado" + j + "_" + i].split(",");
                    for (var l = 0; l < detalle.length; l++) {
                        if (detalle[l] == 'LIQUIDADO') {
                            detalle[l] = "<font color='#E9102F'>" + detalle[l].substr(0, 2) + "</font>";
                        }
                        else {
                            detalle[l] = detalle[l].substr(0, 2);
                        }
                    }
                    data["legado" + j + "_" + i] = detalle.join(",");

                    if (data["decos" + j + "_" + i] == null || $.trim(data["decos" + j + "_" + i]) === '') {
                        data["decos" + j + "_" + i] = ",,,,";
                    }
                    detalle2 = data["decos" + j + "_" + i].split(",");

                    html += "<td onClick='activadetalle(" + contadordet + "," + i + "," + j + ");'>" +
                            "<table>" +
                            "<tr class='bloque1'>" +
                            "<td>" + data["h" + j + "_" + i] + "</td>" +
                            "<td>&nbsp;" + data["mdf" + j + "_" + i] + "</td>" +
                            "</tr>" +
                            "<tr class='bloque1'>" +
                            "<td>" + data["s" + j + "_" + i] + "</td>" +
                            "<td>&nbsp;" + data["legado" + j + "_" + i] + "</td>" +
                            "</tr>" +
                            "<tr class='bloque1'>" +
                            "<td colspan=2><font size='1'>" + data["esfiot" + j + "_" + i] + "</font></td>" +
                            "</tr>" +
                            "<tr class='bloque2'>" +
                            "<td>" + detalle2[0]+ "</td>" +
                            "<td>&nbsp;" + detalle2[1] + "</td>" +
                            "</tr>" +
                            "<tr class='bloque2'>" +
                            "<td>" + detalle2[2] + "</td>" +
                            "<td>&nbsp;" + detalle2[3] + "</td>" +
                            "</tr>" +
                            "</table>" +
                            " </td>";
                }
            }

            //Marcadores Tecnicos
            var tecColor = "";
            var coordTec = "";
            var txtTecColor = "";
            if (data.tecx != null && data.tecy != null)
            {
                coordTec = data.tecy.replace(",", ".") +
                         "," +
                         data.tecx.replace(",", ".");
            } else {
                coordTec = false;
            }

            //Agregar al tecnico solo una vez (quiebre multiple) 2015/06/24
            if (objMarker.tecnico[data.tecnico_id] == null)
            {
                objMarker.tecnico[data.tecnico_id] = {};
                objMarker.tecnico[data.tecnico_id].coord = coordTec;
                objMarker.tecnico[data.tecnico_id].icono = iconos[iconx];
                objMarker.tecnico[data.tecnico_id].nombre = data.tecnico;
                objMarker.tecnico[data.tecnico_id].carnet = data.carnet_tmp;
                objMarker.tecnico[data.tecnico_id].opath = null;
                objMarker.tecnico[data.tecnico_id].tpath = null;
                objMarker.tecnico[data.tecnico_id].tecagd = {};
                objMarker.tecnico[data.tecnico_id].marker = null;
                objMarker.tecnico[data.tecnico_id].tpathMrk = [];
                iconx++;

                $("#slct_tecnico_mapa").append(
                        "<option value='" +
                        data.tecnico_id +
                        "'>" +
                        data.carnet_tmp +
                        " - " +
                        data.tecnico +
                        "</option>"
                        );

                if (iconx == iconos.length)
                {
                    iconx = 0;
                }

                if (coordTec !== false)
                {
                    var ultima = data.ultima.split(" ");
                    var last_time = "";
                    if (ultima[1] != null)
                    {
                        last_time = ultima[1];
                    }

                    var ctArray = coordTec.split(",");
                    //repMarker = new google.maps.Marker({
                    repMarker = new MarkerWithLabel({
                        position: new google.maps.LatLng(ctArray[0], ctArray[1]),
                        icon: "img/icons/visorgps/tec_" +
                             objMarker.tecnico[data.tecnico_id].icono,
                        map: objRepMap,
                        title: "",
                        zIndex: zIndexRep++,
                        labelContent: data.carnet_tmp + " " + last_time,
                        labelAnchor: new google.maps.Point(22, 0),
                        labelClass: "markerLabel",
                        labelStyle: {opacity: 0.85}
                    });

                    mapRepMarker["t" + data.tecnico_id] = repMarker;

                    bounds.extend(new google.maps.LatLng(ctArray[0], ctArray[1]));

                    objMarker.tecnico[data.tecnico_id].marker = repMarker;

                    infocontent = "<div class=\"box box-default\">" +
                                  "    <div class=\"box-header with-border\">" +
                                  "        <h5 class=\"box-title\">" + data.tecnico + "</h5>" +
                                  "        <div class=\"box-tools pull-right\">" +
                                  "            <button class=\"btn btn-box-tool\" data-widget=\"remove\" onclick=\"closeInfoWindow()\"><i class=\"fa fa-times\"></i></button>" +
                                  "        </div>" +
                                  "    </div>" +
                                  "    <div class=\"box-body\">" +
                                  "        <div class=\"row\">" +
                                  "            &nbsp;&nbsp;&nbsp;&nbsp;Última ubicación: " + data.ultima + "" +
                                  "        </div>" +
                                  "        <div class=\"row\">" +
                                  "<a class=\"btn btn-app\" onclick=\"Tecnicos.rutaTecnico('" + data.carnet_tmp + "', '00-00-0000', '" + tecColor + "', " + data.tecnico_id + ")\">" +
                                  "<i class=\"fa fa-car\"></i> Ruta Tecnico" +
                                  "</a>" +
                                  "<a class=\"btn btn-app\" onclick=\"mapRutaActu(" + data.tecnico_id + ")\">" +
                                  "<i class=\"fa fa-home\"></i> Ruta trabajo" +
                                  "</a>" +
                                  "<a class=\"btn btn-app\" href=\"tel:" + data.celular + "\">" +
                                  "<i class=\"fa fa-phone\"></i> Llamar" +
                                  "</a>" +
                                  "        </div>" +
                                  "    </div>" +
                                  "</div>";


                    doInfoWindow(objRepMap, repMarker, infocontent);
                }
            }

            //Colores de marcadores y texto
            tecColor = objMarker.tecnico[data.tecnico_id].icono.substring(0, 6);
            txtTecColor = textByColor("#"+tecColor);
            txtTecColor = txtTecColor.substring(1);

            //Rutas de actuaciones por horario
            var pathTrabajo = [];
            var coordTrabajo = [];
            for (var i = 1; i <= fechacabecera.length; i++) {
                for (var j = 1; j <= horacabecera.length; j++) {
                    if (data["xy" + j + "_" + i] != null)
                    {
                        coordTrabajo = data["xy" + j + "_" + i].split(",");
                        pathTrabajo.push(
                                new google.maps.LatLng(
                                        coordTrabajo[0], coordTrabajo[1]
                                        )
                                );
                    }

                }
            }
            var trabajoPath = new google.maps.Polyline({
                path: pathTrabajo,
                geodesic: true,
                strokeColor: '#' + tecColor,
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            objMarker.tecnico[data.tecnico_id].opath = trabajoPath;

            //Marcadores Ordenes
            var os = data.xyfinales.split("|");
            var strCoord = "";
            var strOrden = "";
            var strEstLg = "";
            var intOrden = 0;
            for (var x = 0; x < os.length; x++) {

                strOrden = os[x].split("_")[0];
                strCoord = os[x].split("_")[1];
                strEstLg = os[x].split("_")[2];

                objMarker.ordenes[os[x].split("_")[0]] = {};
                objMarker.ordenes[os[x].split("_")[0]].xy = strCoord;
                objMarker.ordenes[os[x].split("_")[0]].tec = data.tecnico_id;
                objMarker.ordenes[os[x].split("_")[0]].ico =
                         objMarker.tecnico[data.tecnico_id].icono;
                objMarker.ordenes[os[x].split("_")[0]].num = 0;
                objMarker.ordenes[os[x].split("_")[0]].ini = null;
                objMarker.ordenes[os[x].split("_")[0]].iniend = null;
                objMarker.ordenes[os[x].split("_")[0]].tecagd = null;

                //Secuencia de ordenes
                for (var i = 1; i <= fechacabecera.length; i++) {
                    for (var j = 1; j <= horacabecera.length; j++) {
                        if (data["a" + j + "_" + i] != null)
                        {
                            if (os[x].split("_")[0]==data["a" + j + "_" + i])
                            {
                                intOrden = j;
                            }
                        }

                    }
                }

                var coArray = strCoord.split(",");

                //objMarker.tecnico[data.tecnico_id]["marker"]

                //repMarker = new google.maps.Marker({
                repMarker = new MarkerWithLabel({
                    position: new google.maps.LatLng(coArray[0], coArray[1]),
                    //icon: "img/icons/visorgps/cal_"
                    //        + objMarker.ordenes[os[x].split("_")[0]]["ico"],
                    icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + (x+1) + "|" + tecColor + "|" + txtTecColor,
                    map: objRepMap,
                    title: "",
                    zIndex: zIndexRep++,
                    actu: os[x].split("_")[0],
                    tecnico: data.tecnico_id,
                    labelContent: strEstLg.substring(0, 3) + ".",
                    labelAnchor: new google.maps.Point(22, 0),
                    labelClass: "markerLabel",
                    labelStyle: {opacity: 0.85}
                });

                mapRepMarker["o" + os[x].split("_")[0]] = repMarker;

                bounds.extend(new google.maps.LatLng(coArray[0], coArray[1]));

                gestionarActu(repMarker, strOrden);

                //Distancia Tecnico agenda
                pathTrabajo = [];
                if (mapRepMarker["t" + data.tecnico_id] != null &&
                        mapRepMarker["o" + strOrden] != null)
                {
                    pathTrabajo.push(
                            mapRepMarker["t" + data.tecnico_id].position
                            );
                    pathTrabajo.push(mapRepMarker["o" + strOrden].position);

                    //Distancia del tecnico a la orden
                    var distancia = google.maps.geometry.spherical.computeDistanceBetween(
                        objMarker.tecnico[data.tecnico_id].marker.position,
                        repMarker.position
                    );
                    distancia = distancia.toFixed(2);

                    trabajoPath = new google.maps.Polyline({
                        path: pathTrabajo,
                        geodesic: true,
                        strokeColor: '#' + tecColor,
                        strokeOpacity: 1.0,
                        strokeWeight: 2,
                        tecnico: data.tecnico_id,
                        distancia: distancia + " m."
                    });

                    //Centro del polilyne Tecnico - Agenda
                    var pathBounds = new google.maps.LatLngBounds();
                    pathBounds.extend(
                        objMarker.tecnico[data.tecnico_id].marker.position
                    );
                    pathBounds.extend(repMarker.position);

                    var noMarker = new MarkerWithLabel({
                        position: pathBounds.getCenter(),
                        icon: 'img/icons/transparent.gif',
                        map: null,
                        title: "",
                        zIndex: zIndexRep++,
                        actu: os[x].split("_")[0],
                        tecnico: data.tecnico_id,
                        labelContent: distancia + " m.",
                        labelAnchor: new google.maps.Point(22, 0),
                        labelClass: "markerLabel",
                        labelStyle: {opacity: 0.85}
                    });

                    //objMarker.ordenes[strOrden]["tecagd"] = trabajoPath;
                    objMarker.tecnico[data.tecnico_id].tecagd[strOrden] = trabajoPath;
                    objMarker.tecnico[data.tecnico_id].tecagd[strOrden].dis = noMarker;

                    mostrarViaTecAgd(
                        noMarker,
                        objMarker.tecnico[data.tecnico_id].marker.position,
                        repMarker.position
                    );
                }

            }

            //Marcadores Ordenes inicios
            var osi = data.xyinicios.split("|");
            strCoord = "";
            strOrden = "";
            for (var x = 0; x < osi.length; x++) {
                pathTrabajo = [];
                strOrden = osi[x].split("_")[0];
                strCoord = osi[x].split("_")[1];

                var coArray = strCoord.split(",");
                var hoy = getFechaActual('yyyy-mm-dd');
                var ini_hoy = coArray[2].substring(0, 10);
                var bool_hoy = true;
                if (hoy != ini_hoy)
                {
                    bool_hoy = false;
                }

                if ($.isNumeric(coArray[0]) && 
                        $.isNumeric(coArray[1]) && 
                        coArray[2] != null && 
                        bool_hoy)
                {
                    repMarker = new MarkerWithLabel({
                        position: new google.maps.LatLng(coArray[0], coArray[1]),
                        icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + (x+1) + "i|" + tecColor + "|" + txtTecColor,
                        map: null,
                        title: "",
                        zIndex: zIndexRep++,
                        actu: osi[x].split("_")[0],
                        tecnico: data.tecnico_id,
                        labelContent: coArray[2],
                        labelAnchor: new google.maps.Point(22, 0),
                        labelClass: "markerLabel",
                        labelStyle: {opacity: 0.85}
                    });

                    objMarker.ordenes[osi[x].split("_")[0]].ini = repMarker;

                    bounds.extend(
                            new google.maps.LatLng(coArray[0], coArray[1])
                            );

                    //Path entre inicio y agenda
                    if(mapRepMarker["o" + strOrden] != null)
                    {
                        pathTrabajo.push(repMarker.position);
                        pathTrabajo.push(mapRepMarker["o" + strOrden].position);

                        trabajoPath = new google.maps.Polyline({
                            path: pathTrabajo,
                            geodesic: true,
                            strokeColor: '#' + tecColor,
                            strokeOpacity: 1.0,
                            strokeWeight: 2
                        });
                        objMarker.ordenes[strOrden].iniend = trabajoPath;

                        //Distancia inicio y agenda
                        var distancia = google.maps.geometry.spherical.computeDistanceBetween(
                            mapRepMarker["o" + strOrden].position,
                            repMarker.position
                        );
                        distancia = distancia.toFixed(2);

                        trabajoPath = new google.maps.Polyline({
                            path: pathTrabajo,
                            geodesic: true,
                            strokeColor: '#' + tecColor,
                            strokeOpacity: 1.0,
                            strokeWeight: 2,
                            tecnico: data.tecnico_id,
                            distancia: distancia + " m."
                        });

                        //Centro del polilyne Tecnico - Agenda
                        var pathBounds = new google.maps.LatLngBounds();
                        pathBounds.extend(
                            mapRepMarker["o" + strOrden].position
                        );
                        pathBounds.extend(repMarker.position);

                        var noMarker = new MarkerWithLabel({
                            position: pathBounds.getCenter(),
                            icon: 'img/icons/transparent.gif',
                            map: null,
                            title: "",
                            zIndex: zIndexRep++,
                            actu: os[x].split("_")[0],
                            tecnico: data.tecnico_id,
                            labelContent: distancia + " m.",
                            labelAnchor: new google.maps.Point(22, 0),
                            labelClass: "markerLabel",
                            labelStyle: {opacity: 0.85}
                        });

                        objMarker.ordenes[strOrden].disiniend = noMarker;
                    }
                }
            }

            if (data.total == null || data.total === '' || data.total == 0) {
                data.total = 0;
            }
            else {
                data.total = "<b id='tdb" + contadordet + "_0_0' data-codactu='" + data.ids + "'>" + data.total + "</b>";
            }
            //codactuModal
            html += "<td onClick='activadetalle(" + contadordet + ",0,0);'>" + data.total + "</td>";
            html += "</tr>";

        });

        $("#t_reporte tbody").html(html);
        $("#t_reporte").dataTable();

        CambiaVisibilidad(2);
        /*console.log(horacabecera.length);
         //$.each(datos, function(id, val){
         for (var i=1; i<=horacabecera.length; i++) {
         for (var j=1; j<=fechacabecera.length; j++) {
         var prop = 'a'+i+'_'+j;
         //console.log('a'+i+'_'+j + " : " + val[prop]);
         $.each(datos, function(id, val){
         console.log('a'+i+'_'+j + " : " + val[prop]);
         });
         }
         }*/
        //});

        //Filtros
        $("#slct_tecnico_mapa").multiselect('selectAll');
        $("#slct_tecnico_mapa").multiselect('rebuild');
        $("#slct_tecnico_mapa").multiselect('refresh');

        //Mapa con Marcadores
        objRepMap.fitBounds(bounds);
    };

    activadetalle = function(c, i, j) {
        var valor = $("#tdb" + c + "_" + i + "_" + j).attr("data-codactu");
        //alert(valor);
        $("#eventocodactu").remove();
        $("#form_tecnico_programado").append('<input type="hidden" id="eventocodactu" data-alert="<?= rand(1, 100); ?>" data-toggle="modal" data-target="#codactuModal" data-codactu="' + valor + '">');
        if ($.trim(valor) !== '') {
            $("#eventocodactu").click();
        }
    };

    /**
     * Muestra tecnicos y ordenes en el mapa
     *
     * @param {type} obj
     * @param {type} idtec
     * @param {type} estado
     * @returns {undefined}
     */
    tecOrdMap = function(obj, idtec, estado) {
        //Ocultar todo
        if (typeof obj == 'undefined' && typeof idtec == 'undefined')
        {
            $.each(mapRepMarker, function(idx, valx) {
                if (estado == false)
                {
                    valx.setMap(null);
                } else {
                    valx.setMap(objRepMap);
                }
            });
        }

        //Mostrar un tecnico
        if (obj != 'undefined' && idtec > 0)
        {
            $.each(mapRepMarker, function(idx, valx) {
                if (estado == false && idx == 't' + idtec)
                {
                    valx.setMap(null);
                }

                if (estado == true && idx == 't' + idtec)
                {
                    valx.setMap(objRepMap);
                }

                if (valx.tecnico != null && valx.tecnico == idtec)
                {
                    if (estado == false)
                    {
                        valx.setMap(null);
                    } else {
                        valx.setMap(objRepMap);
                    }
                }
            });
        }
    };

    /**
     * Muestra la ventana de gestion de una
     * orden en el mapa
     *
     * @param {type} element
     * @param {type} actu
     */
    function gestionarActu(element, actu) {
        google.maps.event.addListener(element, 'click', (
                function() {
                    return function() {
                        $("#eventocodactu").remove();
                        $("#form_tecnico_programado").append('<input type="hidden" id="eventocodactu" data-alert="<?= rand(1, 100); ?>" data-toggle="modal" data-target="#codactuModal" data-codactu="' + actu + '">');
                        if ($.trim(actu) !== '') {
                            $("#eventocodactu").click();
                        }
                    };
                })(element, actu));
    }

    mapRutaTecnico = function(carnet, fecha) {
        console.log("Tec." + idtec);
    };

    mapRutaActu = function(idtec) {
        var mapa = objMarker.tecnico[idtec].opath.map;
        if (mapa == null)
        {
            objMarker.tecnico[idtec].opath.setMap(objRepMap);
        } else {
            objMarker.tecnico[idtec].opath.setMap(null);
        }
    };

    rutaDiaTecnico = function(data, code, color, thisMap, tecnico_id) {
        if (data.data.length === 0)
        {
            alert("No se encontro ruta para el tecnico seleccionado.");
            return false;
        }

        if (data.estado === true)
        {
            var x;
            var y;
            var n = 1;
            var markerIcon;
            tecPath = [];

            /**
             * Bounds para un solo elemento: path, marker, etc.
             */
            var pathMarker;
            var pathContent = "";
            var boundsElement = new google.maps.LatLngBounds();

            $.each(data.data, function() {
                x = Number(this.X.replace(",", "."));
                y = Number(this.Y.replace(",", "."));
                myLatlng = new google.maps.LatLng(y, x);

                tecPath.push(myLatlng);

                boundsElement.extend(myLatlng);

                //Marcador de inicio
                if (n === 1)
                {
                    markerIcon = "http://chart.apis.google.com/chart" +
                                 "?chst=d_map_pin_letter&chld=1|" +
                                 color +
                                 "|" +
                                 textByColor("#" + color).substring(1, 7);
                } else {
                    markerIcon = "img/icons/Marker-Ball-Pink.png";
                }

                //Marcador
                pathMarker = new google.maps.Marker({
                    position: myLatlng,
                    map: thisMap,
                    title: this.EmployeeNum,
                    icon: markerIcon,
                    zIndex: zIndex++
                });
                tecCode = this.EmployeeNum;

                objMarker.tecnico[tecnico_id].tpathMrk.push(pathMarker);

                //Contenido + Infowindow
                pathContent = this.EmployeeNum +
                              "<br>" +
                              this.t +
                              "<br>" +
                              this.Battery;

                doInfoWindow(thisMap, pathMarker, pathContent);

                n++;
            });
            thisMap.fitBounds(boundsElement);

            //Dibujar Path
            drawPath = new google.maps.Polyline({
                path: tecPath,
                geodesic: true,
                strokeColor: '#' + color,
                strokeOpacity: 1.0,
                strokeWeight: 3
            });
            tecCode = this.EmployeeNum;

            //Agregar path del tecnico
            objMarker.tecnico[tecnico_id].tpath = drawPath;

            //Mostrar path del tecnico
            drawPath.setMap(thisMap);
        } else {
            console.log("Error");
        }
    };

    doNotPath = function(code) {
        try {
            //Elimina ruta de un tecnico
            $.each(mapObjects[code], function() {
                this.setMap(null);
            });
        } catch (e) {
            console.log(e);
        }
    };

    mostrarTecAgd = function(obj) {
        var lista = $("#slct_tecnico_mapa").val();
        var accion;

        //Solo si existen tecnicos seleccionados
        if (lista != null && lista.length > 0)
        {
            var btnClass = $("#btn_solo_" + obj).attr("class");

            if (btnClass === 'btn btn-default')
            {
                accion = null;
                $("#btn_solo_" + obj).removeClass("btn btn-default");
                $("#btn_solo_" + obj).addClass("btn btn-success");
            } else {
                accion = objRepMap;
                $("#btn_solo_" + obj).removeClass("btn btn-success");
                $("#btn_solo_" + obj).addClass("btn btn-default");
            }

            //ocultar tecnicos
            if (obj == 'agd')
            {
                $.each(lista, function(id, val) {
                    $.each(mapRepMarker, function(idObj, valObj) {
                        if ('t' + val == idObj)
                        {
                            valObj.setMap(accion);
                        }
                    });
                });
            }

            //ocultar agendas
            if (obj == 'tec')
            {
                $.each(lista, function(id, val) {
                    $.each(mapRepMarker, function(idObj, valObj) {
                        if (valObj.tecnico != null && valObj.tecnico == val)
                        {
                            valObj.setMap(accion);
                        }
                    });
                });
            }

        } else {
            $("#btn_solo_agd").removeClass("btn btn-success");
            $("#btn_solo_tec").removeClass("btn btn-success");
            $("#btn_solo_agd").addClass("btn btn-default");
            $("#btn_solo_tec").addClass("btn btn-default");
        }
    };

    /**
     * Redimensiona solo el mapa
     * @returns {Boolean}
     */
    function mapResize() {
        google.maps.event.trigger(objMap, 'resize');
        return true;
    }

    /**
     * Muestra infowindow para un marcador
     *
     * @param {type} thisMap Mapa donde se mostrar marcador
     * @param {type} element Marcador (marker)
     * @param {type} content Contenido (html) del infowindow
     */
    function doInfoWindow(thisMap, element, content) {
        google.maps.event.addListener(element, 'click', (
                function(marker, infocontent, infowindow) {
                    return function() {
                        infowindow.setContent(infocontent);
                        infowindow.open(thisMap, marker);
                    };
                })(element, content, infowindow));
    }

    /**
     * Cerrar InfoWindow
     * @param {type} thisMap
     * @returns {undefined}     */
    function closeInfoWindow() {
        infowindow.close();
    }

    function mostrarPathAgd(){

        var lista = $("#slct_tecnico_mapa").val();
        var accion;

        //Solo si existen tecnicos seleccionados
        if (lista != null && lista.length > 0)
        {
            var btnClass = $("#btn_ruta_agd").attr("class");

            if (btnClass === 'btn btn-default')
            {
                accion = objRepMap;
                $("#btn_ruta_agd").removeClass("btn btn-default");
                $("#btn_ruta_agd").addClass("btn btn-success");
            } else {
                accion = null;
                $("#btn_ruta_agd").removeClass("btn btn-success");
                $("#btn_ruta_agd").addClass("btn btn-default");
            }

            //Mostrar u ocultar
            $.each(objMarker.tecnico, function(idx, valx){
                objMarker.tecnico[idx].opath.setMap(accion);
            });
        }


    }

    function clearMapTecPrg() {
        //Objetos del mapa
        $.each(mapRepMarker, function(idObj, valObj) {
            valObj.setMap(null);
        });

        //Marcador de ruta
        $.each(objMarker.tecnico, function (idx, valx){
            if (objMarker.tecnico[idx].tpathMrk.length > 0)
            {
                $.each(objMarker.tecnico[idx].tpathMrk, function(){
                    this.setMap(null);
                });
                //objMarker.tecnico[idx]["tpathMrk"].setMap(null);
            }
        });

        //Rutas
        $.each(objMarker.tecnico, function (idx, valx){
            objMarker.tecnico[idx].opath.setMap(null);
            if (objMarker.tecnico[idx].tpath != null)
            {
                objMarker.tecnico[idx].tpath.setMap(null);
            }
        });

        //Recta Tecnico - Agenda
        $.each(objMarker.tecnico, function (idt, obj){
            $.each(objMarker.tecnico[idt].tecagd, function(actu, path){
                path.setMap(null);
                if (path.dis != null)
                {
                    path.dis.setMap(null);
                }
            });
        });

        //Recta Inicio - Agenda
        $.each(objMarker.ordenes, function(idx, valx){

            if (objMarker.ordenes[idx].ini != null)
            {
                objMarker.ordenes[idx].ini.setMap(null);

                if (objMarker.ordenes[idx].iniend != null)
                {
                    objMarker.ordenes[idx].iniend.setMap(null);
                }

                if (objMarker.ordenes[idx].disiniend != null)
                {
                    objMarker.ordenes[idx].disiniend.setMap(null);
                }
            }
        });

        directionsDisplay.setMap(null);

        /*
        //Eliminar lista de tecnicos
        $("#slct_tecnico_mapa").empty();
        $("#slct_tecnico_mapa").multiselect('rebuild');
        $("#slct_tecnico_mapa").multiselect('refresh');
        */
        /*
        //Botones
        $("#btn_ruta_agd").removeClass("btn btn-success");
        $("#btn_solo_agd").removeClass("btn btn-success");
        $("#btn_solo_tec").removeClass("btn btn-success");
        $("#btn_ruta_agd").addClass("btn btn-default");
        $("#btn_solo_agd").addClass("btn btn-default");
        $("#btn_solo_tec").addClass("btn btn-default");
        */

        $("#btn_ruta_agd").attr("class","btn btn-default");
        $("#btn_solo_agd").attr("class","btn btn-default");
        $("#btn_solo_tec").attr("class","btn btn-default");
        $("#btn_ini_agd").attr("class","btn btn-default");
        $("#btn_tec_agd").attr("class","btn btn-default");
    }

    /**
     * Tamaño de un objeto Array (obj)
     *
     * @param {type} obj
     * @returns {Number}
     */
    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key))
                size++;
        }
        return size;
    };

    /**
     *
     * Muestra los puntos de inicio
     * de cada orden y la ruta
     * hacia la agenda
     * @returns {undefined}
     * */
    function mostrarIniAgd(){
        var lista = $("#slct_tecnico_mapa").val();
        var accion;

        //Solo si existen tecnicos seleccionados
        if (lista != null && lista.length > 0)
        {
            var btnClass = $("#btn_ini_agd").attr("class");

            if (btnClass === 'btn btn-default')
            {
                accion = objRepMap;
                $("#btn_ini_agd").removeClass("btn btn-default");
                $("#btn_ini_agd").addClass("btn btn-success");
            } else {
                accion = null;
                $("#btn_ini_agd").removeClass("btn btn-success");
                $("#btn_ini_agd").addClass("btn btn-default");
            }

            //Mostrar u ocultar
            $.each(objMarker.ordenes, function(idx, valx){

                if (objMarker.ordenes[idx].ini != null && $.inArray(objMarker.ordenes[idx].ini.tecnico, lista) != -1)
                {
                    objMarker.ordenes[idx].ini.setMap(accion);

                    if (objMarker.ordenes[idx].iniend != null)
                    {
                        objMarker.ordenes[idx].iniend.setMap(accion);
                    }

                    if (objMarker.ordenes[idx].disiniend != null)
                    {
                        objMarker.ordenes[idx].disiniend.setMap(accion);
                    }
                }
            });

        }
    }

    function mostrarRutaTecAgd(){
        var lista = $("#slct_tecnico_mapa").val();
        var accion;

        //Solo si existen tecnicos seleccionados
        if (lista != null && lista.length > 0)
        {
            var btnClass = $("#btn_tec_agd").attr("class");

            if (btnClass === 'btn btn-default')
            {
                accion = objRepMap;
                $("#btn_tec_agd").removeClass("btn btn-default");
                $("#btn_tec_agd").addClass("btn btn-success");
            } else {
                accion = null;
                $("#btn_tec_agd").removeClass("btn btn-success");
                $("#btn_tec_agd").addClass("btn btn-default");
            }

            //Mostrar u ocultar
            $.each(objMarker.tecnico, function (idt, obj){
                if ($.inArray(idt, lista) != -1)
                {
                    //objMarker.ordenes[idx]["iniend"].setMap(accion);
                    $.each(objMarker.tecnico[idt].tecagd, function(actu, path){
                        path.setMap(accion);
                        if (path.dis != null)
                        {
                            path.dis.setMap(accion);
                        }
                    });
                }
            });

        }
    }

    /**
     * Muestra la ruta por donde
     * debe ir el técnico para
     * llegar a la orden.
     *
     * @param object element el maercador (transparente)
     * @param {type} latlng_ini LatLng inicio
     * @param {type} latlng_end LatLng destino
     * @returns {undefined}     */
    function mostrarViaTecAgd(element, latlng_ini, latlng_end){

        google.maps.event.addListener(element, 'click', (
            function() {
                return function() {

                    var start = latlng_ini;
                    var end = latlng_end;
                    var request = {
                        origin:start,
                        destination:end,
                        travelMode: google.maps.TravelMode.DRIVING
                    };
                    directionsService.route(request, function(result, status) {
                        if (status == google.maps.DirectionsStatus.OK)
                        {
                            directionsDisplay.setDirections(result);
                            directionsDisplay.setMap(objRepMap);
                        }
                    });


                };
            })(element, latlng_ini, latlng_end));
    }
</script>
