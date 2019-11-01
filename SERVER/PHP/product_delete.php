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

# initialize product index
if (isset($_REQUEST["product_index"]))
{
    $product_index = $_REQUEST["product_index"];
    if (!is_numeric($product_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_index = intval($product_index);
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "product_index IS EMPTY";
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

# execute product deletion query
try {
    $DB_SQL = "DELETE FROM `Products` WHERE `product_index` = ?";
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
    $DB_STMT->bind_param("i", $product_index);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # product deletion query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "DELETE PRODUCT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product deletion query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# product deletion log
newLog($DB, LogTypes::TYPE_PRODUCT_DELETE, $product_index, $validation["user_index"], NULL);

# user group deletion success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
