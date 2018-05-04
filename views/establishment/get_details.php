<head>

 <!-- add Latest compiled and minified CSS bootstrap style -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">

  <style>

      #poi_website, #poi_addresses, #poi_phones {
        display: inline-block;
        width: 30%;
        margin-right: 3%;
      }

    .jumbotron {
      margin-bottom: -3%;
    }

    body {
          font-family: 'Garamond', cursive;
          font-size: 2em;
    }

  </style>
</head>
<body>

<div class="jumbotron jumbotron-fluid">
  <div class="container">

  <h1 class="display-4">Детальна інформація навчального закладу</h1>

    <?php foreach($details as $detail) { ?>
    <table class="table">
      <thead>
        <tr>
          <th>Номер</th>
          <th>Назва</th>
          <th>Назва категорії</th>
          <th>Створив користувач</th>
          <th>Дата створення</th>

        </tr>
      </thead>
      <tbody>
      <tr>
  	    <td contenteditable="true"><?php echo $detail['id']; ?></td>
  	    <td contenteditable="true"><?php echo $detail['name']; ?></td>
  	    <td contenteditable="true"><?php echo $detail['cat_name']; ?></td>

        <td contenteditable="true"><?php echo $detail['user_login']; ?></td>
        <td contenteditable="true"><?php echo $detail['create_date']; ?></td>

      </tr>
      </tbody>
    </table>

    <!-- <input type="file" onchange="previewFile()"><br> -->
  <!--  <img src="" height="200" alt="Image preview..."> -->

    <h2>Опис закладу</h2>
    <p class="lead"><?php echo $detail['description']; ?></p>

    <div id = "poi_dirs">

      <h2>Напрямки, які має заклад</h2>
      <ul class="list-group" id = "poi_directions">
        <?php if(!isset($detail['directions'][0]) ) echo "відсутні";
                else foreach($detail['directions'] as $dir) { ?>
                    <li class="list-group-item"><?php echo $dir; ?></li>
                <?php } ?>
      </ul>
    </div><!-- end #poi_phones-->

    <?php } ?>
  </div>
</div>

<div class="jumbotron jumbotron-fluid" style="background-color:#FFFFE0;">
  <div class="container">
  <h2>Контактна інформація</h2>

      <?php foreach($details as $detail) { ?>

        <div id = "poi_website">
          <h4>Веб-адреси закладу</h4>
          <ul class="list-group" id = "poi_website_list">
            <li class="list-group-item"><?php echo $detail['url']; ?></li>
          </ul>
        </div>


        <div id = "poi_addresses">

          <h4>Адреси місцезнаходження закладу</h4>
          <ul class="list-group" id = "poi_addresses_list">

            <?php foreach($detail['addresses'] as $address) { ?>
              <li class="list-group-item"><?php echo $address; ?></li>
            <?php } ?>

          </ul>

        </div><!-- end #poi_phones-->

        <div id = "poi_phones">

          <h4>Телефони закладу</h4>
          <ul class="list-group" id = "poi_phones_list">

            <?php foreach($detail['phones'] as $phone) { ?>
              <li class="list-group-item"><?php echo $phone; ?></li>
            <?php } ?>

          </ul>

        </div><!-- end #poi_phones-->

    <?php } ?>
  </div>
</div>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

  <script src="libraries/jquery.sumoselect.js"></script>
    
  <script>

      $( document ).ready(function() {
          //previewFile();
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

      });


  </script>
</body>
</html>