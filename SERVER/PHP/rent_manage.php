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

# key auth
if (isset($_REQUEST["key"]))
{
    $key = $_REQUEST["key"];
    if (!is_string($key)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "key MUST BE STRING";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    if ($key != $MANAGER_KEY) {

    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "key IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# prepare rent due list result
$rentDueJsonResult = array();

# execute rent due list query
try {
    $DB_SQL = "
    SELECT `R`.`rent_index`,
        `R`.`rent_user` as `rent_user_index`,
        `U`.`user_name` as `rent_user_name`,
        `U`.`user_id` as `rent_user_id`,
        `U`.`user_email` as `rent_user_email`,
        `U`.`user_uuid` as `rent_user_uuid`,
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
    WHERE `R`.`rent_status` = 2 AND CAST(`R`.`rent_time_end` AS DATE) = CAST(NOW() AS DATE)";
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
        # rent due list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "RENT DUE LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_RENT_INDEX, $TEMP_RENT_USER_INDEX, $TEMP_RENT_USER_NAME, $TEMP_RENT_USER_ID, $TEMP_RENT_USER_EMAIL, $TEMP_RENT_USER_UUID, $TEMP_RENT_PRODUCT_INDEX, $TEMP_RENT_PRODUCT_GROUP_INDEX,
        $TEMP_RENT_PRODUCT_GROUP_NAME, $TEMP_RENT_PRODUCT_NAME, $TEMP_RENT_PRODUCT_BARCODE, $TEMP_RENT_STATUS, $TEMP_RENT_TIME_START, $TEMP_RENT_TIME_END, $TEMP_RENT_TIME_RETURN);
    while($DB_STMT->fetch()) {
        $rentDueJsonObject = array();
        $rentDueJsonObject["rent_index"] = $TEMP_RENT_INDEX;
        $rentDueJsonObject["rent_user_index"] = $TEMP_RENT_USER_INDEX;
        $rentDueJsonObject["rent_user_name"] = $TEMP_RENT_USER_NAME;
        $rentDueJsonObject["rent_user_id"] = $TEMP_RENT_USER_ID;
        if (isset($TEMP_RENT_USER_EMAIL)) {
            $rentDueJsonObject["rent_user_email"] = $TEMP_RENT_USER_EMAIL;
        }
        if (isset($TEMP_RENT_USER_UUID)) {
            $rentDueJsonObject["rent_user_uuid"] = $TEMP_RENT_USER_UUID;
        }
        $rentDueJsonObject["rent_product_index"] = $TEMP_RENT_PRODUCT_INDEX;
        $rentDueJsonObject["rent_product_group_index"] = $TEMP_RENT_PRODUCT_GROUP_INDEX;
        if (isset($TEMP_RENT_PRODUCT_GROUP_NAME)) {
            $rentDueJsonObject["rent_product_group_name"] = $TEMP_RENT_PRODUCT_GROUP_NAME;
        } else {
            $rentDueJsonObject["rent_product_group_name"] = "";
        }
        $rentDueJsonObject["rent_product_name"] = $TEMP_RENT_PRODUCT_NAME;
        if (isset($TEMP_RENT_PRODUCT_BARCODE)) {
            $rentDueJsonObject["rent_product_barcode"] = $TEMP_RENT_PRODUCT_BARCODE;
        }
        $rentDueJsonObject["rent_status"] = $TEMP_RENT_STATUS;
        $rentDueJsonObject["rent_time_start"] = $TEMP_RENT_TIME_START;
        $rentDueJsonObject["rent_time_end"] = $TEMP_RENT_TIME_END;
        $rentDueJsonObject["rent_time_return"] = $TEMP_RENT_TIME_RETURN;
        array_push($rentDueJsonResult, $rentDueJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent due list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

foreach ($rentDueJsonResult as $dueObject) {
    if (isset($dueObject["rent_user_email"])) {
        $emailMessage = "";
        sendRentEmail($dueObject["rent_user_email"], $emailMessage);
    }
    if (isset($dueObject["rent_user_uuid"])) {
        $fcmMessage = "";
        sendRentFCM($dueObject["rent_user_uuid"], $fcmMessage);
    }
}

# prepare rent late list result
$rentLateJsonResult = array();

# execute rent late list query
try {
    $DB_SQL = "
    SELECT `R`.`rent_index`,
        `R`.`rent_user` as `rent_user_index`,
        `U`.`user_name` as `rent_user_name`,
        `U`.`user_id` as `rent_user_id`,
        `U`.`user_email` as `rent_user_email`,
        `U`.`user_uuid` as `rent_user_uuid`,
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
    WHERE `R`.`rent_status` = 2 AND CAST(`R`.`rent_time_end` AS DATE) < CAST(NOW() AS DATE)";
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
        # rent late list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "RENT LATE LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_RENT_INDEX, $TEMP_RENT_USER_INDEX, $TEMP_RENT_USER_NAME, $TEMP_RENT_USER_ID, $TEMP_RENT_USER_EMAIL, $TEMP_RENT_USER_UUID, $TEMP_RENT_PRODUCT_INDEX, $TEMP_RENT_PRODUCT_GROUP_INDEX,
        $TEMP_RENT_PRODUCT_GROUP_NAME, $TEMP_RENT_PRODUCT_NAME, $TEMP_RENT_PRODUCT_BARCODE, $TEMP_RENT_STATUS, $TEMP_RENT_TIME_START, $TEMP_RENT_TIME_END, $TEMP_RENT_TIME_RETURN);
    while($DB_STMT->fetch()) {
        $rentLateJsonObject = array();
        $rentLateJsonObject["rent_index"] = $TEMP_RENT_INDEX;
        $rentLateJsonObject["rent_user_index"] = $TEMP_RENT_USER_INDEX;
        $rentLateJsonObject["rent_user_name"] = $TEMP_RENT_USER_NAME;
        $rentLateJsonObject["rent_user_id"] = $TEMP_RENT_USER_ID;
        if (isset($TEMP_RENT_USER_EMAIL)) {
            $rentLateJsonObject["rent_user_email"] = $TEMP_RENT_USER_EMAIL;
        }
        if (isset($TEMP_RENT_USER_UUID)) {
            $rentLateJsonObject["rent_user_uuid"] = $TEMP_RENT_USER_UUID;
        }
        $rentLateJsonObject["rent_product_index"] = $TEMP_RENT_PRODUCT_INDEX;
        $rentLateJsonObject["rent_product_group_index"] = $TEMP_RENT_PRODUCT_GROUP_INDEX;
        if (isset($TEMP_RENT_PRODUCT_GROUP_NAME)) {
            $rentLateJsonObject["rent_product_group_name"] = $TEMP_RENT_PRODUCT_GROUP_NAME;
        } else {
            $rentLateJsonObject["rent_product_group_name"] = "";
        }
        $rentLateJsonObject["rent_product_name"] = $TEMP_RENT_PRODUCT_NAME;
        if (isset($TEMP_RENT_PRODUCT_BARCODE)) {
            $rentLateJsonObject["rent_product_barcode"] = $TEMP_RENT_PRODUCT_BARCODE;
        }
        $rentLateJsonObject["rent_status"] = $TEMP_RENT_STATUS;
        $rentLateJsonObject["rent_time_start"] = $TEMP_RENT_TIME_START;
        $rentLateJsonObject["rent_time_end"] = $TEMP_RENT_TIME_END;
        $rentLateJsonObject["rent_time_return"] = $TEMP_RENT_TIME_RETURN;
        array_push($rentLateJsonResult, $rentLateJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent late list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

foreach ($rentLateJsonResult as $dueObject) {
    if (isset($dueObject["rent_user_email"])) {
        $emailMessage = "";
        sendRentEmail($dueObject["rent_user_email"], $emailMessage);
    }
    if (isset($dueObject["rent_user_uuid"])) {
        $fcmMessage = "";
        sendRentFCM($dueObject["rent_user_uuid"], $fcmMessage);
    }
}

# rent manage success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>