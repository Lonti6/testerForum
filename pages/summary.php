<?php

include("../scripts/services/UserService.php");
include("../scripts/services/DataBaseService.php");
include("../scripts/services/TemplatesService.php");

UserService::checkAuth(true);

$type = "inTest";

if (!empty($_GET["type"])) {
    $type = $_GET["type"];
}

$connection = new DataBaseService();

$connection -> startConnection();

$tasks = $connection -> findAllByParam("task", "state", $type);
$taskTypes = $connection -> findAll("task_type");

$connection -> closeConnection();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Задачи</title>

    <link rel="stylesheet" href="../styles/main.css">
</head>
<body>


    <?php echo TemplatesService::getElementFromHTMLWithParams("header.php", ["login" => $_SESSION["user"]["login"] . ", " . $_SESSION["user"]["role"] ]) ?>

    <div class="body-block">

        <div class="tasks-card">

            <h1>Список задач</h1>

            <ul>
                <?php foreach ($tasks as $index => $task) { ?>

                    <li style="font-size: 25px; color: <?php echo $index % 2 == 0 ? 'green' : 'blue' ?>">
                        <a href="/pages/task/view.php?taskID=<?php echo $task['id'] ?>">
                            <?php
                                $type = $taskTypes[array_search($task["state"], array_column($taskTypes, "code"))];
                                echo $task["name"] . " - " . $type["description"];
                            ?>
                        </a>
                    </li>

                <?php } ?>
            </ul>
        </div>
    </div>


</body>
</html>
