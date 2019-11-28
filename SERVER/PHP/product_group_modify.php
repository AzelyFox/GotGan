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

function modifyProductGroup(Mysqli $DB, int $groupIndex, string $modifyQuery, string $bindType, $bindValue) {
    # execute product group modification query
    try {
        $DB_STMT = $DB->prepare($modifyQuery);
        # database query not ready
        if (!$DB_STMT) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : ".$DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->bind_param($bindType, $bindValue, $groupIndex);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # product group modification query error
            $output = array();
            $output["result"] = -4;
            $output["error"] = "MODIFY PRODUCT GROUP FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch(Exception $e) {
        # product group modification query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# initialize product group index
if (isset($_REQUEST["product_group_index"]))
{
    $product_group_index = $_REQUEST["product_group_index"];
    if (!is_numeric($product_group_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_group_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_group_index = intval($product_group_index);
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "product_group_index IS EMPTY";
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

# initialize product group name
if (isset($_REQUEST["product_group_name"]))
{
    $product_group_name = $_REQUEST["product_group_name"];
    if (!is_string($product_group_name)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_group_name MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $nameModifyQuery = "UPDATE `ProductGroup` SET `product_group_name` = ? WHERE `product_group_index` = ?";
    modifyProductGroup($DB, $product_group_index, $nameModifyQuery, "si", $product_group_name);
}

# initialize product group rentable
if (isset($_REQUEST["product_group_rentable"]))
{
    $product_group_rentable = $_REQUEST["product_group_rentable"];
    if (!is_numeric($product_group_rentable)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_group_rentable MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_group_rentable = intval($product_group_rentable);
    }
    $rentableModifyQuery = "UPDATE `ProductGroup` SET `product_group_rentable` = ? WHERE `product_group_index` = ?";
    modifyProductGroup($DB, $product_group_index, $rentableModifyQuery, "ii", $product_group_rentable);
}

# initialize product group priority
if (isset($_REQUEST["product_group_priority"]))
{
    $product_group_priority = $_REQUEST["product_group_priority"];
    if (!is_numeric($product_group_priority)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_group_priority MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_group_priority = intval($product_group_priority);
    }
    $priorityModifyQuery = "UPDATE `ProductGroup` SET `product_group_priority` = ? WHERE `product_group_index` = ?";
    modifyProductGroup($DB, $product_group_index, $priorityModifyQuery, "ii", $product_group_priority);
}

# product group modification log
newLog($DB, LogTypes::TYPE_PRODUCT_GROUP_MODIFY, 0, $validation["user_index"], NULL);

# product modification success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["product_group_index"] = $product_group_index;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
