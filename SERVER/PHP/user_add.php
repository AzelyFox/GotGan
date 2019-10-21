<?php

require_once "./inc.php";

$session = "";
$user_id = "";
$user_pw = "";
$user_level = 0;
$user_name = "";

# initialize user id
if (isset($_REQUEST["user_id"]))
{
    $user_id = $_REQUEST["user_id"];
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_id IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user pw
if (isset($_REQUEST["user_pw"]))
{
    $user_pw = password_hash($_REQUEST["user_pw"], PASSWORD_BCRYPT);
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_pw IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user level
if (isset($_REQUEST["user_level"]))
{
    $user_level = $_REQUEST["user_level"];
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_level IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user name
if (isset($_REQUEST["user_name"]))
{
    $user_name = $_REQUEST["user_name"];
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "user_name IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user group
if (isset($_REQUEST["user_group"]))
{
    $user_group = $_REQUEST["user_group"];
} else {
    $user_group = 0;
}

# initialize user student id if exist
if (isset($_REQUEST["user_sid"]))
{
    $user_sid = $_REQUEST["user_sid"];
} else {
    $user_sid = NULL;
}

# initialize user block if exist
if (isset($_REQUEST["user_block"]))
{
    $user_block = $_REQUEST["user_block"];
} else {
    $user_block = 0;
}

# initialize user mobile id if exist
if (isset($_REQUEST["user_uuid"]))
{
    $user_uuid = $_REQUEST["user_uuid"];
} else {
    $user_uuid = NULL;
}

# initialize user email if exist
if (isset($_REQUEST["user_email"]))
{
    $user_email = $_REQUEST["user_email"];
} else {
    $user_email = NULL;
}

# initialize user phone if exist
if (isset($_REQUEST["user_phone"]))
{
    $user_phone = $_REQUEST["user_phone"];
} else {
    $user_phone = NULL;
}

# session auth
if (isset($_REQUEST["session"]))
{
    $session = $_REQUEST["session"];
    $validation = validateSession($DB, $session);

    # check user level
    if ($validation["user_level"] < 1) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    # if no auth user try to make manager account
    if ($user_level > 0) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# execute user creation query
try {
    $DB_SQL = "INSERT INTO `Users` (`user_id`, `user_pw`, `user_level`, `user_name`, `user_sid`, `user_block`, `user_uuid`, `user_group`, `user_email`, `user_phone`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
    $DB_STMT->bind_param("ssissisiss", $user_id, $user_pw, $user_level, $user_name, $user_sid, $user_block, $user_uuid, $user_group, $user_email, $user_phone);
    $DB_STMT->execute();
    if ($DB_STMT->errno != 0) {
        # user creation query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "ADD USER FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $TEMP_INSERTED_ROW = $DB_STMT->insert_id;
    $DB_STMT->close();
} catch(Exception $e) {
    # user creation query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# user creation success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["user_index"] = $TEMP_INSERTED_ROW;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
