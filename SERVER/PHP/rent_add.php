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
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize product_barcode if exist
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
        $rent_user = intval($product_barcode);
    }
    # execute product detail query
    try {
        $DB_SQL = "SELECT `product_index`, `product_status`, `product_rent`, `product_group_rentable` FROM `Products` AS `P` LEFT OUTER JOIN `ProductGroup` AS `PG` ON (`P`.`product_group` = `PG`.`product_group_index`) WHERE `product_barcode` = ?";
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
        $DB_STMT->bind_param("i", $product_barcode);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # product detail query error
            $output = array();
            $output["result"] = -2;
            $output["error"] = "PRODUCT DETAIL FAILURE : " . $DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->bind_result($TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_STATUS, $TEMP_PRODUCT_RENT, $TEMP_PRODUCT_RENTABLE);
        $DB_STMT->fetch();
        if ($TEMP_PRODUCT_STATUS != 0 || $TEMP_PRODUCT_RENT != 0 || $TEMP_PRODUCT_RENTABLE == 0) {
            # product is on rent
            $output = array();
            $output["result"] = -3;
            $output["error"] = "NOT VALID PRODUCT";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch (Exception $e) {
        # product detail query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "PRODUCT DETAIL FAILURE : " . $DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# initialize rent user if exist
if (isset($_REQUEST["rent_user"]))
{
    $rent_user = $_REQUEST["rent_user"];
    if (!is_numeric($rent_user)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_user MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $rent_user = intval($rent_user);
    }
    if ($validation["user_level"] < 1 && $rent_user != $validation["user_index"]) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $rent_user = $validation["user_index"];
}

# initialize rent product
if (isset($_REQUEST["rent_product"]))
{
    $rent_product = $_REQUEST["rent_product"];
    if (!is_numeric($rent_product)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_product MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $rent_product = intval($rent_product);
    }
    # execute product detail query
    try {
        $DB_SQL = "SELECT `product_index`, `product_status`, `product_rent`, `product_group_rentable` FROM `Products` AS `P` LEFT OUTER JOIN `ProductGroup` AS `PG` ON (`P`.`product_group` = `PG`.`product_group_index`) WHERE `product_index` = ?";
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
        $DB_STMT->bind_param("i", $rent_product);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # product detail query error
            $output = array();
            $output["result"] = -2;
            $output["error"] = "PRODUCT DETAIL FAILURE : " . $DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->bind_result($TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_STATUS, $TEMP_PRODUCT_RENT, $TEMP_PRODUCT_RENTABLE);
        $DB_STMT->fetch();
        if ($TEMP_PRODUCT_STATUS != 0 || $TEMP_PRODUCT_RENT != 0 || $TEMP_PRODUCT_RENTABLE == 0) {
            # product is on rent
            $output = array();
            $output["result"] = -3;
            $output["error"] = "NOT VALID PRODUCT";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch (Exception $e) {
        # product detail query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "PRODUCT DETAIL FAILURE : " . $DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} elseif (isset($TEMP_PRODUCT_INDEX)) {
    $rent_product = $TEMP_PRODUCT_INDEX;
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "rent_product IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize rent start date
if (isset($_REQUEST["rent_time_start"]))
{
    $rent_time_start = $_REQUEST["rent_time_start"];
    if (!is_string($rent_time_start)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_time_start MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    try {
        $timeValid = date_create_from_format('Y-m-d H:i:s', $rent_time_start);
        if (!$timeValid) {
            $output = array();
            $output["result"] = -1;
            $output["error"] = "rent_time_start IS NOT VALID";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } catch (Exception $e) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_time_start IS NOT VALID";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "rent_time_start IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute rent creation query
try {
    $DB_SQL = "INSERT INTO `Rents` (`rent_user`, `rent_product`, `rent_status`, `rent_time_start`) VALUES (?, ?, 1, '$rent_time_start')";
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
    $DB_STMT->bind_param("ii", $rent_user, $rent_product);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # rent creation query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "ADD RENT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $TEMP_INSERTED_ROW = $DB_STMT->insert_id;
    $DB_STMT->close();
} catch(Exception $e) {
    # rent creation query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# update product status
try {
    $DB_SQL = "UPDATE `Products` SET `product_rent` = ? WHERE `product_index` = ?";
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
    $DB_STMT->bind_param("ii", $TEMP_INSERTED_ROW, $rent_product);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # update product query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "UPDATE PRODUCT STATUS FAILURE : " . $DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch (Exception $e) {
    # update product query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# rent creation log
newLog($DB, LogTypes::TYPE_RENT_ADD, $rent_product, $rent_user, NULL);

# rent creation success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["rent_index"] = $TEMP_INSERTED_ROW;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
