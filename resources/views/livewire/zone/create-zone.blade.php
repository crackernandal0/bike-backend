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

    <script
        src="https://maps.google.com/maps/api/js?key=AIzaSyC8DHtH6KQlFbii460Aegpt25GER2Bhshk&libraries=drawing,geometry,places">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"
        integrity="sha512-lzilC+JFd6YV8+vQRNRtU7DOqv5Sa9Ek53lXt/k91HZTJpytHS1L6l1mMKR9K6VVoDt4LiEXaa6XBrYk1YhGTQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('admin/assets/js/polygon/main.js') }}"></script>
    <script>
        // function that initializes the Google Maps, sets its options and calls other functions
        function initMap() {
            var cityCoords = document.getElementById('city_polygon').value;

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,
                center: new google.maps.LatLng(28.2511713, 78.9542579),
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
            if (cityCoords != '') {
                cityCoords = JSON.parse(cityCoords);
                if (cityCoords.length > 0) {
                    var latLng = findAvg(cityCoords);
                    var default_lat = latLng['lat'];
                    var default_lng = latLng['lng'];

                    for (i = 0; i < cityCoords.length; i++) {
                        polygon = new google.maps.Polygon({
                            paths: cityCoords[i],
                            strokeWeight: 1,
                            strokeColor: '#007cff',
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
                            for (i = 0; i < allShapes.length; i++) {
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
                        });
                    }
                    let lat_lng = [];

                    allShapes.forEach(function(data, index) {
                        lat_lng[index] = getCoordinates(data);
                    });

                    document.getElementById('info').value = JSON.stringify(lat_lng);

                    map.setZoom(10);
                    map.setCenter(new google.maps.LatLng(default_lat, default_lng));
                }
            }

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
                allShapes.push(newShape); // save the form to the allShapes list
                let lat_lng = [];
                allShapes.forEach(function(data, index) {
                    lat_lng[index] = getCoordinates(data);
                    console.log(lat_lng);
                });
                document.getElementById('info').value = JSON.stringify(lat_lng);

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


                //update coordinates
                google.maps.event.addListener(newShape, 'click', function(e) {
                    getCoordinates(newShape);
                });
                google.maps.event.addListener(newShape, "dragend", function(e) {
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


</div>
