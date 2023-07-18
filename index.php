<html>
<head>
  <title>devices.wildlife.systems</title>
  <link rel="icon" type="image/png" href="https://devices.wildlife.systems/favicon.png" />
  <link rel="stylesheet" href="https://audioblast.org/ab-api.css">
</head>

<body>
<div id="title">
  <a href="/">
    <img src="https://cdn.audioblast.org/wildlifesystems_flash.png"
    alt="WildlifeSytems flash logo"
    class="audioblast-flash" /></a>
  <h1>devices.wildlife.systems</h1>
</div>

<p>Devices currently reporting their status to the wildlife.systems network.</p>

<?php
include ("config/db.php");

$sql = "SELECT `node_id`, `hostname`, `status`, UNIX_TIMESTAMP()-`timestamp` AS `ago` FROM ws.heartbeats;";
$result = $db->query($sql);
if ($result->num_rows > 0) {
  echo "<table><tr><th>Node ID</th><th>Hostname</th><th>Current action</th><th>Seconds since last update</th></tr>";
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["node_id"] . "</td><td>" . $row["hostname"] . "</td><td>" . $row["status"] . "</td><td>" . $row["ago"] . "</td></tr>";
  }
  echo "</table>";
} else {
  echo "No devices reporting.";
}
$db->close();

?>
</body>
</html>
