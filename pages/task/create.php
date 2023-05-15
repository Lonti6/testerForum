<?php

include("../../scripts/services/UserService.php");
include("../../scripts/services/DataBaseService.php");
include("../../scripts/services/TemplatesService.php");

UserService::checkAuth(true);

$connetction = new DataBaseService();

$connetction -> startConnection();

$programmers = $connetction -> findAllByParam("users", "role", "programmer");

if (!empty($_POST["name"])
    && !empty($_POST["description"])
    && !empty($_POST["programmer"])) {

    $connetction -> insert("task",
        [
            "name" => $_POST["name"],
            "description" => $_POST["description"],
            "programmerID" => $_POST["programmer"],
            "testerID" => $_SESSION["user"]["id"],
            "state" => "created",
            "dateCreated" => date("Y-m-d")
        ]
    );

}

$connetction ->closeConnection();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Создание задачи</title>

    <link rel="stylesheet" href="../../styles/main.css">
</head>
<body>

<?php echo TemplatesService::getElementFromHTMLWithParams("header.php", ["login" => $_SESSION["user"]["login"] . ", " . $_SESSION["user"]["role"] ]) ?>

<div class="center-block">

    <div class="card">
        <form method="POST" class="card-form">
            <h1>
                Создание задачи
            </h1>

            <label for="name">Наименование задачи</label>
            <input name="name" type="text" id="name"/>

            <label for="description">Описание задачи</label>
            <textarea name="description" id="description"></textarea>

            <label for="programmer">Разработчик</label>
            <select name="programmer" id="programmer">

                <?php foreach ($programmers as $user) { ?>
                    <option value="<?php echo $user['id']?>"><?php echo $user["login"]?></option>
                <?php } ?>

            </select>

            <button type="submit">
                Создать
            </button>
        </form>
    </div>
</div>
</body>
</html>

