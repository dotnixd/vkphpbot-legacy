<?php

require_once("sql.php");
require_once("config.php");
$db = new DB($dbhost, $dbuser, $dbpassword, $dbname);
$db->Connect();

$db->GetConn()->prepare("CREATE TABLE role_assign (peer_id VARCHAR(30), user_id VARCHAR(30), role TINYINT);")->execute(array());
$db->GetConn()->prepare("CREATE TABLE dialogs (peer_id VARCHAR(30), greeting BLOB);")->execute(array());
$db->GetConn()->prepare("CREATE TABLE blacklist (id VARCHAR(30));")->execute(array());

?>
