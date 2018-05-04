<head>

 <!-- add Latest compiled and minified CSS bootstrap style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link href="libraries/sumoselect.css" rel="stylesheet" />

<style>
    .catalogue {
         margin: auto;
    width: 80%;
    }
    
</style>

</head>
<body>
	<div class="container">
  <h2>Додавання нової мітки</h2>
  <p>Увага! Новий заклад спочатку повинен пройти модерацію і лише після цього буде доданий в довідник</p>
  <form>
    <div class="form-group">
      <label for="name">Назва закладу</label>
      <input type="text" class="form-control" id="name">
    </div>
    <div class="form-group">
      <label for="descr">Опис закладу</label>
       <textarea class="form-control" rows="5" id="descr"></textarea>
    </div>
    <div class="form-group">
  <select class = "categories" id="categories">
	<option disabled selected>Вибрати фільтр</option>
						      		</select>
  									
	<select class="directions"  id = "directions" multiple>
	  <option disabled selected>Вибрати напрям</option>
    </select>
 									
    </div>
    <div class="form-group">
      <label for="email">Поштова адреса закладу</label>
      <input type="text" class="form-control" id="email">
    </div>
    <div class="form-group">
      <label for="address">Адреса розташування</label>
      <input type="text" class="form-control" id="address">
    </div>
    <div class="form-group">
      <label for="phone">Телефон закладу</label>
      <input type="text" class="form-control" id="phone">
    </div>
    <div class="form-group">
      <label for="website">Веб-сайт закладу</label>
      <input type="text" class="form-control" id="website">
    </div>
   	
  </form>
  <button type="button" id = "create_poi" class = "btn btn-primary">Створити мітку</button>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

<script src="libraries/jquery.sumoselect.js"></script>
<script>
  
 $( document ).ready(function() {
    document.getElementById("create_poi").addEventListener("click", createPoi);

    getCategories();
	getDirections();

var filterCat;
$('#categories').change(function() {
     filterCat = $("#categories").find(':selected').attr('data-id');
});

var filterDirs = [];
$('#directions').change(function() {
     filterDirs = [];
     $(this).find('option:selected').map(function() {
        if ($(this).attr('data-id') != undefined)
          filterDirs.push($(this).attr('data-id'));
    }).get();
});

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

    function createPoi() {
      var name = document.getElementById('name').value;
      var cat_id = filterCat;
      var description = document.getElementById('descr').value;
      var website = document.getElementById('website').value;
      var email = document.getElementById('email').value;
      var dirs_id = filterDirs;
      var address = document.getElementById('address').value;
      var phone = document.getElementById('phone').value;

      if (cat_id == undefined)
          cat_id = '8';
      if (dirs_id == undefined)
          dirs_id == '6';

      $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'text',
        data: {"add_uncheck_poi":"create", "name":name, "cat_id":cat_id, "description":description, "website":website, "email":email, "dirs_id":dirs_id, "address":address, "phone":phone },
        success: function (data) {
          //alert(data);
          alert("Заклад додано");
        },
        error: function (request, status, error) {
          alert("request.responseText");
        }
        }); 
    }

});

</script>
</body>
</html>