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

# initialize product index if exist
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

# initialize product group if exist
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
}

# prepare product list result
$productJsonResult = array();

# execute product list query
try {
    $DB_SQL = "
    SELECT `P`.`product_index`, 
        `P`.`product_group` as `product_group_index`,
        `PG`.`product_group_name` as `product_group_name`, 
        `PG`.`product_group_priority` as `product_group_priority`,
        `P`.`product_name`, `P`.`product_status`,
        `P`.`product_owner` as `product_owner_index`,
        `UG`.`user_group_name` as `product_owner_name`,
        `P`.`product_rent` as `product_rent_index`,
        `R`.`rent_user` as `product_rent_user_index`,
        `U`.`user_name` as `product_rent_user_name`,
        `U`.`user_id` as `product_rent_user_id`,
        `R`.`rent_status` as `product_rent_status`,
        `R`.`rent_time_start` as `product_rent_start`,
        `R`.`rent_time_end` as `product_rent_end`,
        `R`.`rent_time_return` as `product_rent_return`,
        `P`.`product_barcode`, `P`.`product_created`
    FROM `Products` AS `P`
        LEFT OUTER JOIN `UserGroup` AS `UG` ON (`P`.`product_owner` = `UG`.`user_group_index`) 
        LEFT OUTER JOIN `ProductGroup` AS `PG` ON (`P`.`product_group` = `PG`.`product_group_index`) 
        LEFT OUTER JOIN `Rents` AS `R` ON (`P`.`product_rent` = `R`.`rent_index`) 
        LEFT OUTER JOIN `Users` AS `U` ON (`R`.`rent_user` = `U`.`user_index`)
    WHERE 1=1";
    if (isset($product_index)) {
        $DB_SQL .= " AND `product_index` = $product_index";
    }
    if (isset($product_barcode)) {
        $DB_SQL .= " AND `product_barcode` = $product_barcode";
    }
    if (isset($product_group)) {
        $DB_SQL .= " AND `product_group` = $product_group";
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
        # product list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_GROUP_INDEX, $TEMP_PRODUCT_GROUP_NAME, $TEMP_PRODUCT_GROUP_PRIORITY, $TEMP_PRODUCT_NAME, $TEMP_PRODUCT_STATUS, $TEMP_PRODUCT_OWNER_INDEX, $TEMP_PRODUCT_OWNER_NAME,
        $TEMP_PRODUCT_RENT_INDEX, $TEMP_PRODUCT_RENT_USER_INDEX, $TEMP_PRODUCT_RENT_USER_NAME, $TEMP_PRODUCT_RENT_USER_ID, $TEMP_PRODUCT_RENT_STATUS, $TEMP_PRODUCT_RENT_TIME_START, $TEMP_PRODUCT_RENT_TIME_END, $TEMP_PRODUCT_RENT_TIME_RETURN,
        $TEMP_PRODUCT_BARCODE, $TEMP_PRODUCT_CREATED);
    while($DB_STMT->fetch()) {
        $productJsonObject = array();
        $productJsonObject["product_index"] = $TEMP_PRODUCT_INDEX;
        $productJsonObject["product_group_index"] = $TEMP_PRODUCT_GROUP_INDEX;
        if (isset($TEMP_PRODUCT_GROUP_NAME)) {
            $productJsonObject["product_group_name"] = $TEMP_PRODUCT_GROUP_NAME;
        } else {
            $productJsonObject["product_group_name"] = "";
        }
        if (isset($TEMP_PRODUCT_GROUP_PRIORITY)) {
            $productJsonObject["product_group_priority"] = $TEMP_PRODUCT_GROUP_PRIORITY;
        } else {
            $productJsonObject["product_group_priority"] = 0;
        }
        $productJsonObject["product_name"] = $TEMP_PRODUCT_NAME;
        $productJsonObject["product_status"] = $TEMP_PRODUCT_STATUS;
        $productJsonObject["product_owner_index"] = $TEMP_PRODUCT_OWNER_INDEX;
        if (isset($TEMP_PRODUCT_OWNER_NAME)) {
            $productJsonObject["product_owner_name"] = $TEMP_PRODUCT_OWNER_NAME;
        } else {
            $productJsonObject["product_owner_name"] = "";
        }
        $productJsonObject["product_rent_index"] = $TEMP_PRODUCT_RENT_INDEX;
        if (isset($TEMP_PRODUCT_RENT_USER_INDEX)) {
            $productJsonObject["product_rent_user_index"] = $TEMP_PRODUCT_RENT_USER_INDEX;
        }
        if (isset($TEMP_PRODUCT_RENT_USER_NAME)) {
            $productJsonObject["product_rent_user_name"] = $TEMP_PRODUCT_RENT_USER_NAME;
        }
        if (isset($TEMP_PRODUCT_RENT_USER_ID)) {
            $productJsonObject["product_rent_user_id"] = $TEMP_PRODUCT_RENT_USER_ID;
        }
        if (isset($TEMP_PRODUCT_RENT_STATUS)) {
            $productJsonObject["product_rent_status"] = $TEMP_PRODUCT_RENT_STATUS;
        }
        if (isset($TEMP_PRODUCT_RENT_TIME_START)) {
            $productJsonObject["product_rent_time_start"] = $TEMP_PRODUCT_RENT_TIME_START;
        }
        if (isset($TEMP_PRODUCT_RENT_TIME_END)) {
            $productJsonObject["product_rent_time_end"] = $TEMP_PRODUCT_RENT_TIME_END;
        }
        if (isset($TEMP_PRODUCT_RENT_TIME_RETURN)) {
            $productJsonObject["product_rent_time_return"] = $TEMP_PRODUCT_RENT_TIME_RETURN;
        }
        $productJsonObject["product_barcode"] = $TEMP_PRODUCT_BARCODE;
        $productJsonObject["product_created"] = $TEMP_PRODUCT_CREATED;
        array_push($productJsonResult, $productJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# prepare product group list result
$productGroupJsonResult = array();

# execute product group list query
try {
    $DB_SQL = "SELECT `product_group_index`, `product_group_name`, `product_group_rentable`, `product_group_priority` FROM `ProductGroup`";
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
    $DB_STMT->bind_result($TEMP_PRODUCT_TYPE_INDEX, $TEMP_PRODUCT_TYPE_NAME, $TEMP_PRODUCT_TYPE_RENTABLE, $TEMP_PRODUCT_TYPE_PRIORITY);
    while($DB_STMT->fetch()) {
        $productGroupJsonObject = array();
        $productGroupJsonObject["group_index"] = $TEMP_PRODUCT_TYPE_INDEX;
        $productGroupJsonObject["group_name"] = $TEMP_PRODUCT_TYPE_NAME;
        $productGroupJsonObject["group_rentable"] = $TEMP_PRODUCT_TYPE_RENTABLE;
        $productGroupJsonObject["group_priority"] = $TEMP_PRODUCT_TYPE_PRIORITY;
        array_push($productGroupJsonResult, $productGroupJsonObject);
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

# product list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["products"] = $productJsonResult;
$output["groups"] = $productGroupJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
