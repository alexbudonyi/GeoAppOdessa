<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/style.css">

    <link href="libraries/sumoselect.css" rel="stylesheet" />

    <!-- add styles of OpenLayers library -->
    <link rel="stylesheet" href="libraries/OpenLayers/css/ol.css">

    <!-- add Latest compiled and minified CSS bootstrap style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


    
    <style>

		body {
			font-family: 'Garamond', cursive;
            color: #191970;
		}

        button:not(.signin):not(.login) {
			margin: 1%;
        }

      .card:not(.card-header) {
        padding: 3%;
      }

      .card-body {
      	background-color: #F5F5DC !important;
        padding-left: 6%;
      }

      #page {
		width: 100%;
		height: 100%;
 		overflow-y: auto;
		background: blue;
	  }

	/*---------- map ----------*/
	.map_section, .map {
		position: absolute;
		width: 100%;
		height: 100%;
		background: pink;
	}

	/*--------- end map --------*/

	/*------ actions panel ------*/
	.panel {
		right: 0px;

        padding: 2%;
        background: rgba(32, 178, 170, 0.1);
		/*margin-top: -0.7%;*/
		overflow-y: scroll;

		margin-top: 4%;
        height: 100%;
	}
	/*------ end actions panel -----*/

      header {
        position: absolute;
        z-index: 2;

        width: 95%;
        height: 8%;
        margin-left: 5%;
        background: rgba(32, 178, 170, 0.1);
      }


	  a {
	  	cursor: pointer;

	  }

      #registerWarning {
          color: red;
      }

      .menu {
          font-size: 1.5em;
          margin-right: 5%;
      }

      .admin_pan {

          margin-top: -3%;

          z-index: 100;
      }

    </style>
  </head>
  
  <body>
	<div id = "page">
		<div class = "map_section">
			<div id = "js-map" class = "map"></div><!-- end .map</div><!-- .map_section -->
            <div class = "panel">
<!--
                <div class = "poi_details">
				    <div id = "name"></div>
				    <div id = "description"></div>
			    </div>
-->
			    <div id="accordion">

                    <!-- .............Search ............-->
                    <div class="card">
                        <div class="card" id="headingZero">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseZero" aria-expanded="false" aria-controls="collapseZeo">
                                    Пошук</button>
                            </h5>
                        </div><!-- end .card-header -->
                        <div id="collapseZero" class="collapse" aria-labelledby="headingZero" data-parent="#accordion">
                            <div class="card-body">
                                <div id = "search">
                                    <div class="form-group row">
                                        <input class="form-control" id="place_name" placeholder="Адреса для пошуку">
                                        <button id = "search_place" type = "button" class="btn btn-primary">Знайти</button>
                                        <button id = "search_clear" type = "button" class="btn btn-primary">Очистити</button>
                                    </div><!-- .form-group row -->
                                </div><!-- #search -->
                            </div><!-- end .card-body-->
                        </div><!-- end .collapse-->
                    </div><!-- .card-->
                    <!-- .............END. SHOW DETAILS.............-->


                    <!-- .............SHOW DETAILS.............-->
                    <div class="card">
                        <div class="card" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Деталі закладу</button>
                            </h5>
                        </div><!-- end .card-header -->

                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card card-body" id = "details" >
                                <div class="well well-sm" id = "poi_name"></div>
                                <div class="well well-sm" id = "poi_address"></div>
                                <button type="button" class="btn btn-default"><a id = "poi_url"></a></button>
                                <button type="button" class="btn btn-default"><a id = "go_to_details"></a></button>
                                <button type="button" class="btn btn-default" class="button" data-fancybox data-src="#hidden-content"> Оцінити навчальний заклад<a id = "g"></a></button>
                                <div style="display: none;" id="hidden-content">
                                  <form action="mail.php" method="POST">
                                     <div>
                                         <p>Оцініть рівень викладання</p>
                                          <input id="rng" name="teach" type="range" min="1" max="5" step="1" value="1" onchange="document.getElementById('rangeValueTeach').innerHTML = this.value;">
                                          <span id="rangeValueTeach">1</span>

                                      </div>
                                      <div>
                                         <p>Наявність обладнаних лабораторій</p>
                                          <input id="rng" name="labs" type="range" min="1" max="5" step="1" value="1" onchange="document.getElementById('rangeValueLabs').innerHTML = this.value;">
                                          <span id="rangeValueLabs">1</span>
                                      </div>
                                      <div>
                                         <p>Наявність бюджетних місць</p>
                                          <input id="rng" name="free" type="range" min="1" max="5" step="1" value="1" onchange="document.getElementById('rangeValueFree').innerHTML = this.value;">
                                          <span id="rangeValueFree">1</span>
                                      </div>
                                      <div>
                                         <p>Надання практики на великих підприємствах</p>
                                          <input id="rng" name="pr" type="range" min="1" max="5" step="1" value="1" onchange="document.getElementById('rangeValuePr').innerHTML = this.value;">
                                          <span id="rangeValuePr">1</span>
                                      </div>
                                      <input id="def"  name="def" type="hidden" value=""/>
                                      <input class="button-sub" type="submit" value="Надіслати" />
                                  </form>
                              </div>
                            </div><!-- end .card-body-->
                        </div><!-- end .collapse show-->
                    </div><!-- .card-->
                    <!-- .............END. SHOW DETAILS.............-->

                    <!-- .............GEOLOCATOIN.............-->
                    <div class="card">
                        <div class="card " id="headingTwo">
      		                <h5 class="mb-0">
                                <button class="btn btn-link  collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          				Моя геолокація</button>
                            </h5>
    		            </div><!-- end .card-header -->
			            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
      			            <div class="card-body">
					            <div id = "geolocation" class = "geolocation">
						            <div id ="findMe">
						                <button id = "findMe" type = "button" class="btn btn-primary">Знайди мене</button>
						            </div><!-- end #findMe-->
					                <div id = "comment">
                                        <p>Для відображення Вашого місцезнаходження дайте на це будь ласка дозвіл у вспливаючому вікні</p>
					                </div><!-- end #comment-->
				                </div> <!-- end .geolocation-->
			                </div><!-- end .card-body-->
    	                </div><!-- end .collapse show-->
  		            </div><!-- .card-->
                    <!-- ..........END GEOLOCATOIN........-->

    	            <!-- .............ROUTING.............-->
                    <div class="card">
   			            <div class="card" id="headingThree">
      			            <h5 class="mb-0"><button class="btn btn-link  collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          				Прокласти шлях</button></h5>
    		            </div><!-- end .card-header -->
			            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
      		                <div class="card-body">
					            <div id = "routing" class = "routing">
						            <div class="btn-group">
							            <button id = "addRoutPointBtn" type = "button" class="btn btn-default">Додати точки</button>
						            </div><!-- end point-->
                                    <button id = "stopRoutingBtn" type = "button" class="btn btn-default">Прибрати точки</button>
                                    <table id="route_details" class = "table table-hover">
                                        <tr>
          			                        <th>Дистанція(км)</th>
           		                            <th>Час(хв.)</th>
           		                        </tr>
                                    </table>
                                    <div class="route_detailss">
                                        <p class="text-justify" id = "distance"></p>
                                        <p class="text-justify" id = "duration"></p>
                                    </div><!-- end .card-body-->
                                    <div id = "comment">
						                <p>Для того, щоб прокласти маршрут необхідно вибрати початкову і кінцеву точки</p>
						            </div><!-- end #comment-->
					            </div> <!-- end .routing-->
				            </div><!-- end .card-body-->
                        </div><!-- end .collapse show-->
  		            </div><!-- .card-->
                    <!-- ............END ROUTING.......... -->

                    <!-- ............FILTER.......... -->
                    <div class = "card">
                        <div class="card" id="headingFour">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">Моі фільтри</button>
                            </h5>
                        </div><!-- end .card-header -->
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                            <div class="card-body">
                                <div id = "filterPOI">
                                    <select class = "categories" id="categories" multiple>
                                        <option disabled selected>Вибрати тип</option>
                                    </select>
                                    <p></p>
                                    <select class="directions"  id = "directions" multiple>
                                        <option disabled selected>Вибрати напрям</option>
                                    </select>
                                    <p></p>
                                    <div id = "comment"><p>За бажанням можна вибрати фільтр для точок карти</p></div><!-- end #comment-->
                                </div><!-- end #filterPOI-->
                            </div><!-- end .card-body-->
                        </div><!-- end .collapse show-->
                    </div><!-- end .collapse show-->
                    <!-- ............END FILTER.......... -->

                    <!-- .......CREATE ESTABLISHMENT......-->
                    <div class="card">
                        <div class="card" id="headingFive">
      					    <h5 class="mb-0">
        				        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
          Додати новий заклад</button>
                            </h5>
    				    </div><!-- end .card-header-->
                        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
      			            <div class="card-body">
							    <div id = "createNewPOI">
  								    <form>
                                        <div id = "registerWarning" style="display:none">Щоб додати мітку необхідно зареєструватись</div>
                                        <div class="form-group row">
  										    <input class="form-control" id="poiName" placeholder="Назва закладу" >
  								        </div>
 									    <div class="form-group row">
  										    <input class="form-control" id="websiteURL"  placeholder="Посилання на сайт закладу">
      									</div>
                                        <div class="form-group row">
  										    <input class="form-control" id="poiDescript" placeholder="Опис закладу">
  							  		    </div>
                                        <div class="form-group row">
                                            <select class="categories" id = "newPOICat">
                                                <option disabled selected>Вибрати тип</option>
                                            </select>
                                        </div><!-- .form-group row -->
                                        <div class="form-group row">
                                            <select class="directions"  id = "newPOIDirs" multiple >
                                                <option disabled selected>Вибрати напрям</option>
                                            </select>
                                        </div>
                                        <div class="form-group row">
  				                            <div class="custom-file">
					                            <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                                <input name = "photo" type="file" class="custom-file-input" id="photo" /><!-- here removed attr 'required'-->
    					                        <label class="custom-file-label" for="validatedCustomFile">Завантажити фото закладу</label>
    					                        <div class="invalid-feedback">Операція скасована</div>
                                            </div><!-- end .custom-file -->
  				                        </div><!-- form-group row -->
                                        <div class="form-group row">
                                            <button id = "createPOI" type = "button" class="btn btn-primary" >Додати мітку</button>
                                        </div><!-- form-group row -->
                                    </form>
  								
                                    <div id = "comment">
								        <p>Для того, щоб додати мітку натисніть на карту, після цього узгодьте вибір натиснувши на кнопку</p>
								    </div><!-- end #comment -->
                                </div><!-- end #createNewPOI -->
						    </div><!-- end .card-body -->
                        </div><!-- end .collapse -->
                    </div><!-- .card-->
                    <!-- .....END CREATE ESTABLISHMENT....-->

                    <!-- ............SEARCH.......... -->
				    <div class="card">
    				    <div class="card" id="headingSix">
      					    <h5 class="mb-0">
        				        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
          Знайти заклад за назвою</button>
                            </h5>
    					</div><!-- end .card-header -->
    					<div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
      				        <div class="card-body">
								<div id = "search">
									<div class="form-group row">
  								        <input class="form-control" id="searchName" placeholder="Назва закладу">
  									    <button id = "searchPOI" type = "button" class="btn btn-primary">Запитати</button>
                                        <div id = "comment">
								            <p>Для того, щоб знайти учбовий заклад, треба вказати у полі "Пошук" його назву</p>
								        </div><!-- end #comment-->
                                    </div><!-- .form-group row -->
                                </div><!-- #search -->
                            </div><!-- end .card-body-->
    			  	    </div><!-- end .collapse-->
  				 </div><!-- .card-->
                <!-- ............END SEARCH.......... -->
                    <!-- ............testing.......... -->
                    <div class="card">
                        <div class="card" id="headingSeven">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                   Тестування</button>
                            </h5>
                        </div><!-- end .card-header -->
                        <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                            <div class="card-body">
                                <div id = "search">
                                    <div class="form-group row">
                                        <input class="form-control" id="countPOI" placeholder="Кількість точок">
                                        <button id = "generatePOI" type = "button" class="btn btn-primary">Згенерувати</button>
                                        <button id = "clearPOI" type = "button" class="btn btn-primary">Очистити</button>
                                        <input class="form-control" id="timePOI">
                                    </div><!-- .form-group row -->
                                </div><!-- #search -->
                            </div><!-- end .card-body-->
                        </div><!-- end .collapse-->
                    </div><!-- .card-->
                    <!-- ............END SEARCH.......... -->
                </div><!-- end .panel-->
            </div><!-- end map section-->
        </div><!-- end map section-->
    </div><!-- end map section-->

    <!--add libraries before end body tag so html rendered before scripts will work --> 

    <!-- <script src = "libraries/jquery.js"></script> -->
    <script src = "libraries/OpenLayers/build/ol.js"></script>
    <script src="libraries/jquery.js"></script>
    <!-- add libraries to use bootstrap-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="libraries/jquery.sumoselect.js"></script>
    <script src = "scripts/script.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.css" />

    <script>

        if (! <?php if (! isset($_SESSION["logged_in"]) ) echo "false"; else echo "true" ?> ) {
            var createBtnEl = document.getElementById("createPOI");
            var registerWarningEl = document.getElementById("registerWarning");

            createBtnEl.disabled = true;
            createBtnEl.style.background = 'grey';
            registerWarningEl.style.display  = 'initial';
        }

    </script>
  
  </body>
</html>