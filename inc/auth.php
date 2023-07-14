<?php

# Verify node_id against token using
function auth_node($node_id, $token, $db) {
  $sql = "SELECT * FROM `nodes` WHERE `node_id` = '$node_id' AND `node_token` = '$token'";
  $result = $db->query($sql);
  if ($result->num_rows == 1) {
    return true;
  } else {
    return false;
  }
}
