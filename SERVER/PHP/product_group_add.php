<?php

require_once "./inc.php";

# initialize group name
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
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "product_group_name IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize group rentable
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
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "product_group_rentable IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize group priority
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
} else {
    $product_group_priority = 0;
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

# execute product group creation query
try {
    $DB_SQL = "INSERT INTO `ProductGroup` (`product_group_name`, `product_group_rentable`, `product_group_priority`) VALUES (?, ?, ?)";
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
    $DB_STMT->bind_param("sii", $product_group_name, $product_group_rentable, $product_group_priority);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # product group creation query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "ADD PRODUCT GROUP FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $TEMP_INSERTED_ROW = $DB_STMT->insert_id;
    $DB_STMT->close();
} catch(Exception $e) {
    # product group creation query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# product group creation log
newLog($DB, LogTypes::TYPE_PRODUCT_GROUP_ADD, 0, $validation["user_index"], NULL);

# product group creation success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["product_group_index"] = $TEMP_INSERTED_ROW;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
