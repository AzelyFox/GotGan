<?php

require_once "./inc.php";

# initialize group name
if (isset($_REQUEST["user_group_name"]))
{
    $user_group_name = $_REQUEST["user_group_name"];
    if (!is_string($user_group_name)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_group_name MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_group_name IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# session auth
if (isset($_REQUEST["session"]))
{
    $session = $_REQUEST["session"];
    if (!is_string($session)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "session MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $validation = validateSession($DB, $session);

    # check user level
    if ($validation["user_level"] < 1) {
        $output = array();
        $output["result"] = -3;
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

# execute user group creation query
try {
    $DB_SQL = "INSERT INTO `UserGroup` (`user_group_name`) VALUES (?)";
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
    $DB_STMT->bind_param("s", $user_group_name);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # user group creation query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "ADD USER GROUP FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $TEMP_INSERTED_ROW = $DB_STMT->insert_id;
    $DB_STMT->close();
} catch(Exception $e) {
    # user group creation query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# user group creation log
newLog($DB, LogTypes::TYPE_USER_GROUP_ADD, -1, $validation["user_index"], NULL);

# user group creation success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["user_group_index"] = $TEMP_INSERTED_ROW;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
