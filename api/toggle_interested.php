<?php
session_start();

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require "../includes/database_connect.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "is_logged_in" => false]);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$property_id = isset($_GET['property_id']) ? intval($_GET['property_id']) : 0;

if ($property_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid property ID."]);
    exit;
}

$sql_1 = "SELECT * FROM interested_users_properties WHERE user_id = $user_id AND property_id = $property_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) {
    echo json_encode(["success" => false, "message" => "Something went wrong"]);
    exit;
}

if (mysqli_num_rows($result_1) > 0) {
    $sql_2 = "DELETE FROM interested_users_properties WHERE user_id = $user_id AND property_id = $property_id";
    $result_2 = mysqli_query($conn, $sql_2);
    if (!$result_2) {
        echo json_encode(["success" => false, "message" => "Something went wrong"]);
        exit;
    }
    echo json_encode(["success" => true, "is_interested" => false, "property_id" => $property_id]);
    exit;
}

$sql_3 = "INSERT INTO interested_users_properties (user_id, property_id) VALUES ($user_id, $property_id)";
$result_3 = mysqli_query($conn, $sql_3);
if (!$result_3) {
    echo json_encode(["success" => false, "message" => "Something went wrong"]);
    exit;
}

echo json_encode(["success" => true, "is_interested" => true, "property_id" => $property_id]);
exit;
