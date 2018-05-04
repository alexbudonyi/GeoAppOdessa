<?php
require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/privileged_user.php");
/*
if (isset($_SESSION["logged_in"])) {
    $u = PrivilegedUser::getByUsername($_SESSION["logged_in"]);
    echo $_SESSION["logged_in"] = true;
    echo $_SESSION["login"];
    echo $_SESSION["user_id"];
    //echo $_SESSION['user_id'];
    //echo "l7898ogin";
} else echo "no";
*/
?>

<html>
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

        table {
            font-size: 1.0em;
        }

    </style>
</head>
<body>

<div class="container">
    <div class="jumbotron jumbotron-fluid" style="background-color:floralwhite;">
        <h2>Мої мітки</h2>
        <table class = "table table-bordered">
            <tr>
                <th>Назва</th>
                <th>Дата занесення в довідник</th>
                <th>Посилання на місце в довіднику</th>
            </tr>
                <?php foreach($pois as $poi) { ?>
                    <?php foreach($poi as $item) { ?>
                    <tr>
                        <td>
                            <?php echo $item['name'] ?>
                        </td>

                        <td>
                            <?php echo $item['create_date'] ?>
                        </td>
                        <td>
                            <a href="<?php $estab_link = '?controller=establishment&action=get_details&id=' . $item['id']; echo $estab_link?>">Деталі</a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php }  ?>


        </table>
    </div>
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

    $( document ).ready(function() {

    });
</script>

</body>
</html>