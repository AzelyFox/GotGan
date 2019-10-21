<?php

require_once "./inc.php";

function modifyUser(Mysqli $DB, int $userIndex, string $modifyQuery, string $bindType, $bindValue) {
    # execute user modification query
    try {
        $DB_STMT = $DB->prepare($modifyQuery);
        # database query not ready
        if (!$DB_STMT) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : ".$DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->bind_param($bindType, $bindValue, $userIndex);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # user modification query error
            $output = array();
            $output["result"] = -5;
            $output["error"] = "MODIFY USER FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch(Exception $e) {
        # user modification query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

$session = "";
$user_index = 0;

# initialize user index
if (isset($_REQUEST["user_index"]))
{
    $user_index = $_REQUEST["user_index"];
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_index IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# session auth
if (isset($_REQUEST["session"]))
{
    $session = $_REQUEST["session"];
    $validation = validateSession($DB, $session);

    # check user level
    if ($validation["user_level"] < 1) {
        # if target user is session user
        if ($validation["user_index"] != $user_index) {
            $output = array();
            $output["result"] = -4;
            $output["error"] = "NOT ALLOWED";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user id
if (isset($_REQUEST["user_id"]))
{
    $user_id = $_REQUEST["user_id"];
    $idModifyQuery = "UPDATE `Users` SET `user_id` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $idModifyQuery, "si", $user_id);
}

# initialize user pw
if (isset($_REQUEST["user_pw"]))
{
    $user_pw = password_hash($_REQUEST["user_pw"], PASSWORD_BCRYPT);
    $pwModifyQuery = "UPDATE `Users` SET `user_pw` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $pwModifyQuery, "si", $user_pw);
}

# initialize user level
if (isset($_REQUEST["user_level"]))
{
    $user_level = $_REQUEST["user_level"];
    # if target user level is higher than session user level
    if ($user_level > $validation["user_level"]) {
        $output = array();
        $output["result"] = -4;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $levelModifyQuery = "UPDATE `Users` SET `user_level` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $levelModifyQuery, "ii", $user_level);
}

# initialize user name
if (isset($_REQUEST["user_name"]))
{
    $user_name = $_REQUEST["user_name"];
    $nameModifyQuery = "UPDATE `Users` SET `user_name` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $nameModifyQuery, "si", $user_name);
}

# initialize user group
if (isset($_REQUEST["user_group"]))
{
    $user_group = $_REQUEST["user_group"];
    # if normal user try to change its own group
    if ($validation["user_level"] < 1) {
        $output = array();
        $output["result"] = -4;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $groupModifyQuery = "UPDATE `Users` SET `user_group` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $groupModifyQuery, "ii", $user_group);
}

# initialize user student id if exist
if (isset($_REQUEST["user_sid"]))
{
    $user_sid = $_REQUEST["user_sid"];
    $sidModifyQuery = "UPDATE `Users` SET `user_sid` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $sidModifyQuery, "si", $user_sid);
}

# initialize user block if exist
if (isset($_REQUEST["user_block"]))
{
    $user_block = $_REQUEST["user_block"];
    $blockModifyQuery = "UPDATE `Users` SET `user_block` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $blockModifyQuery, "ii", $user_block);
} else {
    $user_block = 0;
}

# initialize user mobile id if exist
if (isset($_REQUEST["user_uuid"]))
{
    $user_uuid = $_REQUEST["user_uuid"];
    $uuidModifyQuery = "UPDATE `Users` SET `user_uuid` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $uuidModifyQuery, "si", $user_uuid);
}

# initialize user email if exist
if (isset($_REQUEST["user_email"]))
{
    $user_email = $_REQUEST["user_email"];
    $emailModifyQuery = "UPDATE `Users` SET `user_email` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $emailModifyQuery, "si", $user_email);
}

# initialize user phone if exist
if (isset($_REQUEST["user_phone"]))
{
    $user_phone = $_REQUEST["user_phone"];
    $phoneModifyQuery = "UPDATE `Users` SET `user_phone` = ? WHERE `user_index` = ?";
    modifyUser($DB, $user_index, $phoneModifyQuery, "si", $user_phone);
}

# user modification success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["user_index"] = $user_index;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
