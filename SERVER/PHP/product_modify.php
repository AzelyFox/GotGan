<?php

require_once "./inc.php";

function modifyProduct(Mysqli $DB, int $productIndex, string $modifyQuery, string $bindType, $bindValue) {
    # execute product modification query
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
        $DB_STMT->bind_param($bindType, $bindValue, $productIndex);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # product modification query error
            $output = array();
            $output["result"] = -4;
            $output["error"] = "MODIFY PRODUCT FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch(Exception $e) {
        # product modification query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
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

# initialize product group
if (isset($_REQUEST["product_group"]))
{
    $product_group = $_REQUEST["product_group"];
    if (!is_numeric($product_group)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_group MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_group = intval($product_group);
    }
    $groupModifyQuery = "UPDATE `Products` SET `product_group` = ? WHERE `product_index` = ?";
    modifyProduct($DB, $product_index, $groupModifyQuery, "ii", $product_group);
}

# initialize product name
if (isset($_REQUEST["product_name"]))
{
    $product_name = $_REQUEST["product_name"];
    if (!is_string($product_name)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_name MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $nameModifyQuery = "UPDATE `Products` SET `product_name` = ? WHERE `product_index` = ?";
    modifyProduct($DB, $product_index, $nameModifyQuery, "si", $product_name);
}

# initialize product status
if (isset($_REQUEST["product_status"]))
{
    $product_status = $_REQUEST["product_status"];
    if (!is_numeric($product_status)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_status MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_status = intval($product_status);
    }
    $statusModifyQuery = "UPDATE `Products` SET `product_status` = ? WHERE `product_index` = ?";
    modifyProduct($DB, $product_index, $statusModifyQuery, "ii", $product_status);
}

# initialize product owner
if (isset($_REQUEST["product_owner"]))
{
    $product_owner = $_REQUEST["product_owner"];
    if (!is_numeric($product_owner)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_owner MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_owner = intval($product_owner);
    }
    $ownerModifyQuery = "UPDATE `Products` SET `product_owner` = ? WHERE `product_index` = ?";
    modifyProduct($DB, $product_index, $ownerModifyQuery, "ii", $product_owner);
}

# initialize product barcode
if (isset($_REQUEST["product_barcode"]))
{
    $product_barcode = $_REQUEST["product_barcode"];
    if (!is_numeric($product_barcode)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "product_barcode MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $product_barcode = intval($product_barcode);
    }
    $barcodeModifyQuery = "UPDATE `Products` SET `product_barcode` = ? WHERE `product_index` = ?";
    modifyProduct($DB, $product_index, $barcodeModifyQuery, "ii", $product_barcode);
}

# product modification log
newLog($DB, LogTypes::TYPE_PRODUCT_MODIFY, $product_index, $validation["user_index"], NULL);

# product modification success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["product_index"] = $product_index;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
