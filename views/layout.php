<?php
session_start();

/*if (isset($_GET["logOut"])) {
    echo "adsaff";
    session_abort();
    header("Refresh:0");
}*/

?>

<DOCTYPE html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Geo-Education Odessa App</title>

    <!-- add Latest compiled and minified CSS bootstrap style -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>

        header {
            position: relative;
        }

        .active {
            background-color: white;
        }

        .admin_pan {
            /* background: #00FF00; */
            margin-top: 2%;
            padding: auto;
            text-align: right;
            /*border-radius: 5%;
            border: 4px double #bce8f1;*/
            width: 40%;
            height: 10%;
            float: right;
            z-index: 100;
        }

        a {
            display: inline;

        }

        .menu {
            /*background: grey;*/
            margin-top: 1%;
            float: left;
            color: #191970;
            width: 80%;
        }

    </style>
  </head>

  <body>

    <header>
    <div class="container">
    <div class = "menu">

  <!--

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Головна <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?controller=establishment&action=index">Мапа</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Довідники
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Університети</a>
          <a class="dropdown-item" href="#">Коледжі</a>
          <a class="dropdown-item" href="#">Училища</a>
          <a class="dropdown-item" href="#">Курси різних напрямів</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
    </ul>

    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>

-->

      <ul class="list-inline">
         <li><a href="?controller=home&action=about">Головна</a></li>
         <li><a href="?controller=establishment&action=index">Мапа</a></li>
         <li><a href="?controller=establishment&action=poi_catalogue">Довідники</a></li>

         <?php if (isset($_SESSION["logged_in"]) && isset($_SESSION["roles"]["admin"]) ) : ?>
         <li><a href="?controller=establishment&action=uncheck_poi_cat">Не затверджені мітки</a></li>
         <?php endif; ?>
         <li><a href="?controller=establishment&action=categories">Категорії</a></li>
         <li><a href="?controller=establishment&action=directions">Напрями</a></li>

         <?php if (isset($_SESSION["logged_in"]) && isset($_SESSION["roles"]["admin"]) ) : ?>
         <li><a href="?controller=establishment&action=uncheck_cat_dir">Неперевірені категорії/напрями</a></li>
         <?php endif; ?>

         <?php if (isset($_SESSION['logged_in'])) : ?>
         <li><a href="?controller=establishment&action=my_pois">Мої мітки</a></li>
         <?php endif; ?>

         <li><a href="?controller=message&action=index">Зворотній зв'язок</a></li>
      </ul>
          </nav>
    </div>

      <div class = "admin_pan">
        <!-- <button type="button" class = "btn btn-primary signin" id = "register">Зареєструватись</button>
        <button type="button" class = "btn btn-primary login" id = "login" >Ввійти</button> -->
          <?php if (! (isset($_SESSION['logged_in']))) : ?>
          <a href="?controller=account&action=register" class = "btn btn-danger">
              Зареєструватися
             <!-- <img id = "reg" border="0" src="resources/images/register2.png"> -->
          </a>

          <a href="?controller=account&action=login" class = "btn btn-danger">
              Ввійти
             <!-- <img id = "log" border="0" src="resources/images/login2.png"> -->
          </a>
          <?php endif; ?>

          <?php if (isset($_SESSION['logged_in'])) : ?>
          <input type = "button" id = "logOut" class = "btn btn-danger" name = "submit" value = "Вийти" style="width:20%; margin: -15%; " />

          <?php endif; ?>

      </div>
    </div>
    </header>

    <?php require_once('routes.php'); ?>

    <script>
      var logoutEl = document.getElementById("logOut");
      if(logoutEl)
          logoutEl.addEventListener("click", logOut);
      /*function register() {

        window.location.href = '?controller=home&action=register';
      };*/

      function logOut() {
          $.ajax({
              type: "POST",
              url: 'controllers/account_controller.php',
              dataType: 'text',
              data: {"logout": 1},
              success: function (data) {
                  //location.reload();
                  window.location.href = '?controller=establishment&action=index';

              },
              error: function (request, status, error) {
                  alert("request.responseText");
              }
          });
      }

    $(function(){
        // this will get the full URL at the address bar
        var url = window.location.href;

        $(".list-inline a").each(function() {
            // checks if its the same on the address bar
            if(url == (this.href)) {
                $(this).closest("li").addClass("active");
            }
        });

    });


    </script>
<!--
    <footer>
      Copyright
    </footer>
-->

  <body>
<html>