<?php
header('Content-Type: application/json');
require("../includes/database_connect.php");

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$college_name = isset($_POST['college_name']) ? trim($_POST['college_name']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';

if ($full_name === '' || $phone === '' || $email === '' || $password === '' || $college_name === '' || $gender === '') {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
    exit;
}

$phone = preg_replace('/[^0-9]/', '', $phone);
if (strlen($phone) < 10) {
    echo json_encode(["success" => false, "message" => "Please enter a valid phone number."]);
    exit;
}

$email = mysqli_real_escape_string($conn, $email);
$full_name = mysqli_real_escape_string($conn, $full_name);
$phone = mysqli_real_escape_string($conn, $phone);
$college_name = mysqli_real_escape_string($conn, $college_name);
$gender = mysqli_real_escape_string($conn, $gender);
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(["success" => false, "message" => "Something went wrong!"]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    echo json_encode(["success" => false, "message" => "This email id is already registered with us!"]);
    exit;
}

$sql = "INSERT INTO users (email, password, full_name, phone, gender, college_name) VALUES ('$email', '$password_hash', '$full_name', '$phone', '$gender', '$college_name')";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo json_encode(["success" => false, "message" => "Something went wrong!"]);
    exit;
}

echo json_encode(["success" => true, "message" => "Your account has been created successfully!"]);
mysqli_close($conn);
