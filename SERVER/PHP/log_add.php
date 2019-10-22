<?php

require_once "./inc.php";

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

# initialize log product if exist
if (isset($_REQUEST["log_product"]))
{
    $log_product = $_REQUEST["log_product"];
    if (!is_numeric($log_product)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_product MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $log_product = intval($log_product);
    }
} else {
    $log_product = -1;
}

# initialize log user if exist
if (isset($_REQUEST["log_user"]))
{
    $log_user = $_REQUEST["log_user"];
    if (!is_numeric($log_user)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_user MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $log_user = intval($log_user);
    }
} else {
    $log_user = -1;
}

# initialize log type
if (isset($_REQUEST["log_type"]))
{
    $log_type = $_REQUEST["log_type"];
    if (!is_numeric($log_type)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_type MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $log_type = intval($log_type);
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "log_type IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize log text if exist
if (isset($_REQUEST["log_text"]))
{
    $log_text = $_REQUEST["log_text"];
    if (!is_string($log_text)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_text MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $log_text = NULL;
}

# new log
$TEMP_LOG_INSERTED = newLog($DB, $log_type, $log_product, $log_user, $log_text);

# new log success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["log_index"] = $TEMP_LOG_INSERTED;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
