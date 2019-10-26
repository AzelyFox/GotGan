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
    if (!is_numeric($log_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $log_index = intval($log_index);
    }
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
    if (!is_numeric($log_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "log_type MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $log_type = intval($log_type);
    }
}

# prepare log list result
$logJsonResult = array();

# execute log list query
try {
    $DB_SQL = "
    SELECT `L`.`log_index`,
       `L`.`log_product` as `log_product_index`,
       `P`.`product_name` as `log_product_name`, 
       `L`.`log_user` as `log_user_index`, 
       `U`.`user_name` as `log_user_name`, 
       `U`.`user_id` as `log_user_id`, 
       `L`.`log_type`, `L`.`log_text`, `L`.`log_time` 
    FROM `Logs` AS `L` 
        LEFT OUTER JOIN `Users` AS `U` ON (`L`.`log_user` = `U`.`user_index`) 
        LEFT OUTER JOIN `Products` AS `P` ON (`L`.`log_product` = `P`.`product_index`) 
    WHERE 1=1";
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
    $DB_STMT->bind_result($TEMP_LOG_INDEX, $TEMP_LOG_PRODUCT_INDEX, $TEMP_LOG_PRODUCT_NAME, $TEMP_LOG_USER_INDEX, $TEMP_LOG_USER_NAME, $TEMP_LOG_USER_ID, $TEMP_LOG_TYPE, $TEMP_LOG_TEXT, $TEMP_LOG_TIME);
    while($DB_STMT->fetch()) {
        $logJsonObject = array();
        $logJsonObject["log_index"] = $TEMP_LOG_INDEX;
        $logJsonObject["log_product_index"] = $TEMP_LOG_PRODUCT_INDEX;
        if (isset($TEMP_LOG_PRODUCT_NAME)) {
            $logJsonObject["log_product_name"] = $TEMP_LOG_PRODUCT_NAME;
        }
        $logJsonObject["log_user_index"] = $TEMP_LOG_USER_INDEX;
        if (isset($TEMP_LOG_USER_NAME)) {
            $logJsonObject["log_user_name"] = $TEMP_LOG_USER_NAME;
        }
        if (isset($TEMP_LOG_USER_ID)) {
            $logJsonObject["log_user_id"] = $TEMP_LOG_USER_ID;
        }
        $logJsonObject["log_type"] = $TEMP_LOG_TYPE;
        if (isset($TEMP_LOG_TEXT)) {
            $logJsonObject["log_text"] = $TEMP_LOG_TEXT;
        }
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

# prepare log type list result
$logTypeJsonResult = array();

# execute log type list query
try {
    $DB_SQL = "SELECT `log_type_index`, `log_type_name`, `log_type_level` FROM `LogType`";
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
        # log type list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "LOG TYPE LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_LOG_TYPE_INDEX, $TEMP_LOG_TYPE_NAME, $TEMP_LOG_TYPE_LEVEL);
    while($DB_STMT->fetch()) {
        $logTypeJsonObject = array();
        $logTypeJsonObject["type_index"] = $TEMP_LOG_TYPE_INDEX;
        $logTypeJsonObject["type_name"] = $TEMP_LOG_TYPE_NAME;
        $logTypeJsonObject["type_level"] = $TEMP_LOG_TYPE_LEVEL;
        array_push($logTypeJsonResult, $logTypeJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # log type list query error
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
$output["types"] = $logTypeJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
