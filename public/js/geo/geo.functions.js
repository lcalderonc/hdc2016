/**
 * Variales globales
 */

//Objeto Mapa
var objMap;

/**
 * z-index de los elementos del mapa
 * @type Number|Number
 */
var zIndex = 1;

//Propiedades globales del mapa
var objMapProps = {
    center: new google.maps.LatLng(-12.109121, -77.016269),
    zoom: 12,
    mapTypeId: google.maps.MapTypeId.ROADMAP

};

//Objeto marcador
var marker;

//Arreglo de marcadores
var markers = [];

//Objeto infowindow
var infowindow;

//Contenido de un infowindow
var infocontent;

//Limites del mapa segun los elementos
var bounds;

//Limites de un solo elemento
var boundsElement;

//Una ubicacion en particular
var myLatlng;

/**
 * Inicia un mapa de google
 * 
 * @param {type} element
 * @param {type} props
 * @returns {objMap|google.maps.Map}
 */
function doObjMap(element, props, mapTools) {
    var thisMap = new google.maps.Map(document.getElementById(element), props);

    infowindow = new google.maps.InfoWindow({
        content: ""
    });

    //Dibujar poligono
    google.maps.event.addDomListener(thisMap.getDiv(), 'mousedown', function(e) {
        //do it with the right mouse-button only
        if (e.button != 2)
            return;
        //the polygon
        poly = new google.maps.Polyline({map: thisMap, clickable: false});
        //move-listener
        var move = google.maps.event.addListener(thisMap, 'mousemove', function(e) {
            poly.getPath().push(e.latLng);
        });
        //mouseup-listener
        google.maps.event.addListenerOnce(thisMap, 'mouseup', function(e) {
            google.maps.event.removeListener(move);

            var path = poly.getPath();
            poly.setMap(null);
            poly = new google.maps.Polygon({map: thisMap, path: path});

        });

    });

    var drawingManager = new google.maps.drawing.DrawingManager({
        //drawingMode: google.maps.drawing.OverlayType.MARKER,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                //google.maps.drawing.OverlayType.MARKER,
                //google.maps.drawing.OverlayType.CIRCLE,
                google.maps.drawing.OverlayType.POLYGON,
                        //google.maps.drawing.OverlayType.POLYLINE,
                        //google.maps.drawing.OverlayType.RECTANGLE
            ]
        },
        //markerOptions: {
        //    icon: 'images/beachflag.png'
        //},
        //circleOptions: {
        //    fillColor: '#ffff00',
        //    fillOpacity: 1,
        //    strokeWeight: 2,
        //    clickable: false,
        //    editable: true,
        //    zIndex: 1
        //}
    });

    //Al completar un polygon agregar el listener CLICk
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
        //event.type == google.maps.drawing.OverlayType.MARKER
        //var infowindow = new google.maps.InfoWindow({
        //    content: '<div id="content" onclick="testPoly()">Hello</div>',
        //    maxWidth: 10
        //});
        var element = event.overlay;

        google.maps.event.addListener(event.overlay, 'click', function() {
            doPolyAction(event, this);
            //planPoly(event, this);
        });


    });

    //Mostrar u ocultar herramientas de dibujo
    if (mapTools.draw)
    {
        drawingManager.setMap(thisMap);
    }

    return thisMap;
}

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

/**
 * Inicia un obj tipo "panorama" de Google Street View
 * @param {type} lat Latitud (y)
 * @param {type} lng Longitud (x)
 * @param {type} item Elemento donde se mostrara el street view (div, p, etc.)
 * @returns {google.maps.StreetViewPanorama}
 */
function geoStreetView(lat, lng, item) {
    var fenway = new google.maps.LatLng(lat, lng);

    // Note: constructed panorama objects have visible: true
    // set by default.
    var panoOptions = {
        position: fenway,
        addressControlOptions: {
            position: google.maps.ControlPosition.BOTTOM_CENTER
        },
        linksControl: true,
        panControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL
        },
        enableCloseButton: false
    };

    var panorama = new google.maps.StreetViewPanorama(
            document.getElementById(item), panoOptions);
    
    return panorama;
}
/**
 * Inicializa Google Maps
 * @y_inicio    coordinada y de inicio
 * @x_inicio    coordinada x de inicio
 * @coord_y     coordinada y de fin
 * @coord_x     coordinada x de fin
 * @returns {undefined}
 * */
function initMaps(y_inicio,x_inicio,coord_y,coord_x,div) {
    var pathTrabajo = [];
    var myLatlng = new google.maps.LatLng(coord_y, coord_x);

    var mapOptions = {
        zoom: 16,
        center: myLatlng
    };
    var map = new google.maps.Map(document.getElementById(div), mapOptions);

    var markerActu = new google.maps.Marker({
        icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|000000|FFFFFF",
        position: myLatlng,
        map: map,
        title: 'Hello World!'
    });

    if (y_inicio != null && x_inicio != null && y_inicio!==''&& x_inicio!=='')
    {
        var myLatlngIni = new google.maps.LatLng(y_inicio, x_inicio);

        var markerIni = new google.maps.Marker({
            icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=Ai|000000|FFFFFF",
            position: myLatlngIni,
            map: map,
            title: 'Hello World!'
        });

        pathTrabajo.push(markerActu.position);
        pathTrabajo.push(markerIni.position);

        var distancia = google.maps.geometry.spherical.computeDistanceBetween(
            markerActu.position,
            markerIni.position
        );
        distancia = distancia.toFixed(2);

        var trabajoPath = new google.maps.Polyline({
            path: pathTrabajo,
            geodesic: true,
            strokeColor: '#000000',
            strokeOpacity: 1.0,
            map: map,
            strokeWeight: 2,
            distancia: distancia + " m."
        });

        //Centro del polilyne Tecnico - Agenda
        var pathBounds = new google.maps.LatLngBounds();
        pathBounds.extend(markerActu.position);
        pathBounds.extend(markerIni.position);

        var noMarker = new MarkerWithLabel({
            position: pathBounds.getCenter(),
            icon: 'img/icons/transparent.gif',
            map: map,
            title: "",
            labelContent: distancia + " m.",
            labelAnchor: new google.maps.Point(22, 0),
            labelClass: "markerLabel",
            labelStyle: {opacity: 0.85}
        });
    }


}