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
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $validation = validateSession($DB, $session);
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $output["debug_file"] = __FILE__;
    $output["debug_line"] = __LINE__;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# execute logout query
try {
    $DB_SQL = "DELETE FROM `usersession` WHERE `user_session_key` = ?";
    $DB_STMT = $DB->prepare($DB_SQL);
    # database query not ready
    if (!$DB_STMT) {
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_param("s", $session);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : " . $DB_STMT->error;
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # logout query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $output["debug_file"] = __FILE__;
    $output["debug_line"] = __LINE__;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# user logout success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
