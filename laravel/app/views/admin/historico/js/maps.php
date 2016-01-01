<script type="text/javascript">
    var input = (document.getElementById('address'));

    $(document).ready(function() {
        //        load_map();
        initialize(-12.046374,-77.0427934);
    });

    function SetXY(x, y) {
        $("#x").val(x);
        $("#y").val(y);
    }

    function situarMarcador(coord_y,coord_x, draggable){
        var markerOptions = {
            draggable: draggable,
            map: map
        };
        var new_marker_position = new google.maps.LatLng(coord_y, coord_x);
        markerCliente = new google.maps.Marker(markerOptions);
        markerCliente.setPosition(new_marker_position);
        SetXY(coord_x, coord_y);
        geoStreetView(coord_y,coord_x,'streetview');
        //geoStreetView(coord_x,coord_y,'streetview');
        var latLng = markerCliente.getPosition(); // returns LatLng object
        map.setCenter(latLng);
    }
    function initialize(y,x) {

        var myLatlng = new google.maps.LatLng(y,x);
        map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 12,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });


        // Create the search box and link it to the UI element.
        //input = /** @type {HTMLInputElement} */(
                //document.getElementById('address'));
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var searchBox = new google.maps.places.SearchBox(
                /** @type {HTMLInputElement} */(input));

        // [START region_getplaces]
        // Listen for the event fired when the user selects an item from the
        // pick list. Retrieve the matching places for that item.
        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }
            if (markerCliente.visible) {
                markerCliente.setMap(null);
                markerCliente=[];
            }

            // For each place, get the icon, place name, and location.
            markerCliente = [];
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {
                var image = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                markerCliente = new google.maps.Marker({
                    map: map,
                    icon: image,
                    title: place.name,
                    position: place.geometry.location
                });

                SetXY(place.geometry.location.lng(), place.geometry.location.lat());

                bounds.extend(place.geometry.location);
            }

            map.fitBounds(bounds);
            map.setZoom(16);
        });

        google.maps.event.addListener(map, 'bounds_changed', function() {
            var bounds = map.getBounds();
            searchBox.setBounds(bounds);
        });

        google.maps.event.addListener(map, 'click', function(evento) {
            var latitud = evento.latLng.lat();
            var longitud = evento.latLng.lng();
            geoStreetView(latitud,longitud,'streetview');

            SetXY(longitud, latitud);
            if (markerCliente.visible) {
                markerCliente.setMap(null);
                markerCliente=[];
            }
            var markerOptions = {
                draggable: true,
                map: map
            };
            var new_marker_position = new google.maps.LatLng(latitud, longitud);
            var marker = new google.maps.Marker(markerOptions);
            marker.setPosition(new_marker_position);
            //markerCliente.push(marker);
            markerCliente=marker;
            $('#slct_quiebre').trigger('change');//cuando hace click en  buscar
            google.maps.event.addListener(marker, 'click', function() {
                var markerLatLng = marker.getPosition();
                SetXY(markerLatLng.lng(), markerLatLng.lat());
                $('#slct_quiebre').trigger('change');
            });
            google.maps.event.addListener(marker, 'dragend', function() {
                var markerLatLng = marker.getPosition();
                SetXY(markerLatLng.lng(), markerLatLng.lat());
                $('#slct_quiebre').trigger('change');
                geoStreetView(markerLatLng.lat(),markerLatLng.lng(),'streetview');
            });

        });

    }
</script>