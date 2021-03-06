<script type="text/javascript">
//DropDown elements
    var objList = ['zonal', 'actividad', 'estado'];

    /**
     * Ubicaciones actuales (finales) de tecnicos como Google Markers
     * @type Array|@exp;marker
     */
    var tecMark = {};

    /**
     * Lista de actuaciones por tecnico
     * @type Array
     */
    var tecActu = [];

    /**
     * Lista de actuaciones temporales, sin gestión
     * @type Array
     */
    var tmpActu = [];

    /**
     * Mostrar trafico en Google Maps
     * 
     * @type google.maps.TrafficLayer
     */
    var trafficLayer;

    /**
     * Ruta de un tecnico segun su ubicacion
     * @type Array
     */
    var tecPath = [];

    /**
     * Coleccion de todos los elementos del mapa
     * @type Array
     */
    var mapObjects = {};

    /**
     * Mostrar herramientas de dibujo Google Maps
     * 
     * @type Boolean
     */
    var mapTools = {draw: true};

    /**
     * Objetos temporales
     * @type @exp;val@pro;icon|@exp;val@pro;icon
     */
    var tmpObject = {};

    /**
     * Horarios de acuerdo a quiebre
     * @type Object
     */
    var horarioGeoPlan = {};

    $(document).ready(function() {

        $("#btnupfile").trigger("click");

        $("[data-toggle='offcanvas']").click();

        // Variables
        var objMain = $('#main');

        // Show sidebar
        function showSidebar() {
            objMain.addClass('use-sidebar');
            mapResize();
        }

        // Hide sidebar
        function hideSidebar() {
            objMain.removeClass('use-sidebar');
            mapResize();
        }

        // Sidebar separator
        var objSeparator = $('#separator');

        objSeparator.click(function(e) {
            e.preventDefault();
            if (objMain.hasClass('use-sidebar')) {
                hideSidebar();
            }
            else {
                showSidebar();
            }
        }).css('height', objSeparator.parent().outerHeight() + 'px');

        //Iniciar mapa
        objMap = doObjMap("mymap", objMapProps, mapTools);

        /**
         * Elementos del formulario
         */

        //Valores de listas
        Visorgps.ListarSlct('actividad');
        //Visorgps.ListarSlct('empresa');
        Visorgps.ListarSlct('estado');
        var ids = []; //para seleccionar un id
        var data = {usuario: 1};
        slctGlobal.listarSlct('quiebre','slct_quiebre','multiple',ids,data);
        slctGlobal.listarSlct('empresa', 'slct_empresa', 'simple', ids, data);
        //Visorgps.ListarSlct('quiebre');
        //Visorgps.ListarSlct('celula');

       /* $('#fecha_agenda').datepicker({ 
            dateFormat: 'dd/mm/yy'
        }); */    
        $('#fecha_agenda').daterangepicker(
        {
            format: 'YYYY-MM-DD'
        }
        );   
        $(".ui-datepicker").css("background-color", "#FFFFFF");

        //DropDown elements multiselect        
        $.each(objList, function(index, value) {

            $("#slct_" + value).multiselect({
                maxHeight: 200, // max altura...
                enableFiltering: true,
                includeSelectAllOption: true,
                buttonContainer: '<div class="btn-group col-xxs-12" />', // actualiza la clase del grupo
                buttonClass: 'btn btn-primary col-xxs-12', // clase boton
                templates: {
                    ul: '<ul class="multiselect-container dropdown-menu col-xxs-12"></ul>',
                },
                buttonText: function(options, select) {
                    if (options.length === 0) {
                        return select[0].id.substring(5);
                    } else if (options.length > 2) {
                        return options.length + ' Seleccionados';//More than 3 options selected!
                    } else {
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
                },
                onDropdownHide: function(event) {
                    /**
                     * Multiselect dependientes
                     */
                    var arrSelected = [];

                    //01. Empresa to Celula                    
                    //if (value === 'empresa')
                    //{
                    //    $("#slct_celula").multiselect('deselectAll', false);
                    //    arrSelected.push($("#slct_" + value).val());
                    //    isRelatedTo("celula", arrSelected);
                    //}
                }
            });
            
        });

        /**
         * Eventos botones
         */

        //Visor GPS
        $("#btn_buscar").click(function() {
            //Validar
            if ( $.trim( $("#slct_quiebre").val() ) == '' ) {
                alert("Seleccione quiebre");
                return false;
            }
            if ( $.trim( $("#slct_empresa").val() ) == '' ) {
                alert("Seleccione Empresa");
                return false;
            }
            if ( $.trim( $("#slct_celula").val() ) == '' ) {
                alert("Seleccione Celula");
                return false;
            }
            if ( $.trim( $("#slct_actividad").val() ) == '' ) {
                alert("Seleccione Actividadd");
                return false;
            }
            
            $("#btn_limpiar_todo").click();
            Geoplan.PanelCelulaTecnico();
            //Geoplan.HorarioGeoPlan();
        });

        //Limpiar mapa de temporales
        $("#btn_limpiar_tmp").click(function() {
            limpiarTmpActu();
        });

        $("#btn_limpiar_todo").click(function() {
            limpiarTmpActu();
            clearMap();
        });

        //Mostrar trafico
        $("#show_traffic").click(function() {
            mostrarTrafico(objMap);
        });

        //Mostrar, ocultar tecnicos
        $("#btn_show_tec").click(function() {
            showHideAll(objMap);
        });

        //Mostrar actuaciones pendientes BOUNCE
        $("#btn_show_pdt").click(function() {
            showBounce();
        });

        //Mostrar actuaciones coordinadas BOUNCE
        $("#btn_show_coo").click(function() {
            showCooBounce();
        });

        $("#myModal").draggable({
            handle: ".modal-header"
        });

        $("#btnclosepla").click(function() {
           undoPlanPoly();
        });

        $("#btn_trazar").click(function() {

            if ( $.trim( $("#slct_nodo").val() ) == '' ) {
                alert("Seleccione almenos un MDF o Nodo ");
                return false;
            }
            else {
            Geoplan.trazarPolygon(objMap);
            }
            
            /*objMap = new google.maps.Map(document.getElementById('mymap'), {
                zoom: 5,
                center: {lat: 24.886, lng: -70.268},
                mapTypeId: google.maps.MapTypeId.TERRAIN
            });*/
            // Define the LatLng coordinates for the polygon.

             /*
              var triangleCoords = [
                  {lat: -12.20565923, lng: -76.94021931},
                  {lat: -12.02159, lng: -76.96724},
                  {lat: -12.03574, lng: -76.95042},
                  {lat: -12.18737711, lng: -76.92598256}
              ];
            
              // Construct the polygon.
              var bermudaTriangle = new google.maps.Polygon({
                paths: triangleCoords,
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 3,
                fillColor: '#FF0000',
                fillOpacity: 0.35
              });
              bermudaTriangle.setMap(objMap);

              bermudaTriangle.addListener('click', showArrays); */
 
        });

    });

    function showArrays(event) {

        doPolyAction(event, this);

       }

    /**
     * Genera child dropdown en base a valores seleccionados
     * Usa bootstrap multiselect
     * 
     * @param {type} subject
     * @param {type} parentValues
     * @returns {undefined}
     */

    function listtiponodo(){
        var tiposel=$("#slct_tipo").val();
        var ids = [];
        var data = {tipo: tiposel, modulo:'geoplan'};
        if (tiposel!=0) {
            $("#divselect").html('');
            var _html="<select class='form-control' name='slct_nodo[]' id='slct_nodo' onchange='' multiple='multiple' ></select>";
            $("#divselect").html(_html);
    
            if (tiposel=='mdf') {
                slctGlobal.listarSlct('mdf','slct_nodo','multiple',null,data);
            }
            if (tiposel=='nodo') {
                slctGlobal.listarSlct('nodo','slct_nodo','multiple',null,data);
            }
        }

    }

    function isRelatedTo(subject, parentValues) {
        $("#slct_" + subject + " option").prop("disabled", true);
        //$("#slct_celula option").hide();

        var optValues = $("#slct_" + subject + ">option").map(
                function() {
                    return $(this).val();
                }
        );

        if (parentValues !== null)
        {
            $.each(parentValues, function(idxEmp, valOrg) {
                $.each(optValues, function(idxOpt, valOpt) {

                    var element = $("#slct_" + subject + " option[value='" + valOpt + "']");
                    var sptVal = valOpt.split("_");

                    if ('E' + valOrg == sptVal[0])
                    {
                        element.prop("disabled", false);
                        //element.show();
                    }
                });
            });
        } else {
            $("#slct_" + subject + " option").prop("disabled", false);
        }

        $("#slct_" + subject).html($("#slct_" + subject + " option").sort(function(x, y) {
            return $(x).prop("disabled") ? 1 : -1;
        }));

        $("#slct_" + subject).multiselect('rebuild');
        $("#slct_" + subject).multiselect('refresh');
    }

    function batteryIcon(level) {
        var icon = "";

        if (level >= 0 && level < 10)
        {
            icon = "0";
        } else if (level >= 10 && level < 20)
        {
            icon = "10";
        } else if (level >= 20 && level < 40)
        {
            icon = "20";
        } else if (level >= 40 && level < 60)
        {
            icon = "40";
        } else if (level >= 60 && level < 80)
        {
            icon = "60";
        } else if (level >= 80 && level < 90)
        {
            icon = "80";
        } else if (level >= 90 && level <= 100)
        {
            icon = "100";
        } else {
            icon = "0";
        }

        icon = "<img alt=\"" + level + "%\" title=\"" + level + "%\" src=\"img/battery/battery_"
                + icon
                + "percent.png\" style=\"height:24px; vertical-align:middle\">";

        return icon;
    }


    function showHideTec(code, show) {

        //Tecnicos en mapa
        if (typeof tecMark[code] !== 'undefined')
        {
            if (!show)
            {
                //Ocultar tecnico
                tecMark[code].setMap(null);
            } else {
                //Mostrar tecnicos
                tecMark[code].setMap(objMap);
            }
        }

        //Agendas en mapa
        if (typeof tecActu[code] !== 'undefined')
        {
            $.each(tecActu[code], function(id, val) {

                if (typeof val === 'object')
                {
                    if (!show)
                    {
                        //Ocultar agendas
                        this.setMap(null);
                    } else {
                        //Mostrar agendas
                        this.setMap(objMap);
                    }
                }

            });
        }

    }

    /**
     * Limpia (oculta) todos elementos del mapa
     * @returns {Boolean}
     */
    function clearMap() {
        try {
            //Elimina ultima ubicacion tecnicos
            $.each(tecMark, function() {
                this.setMap(null);
            });

            //Elimina localizacion de actuaciones
            for (key in tecActu) {
                if (tecActu.hasOwnProperty(key)) {
                    $.each(tecActu[key], function() {
                        this.setMap(null);
                    });
                }
            }

            //Elimina localizacion de actuaciones temporales
            $.each(tmpActu, function() {
                this.setMap(null);
            });

            //Elimina ruta de tecnicos
            //$.each(mapObjects, function(id, obj) {
            //    doNotPath(id);
            //});

            //Variables en blanco
            actuPath = [];
            tecData = [];
            tecActu = [];
            tmpActu = [];
            tecMark = {};
            tecList = [];
            tecPath = [];
            zIndex = 1;
            //mapObjects = {};
            bounds = new google.maps.LatLngBounds();

            //initialize();
            objMap = doObjMap("mymap", objMapProps, mapTools);

            return true;
        } catch (e) {
            console.log(e);
        }
    }


    function doTecList(teclist, icons) {
        try {
            var n = 0;
            var carnet;
            var tecnico = "";
            var color;
            var batteryLevel = 0;
            var htmlTecList = "";
            var tecLatLng;

            $.each(teclist, function() {

                n++;
                carnet = this.carnet_tmp;
                /*
                 if (typeof mapObjects[carnet] === "undefined") {
                 mapObjects[carnet] = new Array();
                 }
                 */

                //Nivel de bateria del tecnico
                var batteryLevel = Number(this.battery);
                var tec_phone = this.phone.substr(-9)
                //var tec_lastUpdate = this.t.substr(-8)
                var tec_x = this.x.replace(",", ".")
                var tec_y = this.y.replace(",", ".")
                var tec_fecha=this.tiempo

                var go_gmap = "";
                tecnico = this.carnet + " / " + this.nombre_tecnico;


                var link_ultima_liquida = " <a href=\"javascript:void(0)\" onclick=\"showLastLiq('" + carnet + "')\">LIQ</a> ";
                /*
                 if(window.usuario_movil){
                 tecnico = this.carnet + " / " + "<a href=\"tel:"+tec_phone+"\">" + this.nombre_tecnico + "</a>"+" ";
                 if(tec_coory != "" && tec_coorx != ""){
                 go_gmap = " http://maps.google.com/maps?saddr="+usuario_movil_y+","+ usuario_movil_x +"&daddr="+ tec_coory + ","+ tec_coorx;
                 go_gmap = " <a class='go-map' href='"+go_gmap+"'> <img src='images/car.png' style=\"height: 24px; vertical-align: middle\"> </a>";
                 }
                 }
                 */

                //AGREGANDOP GRUPO POR CELULA

                var grupo_html = "";
                /*var celula = $("#celula").val();
                 var grupos = this.grupos.split(",")
                 
                 grupo_html += "<select idcel='"+celula+"' idtec='"+this.id+"' id='grupo-"+carnet+"' class='grupoCelula' carnet='"+carnet +"' multiple style='display:none;'>"
                 
                 for(var i=1; i<11;i++){
                 var existe = $.inArray(i+"",grupos); // existe tendra el indice del item del array
                 var selected =""
                 if(existe >-1){
                 selected = "selected='selected'";
                 }
                 grupo_html+= "<option value='"+i+"' "+selected+">Grupo "+i+"</option>";
                 }
                 
                 grupo_html +="</select>";
                 */

                var grupos_class = ""
                /*grupos.forEach(function(i){
                 grupos_class += " g-"+i
                 });
                 */
                
                if (icons[carnet] != null)
                {
                    color = icons[carnet].tec.substring(4, 10);

                    htmlTecList += "<div class=\"tecRow " + grupos_class + " \">"
                            + "<div>"
                            + "<span>" + n + ".</span>"
                            + "<span>" + tecnico + "</span>"
                            + grupo_html
                            + "</div>"
                            + "<div style=\"margin-left: 12px;\">"
                            + "<input type=\"checkbox\" value=\"" + carnet + "\" class=\"chb_tec\" checked>"
                            + "<a href=\"javascript:void(0)\" onclick=\"openInfoWin('" + carnet + "')\"><img src=\"img/icons/visorgps/" + icons[carnet].tec + "\" style=\"height: 24px; vertical-align: middle\" alt=\"Info Tecnico\" title=\"Info Tecnico\"></a>"
                            + "<a href=\"javascript:void(0)\" onclick=\"Visorgps.DoPath('" + carnet + "', '" + color + "')\"><img src=\"img/icons/visorgps/" + icons[carnet].car + "\" style=\"height: 24px; vertical-align: middle\" alt=\"Ruta Tecnico\" title=\"Ruta Tecnico\"></a>"
                            + "<a href=\"javascript:void(0)\" onclick=\"actuTecPath('" + carnet + "')\"><img src=\"img/icons/visorgps/" + icons[carnet].cal + "\" style=\"height: 24px; vertical-align: middle\" alt=\"Ruta Agendas\" title=\"Ruta Agendas\"></a>"
                            + batteryIcon(batteryLevel)
                            //+        "<span id=\"nagtec_" + carnet + "\">(00)</span>"  + tec_lastUpdate + go_gmap + link_ultima_liquida
                            + "</div>"
                            + "</div>"

                    /**
                     * Tecnicos en mapa
                     */
                    if (tec_x !== "" && tec_y !== "")
                    {
                        bounds.extend(new google.maps.LatLng(tec_y, tec_x));

                        //Color tecnico
                        //color = icons[this.EmployeeNum]['tec'].substring(4,10);

                        //Marcador
                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(tec_y, tec_x),
                            icon: "img/icons/visorgps/" + icons[carnet]['tec'],
                            map: objMap,
                            title: carnet,
                            zIndex: zIndex++
                        });

                        //DATA PARA VISOR PUBLICO MOVIL
                        var link_go = "";
                        var link_go_ocultar = "";
                        /*
                         if(window.usuario_movil){
                         link_go = " - <a href=\"javascript:void(0)\" onClick=\"GoToTecnico('"+x+"','"+y+"')\"> >> Go </a>";
                         link_go_ocultar = " - <a href=\"javascript:void(0)\" onClick=\"ocultarGoToTecnico()\"> Hide GoTo </a>";
                         }
                         */
                        //var link_ultima_liquida = " - <a href=\"javascript:void(0)\" onclick=\"showLastLiq('" + carnet_tecnico + "')\">LIQ</a> ";

                        //Detalle de ultima posicion
                        infocontent = "<table>"
                                + "<tr>"
                                + "    <td rowspan=\"6\"><img src=\"img/icons/tecnico.png\"></td>"
                                + "    <td></td>"
                                + "</tr>"
                                + "<tr>"
                                + "    <td>" + tec_fecha + "</td>"
                                + "</tr>"
                                //+ "<tr>"
                                // + "    <td>" + this.EmployeeNum + "</td>"
                                // + "</tr>"
                                + "<tr>"
                                + "    <td>" + "<a href=\"tel:" + tec_phone + "\">" + tec_phone + "</a></td>"
                                + "</tr>"
                                + "<tr>"
                                + "    <td>" + tecnico + "</td>"
                                + "</tr>"
                                + "<tr>"
                                //+ "    <td>" + this.t + "</td>"
                                + "</tr>"
                                //+ "<tr>"
                                //+ "    <td><a href=\"javascript:void(0)\" onClick=\"doPath('" 
                                //    + this.EmployeeNum + "', '" 
                                //    + color + "')\">Mostrar ruta</a> " + link_go + link_ultima_liquida
                                //+  "</td>"
                                //+ "</tr>"
                                + "<tr>"
                                + "    <td><a href=\"javascript:void(0)\" onClick=\"doNotPath('"
                                + this.EmployeeNum
                                + "')\">Ocultar ruta</a> " + link_go_ocultar + " </td>"
                                + "</tr>"
                                + "</table>";
                        //Crear infowindow de la ultima posicion
                        //doInfoWindow(marker, infocontent);
                        /*google.maps.event.addListener(marker, "click", function() {
                            infowindow.setPosition(new google.maps.LatLng(tec_y, tec_x));
                            infowindow.setContent(infocontent);
                            infowindow.open(self.objMap);
                        });*/
                        doInfoWindow(objMap, marker, infocontent);

                        //Agregar marcadores de tecnicos al objeto
                        if (typeof tecMark[carnet] === "undefined") {
                            tecMark[carnet] = new Array();
                        }
                        tecMark[carnet] = marker;
                    }
                }

            });
            $("#tec-list").html(htmlTecList);

            //Mostrar y marcar checkbox
            if (n >= 1)
            {
                $(".show_hide_tec").show();
                $("#show_tec").prop('checked', true);
            }

            //Class chb_tec
            $(".chb_tec").click(function() {
                showHideTec($(this).val(), $(this).prop("checked"));
            });

        } catch (e) {
            console.log(e);
        }
    }


    function doTecAgenda(data, icons) {

        tmpActu = [];
        tecActu = [];
        var agendaIcon = "";
        var suma=0;
        $.each(data, function() {

            var infocontent = "";

            //Carnet de tecnico
            var carnet = this.carnet_tmp;

            //Arreglo de actuaciones por tecnico
            if (typeof tecActu[carnet] === "undefined") {
                tecActu[carnet] = new Array();
            }

            //Agendas con XY (taps o terminales)
            if (this.x !== "" && this.y !== "")
            {
                myLatlng = new google.maps.LatLng(this.y, this.x);

                bounds.extend(myLatlng);

                $.each(icons, function(id, val) {
                    if (carnet === id)
                    {
                        agendaIcon = "img/icons/visorgps/" + val.cal;
                    }
                });

                //Contenido infowindow
                if (this.estado === 'Temporal')
                {
                    //Icono temporales
                    agendaIcon = "img/icons/tmp_actu.png"

                    infocontent = "<div class=\"infow\" style=\"width:300px; height:200px; overflow: auto; text-align: left\">" +
                            "<input type=\"button\" id=\"detalle_actu\" value=\"Mostrar/Ocultar detalle\" onclick=\"mostrarOcultarDetalle()\">" +
                            "<input type=\"button\" value=\"Cerrar\" onclick=\"closeInfoWindow()\">" +
                            "<div class=\"detalle_actu\">" +
                            "Tipo: " + this.tipoactu + "<br>" +
                            "Codigo: " + this.codactu + "<br>" +
                            "Horas: " + this.horas_actu + "<br>" +
                            "Fec. Registro: " + this.fecha_registro + "<br>" +
                            this.nombre_cliente + "<br>" +
                            this.direccion_instalacion + "<br>" +
                            this.fftt + "<br>" +
                            this.telefono + "<br>" +
                            this.x + " / " + this.y + "<br>" +
                            "</div>" +
                            //"<div><a href=\"javascript:void(0)\" onclick=\"gestionActuTmp('" + this.codactu  + "', '0', '" + tipo + "')\">Gestionar <img src=\"../historico/img/gestionar.png\" style=\"vertical-align: middle\"></a>&nbsp;" + 
                            "<div class=\"detalle_gestion\"></div>" +
                            "Gestionar <a class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#bandejaModal\" data-codactu=\""+this.codactu+"\"><i class=\"fa fa-desktop fa-lg\"></i> </a>" +
                            "</div>";
                } else {
                    infocontent += "<div class=\"infow\" style=\"width:300px; height:200px; overflow: auto; text-align: left\"\">" +
                            "<input type=\"button\" id=\"detalle_actu\" value=\"Mostrar/Ocultar detalle\" onclick=\"mostrarOcultarDetalle()\">" +
                            "<input type=\"button\" value=\"Cerrar\" onclick=\"closeInfoWindow()\">" +
                            "<div class=\"detalle_actu\">" +
                            this.tipoactu + "<br>" +
                            this.nombre_cliente + "<br>" +
                            this.fecha_agenda + " / " +
                            this.horario + "<br>" +
                            this.direccion_instalacion + "<br>" +
                            this.codactu + "<br>" +
                            this.fftt + "<br>" +
                            this.codigo_cliente + "<br>" +
                            this.id_atc + "<br>" +
                            this.carnet_tmp + "<br>" +
                            this.quiebre + "<br>" +
                            this.tecnico + "<br>" +
                            "</div>";
                    if ($.trim($("#sinagenda").val()) == '') {
                        infocontent += //"<div><a href=\"javascript:void(0)\" onclick=\"gestionActu('" + this.id  + "', '0', '" + tipo_actu + "')\">Gestionar <img src=\"../historico/img/gestionar.png\" style=\"vertical-align: middle\"></a>&nbsp;" + 
                                "<div class=\"detalle_gestion\"></div>"
                                + "Gestionar <a class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#bandejaModal\" data-codactu=\""+this.codactu+"\"><i class=\"fa fa-desktop fa-lg\"></i> </a>";
                    }
                    infocontent += "<div><a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0001-|" + this.id_atc + "')\">1. Inicio</a>&nbsp;" +
                            "<a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0002-|" + this.id_atc + "')\">2. Supervision</a>&nbsp;" +
                            "<a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0003-|" + this.id_atc + "')\">3. Cierre</a></div>" +
                            "<div class=\"detalle_paso\"></div>" +
                            "</div>";
                }

                try {
                    //Marcador
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(this.y, this.x),
                        map: objMap,
                        title: this.EmployeeNum,
                        icon: agendaIcon,
                        zIndex: zIndex++,
                        codactu: this.codactu,
                        tipoactu: this.tipoactu,
                        actividad: this.actividad,
                        idgestion: this.id,
                        estado: this.estado,
                        coordinado: this.coordinado,
                        fecha_agenda: this.fecha_agenda,
                        horario: this.horario,
                        carnet: carnet,
                        tecnico: this.tecnico,
                        quiebre: this.quiebre
                    });

                    /*
                     //Efecto "BOUNCE" para las agendas pendientes o en curso
                     if ( this.id_estado != 3 && this.id_estado != 19 )
                     {
                     marker.estado = "pendiente";
                     } else {
                     marker.estado = "liquidado";
                     }
                     */

                    //Marcadores temporales
                    if (this.estado === 'Temporal')
                    {
                        tmpActu.push(marker);
                    }

                    //Agrega marcados al arreglo de agendas por tecnico
                    if (carnet !== '')
                    {
                        tecActu[carnet].push(marker);
                    }
                    /*
                     infocontent =   "<div class=\"infow\" style=\"width:300px; height:200px; overflow: auto\">" +
                     "<input type=\"button\" id=\"detalle_actu\" value=\"Mostrar/Ocultar detalle\" onclick=\"mostrarOcultarDetalle()\">" + 
                     "<div class=\"detalle_actu\">" + 
                     this.tipoactu + "<br>" + 
                     this.nombre_cliente_critico + "<br>" + 
                     this.fecha_agenda + " / " + 
                     this.horario + "<br>" + 
                     this.observacion + "<br>" + 
                     this.direccion + "<br>" + 
                     this.codactu + "<br>" + 
                     this.fftt + "<br>" + 
                     this.codcli + "<br>" + 
                     this.mdf + "<br>" + 
                     this.id_atc + "<br>" + 
                     this.lejano + "<br>" +  
                     this.carnet_critico + "<br>" + 
                     this.paquete + "<br>" + 
                     this.quiebre + "<br>" + 
                     this.tecnico + "<br>" + 
                     "</div>";
                     if($.trim($("#sinagenda").val())==''){
                     infocontent+=       "<div><a href=\"javascript:void(0)\" onclick=\"gestionActu('" + this.id  + "', '0', '" + tipo_actu + "')\">Gestionar <img src=\"../historico/img/gestionar.png\" style=\"vertical-align: middle\"></a>&nbsp;" + 
                     "<div class=\"detalle_gestion\"></div>";
                     }
                     infocontent+=       "<div><a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0001-|" + this.id_atc + "')\">1. Inicio</a>&nbsp;" + 
                     "<a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0002-|" + this.id_atc + "')\">2. Supervision</a>&nbsp;" + 
                     "<a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0003-|" + this.id_atc + "')\">3. Cierre</a></div>" +
                     "<div class=\"detalle_paso\"></div>" +                                    
                     "</div>";
                     
                     doInfoWindow(marker, infocontent);
                     */
                    /*
                     google.maps.event.addListener(marker, "click", function (){
                     infowindow.setPosition(new google.maps.LatLng(this.y, this.x));
                     infowindow.setContent(infocontent);
                     infowindow.open(self.objMap);
                     });
                     */
                    doInfoWindow(objMap, marker, infocontent);
                } catch (e) {
                    console.log(e);
                }

                objMap.fitBounds(bounds);

            } else {
                //Agrega agendas sin XY, se agrega ATC
                //tecActu[this.carnet_tmp].push(this.id_atc);
            }

        });

        //Boton muestra todos los tecnicos y agendas
        $("#btn_show_tec").removeClass("btn btn-default");
        $("#btn_show_tec").addClass("btn btn-success");
    }


    function limpiarTmpActu() {
        //Elimina marcadores tmp del mapa
        if (tmpActu.length > 0)
        {
            $.each(tmpActu, function() {
                this.setMap(null);
            });
            tmpActu = [];
        }
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
    function closeInfoWindow(){
        infowindow.close();
    }

    function mostrarTrafico(thisMap) {

        var btnClass = $("#show_traffic").attr("class");

        if (btnClass === 'btn btn-default')
        {
            trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(thisMap);

            $("#show_traffic").html("Ocultar tr&aacute;fico");
            $("#show_traffic").removeClass("btn btn-default");
            $("#show_traffic").addClass("btn btn-success");
        } else {
            trafficLayer.setMap(null);

            $("#show_traffic").html("Mostrar tr&aacute;fico");
            $("#show_traffic").removeClass("btn btn-success");
            $("#show_traffic").addClass("btn btn-default");
        }

    }

    function mostrarOcultarDetalle() {
        $(".detalle_actu").toggle("slow");
    }

    function showHideAll(thisMap) {
        var show;

        if ($("#btn_show_tec").attr("class") === 'btn btn-success')
        {
            show = false;
            $("#btn_show_tec").removeClass("btn btn-success");
            $("#btn_show_tec").addClass("btn btn-default");
        } else {
            show = true;
            $("#btn_show_tec").removeClass("btn btn-default");
            $("#btn_show_tec").addClass("btn btn-success");
        }

        //Tecnicos en mapa
        $.each(tecMark, function() {
            if (!show)
            {
                //Ocultar tecnicos
                this.setMap(null);
            } else {
                //Mostrar tecnicos
                this.setMap(thisMap);
            }
        });

        //Agendas en mapa
        for (key in tecActu) {
            if (tecActu.hasOwnProperty(key))
            {
                $.each(tecActu[key], function(id, val) {

                    if (typeof val === 'object')
                    {
                        if (!show)
                        {
                            //Ocultar agendas
                            this.setMap(null);
                        } else {
                            //Mostrar agendas
                            this.setMap(thisMap);
                        }
                    }


                });
            }
        }

        //Checkbox por tecnico
        $.each($(".chb_tec"), function() {
            if (!show)
            {
                //Ocultar agendas
                $(this).prop("checked", false);
            } else {
                //Mostrar agendas
                $(this).prop("checked", true);
            }
        });

    }


    function drawTecPath(data, code, color, thisMap) {
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
                    markerIcon = "http://chart.apis.google.com/chart"
                            + "?chst=d_map_pin_letter&chld=1|"
                            + color
                            + "|"
                            + textByColor("#" + color).substring(1, 7);
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

                if (typeof mapObjects[this.EmployeeNum] === "undefined") {
                    mapObjects[this.EmployeeNum] = new Array();
                }
                mapObjects[this.EmployeeNum].push(pathMarker);
                tecCode = this.EmployeeNum;


                //Contenido + Infowindow
                pathContent = this.EmployeeNum
                        + "<br>"
                        + this.t
                        + "<br>"
                        + this.Battery
                        + "<br>"
                        + "<a href=\"javascript:void(0)\" onClick=\"doNotPath('" + this.EmployeeNum + "')\">Ocultar ruta</a>";

                doInfoWindow(thisMap, pathMarker, pathContent);
                /*google.maps.event.addListener(pathMarker, "click", function (){
                 infowindow.setPosition(myLatlng);
                 infowindow.setContent(pathContent);
                 infowindow.open(self.objMap);
                 });*/

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

            drawPath.setMap(thisMap);
            mapObjects[code].push(drawPath);
        } else {
            console.log("Error");
        }
    }

    /**
     * Oculta una ruta de tecnico
     * 
     * @param {type} code
     * @returns {undefined}
     */
    function doNotPath(code) {
        try {
            //Elimina ruta de un tecnico
            $.each(mapObjects[code], function() {
                this.setMap(null);
            });
        } catch (e) {
            console.log(e);
        }
    }


    function openInfoWin(code) {
        try {
            if (typeof tecMark[code] === 'undefined')
            {
                throw "[show]T\u00E9cnico sin ubicaci\u00F3n.";
            }
            google.maps.event.trigger(tecMark[code], 'click');
        } catch (e) {
            errorMessage(e);
        }
    }

    function doPolyAction(event, polygon) {

        var nPoliActu = 0;
        var pos;
        var markerColor = getRandomColor();

        var img_coord = "";
        polyActu = [];
        tmpObject = {}
        globalElement = polygon;

        //Lista de tecnicos por celula
        $.ajax({
            type: "POST",
            url: "geoplan/tecnicocelula",
            data: "getTecnicoCelula=ok"
                    + "&empresa_id="
                    + $("#slct_empresa").val()
                    + "&celula_id="
                    + $("#slct_celula").val(),
            dataType: 'json',
            async: false,
            error: function(data) {
                console.log(data);
            },
            success: function(data) {
                teclistNames = data.names;
            }
        });

        function getIconXy(pos, polyActu) {
            var strXy = pos.lat() + "," + pos.lng();
            var iconXy = "green_check_16.png";
            var strPoly = "";
            $.each(polyActu, function(key, str) {
                strPoly = str.getPosition().lat()
                        + ","
                        + str.getPosition().lng();
                if (strXy === strPoly)
                {
                    iconXy = "dialog-warning-20.png";
                }
            });
            return iconXy;
        }
        /*
         <div class="box">
         <div class="box-body no-padding">
         <table class="table table-striped">
         <tbody><tr>
         <th style="width: 10px">#</th>
         <th>Task</th>
         <th>Progress</th>
         <th style="width: 40px">Label</th>
         </tr>
         <tr>
         <td>1.</td>
         <td>Update software</td>
         <td>
         <div class="progress progress-xs">
         <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
         </div>
         </td>
         <td><span class="badge bg-red">55%</span></td>
         </tr>
         <tr>
         <td>2.</td>
         <td>Clean database</td>
         <td>
         <div class="progress progress-xs">
         <div class="progress-bar progress-bar-yellow" style="width: 70%"></div>
         </div>
         </td>
         <td><span class="badge bg-yellow">70%</span></td>
         </tr>
         <tr>
         <td>3.</td>
         <td>Cron job running</td>
         <td>
         <div class="progress progress-xs progress-striped active">
         <div class="progress-bar progress-bar-primary" style="width: 30%"></div>
         </div>
         </td>
         <td><span class="badge bg-light-blue">30%</span></td>
         </tr>
         <tr>
         <td>4.</td>
         <td>Fix and squish bugs</td>
         <td>
         <div class="progress progress-xs progress-striped active">
         <div class="progress-bar progress-bar-success" style="width: 90%"></div>
         </div>
         </td>
         <td><span class="badge bg-green">90%</span></td>
         </tr>
         </tbody></table>
         </div>
         </div>*/


        /*infocontent = "<div class=\"box\">"
         + "  <div class=\"box-body no-padding\">"
         + "      <table class=\"table\" id=\"orderActuTable\">"
         + "          <tbody><tr>"
         +"<th>&nbsp;</th>"
         + "<th>&nbsp;</th>"
         + "<th>Codigo</th>"
         + "<th>Horario</th>"
         + "<th>Planificar</th>"
         + "<th>Coord.</th>"
         + "<th>WebPSI</th>"
         + "<th>OfficeTrack</th>"
         + "<th>Tecnico</th>"
         + "<th>Agenda</th>"
         + "              </tr>";*/

        infocontent = "<div style=\"font-size: 11px;\">";
        infocontent += "<div class=\"msgPlan\" style=\"color: #FF0000; font-size: 12px; text-align: center\"></div>";
        infocontent += "Tecnico: " + htmlTecList(teclistNames, 'tecnicos');
        infocontent += " Agenda: <input type=\"text\" class=\"agendaSelect\" size=\"10\" id=\"plan_agenda\" readonly>";

        infocontent += "<table id=\"orderActuTable\" class=\"table\">";
        infocontent += "<thead><tr>";
        infocontent += "<th>&nbsp;</th>"
                + "<th>&nbsp;</th>"
                + "<th>Codigo</th>"
                + "<th>Horario</th>"
                + "<th>Planificar</th>"
                + "<th>Coord.</th>"
                + "<th>WebPSI</th>"
                + "<th>OfficeTrack</th>"
                + "<th>Tecnico</th>"
                + "<th>Agenda</th>";
        infocontent += "</tr></thead><tbody>";

        //Temporales
        $.each(tmpActu, function(id, val) {

            if (typeof val === 'object')
            {
                //Planificar ordenes dentro del poligono y visibles en el mapa
                if (google.maps.geometry.poly.containsLocation(val.getPosition(), polygon) && val.map !== null)
                {
                    /**
                     * pos:
                     * La Ãºltima ubicaciÃ³n del marcador serÃ¡ 
                     * la posiciÃ³n del infowindow
                     */
                    pos = val.getPosition();
                    var iconXy = getIconXy(pos, polyActu);
                    polyActu.push(val);

                    nPoliActu++;

                    //Almacenar icono anterior
                    if (typeof tmpObject[val.codactu] === 'undefined')
                    {
                        tmpObject[val.codactu] = val.icon;
                    }

                    val.setMap(null);
                    val.icon = "http://chart.apis.google.com/chart"
                            + "?chst=d_map_pin_letter&chld="
                            + nPoliActu
                            + "|"
                            + markerColor.substring(1)
                            + "|"
                            + textByColor(markerColor).substring(1);
                    val.setMap(objMap);

                    infocontent += "<tr>";
                    infocontent += "<td class=\"nActu\" id=\"td_" + val.codactu + "\">" + nPoliActu + "</td>";
                    infocontent += "<td><img src=\"img/icons/" + iconXy + "\" /></td>";
                    infocontent += "<td><a href=\"javascript:void(0)\" onmouseover=\"bounceActuPlan('" + val.codactu + "', true)\" onmouseout=\"bounceActuPlan('" + val.codactu + "', false)\">" + val.codactu + "</a></td>";
                    infocontent += "<td>";
                    infocontent += "<select id=\"hplan_" + val.codactu + "\">";
                    
                    $.each(horarioGeoPlan[val.quiebre], function() {
                        infocontent += "<option value=\""
                                + this.horario_id
                                + "\">"
                                + this.horario
                                + "</option>";
                    });

                    infocontent += "</select></td>";
                    infocontent += "<th><input type=\"checkbox\" class=\"toPlan\" value=\"" + val.codactu + "|" + val.actividad + "|" + val.idgestion + "|" + val.estado + "|0|"+pos.lat()+"|"+pos.lng()+"\"></th>";
                    infocontent += "<th><img src=\"img/icons/stop-24.png\" id=\"coo_" + val.codactu + "\"></div></th>";
                    infocontent += "<th><img src=\"img/icons/stop-24.png\" id=\"psi_" + val.codactu + "\"></div></th>";
                    infocontent += "<th><img src=\"img/icons/stop-24.png\" id=\"ot_" + val.codactu + "\"></div></th>";
                    infocontent += "<th>&nbsp;</th>";
                    infocontent += "<th>&nbsp;</th>";
                    infocontent += "</tr>";
                }
            }

        });

        //Gestionadas en mapa
        for (key in tecActu) {
            if (tecActu.hasOwnProperty(key))
            {
                $.each(tecActu[key], function(id, val) {

                    img_coord = "stop-24.png"

                    if (typeof val === 'object')
                    {
                        //Planificar ordenes dentro del poligono y visibles en el mapa
                        if (google.maps.geometry.poly.containsLocation(val.getPosition(), polygon) && val.map !== null)
                        {
                            /**
                             * pos:
                             * La Ãºltima ubicaciÃ³n del marcador serÃ¡ 
                             * la posiciÃ³n del infowindow
                             */
                            pos = val.getPosition();
                            var iconXy = getIconXy(pos, polyActu);
                            polyActu.push(val);

                            var planHorario = "<select id=\"hplan_" + val.codactu + "\">";
                            
                            $.each(horarioGeoPlan[val.quiebre], function() {
                                planHorario += "<option value=\""
                                        + this.horario_id
                                        + "\">"
                                        + this.horario
                                        + "</option>";
                            });
                            
                            planHorario += "</select>";

                            /**
                             * Coordinado o no coordinado
                             */
                            if (val.coordinado == 1)
                            {
                                img_coord = "phone_talk_32.png";

                                //fecha de agenda
                                if (val.fecha_agenda != "0000-00-00")
                                {
                                    planHorario = val.fecha_agenda.substring(8, 10)
                                            + "/"
                                            + val.fecha_agenda.substring(5, 7)
                                            + "/"
                                            + val.fecha_agenda.substring(0, 4)
                                            + "<br>"
                                            + val.horario;
                                }
                            }

                            nPoliActu++;

                            //Almacenar icono anterior
                            if (typeof tmpObject[val.codactu] === 'undefined')
                            {
                                tmpObject[val.codactu] = val.icon;
                            }

                            val.setMap(null);
                            val.icon = "http://chart.apis.google.com/chart"
                                    + "?chst=d_map_pin_letter&chld="
                                    + nPoliActu
                                    + "|"
                                    + markerColor.substring(1)
                                    + "|"
                                    + textByColor(markerColor).substring(1);
                            val.setMap(objMap);

                            infocontent += "<tr>";
                            infocontent += "<td class=\"nActu\" id=\"td_" + val.codactu + "\">" + nPoliActu + "</td>";
                            infocontent += "<td><img src=\"img/icons/" + iconXy + "\" /></td>";
                            infocontent += "<td><a href=\"javascript:void(0)\" onmouseover=\"bounceActuPlan('" + val.codactu + "', true)\" onmouseout=\"bounceActuPlan('" + val.codactu + "', false)\">" + val.codactu + "</a></td>";
                            infocontent += "<th>";
                            infocontent += planHorario;
                            infocontent += "</th>";
                            infocontent += "<th><input type=\"checkbox\" class=\"toPlan\" value=\"" + val.codactu + "|" + val.actividad + "|" + val.idgestion + "|" + val.estado + "|" + val.coordinado + "|"+pos.lat()+"|"+pos.lng()+"\"></th>";
                            infocontent += "<th><img src=\"img/icons/" + img_coord + "\" id=\"coo_" + val.codactu + "\" style=\"width: 24px\"></div></th>";
                            infocontent += "<th><img src=\"img/icons/stop-24.png\" id=\"psi_" + val.codactu + "\"></div></th>";
                            infocontent += "<th><img src=\"img/icons/stop-24.png\" id=\"ot_" + val.codactu + "\"></div></th>";
                            infocontent += "<th>" + val.tecnico + "</th>";
                            infocontent += "<th>";
                            infocontent += val.fecha_agenda.substring(8, 10)
                                    + "/"
                                    + val.fecha_agenda.substring(5, 7)
                                    + "/"
                                    + val.fecha_agenda.substring(0, 4)
                                    + "<br>"
                                    + val.horario;
                            infocontent += "</th>";
                            infocontent += "</tr>";
                        }
                    }

                });
            }
        }

        /*infocontent += "</tbody></table>"
         + "</div>"
         + "</div>";*/

        infocontent += "</tbody></table>";
        infocontent += "</div>";
        infocontent += "<p>" + nPoliActu + " trabajos</p>";

        var footerContent = "";
        footerContent += "<input type=\"button\" class=\"btn btn-danger\" onclick=\"saveGeoPlan()\" value=\"Guardar Cambios\" />";
        footerContent += "<input type=\"button\" class=\"btn btn-primary\" value=\"Deshacer Plan\" onclick=\"undoPlanPoly()\" />";
        footerContent += "<input type=\"button\" class=\"btn btn-primary\" value=\"Borrar poligono\" onclick=\"deletePlanPoly()\" />";
        //infocontent += "<a href=\"javascript:void(0)\" onclick=\"doPlanPoly()\">Planificar</a>";


        //Actualizar contenido de modal
        $(".modal-footer").html(footerContent);
        $("#contentPlanModal").html(infocontent);
        
        $(".ui-datepicker").css("background-color", "#FFFFFF");

        //Click en las ordenes a planificar
        $(".bounceActu").click(function() {
            bounceActuPlan($(this).val(), $(this).prop("checked"));
        });

        //Ordenar elementos de la tabla
        $("#orderActuTable tbody").sortable({
            stop: function(event, ui) {
                markerColor = getRandomColor();
                //Cambio orden, numeracion
                $.each($("#orderActuTable tbody tr .nActu"), function(id, val) {
                    //codactu
                    var codactu = $(this).attr("id").substring(3);
                    //Nuevo orden
                    $(this).html(id + 1);
                    //Cambiar valor marcador
                    $.each(polyActu, function(index, element) {
                        if (element.codactu === codactu)
                        {
                            element.setMap(null);
                            element.icon = "http://chart.apis.google.com/chart"
                                    + "?chst=d_map_pin_letter&chld="
                                    + (id + 1)
                                    + "|"
                                    + markerColor.substring(1)
                                    + "|"
                                    + textByColor(markerColor).substring(1);
                            element.setMap(objMap);
                        }
                    });
                });
            }
        }).disableSelection();


        $(".agendaSelect").datepicker(
                {
                    closeText: 'Cerrar',
                    prevText: '<Ant',
                    nextText: 'Sig>',
                    currentText: 'Hoy',
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'MiÃ©rcoles', 'Jueves', 'Viernes', 'SÃ¡bado'],
                    dayNamesShort: ['Dom', 'Lun', 'Mar', 'MiÃ©', 'Juv', 'Vie', 'SÃ¡b'],
                    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'SÃ¡'],
                    weekHeader: 'Sm',
                    dateFormat: 'dd/mm/yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: '',
                    minDate: 0,
                    changeMonth: true,
                    changeYear: true
                }
        );
        var currentDate = new Date();
        $(".agendaSelect").datepicker("setDate", currentDate);
        $(".ui-datepicker").css("background-color", "#FFFFFF");
        //Abrir cuadro de dialogo
        //$("#dialog").dialog("open");
        //$('#wdialog').modal('show');
        $("#btnPlanModal").click();
    }

    function htmlTecList(list, id) {
        var htmlList = "<select name=\"list_" + id + "\" id=\"list_" + id + "\"\">";

        $.each(list, function(key, val) {
            htmlList += "<option value=\"" + val.id + "\">"
                    + val.nombre_tecnico
                    + "</option>";
        });

        htmlList += "</select>";

        return htmlList;
    }


    function bounceActuPlan(codactu, checked) {
        //Efecto bounce a la orden seleccionada
        $.each(polyActu, function(id, val) {
            if (val.codactu === codactu && checked)
            {
                val.setAnimation(google.maps.Animation.BOUNCE);
            }
            if (val.codactu === codactu && !checked)
            {
                val.setAnimation(null);
            }
        });
    }

    function undoPlanPoly() {

        $.each(polyActu, function(index, edited) {
            for (key in tmpObject) {
                if (tmpObject.hasOwnProperty(key))
                {
                    if (edited.codactu === key)
                    {
                        edited.setMap(null);
                        edited.icon = tmpObject[key];
                        edited.setMap(objMap);
                    }
                }
            }
        });
    }

    function deletePlanPoly() {
        globalElement.setMap(null);
    }

    function saveGeoPlan() {
        var datos = "";
        //Horario para planificacion tipo=2
        var empresa = $("#slct_empresa").val();
        var celula = $("#slct_celula").val();
        var tecnico = $("#list_tecnicos").val();
        var agenda = $("#plan_agenda").val();

        $.each($(".toPlan"), function() {
            /**
             * arrData: Datos de la actuacion
             * [0]: codactu
             * [1]: Averia o Provision
             * [2]: id_gestion, 0=temporal
             * [3]: true=temporal, false=gestionada
             * [4]: 1=coordinado, 2=no coordinado
             * [5]: Latitud
             * [6]: Longitud
             * @type @call;$@call;val@call;split
             */
            var arrData = $(this).val().split("|");
            if ($(this).prop("checked"))
            {
                datos += "|^" + arrData[1]
                        + "|" + arrData[0]
                        + "|" + arrData[2]
                        + "|" + $("#hplan_" + arrData[0]).val()
                        + "|" + arrData[4]
                        + "|" + arrData[5]
                        + "|" + arrData[6];
            }
        });
        if (datos !== "")
        {
            $("body").addClass("loading");
            datos = empresa
                    + "|" + celula
                    + "|" + tecnico
                    + "|" + agenda
                    + "|^~" + datos.substring(2);

            var data = Geoplan.SavePlan(datos);

        } else {
            //No se ha seleccionado actuacion
            $(".msgPlan").html("Seleccione actuaciones a planificar");
        }
    }

    function sendOt() {
        $.ajax({
            url: "geoplan/sendot",
            type: "POST",
            cache: false,
            data: "enviar=ok",
            dataType: 'json',
            beforeSend: function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            error: function(data) {
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">' +
                        '<i class="fa fa-ban"></i>' +
                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>' +
                        '</div>');
            },
            success: function(data) {

            }
        });
    }

    function uploadGeoPlan() {
    
        //Validaciones
        if ($.trim($("#slct_actividad").val()) == "") {
            alert("Seleccione actividad(es)");
            return false;
        }
        
        if ($.trim($("#slct_empresa").val()) == "") {
            alert("Seleccione empresa");
            return false;
        }
        
        if ($.trim($("#slct_celula").val()) == "") {
            alert("Seleccione celula");
            return false;
        }
    
        var inputFile = document.getElementById("txt_file_plan");
        var file = inputFile.files[0];
        var data = new FormData();

        data.append('archivo', file);
        data.append('empresa', $("#slct_empresa").val());
        data.append('celula', $("#slct_celula").val());
        data.append('zonal', $("#slct_zonal").val());
        data.append('actividad', $("#slct_actividad").val());
        
        var dataToDownload = "data:application/octet-stream,";        

        $.ajax({
            url: "geoplan/uploadfile",
            type: "POST",
            cache: false,
            data: data,
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend: function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            error: function(data) {
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">' +
                        '<i class="fa fa-ban"></i>' +
                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>' +
                        '</div>');
            },
            success: function(datos) {
                $(".overlay,.loading-img").remove();

                if (datos.upload) {
                    //Mostrar asignadas y temporales
                    var nro_asg = 0;
                    var nro_tmp = 0;
                    var nro_not = 0;
                    
                    var bounds = new google.maps.LatLngBounds();
                    
                    //Horarios
                    horarioGeoPlan = datos.horario;

                    //No encontrados
                    $.each(datos.data, function(ind, codactu) {
                        var notFound = true;
                        $.each(datos.agenda.data, function(id, field) {
                            if (codactu === field.codactu)
                            {
                                notFound = false;
                                //console.log(codactu + "/" + field.codactu + "/" + notFound);
                            }
                        });

                        if (notFound) {
                            if (typeof codactu !== 'undefined' && codactu != '')
                            {
                                dataToDownload += codactu + "%0A";
                                nro_not++;
                            }
                            //$(".no_tmp_file").append("<p>" + codactu + "</p>");
                        }
                    });

                    $(".downNotFound").attr("href", dataToDownload);
                    $(".downNotFound").html("Download ( " + nro_not + " )");

                    $.each(datos.agenda.data, function(id, field) {

                        if (typeof field.id_atc !== 'undefined' && field.id_atc != '')
                        {
                            //Gestionados
                            nro_asg++;
                            //Carnet de tecnico
                            var carnet = field.carnet_tmp;

                            //Arreglo de actuaciones por tecnico
                            if (typeof tecActu[carnet] === "undefined") {
                                tecActu[carnet] = new Array();
                            }

                            //Agendas con XY (taps o terminales)
                            if (field.x !== "" && field.y !== "")
                            {
                                myLatlng = new google.maps.LatLng(field.y, field.x);

                                bounds.extend(myLatlng);

                                try {
                                    //Marcador
                                    marker = new google.maps.Marker({
                                        position: new google.maps.LatLng(field.y, field.x),
                                        map: objMap,
                                        title: field.EmployeeNum,
                                        icon: 'img/icons/visorgps/' + field.icon,
                                        zIndex: zIndex++,
                                        codactu: field.codactu,
                                        tipoactu: field.tipoactu,
                                        actividad: field.actividad,
                                        idgestion: field.id,
                                        estado: field.estado,
                                        coordinado: field.coordinado,
                                        fecha_agenda: field.fecha_agenda,
                                        horario: field.horario,
                                        carnet: carnet,
                                        tecnico: field.tecnico,
                                        quiebre: field.quiebre
                                    });

                                    //Efecto "BOUNCE" para las agendas pendientes o en curso
                                    if (field.id_estado != 3 && field.id_estado != 19)
                                    {
                                        //marker.setAnimation(google.maps.Animation.BOUNCE);
                                        marker.estado = "pendiente";
                                        /*
                                         circle = new google.maps.Circle({
                                         strokeColor: '#FF0000',
                                         strokeOpacity: 0.8,
                                         strokeWeight: 3,
                                         map: map,
                                         center: myLatlng,
                                         radius: 100
                                         });
                                         */
                                    } else {
                                        marker.estado = "liquidado";
                                    }

                                    //Agrega marcados al arreglo de agendas por tecnico
                                    tecActu[carnet].push(marker);

                                    infocontent = "<div class=\"infow\" style=\"width:300px; height:200px; overflow: auto\">" +
                                            "<input type=\"button\" id=\"detalle_actu\" value=\"Mostrar/Ocultar detalle\" onclick=\"mostrarOcultarDetalle()\">" +
                                            "<input type=\"button\" value=\"Cerrar\" onclick=\"closeInfoWindow()\">" +
                                            "<div class=\"detalle_actu\">" +
                                            field.tipoactu + "<br>" +
                                            field.nombre_cliente_critico + "<br>" +
                                            field.fecha_agenda + " / " +
                                            field.horario + "<br>" +
                                            field.observacion + "<br>" +
                                            field.direccion + "<br>" +
                                            field.codactu + "<br>" +
                                            field.fftt + "<br>" +
                                            field.codcli + "<br>" +
                                            field.mdf + "<br>" +
                                            field.id_atc + "<br>" +
                                            field.lejano + "<br>" +
                                            field.carnet_tmp + "<br>" +
                                            field.paquete + "<br>" +
                                            field.quiebre + "<br>" +
                                            field.tecnico + "<br>" +
                                            "</div>";
                                    if ($.trim($("#sinagenda").val()) == '') {
                                        infocontent += "<div>Gestionar <a class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#bandejaModal\" data-codactu=\""+field.codactu+"\"><i class=\"fa fa-desktop fa-lg\"></i> </a>&nbsp;" +
                                                "<div class=\"detalle_gestion\"></div>";
                                    }
                                    infocontent += "<div><a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0001-|" + field.id_atc + "')\">1. Inicio</a>&nbsp;" +
                                            "<a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0002-|" + field.id_atc + "')\">2. Supervision</a>&nbsp;" +
                                            "<a href=\"javascript:void(0)\" onclick=\"get_detalle_paso('0003-|" + field.id_atc + "')\">3. Cierre</a></div>" +
                                            "<div class=\"detalle_paso\"></div>" +
                                            "</div>";

                                    doInfoWindow(objMap, marker, infocontent);
                                    /*
                                    google.maps.event.addListener(marker, "click", function() {
                                        infowindow.setPosition(new google.maps.LatLng(field.y, field.x));
                                        infowindow.setContent(infocontent);
                                        infowindow.open(self.objMap);
                                    });
                                    */
                                } catch (e) {
                                    console.log(e);
                                }

                            } else {
                                //Agrega agendas sin XY, se agrega ATC
                                //tecActu[this.carnet_critico].push(this.id_atc);
                            }

                        } else {
                            //Numero temporales encontrados
                            nro_tmp++;
                            //Mostrar solo tmp con XY
                            if (field.x !== '' && field.y !== '' && field.x !== null && field.y !== null)
                            {
                                //Lat/Lng del temporal
                                myLatlng = new google.maps.LatLng(field.y, field.x);

                                bounds.extend(myLatlng);

                                //Marcador
                                marker = new google.maps.Marker({
                                    position: myLatlng,
                                    map: objMap,
                                    title: field.EmployeeNum,
                                    icon: "img/icons/tmp_actu.png",
                                    animation: google.maps.Animation.DROP,
                                    zIndex: zIndex++,
                                    draggable: true,
                                    codactu: field.codactu,
                                    tipoactu: field.tipoactu,
                                    isTmp: true,
                                    idgestion: 0,
                                    tipoact: field.tipo_actividad,
                                    coordinado: field.coordinado,
                                    quiebre: field.quiebre,
                                    actividad: field.actividad,
                                    estado: field.estado
                                });

                                //Agrega marcadores temporales por averia o Provision
                                tmpActu.push(marker);

                                //Infowindow
                                infocontent = "<div class=\"infow\" style=\"width:300px; height:200px; overflow: auto\">" +
                                        "<input type=\"button\" id=\"detalle_actu\" value=\"Mostrar/Ocultar detalle\" onclick=\"mostrarOcultarDetalle()\">" +
                                        "<input type=\"button\" value=\"Cerrar\" onclick=\"closeInfoWindow()\">" +
                                        "<div class=\"detalle_actu\">" +
                                        "Tipo: " + field.tipoactu + "<br>" +
                                        "Codigo: " + field.codactu + "<br>" +
                                        "Horas: " + field.horas_actu + "<br>" +
                                        "Fec. Registro: " + field.fecha_registro + "<br>" +
                                        field.nombre_cliente + "<br>" +
                                        field.direccion_instalacion + "<br>" +
                                        field.fftt + "<br>" +
                                        field.distrito + "<br>" +
                                        field.mdf + "<br>" +
                                        field.telefono + "<br>" +
                                        field.paquete + "<br>" +
                                        field.x + " / " + field.y + "<br>" +
                                        "</div>" +
                                        "Gestionar <a class=\"btn btn-primary btn-sm\" data-toggle=\"modal\" data-target=\"#bandejaModal\" data-codactu=\""+field.codactu+"\"><i class=\"fa fa-desktop fa-lg\"></i> </a>"
                                        "<div class=\"detalle_gestion\"></div>" +
                                        "</div>";

                                doInfoWindow(objMap, marker, infocontent);
                                /*
                                google.maps.event.addListener(marker, "click", function() {
                                    infowindow.setPosition(new google.maps.LatLng(field.y, field.x));
                                    infowindow.setContent(infocontent);
                                    infowindow.open(self.objMap);
                                });
                                */
                            }
                        }
                    });
                    //Actualizar numero tmp y asg
                    $(".nro_asg_file").html(nro_asg);
                    $(".nro_tmp_file").html(nro_tmp);
                    
                    objMap.fitBounds(bounds);
                } else {
                    //Upload Error
                    alert("Error al cargar el archivo, vuelva a intentarlo.");
                }

            }
        });
    }
    
    function listaCelula(){
        var empresa_id = $("#slct_empresa").val();
        Visorgps.DoListaCelula(empresa_id);
    }
    
    function get_detalle_paso(dato){
        Visorgps.GetDetallePaso(dato);
    }

    function cleandate(){
        $('#fecha_agenda').val('');
    }
</script>
