<html>
<head>

 <!-- add Latest compiled and minified CSS bootstrap style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">

    <link href="libraries/sumoselect.css" rel="stylesheet" />

    <style>
        .container {
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

        #add_cat, #edit_cat {
            margin-bottom: 2%;
        }

        #addCategory, #editCategory {
            margin-top: 1%;
            width: 20%;
        }


    </style>

</head>
<body>

<div class = "container">

    <div class="container">

	 	<table id="example" class="cell-border hover" cellspacing="0">
        <thead>
            <tr>
                <th>Номер</th>
                <th>id</th>
                <th>Назва</th>
                <th>Вид даних</th>
                <th>Вид даних id</th>

                <th></th>
                <th></th>

            </tr>
        </thead>

        <tfoot>
            <tr>
               <th>номер</th>
               <th>id</th>
               <th>назва</th>
               <th>Вид даних</th>
               <th>Вид даних id</th>

               <th></th>
               <th></th>
            </tr>
        </tfoot>
        </table>
    </div>
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
        data: {"get_uncheck_cat_dir":"2"},
        success: function (d) {

            table = $('#example').DataTable({
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
            "aaData": d,
            "columns": [
                    { "data": null},
                    { "data": 'id',
                        "targets": [ 1 ],
                        "visible": false,
                        "searchable": false },
                    { "data": 'name'},
                    { "data": 'data_type'},
                    { "data": 'data_type_id',
                        "targets": [ 4 ],
                        "visible": false,
                        "searchable": false },
                    { "data": 'option2'},
                    { "data": 'option3'}
            ],
            "columnDefs": [ {
                "targets": -2,
                    "data": null,
                    "defaultContent": "<button id = 'btnAccept' class = 'btn btn-error'>Прийняти</button>"
                }, {
                "targets": -1,
                     "data": null,
                     "defaultContent": "<button id = 'btnReject' class = 'btn btn-error'>Відхилити</button>"
                } ]
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

        var edit_name, edit_id, index;

        //---------begin. delete row by id
        $('#example').on( 'click', '[id*=btnReject]', function () {
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

                reject_cat_dir(id);

                table.row(index).remove().draw( false );
            }
        });

        function reject_cat_dir(id) {
            if (confirm("Впевнені, що бажаєте видалити запис?")) {
                //txt = "You pressed OK!";

                $.ajax({
                    type: "POST",
                    url: "controllers/establishment_controller.php",
                    dataType: "text",
                    data: {"delete_uncheck_cat_dir": id},
                    success: function (data) {
                    //alert(data)
                    alert("Запис видалено!");
                },
                    error: function (request, status, error) {
                    alert("request.responseText");
                }
                });

                return true;
            } else {
                return false;
            }
        }
        //--------end delete

/*
        $('#example').on( 'click', '[id*=btnEdit]', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                var tr = $(this).closest('tr');
                index = table.row(tr).index();

                var row = table.row(tr);

                edit_id = row.data().id;
                edit_name = row.data().name;
                document.getElementById('edit_cat_name').value = edit_name;

                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');

                document.getElementById("edit_cat_name").focus();
            }

        } );



        function editCategory(id) {
            //var data = table.rows( { selected: true } ).data()[0];
            var edit_cat_id = edit_id;
            var edit_cat_name = document.getElementById('edit_cat_name').value;

            $.ajax({
                type: "POST",
                url: "controllers/establishment_controller.php",
                dataType: "text",
                data: {"edit_category" : "edit", "edit_cat_id": edit_cat_id, "edit_cat_name": edit_cat_name },
                success: function (data) {
                alert("Назву оновлено");
                var oTable = $('#example').dataTable();
                location.reload();
                //dt.row(0).cells().invalidate().render()
                //edit_cat(id);

                //$('#table1').dataTable().fnUpdate(temp,5,undefined,false);

                //table.row(index).remove().draw( false );
            },
                error: function (request, status, error) {
                alert("request.responseText");
            }
            });
        }
        //------end edit
*/

        $('#example').on( 'click', '[id*=btnAccept]', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                var tr = $(this).closest('tr');
                index = table.row(tr).index();

                var row = table.row(tr);

                accept_data_id = row.data().id;
                data_type_id = row.data().data_type_id;
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');

                Accept(accept_data_id, data_type_id);

                table.row(index).remove().draw( false );
                //document.getElementById("edit_cat_name").focus();
            }

        } );

        function Accept(id, data_type_id) {

                $.ajax({
                    type: "POST",
                    url: 'controllers/establishment_controller.php',
                    dataType: 'text',
                    data: {"accept_uncheck_cat_dir": id, "data_type_id": data_type_id},
                    success: function (data) {
                    //alert(data);
                    alert('Запис додано');
                    //location.reload();
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
]