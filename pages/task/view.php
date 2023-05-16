<?php

include("../../scripts/services/DataBaseService.php");
include("../../scripts/services/TemplatesService.php");
include("../../scripts/services/UserService.php");

UserService::checkAuth(true);

$taskID = null;

if (!empty($_GET["taskID"])) {
    $taskID = $_GET["taskID"];
} else if (!empty($_POST["taskID"])) {
    $taskID = $_POST["taskID"];
} else {
    header("Location: /pages/summary.php");
}


$connection = new DataBaseService();
$connection -> startConnection();

if (!empty($_GET["downloadFile"])) {
    $fileName = $_GET["downloadFile"];
    $pathToFile = 'D:\\testerForum\\' . $fileName;
    if (file_exists($pathToFile)) {



        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");

        readfile($pathToFile);
    }
}

if (!empty($_POST["newState"])) {
    $connection -> update("task", ["state" => $_POST["newState"]], ["id" => $taskID]);
}

if (!empty($_POST["newTester"])) {
    $connection -> update("task", ["testerID" => $_POST["newTester"]], ["id" => $taskID]);
}

if (!empty($_POST["newProgrammer"])) {
    $connection -> update("task", ["programmerID" => $_POST["newProgrammer"]], ["id" => $taskID]);
}

if (!empty($_POST["comment"])) {

    $attachID = null;

    if (!empty($_FILES['file'])) {
        $uploaddir = 'D:\\testerForum\\';

        $attachID = uniqid() . basename($_FILES['file']['name']);

        $uploadfile = $uploaddir . $attachID;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
    }



    $connection -> insert("comment",
        [
            "userID" => $_SESSION["user"]["id"],
            "text" => $_POST["comment"],
            "taskID" => $taskID,
            "attachID" => $attachID
        ]);

    header("Location: /pages/task/view.php?taskID=$taskID");
}

$task = $connection -> selectById("task", $taskID);
$taskTypes = $connection -> findAll("task_type");

$users = $connection -> findAll("users");
$programmers = $connection -> findAllByParam("users", "role", "programmer");
$testers = $connection -> findAllByParam("users", "role", "tester");

$otherTypes = array_filter($taskTypes, function ($event) use ($task) {
    return $event["code"] != $task["state"];
});

$type = $taskTypes[array_search($task["state"], array_column($taskTypes, "code"))];

$currentProgrammer = $programmers[array_search($task["programmerID"], array_column($programmers, "id"))];
$currentTester = $testers[array_search($task["testerID"], array_column($testers, "id"))];

$otherTesters = array_filter($testers, function ($tester) use ($currentTester) {
    return $tester["id"] != $currentTester["id"];
});

$otherProgrammers = array_filter($programmers, function ($programmer) use ($currentProgrammer) {
    return $programmer["id"] != $currentProgrammer["id"];
});

$comments = $connection -> findAllByParam("comment", "taskID", $taskID);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $task["name"]?></title>

    <link rel="stylesheet" href="../../styles/main.css">
    <link rel="stylesheet" href="taskStyle.css">
</head>
<body>


    <?php echo TemplatesService::getElementFromHTMLWithParams("header.php", ["login" => $_SESSION["user"]["login"] . ", " . $_SESSION["user"]["role"] ]) ?>

    <div class="body-block">

        <div class="tasks-card">

            <h1><?php echo $task["name"]?></h1>

            <p>
                <?php echo $task["description"]?>
            </p>

            <h1>Обсуждение задачи:</h1>


            <?php foreach ($comments as $comment) {
                $localUser = $users[array_search($comment["userID"], array_column($users, "id"))]
            ?>

                <div class="comment">

                    <div class="icon">
                        <?php echo mb_substr($localUser["login"], 0,1) ?>
                    </div>

                    <div class="comment-body">
                        <div class="comment-header">
                            <?php echo $localUser["login"] ?>
                        </div>

                        <div>
                            <?php echo $comment["text"]; ?>
                        </div>

                        <?php if ($comment['attachID'] != "" && $comment != null) { ?>
                        <form method="GET">
                            <input type="hidden" name="downloadFile" value="<?php echo $comment['attachID']?>">
                            <input type="hidden" value="<?php echo $taskID ?>" name="taskID">
                            <button type="submit">Скачать вложение</button>
                        </form>
                        <?php } ?>
                    </div>


                </div>

            <?php } ?>

            Оставьте комментарий:

            <form method="POST" enctype="multipart/form-data">
                <textarea style="min-width: 450px; margin-bottom: 15px;" name="comment">
                </textarea>


                <input type="file" name="file" style="margin-bottom: 15px;">

                <button type="submit" style="width: 100px;">Отправить</button>

            </form>

        </div>

        <div class="inform-card">

            <h1>О задаче</h1>

            <li>
                Статус:
                <select style="width: fit-content;" id="state">
                    <option value="<?php echo $type['code']; ?>"><?php echo $type["description"]; ?></option>
                    <?php foreach ($otherTypes as $t) { ?>
                        <option value="<?php echo $t['code']?>"><?php echo $t["description"]?></option>
                    <?php } ?>
                </select>
            </li>

            <li>Дата создания: <?php echo  date_format(date_create($task["dateCreated"]),"d.m.Y"); ?></li>
            <li>
                Тестировщик:
                <select style="width: fit-content;" id="tester">
                    <option value="<?php echo $currentTester['id']; ?>"><?php echo $currentTester["login"]; ?></option>
                    <?php foreach ($otherTesters as $t) { ?>
                        <option value="<?php echo $t['id']?>"><?php echo $t["login"]?></option>
                    <?php } ?>
                </select>
            </li>
            <li>
                Разработчик:
                <select style="width: fit-content;" id="programmer">
                    <option value="<?php echo $currentProgrammer['id']; ?>"><?php echo $currentProgrammer["login"]; ?></option>
                    <?php foreach ($otherProgrammers as $p) { ?>
                        <option value="<?php echo $p['id']?>"><?php echo $p["login"]?></option>
                    <?php } ?>
                </select>
            </li>

        </div>


    </div>


    <script src="../../scripts/js/http.js"></script>

    <script>
        var url = "<?php echo $_SERVER['REQUEST_URI']?>";
        url = url.substring(0, url.indexOf("?"))

        var taskId = "<?php echo $taskID; ?>";

        var data = new Map();
        data["taskID"] = taskId;

        var stateInput = document.getElementById("state");
        var testerInput = document.getElementById("tester");
        var programmerInput = document.getElementById("programmer");

        stateInput.addEventListener("change", ev => {
            data["newState"] = stateInput.value;
            post(url, data);
        })

        testerInput.addEventListener("change", ev => {
            data["newTester"] = testerInput.value;
            post(url, data);
        })

        programmerInput.addEventListener("change", ev => {
            data["newProgrammer"] = programmerInput.value;
            post(url, data);
        })
    </script>

</body>
</html>
