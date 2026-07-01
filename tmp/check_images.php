<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'pglife');
$city_name = 'Delhi';
$city_key = strtolower($city_name);
$city_variants = array($city_key, 'bangalore', 'bengaluru', 'bengaluru');
$sql = "SELECT * FROM cities WHERE LOWER(name) IN ('" . implode("','", array_map(function ($value) { return mysqli_real_escape_string($conn, $value); }, $city_variants)) . "')";
$res = mysqli_query($conn, $sql);
$city = mysqli_fetch_assoc($res);
$city_id = $city['id'];
$properties = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM properties WHERE city_id = $city_id"), MYSQLI_ASSOC);
foreach ($properties as $property) {
    $property_images = glob('../img/properties/' . $property['id'] . '/*');
    echo $property['id'] . ' -> ' . print_r($property_images, true) . PHP_EOL;
}
