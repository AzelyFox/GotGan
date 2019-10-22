<?php

require_once "./inc.php";

$user_id = "";
$user_pw = "";
$user_sid = "";
$user_uuid = "";

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
    # initialize user id
    if (isset($_REQUEST["user_id"]))
    {
        $user_id = $_REQUEST["user_id"];
        if (!is_string($user_id)) {
            $output = array();
            $output["result"] = -1;
            $output["error"] = "user_id MUST BE STRING";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
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
        $user_pw = $_REQUEST["user_pw"];
        if (!is_string($user_pw)) {
            $output = array();
            $output["result"] = -1;
            $output["error"] = "user_pw MUST BE STRING";
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } else {
        $output = array();
        $output["result"] = -1;
        $output["error"] = "user_pw IS EMPTY";
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
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }
}

if (!isset($session)) {
    # execute login query
    try {
        $DB_SQL = "SELECT `user_index`, `user_id`, `user_pw`, `user_level`, `user_name`, `user_sid`, `user_block`, `user_group`, `user_email`, `user_phone`, `user_created` FROM `Users` WHERE `user_id` = ?";
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
        $DB_STMT->bind_param("s", $user_id);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->store_result();
        $DB_STMT->bind_result($TEMP_USER_INDEX, $TEMP_USER_ID, $TEMP_USER_PW, $TEMP_USER_LEVEL, $TEMP_USER_NAME, $TEMP_USER_SID, $TEMP_USER_BLOCK, $TEMP_USER_GROUP_INDEX, $TEMP_USER_EMAIL, $TEMP_USER_PHONE, $TEMP_USER_CREATED);
        $DB_STMT->fetch();
    } catch(Exception $e) {
        # login query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # login failed
    if ($DB_STMT->num_rows != 1) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "LOGIN FAILURE";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # password matching
    if (!password_verify($user_pw, $TEMP_USER_PW)) {
        echo $TEMP_USER_PW;
        $output = array();
        $output["result"] = -3;
        $output["error"] = "LOGIN FAILURE";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # login success
    $DB_STMT->close();

    # execute user group query
    try {
        $DB_SQL = "SELECT `user_group_index`, `user_group_name` FROM `UserGroup` WHERE `user_group_index` = ?";
        $DB_STMT = $DB->prepare($DB_SQL);
        # database query not ready
        if (!$DB_STMT) {
            $TEMP_USER_GROUP_NAME = "";
        }
        $DB_STMT->bind_param("i", $TEMP_USER_GROUP_INDEX);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            $TEMP_USER_GROUP_NAME = "";
        }
        $DB_STMT->bind_result($TEMP_USER_GROUP_INDEX, $TEMP_USER_GROUP_NAME);
        $DB_STMT->store_result();
    } catch (Exception $e) {
        # user group query error
        $TEMP_USER_GROUP_NAME = "";
    }

    # user group find failed
    if ($DB_STMT->num_rows != 1) {
        $TEMP_USER_GROUP_NAME = "";
    }

    # user group query success
    $DB_STMT->fetch();
    $DB_STMT->close();

    # generate session key
    $GENERATOR_CHARACTERS = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $GENERATOR_CHARACTERS_LENGTH = strlen($GENERATOR_CHARACTERS);
    $GENERATOR_LENGTH = 12;
    $GENERATOR_RESULT = "";
    for ($i = 0; $i < $GENERATOR_LENGTH; $i++) {
        $GENERATOR_RESULT .= $GENERATOR_CHARACTERS[rand(0, $GENERATOR_CHARACTERS_LENGTH - 1)];
    }

    if ($TEMP_USER_BLOCK == 0) {
        # execute user session creation query for not blocked user
        try {
            $DB_SQL = "INSERT INTO `UserSession` (`user_session_user`, `user_session_key`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `user_session_key` = ?, `user_session_time` = now()";
            $DB_STMT = $DB->prepare($DB_SQL);
            # database query not ready
            if (!$DB_STMT) {
                $output = array();
                $output["result"] = -2;
                $output["error"] = "DB QUERY FAILURE : " . $DB->error;
                $outputJson = json_encode($output);
                echo urldecode($outputJson);
                exit();
            }
            $DB_STMT->bind_param("iss", $TEMP_USER_INDEX, $GENERATOR_RESULT, $GENERATOR_RESULT);
            $DB_STMT->execute();
            if ($DB_STMT->errno != 0) {
                $output = array();
                $output["result"] = -2;
                $output["error"] = "DB QUERY FAILURE : " . $DB->error;
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
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
    } else {
        # no session key for blocked user
        $GENERATOR_RESULT = "";
    }
} else {
    # execute session login query
    try {
        $DB_SQL = "SELECT `user_index`, `user_id`, `user_pw`, `user_level`, `user_name`, `user_sid`, `user_block`, `user_group`, `user_email`, `user_phone`, `user_created` FROM `Users` WHERE `user_index` = ?";
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
        $DB_STMT->bind_param("i", $validation["user_index"]);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            $output = array();
            $output["result"] = -2;
            $output["error"] = "DB QUERY FAILURE : " . $DB->error;
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $DB_STMT->store_result();
        $DB_STMT->bind_result($TEMP_USER_INDEX, $TEMP_USER_ID, $TEMP_USER_PW, $TEMP_USER_LEVEL, $TEMP_USER_NAME, $TEMP_USER_SID, $TEMP_USER_BLOCK, $TEMP_USER_GROUP_INDEX, $TEMP_USER_EMAIL, $TEMP_USER_PHONE, $TEMP_USER_CREATED);
        $DB_STMT->fetch();
    } catch(Exception $e) {
        # login query error
        $output = array();
        $output["result"] = -2;
        $output["error"] = "DB QUERY FAILURE : ".$DB->error;
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # login failed
    if ($DB_STMT->num_rows != 1) {
        $output = array();
        $output["result"] = -3;
        $output["error"] = "LOGIN FAILURE";
        $outputJson = json_encode($output);
        echo urldecode($outputJson);
        exit();
    }

    # login success
    $DB_STMT->close();

    # execute user group query
    try {
        $DB_SQL = "SELECT `user_group_index`, `user_group_name` FROM `UserGroup` WHERE `user_group_index` = ?";
        $DB_STMT = $DB->prepare($DB_SQL);
        # database query not ready
        if (!$DB_STMT) {
            $TEMP_USER_GROUP_NAME = "";
        }
        $DB_STMT->bind_param("i", $TEMP_USER_GROUP_INDEX);
        $DB_STMT->execute();
        if ($DB_STMT->errno != 0) {
            $TEMP_USER_GROUP_NAME = "";
        }
        $DB_STMT->bind_result($TEMP_USER_GROUP_INDEX, $TEMP_USER_GROUP_NAME);
        $DB_STMT->store_result();
    } catch (Exception $e) {
        # user group query error
        $TEMP_USER_GROUP_NAME = "";
    }

    # user group find failed
    if ($DB_STMT->num_rows != 1) {
        $TEMP_USER_GROUP_NAME = "";
    }

    # user group query success
    $DB_STMT->fetch();
    $DB_STMT->close();

    if ($TEMP_USER_BLOCK == 0) {
        # execute user session creation query for not blocked user
        try {
            $DB_SQL = "UPDATE `UserSession` SET `user_session_time` = now() WHERE `user_session_key` = ?";
            $DB_STMT = $DB->prepare($DB_SQL);
            # database query not ready
            if (!$DB_STMT) {
                $output = array();
                $output["result"] = -2;
                $output["error"] = "DB QUERY FAILURE : " . $DB->error;
                $outputJson = json_encode($output);
                echo urldecode($outputJson);
                exit();
            }
            $DB_STMT->bind_param("s", $session);
            $DB_STMT->execute();
            if ($DB_STMT->errno != 0) {
                $output = array();
                $output["result"] = -2;
                $output["error"] = "DB QUERY FAILURE : " . $DB->error;
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
            $outputJson = json_encode($output);
            echo urldecode($outputJson);
            exit();
        }
        $GENERATOR_RESULT = $session;
    } else {
        # no session key for blocked user
        $GENERATOR_RESULT = "";
    }
}

# user session query success
$output = array();
$output["result"] = 0;
$output["error"] = "";
$output["session"] = $GENERATOR_RESULT;
$output["user_index"] = $TEMP_USER_INDEX;
$output["user_id"] = $TEMP_USER_ID;
$output["user_level"] = $TEMP_USER_LEVEL;
$output["user_name"] = $TEMP_USER_NAME;
if ($TEMP_USER_SID !== null) {
    $output["user_sid"] = $TEMP_USER_SID;
}
if ($TEMP_USER_BLOCK !== null) {
    $output["user_block"] = $TEMP_USER_BLOCK;
}
$output["user_group_index"] = $TEMP_USER_GROUP_INDEX;
$output["user_group_name"] = $TEMP_USER_GROUP_NAME;
if ($TEMP_USER_EMAIL !== null) {
    $output["user_email"] = $TEMP_USER_EMAIL;
}
if ($TEMP_USER_PHONE !== null) {
    $output["user_phone"] = $TEMP_USER_PHONE;
}
$output["user_created"] = $TEMP_USER_CREATED;
$outputJson = json_encode($output);
echo urldecode($outputJson);

?>
