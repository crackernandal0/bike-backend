@section('zones_active', 'open')
@section('create_zones_active', 'active')


<div>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Admin /</span> Create Zone
                </h4>

                <div class="d-flex align-items-center">
                    <a href="{{ route('zones') }}" class="btn btn-primary">
                        <span class="tf-icons bx bxs-left-arrow"></span>&nbsp; Go Back
                    </a>
                </div>
            </div>


            <style>
                #map {
                    height: 400px;
                    width: 80%;
                    left: 10px;
                }

                html,
                body {
                    padding: 0;
                    margin: 0;
                    height: 100%;
                }

                #panel {
                    width: 200px;
                    font-family: Arial, sans-serif;
                    font-size: 13px;
                    float: right;
                    margin: 10px;
                    margin-top: 100px;
                }

                #delete-button,
                #add-button,
                #delete-all-button,
                #save-button {
                    margin-top: 5px;
                }

                #search-box {
                    background-color: #f7f7f7;
                    font-size: 15px;
                    font-weight: 300;
                    margin-top: 10px;
                    margin-bottom: 10px;
                    margin-left: 10px;
                    padding: 0 11px 0 13px;
                    text-overflow: ellipsis;
                    height: 25px;
                    width: 80%;
                    border: 1px solid #c7c7c7;
                }

                .map_icons {
                    font-size: 24px;
                    color: white;
                    padding: 10px;
                    background-color: #43439999;
                    margin: 5px;
                }
            </style>

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="box">


                                <div class="col-sm-12">
                                    <form>

                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="serviceLocationName">Zone Name</label>
                                                <div class="input-group input-group-merge">
                                                    <input type="text" wire:model="zone_name"
                                                        class="form-control @error('zone_name') is-invalid @enderror"
                                                        placeholder="Enter zone name">
                                                </div>
                                                @error('zone_name')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label class="form-label" for="countryDropdown">Service Location</label>
                                                <select wire:model="service_location" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach ($serviceLocations as $serviceLocation)
                                                        <option value="{{ $serviceLocation->id }}">
                                                            {{ $serviceLocation->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('service_location')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                        <input type="hidden" id="info" name="coordinates" value="">

                                        <input type="hidden" id="city_polygon" name="city_polygon"
                                            value="{{ old('city_polygon') }}">


                                        <div wire:ignore class="row">
                                            <div class="col-sm-12 mt-5">

                                                <div id="" class="col-sm-1" style="float:right;">
                                                    <ul class="d-flex flex-md-col flex-row align-items-center justify-content-center gap-3 flex-wrap
                                                    "
                                                        style="list-style: none;">
                                                        <li>
                                                            <a id="select-button" href="javascript:void(0)"
                                                                onclick="drawingManager.setDrawingMode(null)"
                                                                class="btn btn-primary zone-add-btn ">
                                                                <i class="bx bxs-hand"></i>
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a id="add-button" href="javascript:void(0)"
                                                                onclick="drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON)"
                                                                class="btn btn-success zone-add-btn ">
                                                                <i class="bx bxs-pen"></i>

                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a id="delete-button" href="javascript:void(0)"
                                                                onclick="deleteSelectedShape()"
                                                                class="btn btn-danger zone-add-btn ">
                                                                <i class="bx bxs-trash"></i>

                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a id="delete-all-button" href="javascript:void(0)"
                                                                onclick="clearMap()"
                                                                class="btn btn-warning zone-add-btn ">
                                                                <i class="bx bx-x"></i>

                                                            </a>
                                                        </li>

                                                    </ul>
                                                </div>

                                                <div id="map" class="col-sm-11" style="float:left;"></div>


                                            </div>
                                        </div>

                                        <div class="mb-3 mt-4">
                                            <div class="form-check form-switch mb-3">
                                                <input wire:model="active" class="form-check-input" type="checkbox"
                                                    id="active">
                                                <label class="form-check-label" for="active">Active</label>
                                            </div>
                                            @error('active')
                                                <div class="error">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <button wire:click.prevent="submit" class="btn btn-primary mt-3"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove>Submit</span>
                                            <div wire:loading>Loading...</div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    @push('scripts')
        <script
            src="https://maps.google.com/maps/api/js?key=AIzaSyC8DHtH6KQlFbii460Aegpt25GER2Bhshk&libraries=drawing,geometry,places">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"
            integrity="sha512-lzilC+JFd6YV8+vQRNRtU7DOqv5Sa9Ek53lXt/k91HZTJpytHS1L6l1mMKR9K6VVoDt4LiEXaa6XBrYk1YhGTQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            // cod jQuery

            // activare tooltips
            $('[data-toggle="tooltip"]').each(function() {
                var options = {
                    html: true
                };
                // setari colorare tooltips
                if ($(this)[0].hasAttribute('data-type')) {
                    options['template'] =
                        '<div class="tooltip ' + $(this).attr('data-type') + '" role="tooltip">' +
                        ' <div class="tooltip-arrow"></div>' +
                        ' <div class="tooltip-inner"></div>' +
                        '</div>';
                }

                $(this).tooltip(options);
            });



            //final cod JQuery



            // inceput Javascript

            // variabile globale
            var map; // harta Google
            var drawingManager; // obiectul care cuprinde majoritatea metodelor si proprietatilor necesare pentru desenare
            var selectedShape; // ajuta la identificarea formei selectate
            var selectedKernel; // ajuta la identificarea nucleului selectat
            var gmarkers = []; // lista cu markerele care vor fi pozitionate in varfurile nucleului
            var coordinates = []; // lista cu coordonatele varfurilor poligonului selectat
            var infowindow = new google.maps.InfoWindow({
                size: new google.maps.Size(150, 50)
            }); // infowindow care apare cand se da click pe markere
            var allShapes = []; // lista cu toate formele desenate pe harta - ajuta pentru stergerea lor in acelasi timp
            var
                sendable_coordinates = []; // lista cu toate formele desenate pe harta - ajuta pentru stergerea lor in acelasi timp
            var shapeColor = "#007cff"; // culoare forma desenata
            var kernelColor = "#000"; // culoare nucleu

            var default_lat = 28.2511713;
            var default_lng = 78.9542579;
            var zones = @json($latLng);

            // let zones = JSON.parse(data.replace(/&quot;/g, '"'));
            // functie care copiaza textul primit ca parametru in clipboard
            // Primeste ca parametri:
            // text - document.getElementById('id-element').innerHTML,
            // copymsg - document.getElementById('id-element')
            function copyToClipboard(text, copymsg) {
                var temp = document.createElement('input');
                temp.type = 'input';
                temp.setAttribute('value', text);
                document.body.appendChild(temp);
                temp.select();
                document.execCommand("copy");
                temp.remove();
                copymsg.innerHTML = "Copiat în clipboard!"; // mesaj care se va afisa la executarea functiei
                setTimeout(function() {
                    copymsg.innerHTML = ""
                }, 1000); // timp afisare mesaj
            }


            // schimba opacitatea containerului "opcard" atunci cand utilizatorul trece cursorul peste acest element
            function changeOpacityHover() {
                var element = document.getElementById("opcard");
                element.classList.remove("ccard");
                element.classList.add("vcard");
            }

            // schimba opacitatea containerului "opcard" la forma initiala dupa ce cursorul nu se mai afla peste elementul "opcard"
            function changeOpacityOut() {
                var element = document.getElementById("opcard");
                element.classList.remove("vcard");
                element.classList.add("ccard");
            }

            // Atribuie fiecarui marcator o harta
            // parametrul "map" va fi trimis cu valoarea hartii Google sau cu "null"
            function setMapOnAll(map) {
                for (var i = 0; i < gmarkers.length; i++) {
                    gmarkers[i].setMap(map);
                }
            }

            // Ascunde toti marcatorii de pe harta
            function clearMarkers() {
                setMapOnAll(null);
            }


            // Sterge toti marcatorii
            function deleteMarkers() {
                clearMarkers();
                gmarkers = [];
            }


            // functie care sterge forma selectata
            function deleteSelectedShape() {
                if (selectedShape) {
                    selectedShape.setMap(null);
                    var index = allShapes.indexOf(selectedShape);
                    if (index > -1) {
                        allShapes.splice(index, 1);
                    }
                    let lat_lng = [];
                    allShapes.forEach(function(data, index) {
                        lat_lng[index] = getCoordinates(data);
                    });
                    document.getElementById('info').value = JSON.stringify(lat_lng);
                    // document.getElementById('info').value = null; // actualizează lista de coordonate afisata
                }

                if (selectedKernel) {
                    selectedKernel.setMap(null);
                    // document.getElementById('info').value = null; // actualizează lista de coordonate afisata
                }
            }



            // functie care sterge toate formele de pe harta
            function clearMap() {
                if (allShapes.length > 0) { // verific daca exista forme desenate

                    for (var i = 0; i < allShapes.length; i++) // sterge toate formele
                    {
                        allShapes[i].setMap(null);
                    }
                    allShapes = [];
                    deleteMarkers();
                    document.getElementById('info').value = null;
                    // document.getElementById('info').innerHTML = "Desenează un poligon. Aici vor apărea coordonatele vârfurilor sale și vor fi actualizate în timp real."; // actualizează lista de coordonate afisata

                }
            }


            // functie care seteaza culoarea formei selectate ca fiind cea aleasa de utilizator prin Color Picker

            function update(picker) {
                shapeColor = picker.toHEXString();
                if (selectedShape) {
                    selectedShape.setOptions({
                        fillColor: shapeColor
                    });
                }
            }



            // a function that sets the color of the core selected as the one chosen by the user through the Color Picker
            // function that cancels the current selection
            function clearSelection() {
                if (selectedShape) { //check that the selected shape is a polygon
                    if (selectedShape.type !== 'marker') {
                        selectedShape.setEditable(false);
                    }
                    selectedShape = null;
                }

                if (selectedKernel) { // check to see if the selected shape is a core
                    if (selectedKernel.type !== 'marker') {
                        selectedKernel.setEditable(false);
                    }
                    selectedKernel = null;
                }
            }

            // function that selects a form and receives as parameters:
            // shape - the form to be selected
            // check - 0 = polygon, 1 = core
            function setSelection(shape, check) {
                clearSelection();
                console.log(shape);
                shape.setEditable(true);
                shape.setDraggable(true);
                if (check) {
                    selectedKernel = shape;
                } else {
                    selectedShape = shape;
                }
            }



            //display function that saves in the list "coordinates" the coordinates of the points of the polygon given as parameter coordinates coordonatele varfurilor poligonului dat ca parametru
            function getCoordinates(polygon) {
                var path = polygon.getPath();
                coordinates = [];
                for (var i = 0; i < path.length; i++) {
                    coordinates.push({
                        lat: path.getAt(i).lat(),
                        lng: path.getAt(i).lng()
                    });
                }
                return coordinates;
                // document.getElementById('info').value = coordinates;
            }



            // functie care creeaza un marker si primeste ca parametri
            // coord = coordonatele unde va fi creat marker-ul
            // nr = numarul marker-ului
            // map = harta Google Maps
            function createMarker(coord, nr, map) {
                var mesaj = "<h6>Vârf " + nr + "</h6><br>" + "Lat: " + coord.lat + "<br>" + "Lng: " + coord.lng;
                var marker = new google.maps.Marker({
                    position: coord,
                    map: map,
                    //zIndex: Math.round(coord.lat * -100000) << 5
                });
                // displaying marker information at "click"
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.setContent(mesaj);
                    infowindow.open(map, marker);
                });
                google.maps.event.addListener(marker, 'dblclick', function() { // delete marker at "double click"

                    marker.setMap(null);
                });
                return marker;
            }


            // function that offers functionality to the search box
            function searchBox() {
                // Create the search box
                var input = document.getElementById('search-box');
                var searchBox = new google.maps.places.SearchBox(input);

                // Results relevant to the current location
                map.addListener('bounds_changed', function() {
                    searchBox.setBounds(map.getBounds());
                });


                // Get more details on the selected place
                searchBox.addListener('places_changed', function() {
                    var places = searchBox.getPlaces();
                    if (places.length == 0) {
                        return;
                    }

                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function(place) {
                        if (!place.geometry) {
                            return;
                        }
                        var icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25)
                        };

                        if (place.geometry.viewport) {
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });

            }


            // function that initializes the Google Maps, sets its options and calls other functions
            function initMap() {
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 4,
                    center: new google.maps.LatLng(default_lat, default_lng),
                    mapTypeControl: false, // disabled
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                        position: google.maps.ControlPosition.LEFT_CENTER
                    },
                    zoomControl: true,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    },
                    scaleControl: false, // disabled
                    scaleControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER
                    },
                    streetViewControl: false, // disabled
                    fullscreenControl: false // disabled
                });

                var i;
                var polygon;
                for (i = 0; i < zones.length; i++) {
                    polygon = new google.maps.Polygon({
                        paths: zones[i],
                        strokeWeight: 1,
                        strokeColor: '#007cf',
                        fillColor: '#007cff',
                        fillOpacity: 0.4,
                    });
                    polygon.setMap(map);
                    addNewPolys(polygon);
                    allShapes.push(polygon); // save the form to the allShapes list
                    google.maps.event.addListener(polygon, 'click', function(e) {
                        getCoordinates(polygon);
                    });
                    google.maps.event.addListener(polygon, "dragend", function(e) {
                        // console.log(polygon);
                        console.log(getCoordinates(polygon));
                        for (i = 0; i < allShapes.length; i++) { // Clear out the old allShapes entry
                            if (polygon.getPath() == allShapes[i].getPath()) {
                                allShapes.splice(i, 1);
                            }
                        }
                        allShapes.push(polygon);
                        let lat_lng = [];
                        allShapes.forEach(function(data, index) {
                            lat_lng[index] = getCoordinates(data);
                        });

                        console.log(lat_lng);
                        document.getElementById('info').value = JSON.stringify(lat_lng);
                        console.log('nice5');

@this.set('latLng', JSON.stringify(lat_lng));
                    });
                    google.maps.event.addListener(polygon.getPath(), "insert_at", function(e) {

                        for (i = 0; i < allShapes.length; i++) { // Clear out the old allShapes entry
                            if (polygon.getPath() == allShapes[i].getPath()) {
                                allShapes.splice(i, 1);
                            }
                        }
                        allShapes.push(polygon);
                        let lat_lng = [];
                        allShapes.forEach(function(data, index) {
                            lat_lng[index] = getCoordinates(data);
                        });

                        document.getElementById('info').value = JSON.stringify(lat_lng);
                        console.log('nice2');

@this.set('latLng', JSON.stringify(lat_lng));
                    });
                    google.maps.event.addListener(polygon.getPath(), "remove_at", function(e) {
                        getCoordinates(polygon);
                    });
                    google.maps.event.addListener(polygon.getPath(), "set_at", function(e) {
                        getCoordinates(polygon);
                    });

                }

                let lat_lng = [];
                allShapes.forEach(function(data, index) {
                    lat_lng[index] = getCoordinates(data);
                });

                document.getElementById('info').value = JSON.stringify(lat_lng);

                console.log('nice1');

@this.set('latLng', JSON.stringify(lat_lng));
                console.log("lat_lng");
                console.log(lat_lng);
                // console.log(allShapes);

                searchBox();
                // settings for drawing shapes and drawing polygon
                var shapeOptions = {
                    strokeWeight: 1,
                    fillOpacity: 0.4,
                    editable: true,
                    draggable: true
                };

                // initializare Drawing Manager
                drawingManager = new google.maps.drawing.DrawingManager({
                    // direct polygon drawing setting
                    // drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingMode: null,
                    drawingControl: false, //dezactivat
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.RIGHT_CENTER,
                        drawingModes: ['polygon'] //  you can also add: 'marker', 'polyline', 'rectangle', 'circle'
                    },
                    polygonOptions: shapeOptions,
                    map: map
                });
                google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
                    var newShape = e.overlay;
                    // console.log(newShape);
                    allShapes.push(newShape); // save the form to the allShapes list

                    console.log(allShapes);

                    let lat_lng = [];

                    allShapes.forEach(function(data, index) {
                        lat_lng[index] = getCoordinates(data);
                        // console.log(lat_lng);
                    });
                    document.getElementById('info').value = JSON.stringify(lat_lng);
                    console.log('nice');

                    @this.set('latLng', JSON.stringify(lat_lng));

                    newShape.setOptions({
                        fillColor: shapeColor
                    }); // color form with the current value of shapeColor

                    getCoordinates(newShape); // find coordinates peaks
                    // exit drawing mode after completion of the polygon
                    drawingManager.setDrawingMode(null);
                    setSelection(newShape, 0);
                    // select polygon at "click"
                    google.maps.event.addListener(newShape, 'click', function(e) {
                        if (e.vertex !== undefined) {
                            var path = newShape.getPaths().getAt(e.path);
                            path.removeAt(e.vertex);
                            getCoordinates(newShape);
                            if (path.length < 3) {
                                newShape.setMap(null);
                            }
                        }
                        setSelection(newShape, 0);
                    });

                    google.maps.event.addListener(newShape, 'mouseup', function() {
                        for (i = 0; i < allShapes.length; i++) { // Clear out the old allShapes entry
                            if (newShape.getPath() == allShapes[i].getPath()) {
                                allShapes.splice(i, 1);
                            }
                        }
                        allShapes.push(newShape);
                    });

                    //update coordinates
                    google.maps.event.addListener(newShape, 'click', function(e) {
                        getCoordinates(newShape);
                    });
                    google.maps.event.addListener(newShape, "dragend", function(e) {
                        console.log(e);
                        getCoordinates(newShape);
                    });
                    google.maps.event.addListener(newShape.getPath(), "insert_at", function(e) {
                        getCoordinates(newShape);
                    });
                    google.maps.event.addListener(newShape.getPath(), "remove_at", function(e) {
                        getCoordinates(newShape);
                    });
                    google.maps.event.addListener(newShape.getPath(), "set_at", function(e) {
                        getCoordinates(newShape);
                    });

                });
                // Deselect the form when changing the drawing mode or when the user clicks on the map
                google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
                google.maps.event.addListener(map, 'click', clearSelection);


            }

            function addNewPolys(newPoly) {
                google.maps.event.addListener(newPoly, 'click', function() {
                    setSelection(newPoly);
                });
            }
            // start application
            google.maps.event.addDomListener(window, 'load', initMap);
        </script>
        <script src="{{ asset('admin/assets/js/polygon/nucleu.js') }}"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                var keyword = $('#city').val();

                if (keyword) getCoordsByKeyword(keyword);
            });

            $(document).on('change', '#city', function() {
                var val = $(this).val();
                getCoordsByKeyword(val);
            });

            $(document).on('click', '.searchCity', function() {
                var val = $('#city option:selected').val();
                if (val) getCoordsByKeyword(val);
            });

            $(document).on('keyup', '.select2-search__field', function() {
                var val = $(this).val();

                if (val != '' && val.length > 2) {
                    $.ajax({
                        url: '{{ url('getCityBySearch') }}',
                        data: {
                            search: val
                        },
                        method: 'get',
                        success: function(results) {
                            if (results.length > 0) {
                                $('#city').html('');

                                results.forEach(city => {
                                    $('#city').append('<option value="' + city[0] + '">' + city[0] +
                                        '</option>');
                                });
                            }
                        }
                    });
                }
            });

            function getCoordsByKeyword(keyword) {
                // $('#loader').css('display','block');
                // $('#map').css('display','none');

                $.ajax({
                    url: "{{ url('zone/coords/by_keyword') }}/" + keyword,
                    data: '',
                    method: 'get',
                    success: function(results) {
                        if (results) {
                            $('#city_polygon').val(results);

                            // setTimeout(function(){
                            // $('#loader').css('display','none');
                            // $('#map').css('display','block');
                            // }, 1000);
                            window.onload = initMap()
                        }
                    }
                });
            }
        </script>
    @endpush


</div>