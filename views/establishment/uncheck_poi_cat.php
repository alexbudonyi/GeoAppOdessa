<html>
<head>

 <!-- add Latest compiled and minified CSS bootstrap style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<style>

    body {
        font-family: 'Garamond', cursive;
        font-size: 2em;
    }

    .catalogue {
        margin: auto;
        width: 80%;
    }

    #parse {
        margin: 1%;
    }
    
</style>

</head>
<body>

 
 <div class="catalogue">
    <button id = "parse" type = "button" class="btn btn-primary">Оновити список закладів із сайту</button>
    <button id = "delete_all" type = "button" class="btn btn-primary">Видалити всі записи</button>
        
        <table id="example" class="cell-border hover" cellspacing="0">
        <thead>
            <tr>
                <th>Номер</th>
                <th>id</th>
                <th>Назва</th>
                <th>Дата створення</th>

                <th></th>
                <th></th>
                <th></th>
                <th></th>

            </tr>
        </thead>
 

    </table>
    </div>

<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
 
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<script>

    var table;
    

    $( document ).ready(function() {
      
      $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'json',
        data: {"get_uncheck_poi_cat":"all"},
        success: function (d) {
        
        table = $('#example').DataTable({
            "language": {
                "decimal": "",
                "emptyTable": "таблиця не має жодних даних",
                "info": "Показано сторінок _END_ із _MAX_",
                "infoEmpty": "Записів немає",
                "infoFiltered": "(відфільтровано _TOTAL_ записів)",
                "infoPostFix": "",
                "lengthMenu": "Виведено по _MENU_  записів на сторінку",
                "thousands": ",",
                //"lengthMenu": "Показано _MENU_ записів",
                "loadingRecords": "Завантаження...",
                "processing": "Обробка...",
                "search": "Пошук",
                "zeroRecords": "На жаль, жодного запису не знайдено =(",
                "paginate": {
                    "first": "Перша",
                    "last": "Остання",
                    "next": "Наступна",
                    "previous": "Попередня"
                },
                "aria": {
                    "sortAscending": ": активувати сортування у зростаючому напрямку",
                    "sortDescending": ": активувати сортування у спадаючому напрямку"
                }


            },

            "bProcessing": true,
                 //"sAjaxSource": "?controller=establishment&action=get_poi_catalogue",
                
                 //"sAjaxSource":"response.php",
            "aaData": d.aaData,
            "columns": [
                { "data": null},
                { "data": 'id',
                    "targets": [ 1 ],
                    "visible": false,
                    "searchable": false },
                { "data": 'name'},
                        //{ "data": 'cat_name'},
                        //{ "data": 'user_login'},
                { "data": 'create_date'},
                { "data": 'option1'},
                { "data": 'option2'},
                { "data": 'option3'},
                { "data": 'option4'}
            ],
            "columnDefs": [ {
                "targets": -1,
                "data": null,
                "defaultContent": "<button id = 'btnAccept' class = 'btn btn-error'>Прийняти</button>"
                },{
                "targets": -2,
                "data": null,
                "defaultContent": "<button id = 'btnDelete' class = 'btn btn-primary'>Видалити</button>"
                }, {
                "targets": -3,
                "data": null,
                "defaultContent": "<button id = 'btnEdit' class = 'btn btn-error'>Редагувати</button>"
                }, {
                "targets": -4,
                "data": null,
                "defaultContent": "<button id = 'btnDetails' class = 'btn btn-primary'>Деталі</button>"
                }],
            "order": [[ 3, "desc" ]],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Всі"]]
        });
            table.on( 'order.dt search.dt', function () {
                table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        },
        error: function (request, status, error) {
          alert("request.responseText");
        }
      });

  //-------------------mark row as selected    
    var id = 1;
    $('#example').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
               var tr = $(this).closest('tr');
               var row = table.row(tr);
               id = row.data().id;

            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
    //-------------------end. mark row as selected


    //--------------------------------------------------------accept
    //-------------------function of accept button
    $('#example').on( 'click', '[id*=btnAccept]', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
               var tr = $(this).closest('tr');
               index = table.row(tr).index();

               var row = table.row(tr);
               id = row.data().id;

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                accept_uncheck_poi(id);
                table.row(index).remove().draw( false );
        }

    } );    
    //-------------------end. function of delete button

    //-------------------function for accept uncheck poi
    function accept_uncheck_poi(id) {
        //var data = table.rows( { selected: true } ).data()[0];
        $.ajax({
        type: "POST",
        url: 'controllers/establishment_controller.php',
        dataType: 'text',
        data: {"accept_uncheck_poi":id},
        success: function (data) {
            //alert("Запис прийнято!");
          
        },
        error: function (request, status, error) {
          alert("request.responseText");
        }
      });
    }
  //-------------------end. function for accept uncheck poi 
  //--------------------------------------------------------end. accept


    //--------------------------------------------------------delete
    //-------------------function of delete button
    $('#example').on( 'click', '[id*=btnDelete]', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
               var tr = $(this).closest('tr');
               index = table.row(tr).index();

               var row = table.row(tr);
               id = row.data().id;

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                var is_deleted = false;
                is_deleted = delete_uncheck_poi(id);

                if (is_deleted == true)
                    table.row(index).remove().draw( false );
        }

    } );    
    //-------------------end. function of delete button

    //-------------------function for delete uncheck poi
    function delete_uncheck_poi(id) {
        //var data = table.rows( { selected: true } ).data()[0];
        if (confirm("Впевнені, що бажаєте видалити запис?")) {
            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'text',
                data: {"delete_uncheck_poi":id},
                success: function (data) {
                    alert("Запис видалено!");
                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });
            return true;
        } else
            return false;
    }
  //-------------------end. function for delete uncheck poi 
  //--------------------------------------------------------end. delete

  //-------------------function for getting details uncheck poi
    $('#example').on( 'click', '[id*=btnDetails]', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
               var tr = $(this).closest('tr');
               //index = table.row(tr).index();

               var row = table.row(tr);
               id = row.data().id;

              window.location.href = '?controller=establishment&action=get_uncheck_details&id=' + id;
        }

    } );   
  //-------------------end. function for getting details uncheck poi
  //------------------------------------------------------end. details
  document.getElementById("parse").addEventListener("click", parse);
  
  function parse()
  {
      $.ajax({
      type: "POST",
      url: 'controllers/establishment_controller.php',
      dataType: 'text',
      data: {"parse":"all"},
      success: function () {
        //var amount = data[0];
        alert("Дані занесено до каталогу незареєстрованих міток");    
        location.reload();
        //location.reload();
    },
    error: function (request, status, error) {
        alert(request.responseText);
        }
      });

  //    table.ajax.reload();
  //$('#example').DataTable().ajax.reload();
  }


  document.getElementById("delete_all").addEventListener("click", delete_all);

  function delete_all()
  {
      $.ajax({
      type: "POST",
      url: 'controllers/establishment_controller.php',
      dataType: 'text',
      data: {"delete_uncheck_pois":"all"},
      success: function (data) {
        alert("Каталог очищено");
        window.location.reload(true);
    },
    error: function (request, status, error) {
      alert("помилка");
        }
      });

  //$('#example').DataTable().ajax.reload();
  }

        $('#example').on( 'click', '[id*=btnEdit]', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                var tr = $(this).closest('tr');
                index = table.row(tr).index();

                var row = table.row(tr);
                id = row.data().id;

                window.location.href = '?controller=establishment&action=edit_uncheckpoi&id=' + id;
            }

        } );
        //-------------------end. function of delete button



});


</script>
</body>
</html>