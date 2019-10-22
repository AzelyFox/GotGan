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

# initialize rent index
if (isset($_REQUEST["rent_index"]))
{
    $rent_index = $_REQUEST["rent_index"];
    if (!is_numeric($rent_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $rent_index = intval($rent_index);
    }
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
}

if (!isset($rent_index) && !isset($product_barcode)) {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "rent_index OR product_barcode IS NEEDED";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

if (!isset($rent_index)) {
    $rent_index = -1;
}
if (!isset($product_barcode)) {
    $product_barcode = -1;
}

# get rent details
try {
    $DB_SQL = "SELECT `PG`.`product_group_rentable`, `R`.rent_index, `R`.`rent_product`, `R`.`rent_user` FROM `Rents` as `R` LEFT OUTER JOIN `Products` AS `P` ON (`R`.`rent_product` = `P`.`product_index`) LEFT OUTER JOIN `ProductGroup` AS `PG` ON (`P`.`product_group` = `PG`.`product_group_index`) WHERE `R`.`rent_index` = ? OR `P`.`product_barcode` = ?";
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
    $DB_STMT->bind_param("ii", $rent_index, $product_barcode);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # rent detail query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "RENT PRODUCT SEARCH FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_RENTABLE, $TEMP_RENT_INDEX, $TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_USER);
    $DB_STMT->fetch();
    $rent_index = $TEMP_RENT_INDEX;
    $DB_STMT->close();
} catch(Exception $e) {
    # rent detail query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute rent return query
try {
    $DB_SQL = "UPDATE `Rents` SET `rent_status` = 0, `rent_time_return` = NOW() WHERE `rent_index` = ?";
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
    $DB_STMT->bind_param("i", $rent_index);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # rent return query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "UPDATE RENT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent return query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute rent return query
try {
    $DB_SQL = "UPDATE `Products` SET `product_rent` = 0 WHERE `product_index` = ?";
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
    $DB_STMT->bind_param("i", $TEMP_PRODUCT_INDEX);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # rent return query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "UPDATE RENT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent return query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# rent return log
newLog($DB, LogTypes::TYPE_RENT_RETURN, $TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_USER, NULL);

# rent return success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
