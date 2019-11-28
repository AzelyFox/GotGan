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

if (getSystemSwitch($DB, SwitchTypes::SWITCH_RENT) == 0) {
    $output = array();
    $output["result"] = -3;
    $output["error"] = "RENT SWITCH IS OFF";
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

# initialize rent index if exist
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
}

# initialize rent product if exist
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
}

# initialize product barcode if exist
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

# initialize rent status if exist
if (isset($_REQUEST["rent_status"]))
{
    $rent_status = $_REQUEST["rent_status"];
    if (!is_numeric($rent_status)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_status MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $rent_status = intval($rent_status);
    }
}

# initialize rent delayed if exist
if (isset($_REQUEST["rent_delayed"]))
{
    $rent_delayed = $_REQUEST["rent_delayed"];
    if (!is_numeric($rent_delayed)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "rent_delayed MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $rent_delayed = intval($rent_delayed);
    }
}

# prepare rent list result
$rentJsonResult = array();

# execute rent list query
try {
    $DB_SQL = "
    SELECT `R`.`rent_index`,
        `R`.`rent_user` as `rent_user_index`,
        `U`.`user_name` as `rent_user_name`,
        `U`.`user_id` as `rent_user_id`,
        `R`.`rent_product` as `rent_product_index`,
        `P`.`product_group` as `rent_product_group_index`,
        `PG`.`product_group_name` as `rent_product_group_name`,
        `P`.`product_name` as `rent_product_name`,
        `P`.`product_barcode` as `rent_product_barcode`,
        `R`.`rent_status`, `R`.`rent_time_start`,
        `R`.`rent_time_end`, `R`.`rent_time_return`
    FROM `Rents` AS `R`
        LEFT OUTER JOIN `Users` AS `U` ON (`R`.`rent_user` = `U`.`user_index`) 
        LEFT OUTER JOIN `Products` AS `P` ON (`R`.`rent_product` = `P`.`product_index`) 
        LEFT OUTER JOIN `ProductGroup` AS `PG` ON (`P`.`product_group` = `PG`.`product_group_index`) 
    WHERE 1=1";
    if (isset($rent_index)) {
        $DB_SQL .= " AND `rent_index` = $rent_index";
    }
    if (isset($rent_user)) {
        $DB_SQL .= " AND `rent_user` = $rent_user";
    }
    if (isset($rent_product)) {
        $DB_SQL .= " AND `rent_product` = $rent_product";
    }
    if (isset($product_barcode)) {
        $DB_SQL .= " AND `product_barcode` = $product_barcode";
    }
    if (isset($rent_status)) {
        $DB_SQL .= " AND `rent_status` = $rent_status";
    }
    if (isset($rent_delayed) && $rent_delayed == 1) {
        $DB_SQL .= " AND (`rent_time_end` IS NOT NULL AND `rent_time_return` IS NOT NULL AND `rent_time_return` > `rent_time_end`)";
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
        # rent list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "RENT LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_RENT_INDEX, $TEMP_RENT_USER_INDEX, $TEMP_RENT_USER_NAME, $TEMP_RENT_USER_ID, $TEMP_RENT_PRODUCT_INDEX, $TEMP_RENT_PRODUCT_GROUP_INDEX,
        $TEMP_RENT_PRODUCT_GROUP_NAME, $TEMP_RENT_PRODUCT_NAME, $TEMP_RENT_PRODUCT_BARCODE, $TEMP_RENT_STATUS, $TEMP_RENT_TIME_START, $TEMP_RENT_TIME_END, $TEMP_RENT_TIME_RETURN);
    while($DB_STMT->fetch()) {
        $rentJsonObject = array();
        $rentJsonObject["rent_index"] = $TEMP_RENT_INDEX;
        $rentJsonObject["rent_user_index"] = $TEMP_RENT_USER_INDEX;
        $rentJsonObject["rent_user_name"] = $TEMP_RENT_USER_NAME;
        $rentJsonObject["rent_user_id"] = $TEMP_RENT_USER_ID;
        $rentJsonObject["rent_product_index"] = $TEMP_RENT_PRODUCT_INDEX;
        $rentJsonObject["rent_product_group_index"] = $TEMP_RENT_PRODUCT_GROUP_INDEX;
        if (isset($TEMP_RENT_PRODUCT_GROUP_NAME)) {
            $rentJsonObject["rent_product_group_name"] = $TEMP_RENT_PRODUCT_GROUP_NAME;
        } else {
            $rentJsonObject["rent_product_group_name"] = "";
        }
        $rentJsonObject["rent_product_name"] = $TEMP_RENT_PRODUCT_NAME;
        if (isset($TEMP_RENT_PRODUCT_BARCODE)) {
            $rentJsonObject["rent_product_barcode"] = $TEMP_RENT_PRODUCT_BARCODE;
        }
        $rentJsonObject["rent_status"] = $TEMP_RENT_STATUS;
        $rentJsonObject["rent_time_start"] = $TEMP_RENT_TIME_START;
        $rentJsonObject["rent_time_end"] = $TEMP_RENT_TIME_END;
        $rentJsonObject["rent_time_return"] = $TEMP_RENT_TIME_RETURN;
        array_push($rentJsonResult, $rentJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# rent list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["rents"] = $rentJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
