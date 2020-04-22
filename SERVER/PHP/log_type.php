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
        $logTypeJsonObject["log_type_index"] = $TEMP_LOG_TYPE_INDEX;
        $logTypeJsonObject["log_type_name"] = $TEMP_LOG_TYPE_NAME;
        $logTypeJsonObject["log_type_level"] = $TEMP_LOG_TYPE_LEVEL;
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

# log type list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["types"] = $logTypeJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
