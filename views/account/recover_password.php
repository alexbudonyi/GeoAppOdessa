<!DOCTYPE html>
<head>
  <title>Registration system PHP and MySQL</title>

  <!-- add Latest compiled and minified CSS bootstrap style -->

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <style>

    .container {
    width: 65%;
    margin: auto;
}

    .has-error {
    border-color: coral;
        -moz-box-shadow: 0 0 3px #ccc;
    -webkit-box-shadow: 0 0 3px #ccc;
        box-shadow: 0 0 30px #ccc;
    }

  </style>
</head>
<body>

<div class="container">
  <div class="header">
    <h2>Форма входу</h2>
  </div>

  <form>
      <p>
          Введіть будь ласка свою поштову адресу на яку зареєстровані у нашому сервісі
      </p>
      <p>
          На введену адресу буде надісланий лист із новим паролем
      </p>
    <div class="form-group">
      <label for="email">Поштова адреса</label>
      <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Поштова адреса">
    </div>

    <button type = "button" class = "btn btn-primary" id = "btnSendPass" >Надіслати новий пароль</button>
</form>

    <script
        src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>

    <script>
        var sendNewPassBtn = document.getElementById("btnSendPass");
        if (sendNewPassBtn)
            sendNewPassBtn.addEventListener("click", sendNewPass);

        function sendNewPass() {
            //alert('login');
            var email = document.getElementById('email').value;

            $.ajax({
                type: "POST",
                url: 'controllers/account_controller.php',
                dataType: 'text',
                data: {"sendNewPass": email},
                success: function (data) {
                //alert(data);
                if (data == true) {
                    alert('Вам на пошту надісланий лист із новим паролем');
                    window.location.href = '?controller=account&action=login';
                } else
                    alert('Користувача із такою поштою не знайдено');
            },
                error: function (request, status, error) {
                alert("request.responseText");
            }
            });
        }
    </script>
</body>
</html>
