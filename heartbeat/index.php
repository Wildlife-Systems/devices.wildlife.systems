<?php
include ("../inc/db.php");
include("../inc/auth.php");

# Convert GET to POST
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $_POST = $_GET;
}

# Check node_id, token and payload are set in POST
if (!isset($_POST['node_id']) || !isset($_POST['token'])  || !isset($_POST['heartbeat'])) {
  http_response_code(400);
  die("Invalid node_id or token");
}

# Sanitise node_id, token and heartbeat payload
$_POST['node_id'] = $db->real_escape_string($_POST['node_id']);
$_POST['token'] = $db->real_escape_string($_POST['token']);

# Authenticate node_id against token from POST
if (!auth_node($_POST['node_id'], $_POST['token'], $db)) {
  http_response_code(401);
  die("Invalid node_id or token");
}

# Check string is JSON formatted
$heartbeat = json_decode(urldecode($_POST['heartbeat'], ));
if (!is_object($heartbeat) && !is_array($heartbeat)) {
  http_response_code(422);
  die("Heartbeat payload not valid JSON");
}

# Submit payload to database
$sensors = array();
$timestamp = $heartbeat[0]->timestamp;
foreach ($heartbeat as $sensor) {
  $sensors[$sensor->sensor] = $db->real_escape_string($sensor->value);
}

$sql  = "INSERT INTO `heartbeats` (`node_id`, `timestamp`, `cpu_temp`, `gpu_temp`, `memory_used`, `storage_used`) ";
$sql .= "VALUES ('{$_POST['node_id']}', ";
$sql .= "'".$timestamp."', ";
$sql .= "'".$sensors['onboard_cpu']."', ";
$sql .= "'".$sensors['onboard_gpu']."', ";
$sql .= "'".$sensors['memory_used']."', ";
$sql .= "'".$sensors['storage_used']."') ";
$sql .= " ON DUPLICATE KEY UPDATE ";
$sql .= "`timestamp` = '".$timestamp."', ";
$sql .= "`cpu_temp` = '".$sensors['onboard_cpu']."', ";
$sql .= "`gpu_temp` = '".$sensors['onboard_gpu']."', ";
$sql .= "`memory_used` = '".$sensors['memory_used']."', ";
$sql .= "`storage_used` = '".$sensors['storage_used']."';";

if (!$db->query($sql)) {
  http_response_code(500);
  die("Database error");
}

http_response_code(200);
echo "OK";
