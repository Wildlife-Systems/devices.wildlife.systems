<html>
<head>
<title>devices.wildlife.systems</title>
</head>

<body>
<h1>devices.wildlife.systems</h1>

<p>Devices currently reporting their status to the wildlife.systems network.</p>

<?php
include ("config/db.php");

$sql = "SELECT `hostname`, `status`, UNIX_TIMESTAMP()-`timestamp` AS `ago` FROM ws.heartbeats;";
$result = $db->query($sql);
if ($result->num_rows > 0) {
  echo "<table><tr><th>Hostname</th><th>Current action</th><th>Seconds since last update</th></tr>";
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row["hostname"] . "</td><td>" . $row["status"] . "</td><td>" . $row["ago"] . "</td></tr>";
  }
  echo "</table>";
} else {
  echo "0 results";
}
$db->close();

?>
</body>
</html>
