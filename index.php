<?php

error_reporting(0);

require_once("handlers.php");
require_once("api.php");
require_once("sql.php");
require_once("config.php");

$data = json_decode(file_get_contents('php://input'));

$db = new DB($dbhost, $dbuser, $dbpassword, $dbname);
$db->Connect();

$api = new VKApi($token);
$handle = new Handlers($api, $db);

switch($data->type) {
    case "confirmation":
        echo $confirmation_token;
        exit();
    break;
    default:
        $handle->Run($data);
}

echo "ok";

?>