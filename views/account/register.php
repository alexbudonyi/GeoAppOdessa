<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
  
  <!-- add Latest compiled and minified CSS bootstrap style -->
    <!-- Latest compiled and minified JavaScript -->

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
  	<h2>Реєстраційна форма</h2>
  </div>

  	<form>
   <div class="form-group">
    <label for="login">Логін</label>
    <input type="username" class="form-control" id="login" aria-describedby="login" placeholder="Введіть будь ласка логін" required />
    <small id="loginError" class="invisible form-text text-muted" >Такий логін вже існує в системі</small>
  </div>
  <div class="form-group">
    <label for="email">Поштова адреса</label>
    <input type="email" class="form-control" id="email" aria-describedby="email" placeholder="Введіть будь ласка адресу поштової скриньки. Приклад mail@i.ua">
    <small id="emailHelp" class="form-text text-muted">Ми ні з ким не ділитимемось Вашою поштою</small>
    <small id="emailError" class="invisible form-text text-muted">Така пошта вже існує в системі</small>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Пароль</label>
    <input type="password" class="form-control" id="password" placeholder="Місце для пароля">
    <small id="passwordError" class="invisible form-text text-muted">Пароль має складатися мінімум із 6 літер</small>
  </div>
  <!--<div class="form-group">
    <label for="exampleInputPassword2">Введіть, будь ласка, пароль повторно</label>
    <input type="password" class="form-control" id="check_pass" placeholder="Пароль" required>
  </div>-->
      <button type = "button" id = "registerUser" class="btn btn-primary">Зареєструватися</button>
</form>

<!-- <button type = "submit" class="btn btn-primary" id = "registerInSys">Зареєструватися</button>-->

  	<p>
  		Вже належите до нашої спільности? Тоді скоріше<a href="?controller=account&action=login"> заходьте</a>
  	</p>
    
  </form>

  </div>

  <script
          src="https://code.jquery.com/jquery-3.3.1.js"
          integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
          crossorigin="anonymous"></script>
  <script>
    
    var el = document.getElementById("registerUser");
    if(el)
        el.addEventListener("click", registerUser);

    function isEmpty(login, email, password){
        var is_empty = false;
        if (login == ''){
            is_empty = true;
            $("#login").addClass("has-error");
        } else
            $("#login").removeClass("has-error");

        if (email == ''){
            is_empty = true;
            $("#email").addClass("has-error");
        } else
            $("#email").removeClass("has-error");

        if (password == ''){
            is_empty = true;
            $("#password").addClass("has-error");
        } else
            $("#password").removeClass("has-error");

        return is_empty;
    }

    function validateEmail(email)
    {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))
        {
            return (true)
            $("#email").removeClass("has-error");
        }

        alert('Некоректно написана пошта');
        $("#email").addClass("has-error");
        return (false)
    }

    function registerUser() {

        var login = document.getElementById('login').value;
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;

        if (isEmpty(login, email, password) == true)
            return;

        if (validateEmail(email) == false)
            return;

        var pass_length = password.length;
        if (pass_length < 6)
        {
            $("#password").addClass("has-error");
            $("#passwordError").removeClass("invisible");
            $("#passwordError").addClass("visible");

            return;
        } else {
            $("#password").removeClass("has-error");
            $("#passwordError").removeClass("visible");
            $("#passwordError").addClass("invisible");
        }

        var role_id = 2;
        var userInfo = [];

        userInfo.push({
            "login": login,
            "email": email,
            "password": password,
            "role_id": role_id
        });

        var userInfoJSON = JSON.stringify(userInfo);

      $.ajax({
        type: "POST",
        url: 'controllers/account_controller.php',
        dataType: 'text',
        data: {"register": userInfoJSON},
        success: function (data) {
            if (data == true) {
                alert('Ви зареєстровані в системі');
                window.location.href = '?controller=account&action=login';
            } else if (data == "dublicate_login") {
                $("#login").addClass("has-error");
                $("#loginError").removeClass("invisible");
                $("#loginError").addClass("visible");

                $("#email").removeClass("has-error");
                $("#emailError").removeClass("visible");
                $("#emailError").addClass("invisible");

            } else if (data == "dublicate_email") {
                $("#email").addClass("has-error");
                $("#emailError").removeClass("invisible");
                $("#emailError").addClass("visible");

                $("#login").removeClass("has-error");
                $("#loginError").removeClass("visible");
                $("#loginError").addClass("invisible");
            } else if (data == "dublicate_login_email") {
                $("#login").addClass("has-error");
                $("#loginError").removeClass("invisible");
                $("#loginError").addClass("visible");

                $("#email").addClass("has-error");
                $("#emailError").removeClass("invisible");
                $("#emailError").addClass("visible");
            }

        },
        error: function (request, status, error) {
          alert("request.responseText");
        }
      });

    }
  </script>

</body>
</html>