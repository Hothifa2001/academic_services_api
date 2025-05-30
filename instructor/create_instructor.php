<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password) || !isset($data->full_name) || !isset($data->major_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$username = $data->username;
$password = $data->password; // نص صريح بدون تشفير
$full_name = $data->full_name;
$major_id = $data->major_id;
$status = isset($data->status) ? $data->status : 0;

$query = "INSERT INTO instructor (username, password, full_name, major_id, status)
          VALUES ('$username', '$password', '$full_name', '$major_id', '$status')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Instructor created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
