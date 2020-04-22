<?php

require_once "./inc.php";

if (getSystemSwitch($DB, SwitchTypes::SWITCH_MASTER) == 0) {
    $output = array();
    $output["result"] = -3;
    $output["error"] = "SYSTEM SWITCH IS OFF";
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

# initialize user index
if (isset($_REQUEST["user_index"]))
{
    $user_index = $_REQUEST["user_index"];
    if (!is_numeric($user_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $user_index = intval($user_index);
    }
}

# initialize title
if (isset($_REQUEST["title"]))
{
    $title = $_REQUEST["title"];
    if (!is_string($title)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "title MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "title IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize message
if (isset($_REQUEST["message"]))
{
    $message = $_REQUEST["message"];
    if (!is_string($message)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "message MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "message IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# ready for user fcm result
$userFCMResult = array();

# execute user fcm query
try {
    $DB_SQL = "SELECT `user_id`, `user_uuid` FROM `Users` WHERE 1=1";
    if (isset($user_index)) {
        $DB_SQL .= " AND `user_index` = ?";
    }
    $DB_STMT = $DB->prepare($DB_SQL);
    # database query not ready
    if (!$DB_STMT) {
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : " . $DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    if (isset($user_index)) {
        $DB_STMT->bind_param("i", $user_index);
    }
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : " . $DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_USER_ID, $TEMP_USER_UUID);
    while ($DB_STMT->fetch()) {
        sendFCM($TEMP_USER_UUID, $title, $message, $GOOGLE_API_KEY);
    }
    $DB_STMT->close();
} catch (Exception $e) {
    # user fcm query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# fcm log
newLog($DB, LogTypes::TYPE_SEND_FCM, 0, $validation["user_index"], $title);

# send fcm success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
