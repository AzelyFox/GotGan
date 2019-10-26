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

# prepare history list result
$historyJsonResult = array();

# execute history list query
try {
    $DB_SQL = "SELECT `history_index`, `history_time`, `history_user_total`, `history_product_total`, `history_product_available`, `history_product_rent` FROM `History` ORDER BY `history_time` DESC LIMIT 30";
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
        # history list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "HISTORY LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_HISTORY_INDEX, $TEMP_HISTORY_TIME, $TEMP_HISTORY_USER_TOTAL, $TEMP_HISTORY_PRODUCT_TOTAL, $TEMP_HISTORY_PRODUCT_AVAILABLE, $TEMP_HISTORY_PRODUCT_RENT);
    while($DB_STMT->fetch()) {
        $historyJsonObject = array();
        $historyJsonObject["history_index"] = $TEMP_HISTORY_INDEX;
        $historyJsonObject["history_time"] = $TEMP_HISTORY_TIME;
        $historyJsonObject["history_user_total"] = $TEMP_HISTORY_USER_TOTAL;
        $historyJsonObject["history_product_total"] = $TEMP_HISTORY_PRODUCT_TOTAL;
        $historyJsonObject["history_product_available"] = $TEMP_HISTORY_PRODUCT_AVAILABLE;
        $historyJsonObject["history_product_rent"] = $TEMP_HISTORY_PRODUCT_RENT;
        array_push($historyJsonResult, $historyJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # history list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# history list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["histories"] = $historyJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
