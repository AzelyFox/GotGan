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
    if ($validation["user_level"] < 1) {
        # prevent normal user try to show other user info
        if (isset($_REQUEST["user_index"]))
        {
            $user_index = $_REQUEST["user_index"];
            if (!is_numeric($user_index)) {
                $output = array();
                $output["result"] = -1;
                $output["error"] = "user_index MUST BE INT";
                $outputJson = json_encode($output);
                echo urldecode($outputJson);
                exit();
            } else {
                $user_index = intval($user_index);
            }
            if ($user_index != $validation["user_index"]) {
                $output = array();
                $output["result"] = -3;
                $output["error"] = "NOT ALLOWED";
                $outputJson = json_encode($output);
                echo urldecode($outputJson);
                exit();
            }
        } else {
            $output = array();
            $output["result"] = -3;
            $output["error"] = "NOT ALLOWED";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    }
} else {
    $output = array();
    $output["result"] = -1;
    $output["error"] = "session IS EMPTY";
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# initialize user index if exist
if (isset($_REQUEST["user_index"]))
{
    $user_index = $_REQUEST["user_index"];
    if (!is_numeric($user_index)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_index MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $user_index = intval($user_index);
    }
}

# initialize user group if exist
if (isset($_REQUEST["user_group"]))
{
    $user_group = $_REQUEST["user_group"];
    if (!is_numeric($user_group)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_group MUST BE INT";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    } else {
        $user_group = intval($user_group);
    }
}

# prepare user list result
$userJsonResult = array();

# execute log list query
try {
    $DB_SQL = "
    SELECT `U`.`user_index`,
        `U`.`user_id`,
        `U`.`user_level`,
        `U`.`user_name`,
        `U`.`user_sid`,
        `U`.`user_block`,
        `U`.`user_uuid`,
        `U`.`user_group` as `user_group_index`,
        `UG`.`user_group_name` as `user_group_name`,
        `U`.`user_email`,
        `U`.`user_phone`,
        `U`.`user_created`
    FROM `Users` AS `U` 
        LEFT OUTER JOIN `UserGroup` AS `UG` ON (`U`.`user_group` = `UG`.`user_group_index`) 
    WHERE 1=1";
    if (isset($user_index)) {
        $DB_SQL .= " AND `user_index` = $user_index";
    }
    if (isset($user_group)) {
        $DB_SQL .= " AND `user_group` = $user_group";
    }
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
        # user list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "USER LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_USER_INDEX, $TEMP_USER_ID, $TEMP_USER_LEVEL, $TEMP_USER_NAME, $TEMP_USER_SID, $TEMP_USER_BLOCK, $TEMP_USER_UUID, $TEMP_USER_GROUP_INDEX, $TEMP_USER_GROUP_NAME, $TEMP_USER_EMAIL, $TEMP_USER_PHONE, $TEMP_USER_CREATED);
    while($DB_STMT->fetch()) {
        $userJsonObject = array();
        $userJsonObject["user_index"] = $TEMP_USER_INDEX;
        $userJsonObject["user_id"] = $TEMP_USER_ID;
        $userJsonObject["user_level"] = $TEMP_USER_LEVEL;
        $userJsonObject["user_name"] = $TEMP_USER_NAME;
        if (isset($TEMP_USER_SID)) {
            $userJsonObject["user_sid"] = $TEMP_USER_SID;
        }
        if (isset($TEMP_USER_BLOCK)) {
            $userJsonObject["user_block"] = $TEMP_USER_BLOCK;
        }
        if (isset($TEMP_USER_UUID)) {
            $userJsonObject["user_uuid"] = $TEMP_USER_UUID;
        }
        $userJsonObject["user_group_index"] = $TEMP_USER_GROUP_INDEX;
        if (isset($TEMP_USER_GROUP_NAME)) {
            $userJsonObject["user_group_name"] = $TEMP_USER_GROUP_NAME;
        } else {
            $userJsonObject["user_group_name"] = "";
        }
        if (isset($TEMP_USER_EMAIL)) {
            $userJsonObject["user_email"] = $TEMP_USER_EMAIL;
        }
        if (isset($TEMP_USER_PHONE)) {
            $userJsonObject["user_phone"] = $TEMP_USER_PHONE;
        }
        $userJsonObject["user_created"] = $TEMP_USER_CREATED;
        array_push($userJsonResult, $userJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # user list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# prepare user group list result
$userGroupJsonResult = array();

# execute user group list query
try {
    $DB_SQL = "SELECT `user_group_index`, `user_group_name` FROM `UserGroup`";
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
        # user group list query error
        $output = array();
        $output["result"] = -4;
        $output["error"] = "USER GROUP LIST FAILURE : ".$DB_STMT->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->bind_result($TEMP_USER_TYPE_INDEX, $TEMP_USER_TYPE_NAME);
    while($DB_STMT->fetch()) {
        $userGroupJsonObject = array();
        $userGroupJsonObject["group_index"] = $TEMP_USER_TYPE_INDEX;
        $userGroupJsonObject["group_name"] = $TEMP_USER_TYPE_NAME;
        array_push($userGroupJsonResult, $userGroupJsonObject);
    }
    $DB_STMT->close();
} catch(Exception $e) {
    # user group list query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# log list success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["users"] = $userJsonResult;
if ($validation["user_level"] > 0) {
    $output["groups"] = $userGroupJsonResult;
}
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
