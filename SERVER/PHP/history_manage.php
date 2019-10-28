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
        $output = array();
        $output["result"] = -3;
        $output["error"] = "key NOT VALID";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "key IS EMPTY";
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

# execute product rent count query
try {
    $DB_SQL = "SELECT COUNT(*) FROM `Products` WHERE `product_rent` != 0";
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
        # product rent count query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "PRODUCT RENT COUNT FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_PRODUCT_RENT_COUNT);
    $DB_STMT->fetch();
    $DB_STMT->close();
} catch(Exception $e) {
    # product rent count query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute insert history query
try {
    $DB_SQL = "INSERT INTO `History` (`history_time`, `history_user_total`, `history_product_total`, `history_product_available`, `history_product_rent`, `history_rent_total`) VALUES (CAST(NOW() AS DATE), ?, ?, ?, ?, ?)";
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
    $DB_STMT->bind_param("iiiii", $TEMP_USER_COUNT, $TEMP_PRODUCT_TOTAL_COUNT, $TEMP_PRODUCT_AVAILABLE_COUNT, $TEMP_PRODUCT_RENT_COUNT, $TEMP_RENT_COUNT);
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