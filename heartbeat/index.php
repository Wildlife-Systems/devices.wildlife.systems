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
if (!is_object($heartbeat[0]) && !is_array($heartbeat)) {
  http_response_code(422);
  die("Heartbeat payload not in expected format");
}

# Prepare payload for database
$sensors = array();
$timestamp = time();
foreach ($heartbeat as $sensor) {
  if (!isset($sensor->value)) {
    http_response_code(422);
    die("Heartbeat payload not in expected format");
  }
  $sensors[$sensor->sensor] = $db->real_escape_string($sensor->value);
  if (isset($sensor->timestamp)) {
    if ($sensor->$timestamp < $timestamp) {
      $timestamp = $sensor->timestamp;
    }
  }
}
if (!array_key_exists('onboard_cpu', $sensors)) { $sensors['onboard_cpu'] = 0; }
if (!array_key_exists('onboard_gpu', $sensors)) { $sensors['onboard_gpu'] = 0; } 
if (!array_key_exists('memory_used', $sensors)) { $sensors['memory_used'] = 0; }
if (!array_key_exists('storage_used', $sensors)) { $sensors['storage_used'] = 0; }

# Insert payload into database
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
