<?php

require_once "./inc.php";

# session auth
if (isset($_REQUEST["session"]))
{
    $session = $_REQUEST["session"];
    $validation = validateSession($DB, $session);
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize log index if exist
if (isset($_REQUEST["log_index"]))
{
    $log_index = $_REQUEST["log_index"];
    if (!is_int($log_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# initialize log product if exist
if (isset($_REQUEST["log_product"]))
{
    $log_product = $_REQUEST["log_product"];
    if (!is_int($log_product)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_product MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# initialize log user if exist
if (isset($_REQUEST["log_user"]))
{
    $log_user = $_REQUEST["log_user"];
    if (!is_int($log_user)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_user MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    # if normal user want to see other user log
    if ($validation["user_level"] < 1 && $log_user != $validation["user_index"]) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# initialize log type if exist
if (isset($_REQUEST["log_type"]))
{
    $log_type = $_REQUEST["log_type"];
    if (!is_int($log_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_type MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# prepare log list result
$logJsonResult = array();

# execute log list query
try {
    $DB_SQL = "SELECT `log_index`, `log_product`, `log_user`, `log_type`, `log_text`, `log_time` FROM `Logs` WHERE 1=1";
    if (isset($log_index)) {
        $DB_SQL .= " AND `log_index` = $log_index";
    }
    if (isset($log_product)) {
        $DB_SQL .= " AND `log_product` = $log_product";
    }
    if (isset($log_user)) {
        $DB_SQL .= " AND `log_user` = $log_user";
    }
    if (isset($log_type)) {
        $DB_SQL .= " AND `log_type` = $log_type";
    }
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
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # log list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "LOG LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_LOG_INDEX, $TEMP_LOG_PRODUCT, $TEMP_LOG_USER, $TEMP_LOG_TYPE, $TEMP_LOG_TEXT, $TEMP_LOG_TIME);
    while($DB_STMT->fetch()) {
        $logJsonObject = array();
        $logJsonObject["log_index"] = $TEMP_LOG_INDEX;
        $logJsonObject["log_product"] = $TEMP_LOG_PRODUCT;
        $logJsonObject["log_user"] = $TEMP_LOG_USER;
        $logJsonObject["log_type"] = $TEMP_LOG_TYPE;
        $logJsonObject["log_text"] = $TEMP_LOG_TEXT;
        $logJsonObject["log_time"] = $TEMP_LOG_TIME;
        array_push($logJsonResult, $logJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # log list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# log list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["logs"] = $logJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
