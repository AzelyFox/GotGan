<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST');
if (isset($_REQUEST["value"]))
{
    $gotValue = $_REQUEST["value"];
    echo "VALUE is "."$gotValue";
} else {
    echo "NO VALUE!";
}
?>