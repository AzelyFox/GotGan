<?php

require_once "./inc.php";

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

# execute user deletion query
try {
    $DB_SQL = "DELETE FROM `Users` WHERE `user_index` = ?";
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
    $DB_STMT->bind_param("i", $user_index);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # user deletion query error
        $output = array();
        $output["result"] = -5;
        $output["error"] = "DELETE USER FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # user deletion query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# user deletion success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
