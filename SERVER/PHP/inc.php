<?php

declare(strict_types = 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: text/html; charset=UTF-8');

require_once "constants.php";

# connects to local database
$DB = mysqli_connect($DB_HOST, $DB_ID, $DB_PW, $DB_NAME);
if (mysqli_connect_errno()) {
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB CONNECTION FAILURE : ".mysqli_connect_error();
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

function validateSession(Mysqli $DB, string $session) {
    $_validation = array();
    $_validation["user_index"] = -1;
    $_validation["user_level"] = -1;
    $_validation["user_id"] = "";
    $_validation["user_name"] = "";

    # execute session validate query
    try {
        $DB_SQL_SESSION = "SELECT `user_session_user` FROM `UserSession` WHERE `user_session_key` = ?";
        $DB_STMT_SESSION = $DB->prepare($DB_SQL_SESSION);
        # database query not ready
        if (!$DB_STMT_SESSION) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT_SESSION->bind_param("s", $session);
        $DB_STMT_SESSION->execute();
        if ($DB_STMT_SESSION->errno != 0) {
            # user session query error
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : ".$DB_STMT_SESSION->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT_SESSION->bind_result($_validation["user_index"]);
        $DB_STMT_SESSION->store_result();
    } catch (Exception $e) {
        # user session query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # session is not valid
    if ($DB_STMT_SESSION->num_rows != 1) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "USER SESSION NOT VALID";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # session is valid
    $DB_STMT_SESSION->fetch();
    $DB_STMT_SESSION->close();

    # execute user level query
    try {
        $DB_SQL_SESSION = "SELECT `user_id`, `user_name`, `user_level` FROM `Users` WHERE `user_index` = ?";
        $DB_STMT_SESSION = $DB->prepare($DB_SQL_SESSION);
        # database query not ready
        if (!$DB_STMT_SESSION) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT_SESSION->bind_param("i", $_validation["user_index"]);
        $DB_STMT_SESSION->execute();
        if ($DB_STMT_SESSION->errno != 0) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT_SESSION->bind_result($_validation["user_id"], $_validation["user_name"], $_validation["user_level"]);
        $DB_STMT_SESSION->store_result();
    } catch (Exception $e) {
        # user session query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # query result is not valid
    if ($DB_STMT_SESSION->num_rows != 1) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "USER SESSION INDEX NOT VALID";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # query result is valid
    $DB_STMT_SESSION->fetch();
    $DB_STMT_SESSION->close();

    return $_validation;
}

class LogTypes {
    const TYPE_LOG_NORMAL = 1;
    const TYPE_LOG_IMPORTANT = 2;
    const TYPE_LOG_CRITICAL = 3;
    const TYPE_LOGIN = 4;
    const TYPE_USER_ADD = 5;
    const TYPE_USER_MODIFY = 6;
    const TYPE_USER_DELETE = 7;
    const TYPE_USER_GROUP_ADD = 8;
    const TYPE_USER_GROUP_MODIFY = 9;
    const TYPE_USER_GROUP_DELETE = 10;
    const TYPE_PRODUCT_ADD = 11;
    const TYPE_PRODUCT_MODIFY = 12;
    const TYPE_PRODUCT_DELETE = 13;
    const TYPE_PRODUCT_GROUP_ADD= 14;
    const TYPE_PRODUCT_GROUP_MODIFY = 15;
    const TYPE_PRODUCT_GROUP_DELETE = 16;
    const TYPE_RENT_ADD = 17;
    const TYPE_RENT_ALLOW = 18;
    const TYPE_RENT_RETURN = 19;
    const TYPE_RENT_MODIFY = 20;
    const TYPE_RENT_DELETE = 21;
}

function newLog(Mysqli $DB, $logType, $logProduct, $logUser, $logText) {
    if (!isset($logType) || !isset($logProduct) || !isset($logUser)) return -1;
    # execute log query
    try {
        $DB_SQL_LOG = "INSERT INTO `Logs` (`log_product`, `log_user`, `log_type`, `log_text`) VALUES (?, ?, ?, ?)";
        $DB_STMT_LOG = $DB->prepare($DB_SQL_LOG);
        # database query not ready
        if (!$DB_STMT_LOG) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT_LOG->bind_param("iiis", $logProduct, $logUser, $logType, $logText);
        $DB_STMT_LOG->execute();
        if ($DB_STMT_LOG->errno != 0) {
            # log query error
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : ".$DB_STMT_LOG->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $TEMP_LOG_INSERTED = $DB_STMT_LOG->insert_id;
        $DB_STMT_LOG->close();
    } catch (Exception $e) {
        # log query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    return $TEMP_LOG_INSERTED;
}

?>