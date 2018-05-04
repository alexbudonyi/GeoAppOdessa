<!DOCTYPE html>
<head>
    <title>Форма зміни паролю</title>

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
        <h2>Зміна паролю</h2>
    </div>

    <form>

        <div class="form-group">
            <label for="password">Новий пароль</label>
            <input type="text" class="form-control" id="password" placeholder="Пароль">
        </div>

        <button type = "button" class = "btn btn-primary" id = "btnChangePass" onclick="changePass()" >Змінити пароль</button>
    </form>

    <p>
        Введіть новий пароль будь ласка
    </p>


</div>


<script
    src="https://code.jquery.com/jquery-3.3.1.js"
    integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
    crossorigin="anonymous"></script>

<script>

    /*var changePass = document.getElementById("btnChangePass");
    if (changePass)
        changePass.addEventListener("click", changePass);*/

    function changePass() {
        //alert('login');
        var id = "<?php echo $id ?>";
        //var id = 2;
        var password = document.getElementById('password').value;
        if (password == "")
            return alert("Напишіть будь ласка новий пароль");

        var userInfo = [];

        userInfo.push({
            "id": id,
            "password": password,
        });

        var userInfoJSON = JSON.stringify(userInfo);

        $.ajax({
            type: "POST",
            url: 'controllers/account_controller.php',
            dataType: 'text',
            data: {"changePassword": userInfoJSON},
            success: function (data) {
                //alert(data);
                if (data == true) {
                    alert('Пароль змінено');
                    window.location.href = '?controller=account&action=login';
                } else
                    alert('Пароль не змінено');
            },
            error: function (request, status, error) {
                alert("request.responseText");
            }
        });
    }
</script>
</body>
</html>