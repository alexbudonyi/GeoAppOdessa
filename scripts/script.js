 /*
  STRUCTURE

  0. DEFAULT SETTINGS
  1. MAP SOURCES
  2. MAP STYLES
  3. MAP LAYERS
  4. CREATING MAP
  5. MAP FUNCTIONS
  6. FUNCTIONS
*/

//----------------------0. DEFAULT SETTINGS
 var time = performance.now();

$( document ).ready(function() {


    document.getElementById("findMe").addEventListener("click", findMe);
    document.getElementById("searchPOI").addEventListener("click", searchPOI);
    //document.getElementById("add_poi_markerBtn").addEventListener("click", add_poi_marker);
    //document.getElementById("createPOI").addEventListener("click", createPOI);
    document.getElementById("addRoutPointBtn").addEventListener("click", addRoutPoint);
    document.getElementById("stopRoutingBtn").addEventListener("click", stopRouting);

    getFilteredPOI();
    getCategories();
    getDirections();

    var listenAddPBtn = 0;
    //---coordinates for routing
    var points = [];
    //msg_el = document.getElementById('msg'),
    var url_osrm_nearest = '//router.project-osrm.org/nearest/v1/driving/';
    var url_osrm_route = '//router.project-osrm.org/route/v1/driving/';
    var icon_url = '//cdn.rawgit.com/openlayers/ol3/master/examples/data/icon.png';

/*function getFilterVals() {
  var filterVals = [];
     $(this).find('option:selected').map(function() {
        if ($(this).attr('data-id') != undefined)
          filterVals.push($(this).attr('data-id'));
    }).get();

     if (filterVals == '')
      getFilteredPOI(true, filterVals);  
     else 
      getFilteredPOI(false, filterVals);  
}*/
   //----------------------END DEFAULT SETTINGS

   //----------------------1.MAP SOURCES

   //---source for filtered POI-s (filter on amenity...)
   var flickrSource = new ol.source.Vector();
   var testSource = new ol.source.Vector();
   //---source of new POI marker
   var newPOISrc = new ol.source.Vector();
   //---source for routing
   var routingSource = new ol.source.Vector();

   //---add an empty iconFeature to the source of the layer
   var iconFeature = new ol.Feature();
   var iconSource = new ol.source.Vector({
    features: [iconFeature]
  });    
   //----------------------END MAP SOURCES

  //-------------------------------2. MAP STYLES
  var styles = {
      
      univerStyle: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: 'yellow'
          })
        })
      }),
      collegeStyle: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: '#00FA9A'
          })
        })
      }),
      techShoolStyle: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: 'brown'
          })
        })
      }),
      coursesStyle:new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: 'purple'
          })
        })
      }),
      insitituteStyle:new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: 'LightSeaGreen	'
          })
        })
      }),
      unSpecifGroupStyle:new ol.style.Style({
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
      }),
      academyStyle: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: '#ADFF2F'
          })
        })
      }),
      conservatoryStyle: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: '#191970'
          })
        })
      }),
      specSchoolStyle: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: '#8B0000'
          })
        })
      }),
      //style for routing geometry
      route: new ol.style.Style({
        stroke: new ol.style.Stroke({
          width: 3, color: [205, 92, 92, 0.8]
        })
      }),
      //style for icon for routing
      icon: new ol.style.Style({
        image: new ol.style.Icon({
          anchor: [0.5, 1],
          src: icon_url
        })
      }),
      testStyle: new ol.style.Style({
          image: new ol.style.Circle({
              radius: 6,
              stroke: new ol.style.Stroke({
                  color: '#00BFFF',
                  width: 2
              }),
              fill: new ol.style.Fill({
                  color: '#32CD32'
              })
          })
      }),
      default: new ol.style.Style({
        image: new ol.style.Circle({
          radius: 6,
          stroke: new ol.style.Stroke({
            color: 'white',
            width: 2
          }),
          fill: new ol.style.Fill({
            color: 'violet'
          })
        })
      })
    };

//---add icon for display current position on the map
  var iconFeatures=[];
  var iconStyle = new ol.style.Style({
    image: new ol.style.Icon({
      anchor: [0.5, 100],
       // the scale factor
      scale: 0.1,
 //    anchorXUnits: 'fraction',
 //     anchorYUnits: 'pixels',
      anchor: [0.5, 1],
      opacity: 1.0,
      src: 'resources/images/geolocation.png'
       })
      });
//---end. add icon for display current position on the map 

//---style function. return style by amenity
    
//-----------------------REDO
    function getPOIStyle(feature) {
      var fp = feature.getProperties().cat_id;
      //alert(fp);
      if (fp == 3)
        return [styles.univerStyle];
      else if (fp == 4)
        return [styles.collegeStyle];
      else if (fp == 6)
        return [styles.coursesStyle];
      else if (fp == 7)
        return [styles.insitituteStyle];
      else if (fp == 8)
        return [styles.unSpecifGroupStyle];
      else if (fp == 9)
        return [styles.academyStyle];
      else if (fp == 11)
        return [styles.conservatoryStyle];
      else if (fp == 12)
        return [styles.techShoolStyle];
      else if (fp == 13)
        return [styles.specSchoolStyle];
      else if (fp==1)
          return [styles.testStyle];
      //!!!!!!!!!!!!!!!!!!!!!??????????????????????????
      //???????????????????????????????????????????????
      else 
        return [styles.default];
    }
//---end style function. return style by amenity
//-----------------------------END MAP STYLES

//-----------------------------3. MAP LAYERS
//---add layer for filtered POI-S
var filteredLayer = new ol.layer.Vector({
      source: flickrSource,
      style: getPOIStyle
    });
    //---add layer for test point
var testLayer = new ol.layer.Vector({
    source: testSource,
    style: getPOIStyle
})
//---end add layer for test point

//---add layer for new marker??
  var vectorLayer = new ol.layer.Vector({
      source: newPOISrc
  });
  //---end layers for new marker

  //---add layer for geolocation
  var iconLayer = new ol.layer.Vector({
    source: iconSource,
    style : iconStyle
  });

    //---add layer for routing
    var routingLayer = new ol.layer.Vector({
        source: routingSource
    });
    //----------------------------END MAP LAYERS

    //----------------------------4. CREATING MAP
    //---map extent
    var extent = ol.proj.transformExtent(
        [22.15, 40.18, 43.18, 52.38],
        'EPSG:4326', 'EPSG:3857'
    );

    //---create map
    var map = new ol.Map({
      layers: [
          new ol.layer.Tile({
            source: new ol.source.OSM(),
            preload: Infinity,          
            extent: extent  
          }), filteredLayer, testLayer, vectorLayer, routingLayer
        ],        
        target: 'js-map',
        loadTilesWhileAnimating: true,
        view: new ol.View({
          zoom: 14,
          minZoom: 7,
          center: ol.proj.transform([30.706787, 46.465302], 'EPSG:4326', 'EPSG:3857'),
          extent: extent
        })
      });

    //-----------------------END CREATING MAP

    //-----------------------5. MAP FUNCTIONS
    //---map event for click. create POI
    var featureCoord = 0;

    var longpress = false;

    var startTime, endTime;
    $("#js-map").on('mousedown', function () {
        startTime = new Date().getTime();
    });

    $("#js-map").on('mouseup', function () {
        endTime = new Date().getTime();
        longpress = (endTime - startTime < 200) ? false : true;
    });


    map.on('click', function(evt){

  //$("#js-map").on('click', function () {
        if (longpress) { 
          //alert("Long Press");

          newPOISrc.clear();
          var feature = new ol.Feature(
            new ol.geom.Point(evt.coordinate)
          );
          featureCoord = evt.coordinate;

          feature.setStyle(iconStyle); 
          newPOISrc.addFeature(feature);
        }; 
    //});

map.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
        var att = feature.getProperties();
        var id = att.id;
        var name = att.name;

        $.ajax({
          type: "POST",
          url: 'controllers/establishment_controller.php',
          dataType: 'json',

          data: {"get_poi_details":id},
          success: function (data) {
             // $("#collapseOne").on("show.bs.collapse", function(){
                document.getElementById("poi_name").innerHTML = data[0].name;
                addresses_amount = data[0].addresses.length;
                if (addresses_amount > 0)
                  document.getElementById("poi_address").innerHTML = data[0].addresses[0];
                else
                    document.getElementById("poi_address").innerHTML = '';

                var a_url = document.getElementById('poi_url'); //or grab it by tagname etc
                a_url.href = 'http://' + data[0].url;
                a_url.innerHTML = "На сайт закладу";

                var a_go_to_details = document.getElementById('go_to_details'); //or grab it by tagname etc
                a_go_to_details.href = "?controller=establishment&action=get_details&id=" + data[0].id;
                a_go_to_details.innerHTML = "В довідник";

                 //var anchor = window.location.hash;
                 //$(".collapse").collapse('hide');
                 $("#collapseOne").collapse('show');
                 
                //document.getElementById("poi_category").innerHTML = data[0].cat_name;
            //});
         },
          error: function (request, status, error) {
            alert(request.responseText);
          }
        });
        
        }, null, function(layer) {
          return layer === filteredLayer;
    });

    //routing. add start and end point
    if (listenAddPBtn == 1)
    {
        utils.getNearest(evt.coordinate).then(function(coord_street){
        var last_point = points[points.length - 1];
        var points_length = points.push(coord_street);
        
        if (points.length <= 2)
          utils.createFeature(coord_street);

        if (points_length < 2) {
          //msg_el.innerHTML = 'Click to add another point';
          return;
        }
        
        //get the route
        var point1 = last_point.join();
        var point2 = coord_street.join();
    
        fetch(url_osrm_route + point1 + ';' + point2).then(function(r) { 
          return r.json();
        }).then(function(json) {
          if (points.length <= 2) {
          //meteres
          var distance_m = json.routes['0'].distance;
          var distance_km = distance_m / 1000;
            
          //seconds
          var duration_sec = json.routes['0'].duration;
          var duration_min = duration_sec / 60;
          
          //var table = document.getElementById("route_details");
          var tr = (
  '<tr>' + 
    '<td>'+ distance_km.toFixed(3) +'</td>'+
    '<td>'+ duration_min.toFixed(3) +'</td>'+
  '</tr>'
    );
          
       $("#route_details").append(tr);
              }
          if(json.code !== 'Ok') {
        //msg_el.innerHTML = 'No route found.';
          return;
        }
        if (points.length <= 2)
          //utils.createFeature(coord_street);
          utils.createRoute(json.routes[0].geometry);
        });
      });
          
      return;
    }
  });

    var geolocation = new ol.Geolocation({
    projection: map.getView().getProjection(),
    tracking: true,
    trackingOptions: {
        enableHighAccuracy: true,
        maximumAge: 2000
    }
  });

    //---end map event for click. create POI




    //-----------------------6. FUNCTIONS

    $("#search_clear").click(function(callback) {
        newPOISrc.clear();
    });

    $("#search_place").click(function(callback){
        var place_name = "Одеса " + document.getElementById("place_name").value;





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


            //iconFeature.setCoordinates(ol.proj.transform([lon, lat ], 'EPSG:4326',
            //  'EPSG:3857'));
            var feature = new ol.Feature(
                new ol.geom.Point(ol.proj.transform([lon, lat], 'EPSG:4326', 'EPSG:3857'))
            );
            featureCoord = ol.proj.transform([lon, lat ], 'EPSG:4326', 'EPSG:3857');

            feature.setStyle(iconStyle);





            newPOISrc.addFeature(feature);
        });

    });



    //---geolocation
    function findMe()
    {
        //alert("find");
        //myGeolocation();
        var pos = geolocation.getPosition();
        iconFeature.setGeometry(new ol.geom.Point(pos));
        map.getView().setCenter(pos);
        map.getView().setZoom(18);
    }

    map.addLayer(iconLayer);
    //--- end geolocation

    //--- routing
    function addRoutPoint(){
  listenAddPBtn = 1;
}

    var utils = {

  getNearest: function(coord){
    var coord4326 = utils.to4326(coord);    
    return new Promise(function(resolve, reject) {
      //make sure the coord is on street
      fetch(url_osrm_nearest + coord4326.join()).then(function(response) { 
        // Convert to JSON
        return response.json();
      }).then(function(json) {
        if (json.code === 'Ok') resolve(json.waypoints[0].location);
        else reject();
      });
    });
  },
  createFeature: function(coord) {
    var feature = new ol.Feature({
      type: 'place',
      geometry: new ol.geom.Point(ol.proj.fromLonLat(coord))
    });
    feature.setStyle(styles.icon);
    routingSource.addFeature(feature);
  },
  createRoute: function(polyline) {
    // route is ol.geom.LineString
    var route = new ol.format.Polyline({
      factor: 1e5
    }).readGeometry(polyline, {
      dataProjection: 'EPSG:4326',
      featureProjection: 'EPSG:3857'
    });
    var feature = new ol.Feature({
      type: 'route',
      geometry: route
    });
    feature.setStyle(styles.route);
    routingSource.addFeature(feature);
  },
  to4326: function(coord) {
    return ol.proj.transform([
      parseFloat(coord[0]), parseFloat(coord[1])
    ], 'EPSG:3857', 'EPSG:4326');
  }
};

    function stopRouting() {
  routingSource.clear();
  listenAddPBtn = 0;
}
    //--- end routing



    //---fetch geodata POI
    function successHandler(data) {

        var time = performance.now();
        //(data.features).length();
      var featureAmount = Object.keys(data.features).length;
      //alert("I`m in");

      //alert(data);
      // we need to transform the geometries into the view's projection
      var transform = ol.proj.getTransform('EPSG:4326', 'EPSG:3857');
      // loop over the items in the response
      for (var i = 0; i < featureAmount; i++)
      {
          var featureItem = data.features[i];
          var point = featureItem.geometry;
          //var pointCoordinates = point.type;
          var pointCoordinates = point.coordinates;

          var longitude = pointCoordinates[1];
          var latitude = pointCoordinates[0];
          var coordInEPSG = transform([longitude, parseFloat(latitude)]);

          var feature = new ol.Feature({
            geometry: new ol.geom.Point(coordInEPSG),
            id: featureItem.properties[1],       
            name: featureItem.properties[2],
            cat_id: featureItem.properties[3]
          });





          
          //var format = new ol.format.GeoJSON(); 
          //feature.setProperties({'id':featureItem.properties[1], 'amenity':featureItem.properties[2]});
          //flickrSource.getSource().addFeature(format.readFeatures(feature, {
          //featureProjection: 'EPSG:3857'}));
          //feature.setGeometry(geometry);
          flickrSource.addFeature(feature);


    }

}
    $('#clearPOI').click(function(callback){
        //;
        testSource.clear();
        $('#timePOI').val('')

    });

    $('#generatePOI').click(function(callback){
        //;

        generate($('#countPOI').val())
    });

    function normalizeLongitude(lon) {
        var n=Math.PI;
        if (lon > n) {
            lon = lon - 2*n
        } else if (lon < -n) {
            lon = lon + 2*n
        }
        return lon;
    }
    function rad(dg) {
        return (dg* Math.PI / 180);
    }

    function deg(rd) {
        return (rd* 180 / Math.PI);
    }

    function generate(data) {
        time = performance.now();

        coord = 0;
        coord = ol.proj.transform(featureCoord, 'EPSG:3857', 'EPSG:4326');
        if (coord != 0) {
            var longitude = coord[1];
            var latitude = coord[0];
            testSource.clear();

          var   startlon=rad(longitude);
          var startlat=rad(latitude);
        // we need to transform the geometries into the view's projection
        var transformTest = ol.proj.getTransform( 'EPSG:4326','EPSG:3857');
        // loop over the items in the response
        var len = parseInt(data);
        var radiusEarth=6372.796924;
        var maxdist = 10; //radius 10 km

        maxdist=maxdist/radiusEarth;
            var dist = 0;
        for (var i = 0; i < len; i++)
        {

            var cosdif = Math.cos(maxdist) - 1;
            dist =  Math.acos( Math.random()*cosdif + 1);
            var sinstartlat = Math.sin(startlat);
            var cosstartlat = Math.cos(startlat);
            var rad360=2*Math.PI;

            var brg = rad360*Math.random();
            var lat=Math.asin(sinstartlat*Math.cos(dist) + cosstartlat*Math.sin(dist)*Math.cos(brg));
            var lon=deg(normalizeLongitude(startlon*1 + Math.atan2(Math.sin(brg)*Math.sin(dist)*cosstartlat, Math.cos(dist)-sinstartlat*Math.sin(lat))));
            lat = deg(lat);
            dist=Math.round(dist*radiusEarth*10000)/10000;
            brg=Math.round(deg(brg)*1000)/1000;
           console.log(lat+","+lon)



            var coordInEPSGTest = transformTest([parseFloat(lat), parseFloat(lon)]);
            var featureTest = new ol.Feature({
                geometry: new ol.geom.Point(coordInEPSGTest),
                cat_id: 1,
                name: "test"+i

            });



            testSource.addFeature(featureTest);
        }
            time = performance.now() - time;
            $('#timePOI').val('Час виконання '+ parseInt(time) + ' mc')
        }
    }




    //-----------------------END FUNCTIONS

    //-----------------------QUERIES
    function getFilteredPOI(clear, values)
    {
    if (clear == true)
    {
      flickrSource.clear();
      
    } else {

    var filterVals = values;
    var jsonFilterVals;

    if (filterVals == null)
      jsonFilterVals = "all";
    else 
      jsonFilterVals = JSON.stringify(filterVals);
    
    $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'json',
        //dataType: 'text',
        data: {"get_filtered_poi":jsonFilterVals},
        success: function (data) {
            //alert(data);
            flickrSource.clear();
            successHandler(data);        
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
    });
  } 
}

    function getCategories()
    {
      $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'json',
        data: {"get_categories":"2"},
        success: function (data) {
        //alert("I`m in dropdown");  
          //successHandler(data);
          for (var i = 0; i < data.length; i++){
            
            var ukr_name = data[i].name;
            var o = new Option(ukr_name);
            o.setAttribute("data-id", data[i].id);
            $(o).html(ukr_name);
            $(".categories").append(o);
          };
          
          $(".categories").SumoSelect({
             placeholder: 'This is a placeholder',
            csvDispCount: 3 
          });
        },
        error: function (request, status, error) {
          alert("request.responseText");
        }
      });
}


    function getDirections()
    {
  $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'json',
        data: {"get_directions":"all"},
        success: function (data) {
        //alert("directions");  
          //successHandler(data);
          
          for (var i = 0; i < data.length; i++){
            
            var ukr_name = data[i].name;
            var o = new Option(ukr_name);
            o.setAttribute("data-id", data[i].id);
            $(o).html(ukr_name);
            $(".directions").append(o);
          };
          
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

    function getPhoto() {
       // var fn = (function() {
            var encodedFile;
         //   return function() {
                //get html file element
            var fileElem = document.querySelector('input[type=file]');
                //var fileElem = document.getElementById("photo");
                //get file from file element
                //file is FileList object, representing the file or files selected with the file upload button
            var fileObject = fileElem.files[0];
                //check if file was loaded by user
            if (fileObject == undefined)
                return null;
                //get file type
            var fileType = fileObject.type;
               //validate file type
            switch (fileType) {
                case 'image/png':
                case 'image/gif':
                case 'image/jpeg':
                case 'image/pjpeg':
                    break;
                default:
                    return null;
            }

                //var preview = document.querySelector('img');
            var fileReader = new FileReader();
               //read file. that will help to encode file to base64
            var readerFile = fileReader.readAsDataURL( fileObject );
            var encodedFile;
            fileReader.onload = function(e) {
                encodedFile = encodedFile = fileReader.result;
            }

            return encodedFile;
          //  }
        //})();
        //var photo = fn();

        //return photo;

    }

//-----------------------add POI to map db
    function createPOI() {
     var cat_id = null;
     cat_id = $("#newPOICat").find(':selected').attr("data-id");
     if (cat_id != null) {

         //get list of directions
         var directions = [];
         $('#newPOIDirs').find('option:selected').map(function () {
             if ($(this).attr('data-id') != undefined)
                 directions.push($(this).attr('data-id'));
         }).get();

         var url = $("#websiteURL").val();
         var descript = $("#poiDescript").val();
         //---var image = NULL
         var name = $("#poiName").val();

                     coord = 0;
             coord = ol.proj.transform(featureCoord, 'EPSG:3857', 'EPSG:4326');
             if (coord != 0) {
                 var longitude = coord[1];
                 var latitude = coord[0];

             /*var getPhoto2 = (function() {
                 var encodedFile;
                 //return function() {
                 var fn = (function() {
                     var encodedFile;
                     return function() {
                         //get html file element
                         var fileElem = document.querySelector('input[type=file]');
                         //var fileElem = document.getElementById("photo");

                         //get file from file element
                         //file is FileList object, representing the file or files selected with the file upload button
                         var fileObject = fileElem.files[0];
                         //check if file was loaded by user
                         if (fileObject == undefined)
                             return null;
                         //get file type
                         var fileType = fileObject.type;
                         //validate file type
                         switch (fileType) {
                             case 'image/png':
                             case 'image/gif':
                             case 'image/jpeg':
                             case 'image/pjpeg':
                                 break;
                             default:
                                 return null;
                         }

                         //var preview = document.querySelector('img');
                         var fileReader = new FileReader();
                         //read file. that will help to encode file to base64
                         var readerFile = fileReader.readAsDataURL( fileObject );
                         //    var readerFile;
                         var encodedFile = encodedFile = fileReader.result;
                         return encodedFile;
                     }
                 })();
                 var photo = fn();

                 return photo;
//        }
             })();
*/
             //var photo = getPhoto();

             var photo;
             var fileElem = document.querySelector('input[type=file]');
             //var fileElem = document.getElementById("photo");
             //get file from file element
             //file is FileList object, representing the file or files selected with the file upload button           
             //var preview = document.querySelector('img');
             var fileReader = new FileReader();
             var filesAmount = fileElem.files.length;
             var photo = "";
             //check if file was loaded by user
             if (filesAmount == 0)
             {
                poi = packEstabData(cat_id, url, descript, name, directions, longitude, latitude, photo);
                poiJSON = JSON.stringify(poi);
                //var poi = packEstabData(cat_id, url, descript, name, directions, longitude, latitude, photo);
                $.ajax({
                    type: "POST",
                    url: "controllers/establishment_controller.php",
                    dataType: "text",
                    data: {"create_poi": poiJSON},
                    success: function (data) {
                        alert(data);
                        //alert("Мітка відправлена на модерацію");

                 },
                 error: function (request, status, error) {
                    alert("request.responseText");
                 }
              });    
             }
              else {
                var fileObject = fileElem.files[0];
                //get file type
                var fileType = fileObject.type;
                //validate file type
                switch (fileType) {
                 case 'image/png':
                 case 'image/gif':
                 case 'image/jpeg':
                 case 'image/pjpeg':
                     break;
                 default:
                     return null;
             }

              //read file. that will help to encode file to base64
              var readerFile = fileReader.readAsDataURL( fileObject );
             
             }
             
             var poi, poiJSON;
             fileReader.onload = function(e) {
                 photo = fileReader.result;
                 poi = packEstabData(cat_id, url, descript, name, directions, longitude, latitude, photo);
                 poiJSON = JSON.stringify(poi);
//             var poi = packEstabData(cat_id, url, descript, name, directions, longitude, latitude, photo);
                 $.ajax({
                     type: "POST",
                     url: "controllers/establishment_controller.php",
                     dataType: "text",
                     data: {"create_poi": poiJSON},
                     success: function (data) {
                         alert(data);
                         //alert("Мітка відправлена на модерацію");

                     },
                     error: function (request, status, error) {

                         alert("request.responseText");
                     }
                 });
             }

         } else alert('Додайте, будь ласка, мітку із новим закладом на карту');
     } else alert('Додайте, будь ласка, категорію');
 }

    $('#createPOI').click(function(callback){
        //;
        //callback();
        createPOI();

    });




    function packEstabData(cat_id, url, descript, name, directions, longitude, latitude, photo) {
        var poi = {
            poiInfo: []
        };

        poi.poiInfo.push({
            "cat_id": cat_id,
            //"dir_id"  : dir_id,
            "url": url,
            "description": descript,
            "name": name,
            "directions": directions,
            "longitude": longitude,
            "latitude": latitude,
            "photo": photo
        });

        return poi;
    }


//-----------------------end

//-----------------------filter
function getFilterVals() {
  var filterCats = [];
  var filterDirs = [];
  
  $('#categories').find('option:selected').map(function() {
      if ($(this).attr('data-id') != undefined)
        filterCats.push($(this).attr('data-id'));
  }).get();


  $('#directions').find('option:selected').map(function() {
      if ($(this).attr('data-id') != undefined)
        filterDirs.push($(this).attr('data-id'));
  }).get();


  //jsonFilterCat = JSON.stringify(filterCat);
  //jsonFilterDirs = JSON.stringify(filterDirs);
    cats = filterCats.join();
    dirs = filterDirs.join();
  $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'json',
        data: { "get_filtered_cat":cats, "get_filtered_dir":dirs },
        success: function (data) {
            //alert(data);
            //var data = JSON.parse(data);
            flickrSource.clear();
            successHandler(data);        
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
    });
}

$('#categories').change(function() {
    getFilterVals();
     
});

$('#directions').change(function() {
  getFilterVals();

});
//-----------------------end filter


//-----------------------search poi
  function searchPOI()
  {
    var search_pattern = $("#searchName" ).val();
    
    //alert("search");
    $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'json',
        data: {"search_poi":search_pattern},
        success: function (data) {
          //alert("second");  
          flickrSource.clear();
          successHandler(data);
        },
        error: function (request, status, error) {
          alert("request.responseText");
        }
        }); 
  }
//-----------------------end search poi
 });