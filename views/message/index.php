<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
    .container {
         margin: auto;
         width: 70%;
    }

</style>


</head>
<body>

<div class="container">
  <h2>Надіслати лист в тех підтримку</h2>
  <p>Якщо є ідеї чи побажання щодо покращення контенту, то обов`язково надішліть нам листа</p>
  <form id = "my-form">
    <div class="form-group">
      <label for="usr">Тема листа</label>
      <input type="text" class="form-control" id="subject">
    </div>
    <div class="form-group">
      <label for="pwd">Електронна пошта:</label>
      <textarea class="form-control" rows="5" id="email"></textarea>
    </div>
    <div class="form-group">
      <label for="pwd">Текст листа:</label>
      <textarea class="form-control" rows="5" id="message"></textarea>

    </div>
    <button type="button" id = "send_message" class = "btn btn-primary">Надіслати</button>
  </form>
</div>

<script>
  
 $( document ).ready(function() {
    document.getElementById("send_message").addEventListener("click", sendMessage);

    function sendMessage() {
    var subject = document.getElementById('subject').value;
    var message = document.getElementById('message').value;
    var message = document.getElementById('email').value;
    

    $.ajax({
        type: "POST",
        url: 'controllers/message_controller.php',
        dataType: 'text',
        data: {"send_message":"message", "subject":subject, "message":message},
        success: function (data) {
          alert("Коментар відправлено");  
        },
        error: function (request, status, error) {
          //alert("request.responseText");
        }
        }); 
    }

});

</script>

</body>
</html>
