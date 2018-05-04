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

        header {
            position: relative;
        }

        body {
            font-family: 'Garamond', cursive;
            font-size: 2em;
        }

        #create_uncheck_poi {
            margin-bottom: 1%;
        }
</style>

</head>
<body>

 <div class="catalogue">
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
     <button type = "button" id = "create_uncheck_poi" class = "btn btn-primary" style="width:20%; ">Додати точку</button>

     <table id="example" class="cell-border hover" cellspacing="0">
        <thead>

            <tr>
                <th>Номер</th>
                <th>id</th>
                <th>Назва</th>
                <th>Вебсайт</th>
                <th>Логін користувача</th>

                <th></th>
                <th></th>
                <th></th>

            </tr>
        </thead>

    </table>
    </div>

<!-- jQuery -->
 <script src = "https://code.jquery.com/jquery-3.1.1.min.js"></script>
<!-- DataTables -->
 <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="libraries/jquery.sumoselect.js"></script>

<script>

    var table, d;
    
    $( document ).ready(function() {

        //document.getElementsByTagName("th").item(7).style.visibility = "hidden";
        //var tbl = document.getElementById('example');
        //var col = tbl.getElementsByTagName('col')[5];
        //col.style.visibility = "hidden";
        //document.getElementsByTagName("tr").item(7).style.visibility = "hidden";

        document.getElementById("create_uncheck_poi").addEventListener("click", createUncheckPoi);
        loadTableData();

        getCategories();
        getDirections();

        function loadTable(d) {
            table = $('#example').DataTable({
                "initComplete": function(settings, json) {
                    if (<?php
                        if (isset($_SESSION["logged_in"]) && isset($_SESSION["roles"]["admin"]) )
                            echo "true";
                        else
                            echo "false"; ?>) {

                        this.fnSetColumnVis( 5, true );
                        this.fnSetColumnVis( 6, true );
                        this.fnSetColumnVis( 7, true );
                    }
                },
                "language": {
                    "decimal": "",
                    "emptyTable": "таблиця не має жодних даних",
                    "info": "Показано сторінок _END_ із _MAX_",
                    "infoEmpty": "Записів немає",
                    "infoFiltered": "(відфільтровано із _MAX_ записів)",
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
                    {"data": 'name'},
                    {
                        "data": 'url',
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a href='http://" + oData.url + "'>" + oData.url + "</a>");
                        }
                    },
                    {"data": 'user_login'},
                    {"data": 'option1'},
                    {"data": 'option2'},
                    {"data": 'option2'}
                ],
                "columnDefs": [{
                    "targets": -1,
                    "visible": false,
                    "data": null,
                    "defaultContent": "<button id = 'btnDelete' class = 'btn btn-primary'>Видалити</button>"
                }, {
                    "targets": -2,
                    "data": null,
                    "defaultContent": "<button id = 'btnDetails' class = 'btn btn-warning'>Деталі</button>"
                }, {
                    "targets": -3,
                    "visible": false,
                    "data": null,
                    "defaultContent": "<button id = 'btnEdit' class = 'btn btn-error'>Редагувати</button>"
                }
                ],
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Всі"]]

            });

            table.on( 'order.dt search.dt', function () {
                table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        }

        function loadTableData() {

            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'json',
                data: {"get_poi_catalogue" : "all"},
                success: function (d) {
                    loadTable(d);

                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });
        }

        function createUncheckPoi() {
            //alert("create poi");
            if (<?php if (isset($_SESSION["logged_in"])) echo "true"; else echo "false"; ?>) {
                window.location.href = '?controller=establishment&action=create_uncheck_poi';
            } else
                alert("Зареєструйтесь або ввійдіть в систему будь ласка, щоб додати мітку");
        }

        var id;

        //-----------begin row highlight
        //if click the row(i.e. tag tr) it will be highlighted
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

    //---------end row highlight


    //---------begin. delete row by id
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
                is_deleted = delete_poi(id);

                if (is_deleted == true)
                    table.row(index).remove().draw( false );
        }

    } );    

    function delete_poi(id) {
        //var data = table.rows( { selected: true } ).data()[0];
        if (confirm("Впевнені, що бажаєте видалити запис?")) {

            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'text',
                data: {"delete_poi": id},
                success: function (data) {
                    //alert("Запис видалено!");
                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });

            return true;
        } else
            return false;
    }

    //---------end. delete row by id

    $('#example').on( 'click', '[id*=btnDetails]', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
               var tr = $(this).closest('tr');
               //index = table.row(tr).index();

               var row = table.row(tr);
               id = row.data().id;

              window.location.href = '?controller=establishment&action=get_details&id=' + id;
        }

    } );   

    $('#example').on( 'click', '[id*=btnEdit]', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
               var tr = $(this).closest('tr');
               //index = table.row(tr).index();

               var row = table.row(tr);
               id = row.data().id;

              window.location.href = '?controller=establishment&action=edit_poi&id=' + id;
        }

    } );

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

            cat = filterCats.join();
            dirs = filterDirs.join();
            $.ajax({
                type: "POST",
                url: 'controllers/establishment_controller.php',
                dataType: 'json',
                data: { "get_filtered_list":"1", "cat":cat, "dirs":dirs },
                success: function (d) {
                    //alert(d);
                    var oTable = $('#example').dataTable();
                    oTable.fnClearTable();

                    if (d.aaData != '') {
                        oTable.fnAddData(d.aaData);
                    }
                    //loadTable(d);
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
            /* filterCat = $("#categories").find(':selected').attr('data-id');
             var filterDirs = [];
             $(this).find('option:selected').map(function() {
             if ($(this).attr('data-id') != undefined)
             filterDirs.push($(this).attr('data-id'));
             }).get();

             if (filterVals == '')
             getFilteredPOI(true, filterVals);
             else
             getFilteredPOI(false, filterVals); */
        });
        //-----------------------end filter


    });


</script>
</body>
</html>