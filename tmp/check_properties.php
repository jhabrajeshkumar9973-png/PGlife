<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'pglife');
if (!$conn) { echo 'DB_FAIL'; exit; }
$res = mysqli_query($conn, 'SELECT id, name FROM cities');
while ($row = mysqli_fetch_assoc($res)) {
    $p = mysqli_query($conn, "SELECT COUNT(*) as c FROM properties WHERE city_id = {$row['id']}");
    $count = mysqli_fetch_assoc($p)['c'];
    echo $row['name'] . ':' . $count . PHP_EOL;
}
mysqli_close($conn);
