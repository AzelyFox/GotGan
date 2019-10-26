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

# execute rent count query
try {
    $DB_SQL = "SELECT COUNT(*) FROM `Rents` WHERE `rent_status` != 1";
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
        # rent count query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "RENT COUNT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_RENT_COUNT);
    $DB_STMT->fetch();
    $DB_STMT->close();
} catch(Exception $e) {
    # rent count query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute user count query
try {
    $DB_SQL = "SELECT COUNT(*) FROM `Users`";
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
        # user count query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "USER COUNT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_USER_COUNT);
    $DB_STMT->fetch();
    $DB_STMT->close();
} catch(Exception $e) {
    # user count query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute product total count query
try {
    $DB_SQL = "SELECT COUNT(*) FROM `products`";
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
        # product total count query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT TOTAL COUNT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_TOTAL_COUNT);
    $DB_STMT->fetch();
    $DB_STMT->close();
} catch(Exception $e) {
    # product total count query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute product available count query
try {
    $DB_SQL = "SELECT COUNT(*) FROM `Products` WHERE `product_status` = 0";
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
        # product available count query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT AVAILABLE COUNT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_AVAILABLE_COUNT);
    $DB_STMT->fetch();
    $DB_STMT->close();
} catch(Exception $e) {
    # product available count query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute insert history query
try {
    $DB_SQL = "INSERT INTO `History` (`history_time`, `history_user_total`, `history_product_total`, `history_product_available`, `history_product_rent`) VALUES (CAST(NOW() AS DATE), ?, ?, ?, ?)";
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
    $DB_STMT->bind_param("iiii", $TEMP_USER_COUNT, $TEMP_PRODUCT_TOTAL_COUNT, $TEMP_PRODUCT_AVAILABLE_COUNT, $TEMP_RENT_COUNT);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        if ($DB_STMT->errno == 1062) {
            # already today history is in database
            $output = array();
            $output["result"] = -3;
            $output["error"] = "ALREADY TODAY HISTORY IS IN DATABASE";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        } else {
            # insert history query error
            $output = array();
            $output["result"] = -4;
            $output["error"] = "INSERT HISTORY FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # insert history query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# history manage success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>