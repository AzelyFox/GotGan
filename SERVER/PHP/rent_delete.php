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
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "rent_index IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# get rent details
try {
    $DB_SQL = "SELECT `PG`.`product_group_rentable`, `R`.`rent_product`, `R`.`rent_user`, `R`.`rent_status` FROM `Rents` as `R` LEFT OUTER JOIN `Products` AS `P` ON (`R`.`rent_product` = `P`.`product_index`) LEFT OUTER JOIN `ProductGroup` AS `PG` ON (`P`.`product_group` = `PG`.`product_group_index`) WHERE `rent_index` = ?";
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
        # rent detail query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "RENT PRODUCT SEARCH FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_RENTABLE, $TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_USER, $TEMP_RENT_STATUS);
    $DB_STMT->fetch();
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

if ($validation["user_level"] < 1 && ($TEMP_RENT_STATUS != 1 || $TEMP_PRODUCT_USER != $validation["user_index"])) {
    $output = array();
    $output["result"] = -3;
    $output["error"] = "NOT ALLOWED";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute rent deletion query
try {
    $DB_SQL = "DELETE FROM `Rents` WHERE `rent_index` = ?";
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
        # rent deletion query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "UPDATE RENT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent deletion query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute rent deletion query
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
        # rent deletion query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "UPDATE RENT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # rent deletion query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# rent deletion log
newLog($DB, LogTypes::TYPE_RENT_DELETE, $TEMP_PRODUCT_INDEX, $TEMP_PRODUCT_USER, NULL);

# rent deletion success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
