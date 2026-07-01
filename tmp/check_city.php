<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'pglife');
$city_name = 'bengaluru';
$res = mysqli_query($conn, "SELECT * FROM cities WHERE LOWER(name) = '$city_name'");
if ($row = mysqli_fetch_assoc($res)) {
    echo 'CITY_ID=' . $row['id'] . PHP_EOL;
    $prop_res = mysqli_query($conn, "SELECT * FROM properties WHERE city_id = {$row['id']}");
    echo 'PROPERTY_COUNT=' . mysqli_num_rows($prop_res) . PHP_EOL;
    while ($prop = mysqli_fetch_assoc($prop_res)) {
        echo 'PROPERTY=' . $prop['name'] . PHP_EOL;
    }
} else {
    echo 'NO_CITY' . PHP_EOL;
}
mysqli_close($conn);
