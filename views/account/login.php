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

    .menu {
        font-size: 1.5em;
        margin-right: 5%;
    }

    .admin_pan {
        margin-top: -3%;
    }

    body {
        font-family: 'Garamond', cursive;

    }


  </style>
</head>
<body>

<div class="container">
  <div class="header">
    <h2>Форма входу</h2>
  </div>
	 
  <form>

    <div class="form-group">
      <label for="login">Логін</label>
      <input type="login" class="form-control" id="login" aria-describedby="loginHelp" placeholder="Логін">
    </div>

    <div class="form-group">
      <label for="password">Пароль</label>
      <input type="password" class="form-control" id="password" placeholder="Пароль">
    </div>

    <button type = "button" class = "btn btn-primary" id = "btnLogin" >Ввійти</button>
</form>

    <p>
        Якщо забули пароль, то перейдіть будь ласка за наступним посиланням <a href="?controller=account&action=recover_password">Відновити пароль </a>
    </p>

  	<p>
  		Якщо ще не зареєтрувалися, то Вам <a href="?controller=account&action=register">сюди Зареєструватись</a>
  	</p>
</div>


    <script
        src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>

    <script>
        var loginEl = document.getElementById("btnLogin");
        if (loginEl)
            loginEl.addEventListener("click", login);

        function isEmpty(login, password){
            var is_empty = false;
            if (login == ''){
                is_empty = true;
                $("#login").addClass("has-error");
            } else
                $("#login").removeClass("has-error");

            if (password == ''){
                is_empty = true;
                $("#password").addClass("has-error");
            } else
                $("#password").removeClass("has-error");

            return is_empty;
        }

        function login() {
            //alert('login');
            var login = document.getElementById('login').value;
            var password = document.getElementById('password').value;

            if (isEmpty(login, password) == true)
                return;

            var userInfo = [];

            userInfo.push({
                "login": login,
                "password": password,
            });

            var userInfoJSON = JSON.stringify(userInfo);

            $.ajax({
                type: "POST",
                url: 'controllers/account_controller.php',
                dataType: 'text',
                data: {"login": userInfoJSON},
                success: function (data) {
                    //alert(data);
                    if (data == true) {
                        alert('Ввійшли');
                        window.location.href = '?controller=establishment&action=my_pois';
                    } else
                        alert('Користувача із такими даними немає');
                },
                error: function (request, status, error) {
                    alert("request.responseText");
                }
            });
        }
    </script>
</body>
</html>