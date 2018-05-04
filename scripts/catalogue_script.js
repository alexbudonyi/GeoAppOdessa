getCategories();

$('#categories').change(function() {
     var filterVals = [];
     $(this).find('option:selected').map(function() {
        if ($(this).attr('data-id') != undefined)
          filterVals.push($(this).attr('data-id'));
    }).get();

      getFilteredPOI(filterVals);  
});

document.getElementById("delete_poi").addEventListener("click", function() {
 	
 	var poi_id = this.getAttribute('data-id');
 	$.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'text',
        data: {"delete_poi":poi_id},
        success: function (data) {
        	alert("Запис видалено!");
          
    	},
        error: function (request, status, error) {
          alert("request.responseText");
        }
      });
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
            var eng_name = data[i][1];
            var ukr_name = data[i][2];
            var o = new Option(ukr_name, eng_name);
            o.setAttribute("data-id", data[i][0]);
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


function delete_poi()
{   
	alert("filter");
    /*
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
        data: {"get_filtered_catalogue":jsonFilterVals},
        success: function (data) {
            //alert(data);
            //flickrSource.clear();
            //successHandler(data);        
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
    });
  	*/
}
