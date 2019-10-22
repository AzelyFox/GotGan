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

# initialize products
if (isset($_REQUEST["products"]))
{
    $products = $_REQUEST["products"];
    if (!is_string($products)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "products MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    try {
        $productArray = json_decode($products, true);
        if (count($productArray) < 1) {
            $output = array();
            $output["result"] = -1;
            $output["error"] = "products MUST HAVE DATA";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } catch (JsonException $e) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "products MUST BE JSON ARRAY : ".$e->getMessage();
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "products IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# prepare product group list result
$productGroupResult = array();

# execute product group list query
try {
    $DB_SQL = "SELECT `product_group_index`, `product_group_name` FROM `ProductGroup`";
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
        # product group list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_GROUP_INDEX, $TEMP_PRODUCT_GROUP_NAME);
    while($DB_STMT->fetch()) {
        $productGroupResult[$TEMP_PRODUCT_GROUP_INDEX] = $TEMP_PRODUCT_GROUP_NAME;
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product group list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# change products empty values to default
for ($i = 0; $i < count($productArray); $i++) {
    if (!isset($productArray[$i]["product_group"])) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "products MUST HAVE product_group";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    if (!isset($productArray[$i]["product_name"])) {
        $productArray[$i]["product_name"] = $productGroupResult[$productArray[$i]["product_group"]];
    }
    if (!isset($productArray[$i]["product_status"])) {
        $productArray[$i]["product_status"] = 0;
    }
    if (!isset($productArray[$i]["product_owner"])) {
        $productArray[$i]["product_owner"] = 0;
    }
    if (!isset($productArray[$i]["product_rent"])) {
        $productArray[$i]["product_rent"] = 0;
    }
    if (!isset($productArray[$i]["product_barcode"])) {
        $productArray[$i]["product_barcode"] = 0;
    }
}

# execute product creation query
try {
    $DB_SQL = "INSERT INTO `Products` (`product_group`, `product_name`, `product_status`, `product_owner`, `product_rent`, `product_barcode`) VALUES (?, ?, ?, ?, ?, ?)";
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
    for ($i = 0; $i < count($productArray); $i++) {
        $DB_STMT->bind_param("isiiii", $productArray[$i]["product_group"], $productArray[$i]["product_name"], $productArray[$i]["product_status"], $productArray[$i]["product_owner"], $productArray[$i]["product_rent"], $productArray[$i]["product_barcode"]);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # product creation query error
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        newLog($DB, LogTypes::TYPE_PRODUCT_ADD, $DB_STMT->insert_id, $validation["user_index"], NULL);
    }
    $DB_STMT->close();
} catch (Exception $e) {
    # product creation query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# product creation success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
