<?php

include("./scripts/services/DataBaseService.php");

$db = new DataBaseService();

$db -> startConnection();

$types = $db -> findAll("role_types");

if (!empty($_POST['login']) && !empty($_POST['password'])) {

    $existingUser = ($db -> selectByParam("users", "login", $_POST["login"]));

    if ($existingUser == null) {
        $db -> insert("users", ["login" => $_POST["login"], "password" => $_POST["password"], "role" => $_POST["userRole"]]);
    } else {
        echo("Пользователь с таким именем уже существует!");
    }

}

$db -> closeConnection();
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Регистрация</title>

    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <div class="center-block">
        
        <div class="card">
            <form method="POST" class="card-form">
                <h1>
                    Регистрация
                </h1>
                <input name="login" type="text" placeholder="Введите логин"/>
                <input id="password" name="password" type="password" placeholder="Введите пароль"/>
                <input id="confirmPassword" name="confirmPassword" type="password" placeholder="Повторите пароль"/>

                <select name="userRole">
                    <?php foreach ($types as $type) { ?>
                        <option value="<?php echo $type['code']; ?>"><?php echo $type['description']?></option>
                    <?php } ?>
                </select>

                <button type="submit" id="regButton">
                    Зарегистрироваться
                </button>
            </form>

            <a href="/index.php" style="width: 100%;">
                <button>
                    Назад
                </button>
            </a>
        </div>
    </div>

    <script>

        var password = document.getElementById("password");
        var confirmPassword = document.getElementById("confirmPassword");

        var regButton = document.getElementById("regButton");
        regButton.disabled = true;

        function checkReg(event) {
            regButton.disabled = this.value != password.value; 
        }

        password.addEventListener("input", checkReg);
        confirmPassword.addEventListener("input", checkReg);

    </script>
</body>
</html>
