<?php

require_once "./inc.php";

$session = "";
$user_group_index = 0;

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

# execute user group deletion query
try {
    $DB_SQL = "DELETE FROM `UserGroup` WHERE `user_group_index` = ?";
    $DB_STMT = $DB->prepare($DB_SQL);
    # database query not ready
    if (!$DB_STMT) {
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_param("i", $user_group_index);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # user group deletion query error
        $output = array();
        $output["result"] = -5;
        $output["error"] = "DELETE USER GROUP FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # user group deletion query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# user group deletion success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
