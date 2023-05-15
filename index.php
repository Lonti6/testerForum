<?php
include("./scripts/services/DataBaseService.php");

if (!empty($_POST["login"] && !empty($_POST["password"]))) {

    $connection = new DataBaseService();
    $connection -> startConnection();

    $user = $connection -> selectByParam("users", "login", $_POST["login"]);

    $isAuth = true;

    if ($user == null) {
        echo "Пользователь с таким именем не найден!";
        $isAuth = false;
    }

    if ($isAuth && $user["password"] != $_POST["password"]) {
        echo "Неверный пароль!";
        $isAuth = false;
    }

    if ($isAuth) {
        session_start();

        $_SESSION["isAuth"] = true;
        $_SESSION["user"] = $user;

        header("Location: /pages/summary.php");
    }


    $connection -> closeConnection();
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Авторизация</title>

    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <div class="center-block">
        
        <div class="card">
            <form method="POST" class="card-form">
                <h1>
                    Авторизация
                </h1>
                <input name="login" type="text"/>
                <input name="password" type="password"/>

                <button type="submit">
                    Войти
                </button>
            </form>

            <a href="./register.php" style="width: 100%;">
                <button>
                    Зарегистрироваться
                </button>
            </a>


        </div>
    </div>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <div style="display: flex; flex-direction: column; align-items: end; margin-right: 20px;">
        <div>
            <p>ИВТ-20-1, 2023 г.</p>
            <p>Жильцов Н.С.</p>
            <p>Лямина О.С.</p>
        </div>
    </div>
</body>
</html>
