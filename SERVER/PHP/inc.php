<?php

declare(strict_types = 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: text/html; charset=UTF-8');

$DB_HOST = "localhost";
$DB_ID = "gotgan";
$DB_PW = "tfGjdp1wrihk0Y8z";
$DB_NAME = "gotgan";
$DB_PORT = "3306";

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
    # execute session validate query
    try {
        $DB_SQL_SESSION = "SELECT user_session_user FROM UserSession WHERE UserSession.user_session_key = ?";
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
        $DB_SQL_SESSION = "SELECT user_level FROM Users WHERE Users.user_index = ?";
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
        $DB_STMT_SESSION->bind_result($_validation["user_level"]);
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

?>