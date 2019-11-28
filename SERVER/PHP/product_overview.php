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

# execute product group overview - available query
try {
    $DB_SQL = "SELECT `product_group`, COUNT(`product_index`) FROM `Products` WHERE `product_status` = 0 GROUP BY `product_group`";
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
        # product group overview query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_TYPE_INDEX, $TEMP_PRODUCT_TYPE_AVAILABLE);
    while($DB_STMT->fetch()) {
        foreach ($productGroupJsonResult as &$productGroupObject) {
            if ($productGroupObject["group_index"] == $TEMP_PRODUCT_TYPE_INDEX) {
                $productGroupObject["group_count_available"] = $TEMP_PRODUCT_TYPE_AVAILABLE;
                break;
            }
        }
        unset($productGroupObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product group overview query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute product group overview - unavailable query
try {
    $DB_SQL = "SELECT `product_group`, COUNT(`product_index`) FROM `Products` WHERE `product_status` = 1 GROUP BY `product_group`";
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
        # product group overview query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_TYPE_INDEX, $TEMP_PRODUCT_TYPE_UNAVAILABLE);
    while($DB_STMT->fetch()) {
        foreach ($productGroupJsonResult as &$productGroupObject) {
            if ($productGroupObject["group_index"] == $TEMP_PRODUCT_TYPE_INDEX) {
                $productGroupObject["group_count_unavailable"] = $TEMP_PRODUCT_TYPE_UNAVAILABLE;
                break;
            }
        }
        unset($productGroupObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product group overview query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute product group overview - broken query
try {
    $DB_SQL = "SELECT `product_group`, COUNT(`product_index`) FROM `Products` WHERE `product_status` = 2 GROUP BY `product_group`";
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
        # product group overview query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_TYPE_INDEX, $TEMP_PRODUCT_TYPE_BROKEN);
    while($DB_STMT->fetch()) {
        foreach ($productGroupJsonResult as &$productGroupObject) {
            if ($productGroupObject["group_index"] == $TEMP_PRODUCT_TYPE_INDEX) {
                $productGroupObject["group_count_broken"] = $TEMP_PRODUCT_TYPE_BROKEN;
                break;
            }
        }
        unset($productGroupObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product group overview query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute product group overview - repair query
try {
    $DB_SQL = "SELECT `product_group`, COUNT(`product_index`) FROM `Products` WHERE `product_status` = 3 GROUP BY `product_group`";
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
        # product group overview query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_TYPE_INDEX, $TEMP_PRODUCT_TYPE_REPAIR);
    while($DB_STMT->fetch()) {
        foreach ($productGroupJsonResult as &$productGroupObject) {
            if ($productGroupObject["group_index"] == $TEMP_PRODUCT_TYPE_INDEX) {
                $productGroupObject["group_count_repair"] = $TEMP_PRODUCT_TYPE_REPAIR;
                break;
            }
        }
        unset($productGroupObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product group overview query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute product group overview - rent query
try {
    $DB_SQL = "SELECT `product_group`, COUNT(`product_index`) FROM `Products` WHERE `product_rent` != 0 GROUP BY `product_group`";
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
        # product group overview query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_TYPE_INDEX, $TEMP_PRODUCT_TYPE_RENT);
    while($DB_STMT->fetch()) {
        foreach ($productGroupJsonResult as &$productGroupObject) {
            if ($productGroupObject["group_index"] == $TEMP_PRODUCT_TYPE_INDEX) {
                $productGroupObject["group_count_rent"] = $TEMP_PRODUCT_TYPE_RENT;
                break;
            }
        }
        unset($productGroupObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # product group overview query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# change products overview empty values to default
foreach ($productGroupJsonResult as &$productGroupJsonObject) {
    if (!isset($productGroupJsonObject["group_count_available"])) {
        $productGroupJsonObject["group_count_available"] = 0;
    }
    if (!isset($productGroupJsonObject["group_count_unavailable"])) {
        $productGroupJsonObject["group_count_unavailable"] = 0;
    }
    if (!isset($productGroupJsonObject["group_count_broken"])) {
        $productGroupJsonObject["group_count_broken"] = 0;
    }
    if (!isset($productGroupJsonObject["group_count_repair"])) {
        $productGroupJsonObject["group_count_repair"] = 0;
    }
    if (!isset($productGroupJsonObject["group_count_rent"])) {
        $productGroupJsonObject["group_count_rent"] = 0;
    }
}
unset($productGroupJsonObject);

# product group overview success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["groups"] = $productGroupJsonResult;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
