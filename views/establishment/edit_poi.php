<head>

    <!-- add Latest compiled and minified CSS bootstrap style -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- add styles of OpenLayers library -->
    <link rel="stylesheet" href="libraries/OpenLayers/css/ol.css">

    <link href="libraries/sumoselect.css" rel="stylesheet" />

    <!-- add styles of OpenLayers library -->
    <link rel="stylesheet" href="libraries/OpenLayers/css/ol.css">

    <!-- add Latest compiled and minified CSS bootstrap style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Garamond', cursive;
            font-size: 2em;
        }

        textarea.form-control {
            height: 30%;
        }

        #poi_directions, #poi_categories {}

        #poi_url {
            width: 30%;
        }

        #addresses, #phones {
            display: inline-block;
            width: 30%;
            margin-right: 3%;
        }

        .jumbotron {
            margin-bottom: -3%;
        }

        button {
            margin: 1%;
        }

        .box {
             display: inline-block;    
             margin-top: 1%;
             margin-left: 0.5%;
        }

        .reset_point {
            width: 28%;
                
        }
            
        .search_field {
            width: 40%;
             
        }

        #search_place {
            width: 30%;
             
        }


    </style>
</head>
<body>

<div class="container">
    <div class="jumbotron jumbotron-fluid" style="background-color:#FFEBCD;">
        <h2>Детальна інформація навчального закладу</h2>
    </div>

    <!-- <input type="file" onchange="previewFile()"><br> -->
    <img src="" height="200" alt="Image preview...">

    <form data-toggle="validator" role="form">
        <div class="jumbotron jumbotron-fluid" style="background-color:#FAF0E6;">
            <?php foreach($details as $detail) { ?>

            <div class="form-group" aria-labelledby="poi_name" aria-describedby="poi_name">
                <div class = "col">
                    <label for="create_date">Дата створення</label>
                    <p><?php echo $detail['create_date']; ?></p>
                </div>
            </div><!-- end form-group poi_name -->

            <div class="form-group" aria-labelledby="user_login" aria-describedby="user_login">
                <div class = "col">
                    <label for="user_login">Додав користувач:</label>
                    <p id = "user_login"><?php echo $detail['user_login']; ?></p>
                </div>
            </div>
        </div>


        <div class="jumbotron jumbotron-fluid" style="background-color:#F5F5F5;">

            <div class="form-group" aria-labelledby="name" aria-describedby="name">
                <div class = "col">
                    <label for="name">Назва закладу</label>
                    <input type="text" class="form-control" id="name" placeholder="Назва" value ="<?php echo $detail['name']; ?>" required >
                    <div class="help-block with-errors"></div>
                </div>
            </div>

            <div class="form-group" aria-labelledby="description" aria-describedby="description">
                <div class = "col">
                    <label for="description">Опис закладу</label>
                    <textarea class="form-control" id = "description" aria-label="With textarea" ><?php echo $detail['description']; ?></textarea>
                </div>
            </div>

            <div class="form-group" aria-labelledby="website" aria-describedby="website">
                <div class = "col">
                    <label for="website">Адреса сайту закладу</label>
                    <input type="text" class="form-control" id = "website" placeholder="website" aria-label="website"
                           aria-describedby="basic-addon1" value = "<?php echo $detail['url']; ?>" >
                </div>
            </div>

            <div class="form-group input-group" aria-labelledby="cat_type" aria-describedby="cat_type">
                <div class = "col">
                    <label for="cat_type">Тип закладу</label>
                </div>
                <div class = "col">
                    <select class="categories" id = "cat_type" required>
                        <option disabled selected>Вибрати тип</option>
                    </select>
                </div>
            </div>

            <div class="form-group input-group" aria-labelledby="poi_dirs" aria-describedby="poi_dirs">
                <div class = "col">
                    <label for="poi_dirs">Напрями закладу</label>
                </div>
                <div class = "col">
                    <select class="directions" id = "poi_dirs" multiple >
                        <option disabled selected>Вибрати напрям</option>
                    </select>
                </div>
            </div>

            <div class="form-group input-group" aria-labelledby="addresses" aria-describedby="addresses">
                <div class = "col">
                    <ul class="list-group" id = "addresses">
                        <li class="list-group-item active">Адреси</li>

                        <?php foreach($detail['addresses'] as $address) { ?>
                            <li class="list-group-item" contenteditable="true"><?php echo $address; ?></li>
                        <?php } ?>

                        <li class="list-group-item" contenteditable="true" ></li>
                    </ul>
                    <button type="button" class="btn btn-error" id = "addr_field">Додати поле для нової адреси</button>
                </div>
            </div>
            <!-- <div class="help-block with-errors"></div>data-error="Адреса неправильного формату. Приклад: http//google.com "data-minlength="5" -->
            <div class="form-group input-group" aria-labelledby="phones" aria-describedby="phones">
                <div class = "col">
                    <ul class="list-group" id = "phones">
                        <li class="list-group-item active" id = "poi_phones" >Номери</li>

                        <?php foreach($detail['phones'] as $phone) { ?>
                            <li class="list-group-item" >
                                <input type="number" id="phone" value = "<?php echo $phone; ?>" style="border:none" /></li>
                        <?php } ?>
                        <li class="list-group-item"  ><input type="number"   placeholder="" data-minlength="6" id="phone" placeholder="phone" style="border:none" value = "" ></li>
                    </ul>

                    <button type="button" id = "phone_field" class="btn btn-error" >Додати номер</button>
                </div>
            </div>
            <!--
            <div class="form-group input-group" aria-labelledby="poi_dirs" aria-describedby="poi_dirs">
                <div class = "col">
                    <label for="poi_dirs">Напрями закладу</label>
                </div>
                <div class = "col">
                    <select class="directions" id = "poi_dirs" multiple >
                        <option disabled selected>Вибрати напрям</option>
                    </select>
                </div>
            </div> -->
            <div class="form-group">
            <div class = "col">
                <div id="map" class="map" tabindex="0" style="height:35%"></div>
            </div>

            <div class = "col">
                    <div class="reset_point box">
                        <button type="button" id = "reset_map" class="btn btn-warning" style="width:100%;">Виставити початкові координати закладу</button>
                    </div>  
                    <div class="search_field box">
                    
                        <input type="text" id = "place_name" class="form-control" placeholder="Пошук вулиці, номера будинку...">
                    </div>   
                    <div class="search_btn box">
                        <button type="button" id = "search_place" class="btn btn-warning" style="width:100%;" >Пошук</button>
                    </div>                 
                </div>

            <div class="form-group input-group">
            <div class = "col">
                <!-- <button type="button" id = "delete_poi" class="btn btn-danger">Видалити мітку</button> -->
                <button type="button" id = "update_poi" class="btn btn-danger">Зберегти зміни</button>
            </div>
        </div>
    </form>
</div><!-- end .container-->
</div>


<!--add libraries before end body tag so html rendered before scripts will work -->
<script src = "https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src = "libraries/OpenLayers/build/ol.js"></script>
<!-- add libraries to use bootstrap-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script src="libraries/jquery.sumoselect.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.js"></script>

<script src = "libraries/OpenLayers/build/ol.js"></script>

<script>
    ol.proj.transform = function(coordinate, source, destination) {
        var transformFn = ol.proj.getTransform(source, destination);
        return transformFn(coordinate, undefined, coordinate.length);
    };

    $( document ).ready(function() {

        $("#search_place").on("click", function(){
                var place_name = "Одеса " + document.getElementById("place_name").value;
                //alert(place_name);

                var API_KEY = "953c5128a60dd9";
                var settings = {
                    "async": false,
                    "crossDomain": true,
                    "url": "https://us1.locationiq.org/v1/search.php?key=" + API_KEY + "&q=" + place_name + "&format=json",
                    "method": "GET"
                }

            $.ajax(settings).done(function (response) {
                //alert(response);
                var lon = parseFloat(response[0].lon);
                var lat = parseFloat(response[0].lat);
                
               iconGeometry.setCoordinates(ol.proj.transform([lon, lat ], 'EPSG:4326',
                'EPSG:3857'));
            });
        }); 

        previewFile();
        function previewFile() {
            var res = "<?php if(isset($detail['photo']) ) echo $detail['photo']; else echo "" ?>";

            var preview = document.querySelector('img');
            //var file    = document.querySelector('input[type=file]').files[0];
            //var reader  = new FileReader();

            //reader.onloadend = function () {
            //preview.src = reader.result;
            preview.src = res;
            //}

            //if (file) {
            //reader.readAsDataURL(file);
            //} else {
            //  preview.src = "";
            //}
        }

        var coordinatesGeom = 0, latStart = 0, lonStart = 0, lat, lon, iconGeometry, center;
        var coordinates = <?php echo is_null($detail["coordinates"]) ? "null" : $detail["coordinates"] ?>;

        if (coordinates != null)
        {
            coordinatesGeom = coordinates;

            latStart = coordinatesGeom.coordinates[0];
            lonStart = coordinatesGeom.coordinates[1];

            lat = coordinatesGeom.coordinates[0];
            lon = coordinatesGeom.coordinates[1];

            iconGeometry =  new ol.geom.Point(ol.proj.transform([lon, lat], 'EPSG:4326',
            'EPSG:3857'));
            center = ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857');
        } else {
            //coordinatesGeom = coordinates;

            latStart =  0;
            lonStart = 0;

            lat =  0;
            lon = 0;

            iconGeometry =  new ol.geom.Point(0,0);
            center = ol.proj.transform([30.706787, 46.465302], 'EPSG:4326', 'EPSG:3857');
        }

        var iconFeatures=[];
        var iconFeature = new ol.Feature({
            geometry: iconGeometry,
            name: 'Null Island'
            //population: 4000,
            //rainfall: 500
        });

        iconFeatures.push(iconFeature);

        var vectorSource = new ol.source.Vector({
            features: iconFeatures //add an array of features
        });

        var iconStyle = new ol.style.Style({
            image: new ol.style.Circle({
                radius: 6,
                stroke: new ol.style.Stroke({
                    color: 'white',
                    width: 2
                }),
                fill: new ol.style.Fill({
                    color: 'red'
                })
            })
        });

        //});
        var vectorLayer = new ol.layer.Vector({
            source: vectorSource,
            style: iconStyle
        });


        var map = new ol.Map({
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM(),
                    preload: Infinity
                }), vectorLayer
            ],
            target: 'map',
            view: new ol.View({
                center: center,
                zoom: 14

            })
        });

        map.on('click', function(evt) {
            var coords = ol.proj.toLonLat(evt.coordinate);
            lon = coords[0];
            lat = coords[1];

            if (latStart == '' && lonStart == '')
            {
                latStrart = lat;
                lonStart = lon;
            }
            iconGeometry.setCoordinates(evt.coordinate);
            /*
            var coords = ol.proj.toLonLat(evt.coordinate);
            lon = coords[0];
            lat = coords[1];
            */
        });

        document.getElementById("addr_field").addEventListener("click", addLi);
        document.getElementById("phone_field").addEventListener("click", addLi);

        document.getElementById("reset_map").addEventListener("click", reset_map);
        //document.getElementById("delete_poi").addEventListener("click", delete_poi);
        document.getElementById("update_poi").addEventListener("click", update_poi);

        getCategories();
        getDirections();

        function isValid(p) {
            var phoneRe = /^[2-9]\d{2}[2-9]\d{2}\d{4}$/;
            var digits = p.replace(/\D/g, "");
            return phoneRe.test(digits);
        }

        function getCategories() {
            var cat_id = "<?php echo $detail['cat_id']; ?>";

            //var myNode = document.getElementById('cat_type');
            /*while (myNode.firstChild) {
             myNode.removeChild(myNode.firstChild);
             }*/
            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'json',
                data: {"get_categories": "2"},
                success: function (data) {
                    //alert("I`m in dropdown");
                    //successHandler(data);
                    var index = 0;
                    for (var i = 0; i < data.length; i++) {

                        var ukr_name = data[i].name;
                        var o = new Option(ukr_name);
                        o.setAttribute("data-id", data[i].id);
                        $(o).html(ukr_name);

                        if (data[i].id == cat_id)
                            index = i+1;
                        $("#cat_type").append(o);
                    };

                    $('#cat_type').prop('selectedIndex', index);

                    $("#cat_type").SumoSelect({
                        placeholder: 'This is a placeholder',
                        csvDispCount: 3
                    });
                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });
        }

        function getDirections() {
            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'json',
                data: {"get_directions": "all"},
                success: function (data) {

                    for (var i = 0; i < data.length; i++) {
                        var dirs = [];
                        <?php foreach($detail['directions'] as $dir) { ?>
                        dirs.push("<?php echo $dir; ?>");
                        <?php } ?>

                        var ukr_name = data[i].name;

                        //$('.directions').prop('selectedIndex', i);

                        var o = new Option(ukr_name);
                        if (dirs.includes(ukr_name) == true)
                        //$('.directions').prop('selectedIndex', i);
                            o.setAttribute('selected', 'selected');
                        o.setAttribute("data-id", data[i].id);
                        $(o).html(ukr_name);
                        $(".directions").append(o);
                    }
                    ;

                    $(".directions").SumoSelect({
                        placeholder: 'This is a placeholder',
                        csvDispCount: 3
                    });

                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });
        }

        function addLi() {
            var clicked_id = window.event.target.id;
            var ul, last_element;

            var li = document.createElement("li");
            li.setAttribute("class", "list-group-item");
            //li.setAttribute("contenteditable", "true");
            //x.after(li);

            if (clicked_id == "addr_field") {
                ul = document.getElementById("addresses");
                li.setAttribute("contenteditable", "true");
                //last_element = document.getElementById("poi_addresses").lastChild;
                //ul = last_element.parentNode;
                ul.appendChild(li);
            }
            else if (clicked_id == "phone_field") {
                ul = document.getElementById("phones");

                var x = document.createElement("INPUT");
                x.setAttribute("type", "number");
                x.setAttribute("style", "border:none");
                //x.setAttribute("data-minlength", "6");
                //x.setAttribute("class", "form-control");
                li.appendChild(x);
                ul.appendChild(li);
                //last_element = document.getElementById("phone_field");
                //ul = $('#poi_phones li').last();
            }
            //ul.insertBefore(li, last_element);
        }

        function reset_map() {
            iconGeometry.setCoordinates(ol.proj.transform([lonStart, latStart], 'EPSG:4326',
                'EPSG:3857'));
        }

        /*function delete_poi() {
            //need to add php tag
            var id = " echo $detail['id'] ";
            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'text',
                data: {"delete_poi":id},
                success: function (data) {
                    alert("Запис видалено!");
                    window.location.href = '?controller=establishment&action=poi_catalogue';
                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });
        }*/

        function update_poi() {
            var id = "<?php echo $detail['id']; ?>";
            var coordinates = '';
            if (!((lon == lonStart) & (lat == latStart)))
                coordinates = lon + ',' + lat;
            var name = document.getElementById('name').value;
            var description = document.getElementById('description').value;
            if (description == null)
                description == '';
            var website = document.getElementById('website').value;
            var cat_id = $(".categories").find(':selected').attr('data-id');
            /* if (cat_id == null)
             cat_id = '';
             */
            //var email = document.getElementById('email').value;
            var dirs = [];
            $(".directions").find('option:selected').map(function() {
                if ($(this).attr('data-id') != undefined)
                    dirs.push($(this).attr('data-id'));
            }).get();
            if (dirs.length == 0)
                dirs = '';

            var li_addresses = document.getElementById("addresses").getElementsByTagName("li");
            var addresses = [];
            if (li_addresses.length != 0) {
                for (var i = 1; i < li_addresses.length; i++) {
                    if (!(li_addresses[i].innerText == ""))
                        addresses.push(li_addresses[i].innerText)
                }
            } else addresses = '';
            if (addresses.length == 0)
                addresses = '';

            var li_phones = document.getElementById("phones").getElementsByTagName("li");
            var phones = [];
            if (li_phones.length != 0) {
                for (var i = 1; i < li_phones.length; i++) {
                    //var input_t = li_phones[i].firstChild.value;
                    var input_t = $(li_phones[i]).children('input').val();

                    //var input_t = li_phones[i].firstChild.innerHTML;
                    //var input_t = li_phones[i].firstChild.nodeValue;
                    if (!(input_t == ""))
                        phones.push(input_t);
                }
            } else phones = '';
            if (phones.length == 0)
                phones = '';

            var dirs_str = JSON.stringify(dirs);
            var addresses = JSON.stringify(addresses);
            var phones = JSON.stringify(phones);

            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'text',
                data: {
                    "update_poi": "update",
                    "id": id,
                    "coordinates": coordinates,
                    "name": name,
                    "cat_id": cat_id,
                    "description": description,
                    "website": website,
                    //"email": email,
                    "dirs": dirs_str,
                    "addresses": addresses,
                    "phones": phones
                },
                success: function (data) {
                    //alert(data);
                    alert("Дані оновлено");
                    location.reload();
                },
                error: function (request, status, error) {
                    alert("Помилка оновлення даних. Необхідно звернутись до адміністратора");
                    //alert(request.responseText);
                }
            });
        }
    });

</script>
<?php } ?>

</body>
</html>