<?php

require_once "./inc.php";

function modifySystem(Mysqli $DB, string $switchQuery, string $bindType, $bindValue) {
    # execute system switch query
    try {
        $DB_STMT = $DB->prepare($switchQuery);
        # database query not ready
        if (!$DB_STMT) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : ".$DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->bind_param($bindType, $bindValue);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            # system switch query error
            $output = array();
            $output["result"] = -4;
            $output["error"] = "SYSTEM SWITCH FAILURE : ".$DB_STMT->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->close();
    } catch(Exception $e) {
        # system switch query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
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
    if ($validation["user_level"] < 2) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "NOT ALLOWED";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize system master switch
if (isset($_REQUEST["system_on"]))
{
    $system_on = $_REQUEST["system_on"];
    if (!is_numeric($system_on)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "system_on MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $system_on = intval($system_on);
    }
    $masterSwitchQuery = "UPDATE `System` SET `system_on` = ?";
    modifySystem($DB, $masterSwitchQuery, "i", $system_on);
}

# initialize system login switch
if (isset($_REQUEST["system_login"]))
{
    $system_login = $_REQUEST["system_login"];
    if (!is_numeric($system_login)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "system_login MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $system_login = intval($system_login);
    }
    $loginSwitchQuery = "UPDATE `System` SET `system_login` = ?";
    modifySystem($DB, $loginSwitchQuery, "i", $system_login);
}

# initialize system rent switch
if (isset($_REQUEST["system_rent"]))
{
    $system_rent = $_REQUEST["system_rent"];
    if (!is_numeric($system_rent)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "system_rent MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $system_rent = intval($system_rent);
    }
    $rentSwitchQuery = "UPDATE `System` SET `system_rent` = ?";
    modifySystem($DB, $rentSwitchQuery, "i", $system_rent);
}

# initialize system message switch
if (isset($_REQUEST["system_message"]))
{
    $system_message = $_REQUEST["system_message"];
    if (!is_numeric($system_message)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "system_message MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $system_message = intval($system_message);
    }
    $messageSwitchQuery = "UPDATE `System` SET `system_message` = ?";
    modifySystem($DB, $messageSwitchQuery, "i", $system_message);
}

# history list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["system_on"] = getSystemSwitch($DB, SwitchTypes::SWITCH_MASTER);
$output["system_login"] = getSystemSwitch($DB, SwitchTypes::SWITCH_LOGIN);
$output["system_rent"] = getSystemSwitch($DB, SwitchTypes::SWITCH_RENT);
$output["system_message"] = getSystemSwitch($DB, SwitchTypes::SWITCH_MESSAGE);
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
