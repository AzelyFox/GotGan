<?php

require_once "./inc.php";

function modifyUserGroup(Mysqli $DB, int $groupIndex, string $modifyQuery, string $bindType, $bindValue) {
    # execute user group modification query
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
        $DB_STMT->bind_param($bindType, $bindValue, $groupIndex);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # user group modification query error
            $output = array();
            $output["result"] = -5;
            $output["error"] = "MODIFY USER GROUP FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch(Exception $e) {
        # user group modification query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

$session = "";
$user_group_index = 0;
$user_group_name = "";

# initialize user group index
if (isset($_REQUEST["user_group_index"]))
{
    $user_group_index = $_REQUEST["user_group_index"];
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_group_index IS EMPTY";
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
        $output = array();
        $output["result"] = -4;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user group name
if (isset($_REQUEST["user_group_name"]))
{
    $user_group_name = $_REQUEST["user_group_name"];
    $nameModifyQuery = "UPDATE `UserGroup` SET `user_group_name` = ? WHERE `user_group_index` = ?";
    modifyUserGroup($DB, $user_group_index, $nameModifyQuery, "si", $user_group_name);
}

# user modification success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["user_group_index"] = $user_group_index;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
