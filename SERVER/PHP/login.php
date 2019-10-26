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
    # initialize user id
    if (isset($_REQUEST["user_id"]))
    {
        $user_id = $_REQUEST["user_id"];
        if (!is_string($user_id)) {
            $output = array();
            $output["result"] = -1;
            $output["error"] = "user_id MUST BE STRING";
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } else {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_id IS EMPTY";
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # initialize user pw
    if (isset($_REQUEST["user_pw"]))
    {
        $user_pw = $_REQUEST["user_pw"];
        if (!is_string($user_pw)) {
            $output = array();
            $output["result"] = -1;
            $output["error"] = "user_pw MUST BE STRING";
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } else {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_pw IS EMPTY";
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# initialize user mobile id if exist
if (isset($_REQUEST["user_uuid"]))
{
    $user_uuid = $_REQUEST["user_uuid"];
    if (!is_string($user_uuid)) {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_uuid MUST BE STRING";
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

# execute login query
try {
    $DB_SQL = "SELECT `U`.`user_index`, `U`.`user_id`, `U`.`user_pw`, 
`U`.`user_level`, `U`.`user_name`, `U`.`user_sid`, `U`.`user_block`, 
`U`.`user_group` as `user_group_index`, `UG`.`user_group_name`, 
`U`.`user_email`, `U`.`user_phone`, `U`.`user_created` 
FROM `Users` AS `U` LEFT OUTER JOIN `UserGroup` AS `UG` ON (`U`.`user_group` = `UG`.`user_group_index`) 
WHERE `user_id` = ? or `user_index` = ?";
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
    $DB_STMT->bind_param("si", $user_id, $validation["user_index"]);
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
    $DB_STMT->bind_result($TEMP_USER_INDEX, $TEMP_USER_ID, $TEMP_USER_PW, $TEMP_USER_LEVEL, $TEMP_USER_NAME, $TEMP_USER_SID, $TEMP_USER_BLOCK, $TEMP_USER_GROUP_INDEX, $TEMP_USER_GROUP_NAME, $TEMP_USER_EMAIL, $TEMP_USER_PHONE, $TEMP_USER_CREATED);
    $DB_STMT->store_result();
    if ($DB_STMT->num_rows != 1) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "LOGIN FAILURE";
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
    $DB_STMT->fetch();
    $DB_STMT->close();
} catch(Exception $e) {
    # login query error
    $output = array();
    $output["result"] = -2;
    $output["error"] = "DB QUERY FAILURE : ".$DB->error;
    $output["debug_file"] = __FILE__;
    $output["debug_line"] = __LINE__;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

# password matching
if (!isset($session) && !password_verify($user_pw, $TEMP_USER_PW)) {
    $output = array();
    $output["result"] = -3;
    $output["error"] = "LOGIN FAILURE";
    $output["debug_file"] = __FILE__;
    $output["debug_line"] = __LINE__;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

if (getSystemSwitch($DB, SwitchTypes::SWITCH_MASTER) == 0) {
    if (isset($session)) {
        if ($validation["user_level"] < 2) {
            $output = array();
            $output["result"] = -3;
            $output["error"] = "SYSTEM SWITCH IS OFF";
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } else {
        if ($TEMP_USER_LEVEL < 2) {
            $output = array();
            $output["result"] = -3;
            $output["error"] = "SYSTEM SWITCH IS OFF";
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    }
}

if (getSystemSwitch($DB, SwitchTypes::SWITCH_LOGIN) == 0) {
    if (isset($session)) {
        if ($validation["user_level"] < 2) {
            $output = array();
            $output["result"] = -3;
            $output["error"] = "LOGIN SWITCH IS OFF";
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } else {
        if ($TEMP_USER_LEVEL < 2) {
            $output = array();
            $output["result"] = -3;
            $output["error"] = "LOGIN SWITCH IS OFF";
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    }
}

if ($TEMP_USER_BLOCK > 0) {
    $output = array();
    $output["result"] = -3;
    $output["error"] = "LOGIN FAILURE : BANNED ".$TEMP_USER_BLOCK." DAYS";
    $output["debug_file"] = __FILE__;
    $output["debug_line"] = __LINE__;
    $outputJson = json_encode($output);
    echo urldecode($outputJson);
    exit();
}

if (!isset($session)) {
    # generate session key
    $GENERATOR_CHARACTERS = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $GENERATOR_CHARACTERS_LENGTH = strlen($GENERATOR_CHARACTERS);
    $GENERATOR_LENGTH = 12;
    $GENERATOR_RESULT = "";
    for ($i = 0; $i < $GENERATOR_LENGTH; $i++) {
        $GENERATOR_RESULT .= $GENERATOR_CHARACTERS[rand(0, $GENERATOR_CHARACTERS_LENGTH - 1)];
    }
    # execute user session creation query for not blocked user
    try {
        $DB_SQL = "INSERT INTO `UserSession` (`user_session_user`, `user_session_key`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `user_session_key` = ?, `user_session_time` = now()";
        $DB_STMT = $DB->prepare($DB_SQL);
        # database query not ready
        if (!$DB_STMT) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $output["debug_file"] = __FILE__;
            $output["debug_line"] = __LINE__;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->bind_param("iss", $TEMP_USER_INDEX, $GENERATOR_RESULT, $GENERATOR_RESULT);
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
    } catch (Exception $e) {
        # user session query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $output["debug_file"] = __FILE__;
        $output["debug_line"] = __LINE__;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
} else {
    $GENERATOR_RESULT = $session;
    $TEMP_USER_INDEX = $validation["user_index"];
}

# user login log
newLog($DB, LogTypes::TYPE_LOGIN, 0, $TEMP_USER_INDEX, NULL);

# user login success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["session"] = $GENERATOR_RESULT;
$output["user_index"] = $TEMP_USER_INDEX;
$output["user_id"] = $TEMP_USER_ID;
$output["user_level"] = $TEMP_USER_LEVEL;
$output["user_name"] = $TEMP_USER_NAME;
if (isset($TEMP_USER_SID)) {
    $output["user_sid"] = $TEMP_USER_SID;
}
if (isset($TEMP_USER_BLOCK)) {
    $output["user_block"] = $TEMP_USER_BLOCK;
}
$output["user_group_index"] = $TEMP_USER_GROUP_INDEX;
$output["user_group_name"] = $TEMP_USER_GROUP_NAME;
if (isset($TEMP_USER_EMAIL)) {
    $output["user_email"] = $TEMP_USER_EMAIL;
}
if (isset($TEMP_USER_PHONE)) {
    $output["user_phone"] = $TEMP_USER_PHONE;
}
$output["user_created"] = $TEMP_USER_CREATED;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
