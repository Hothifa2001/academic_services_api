<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->username) || !isset($data->password) || !isset($data->full_name) || !isset($data->department_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$username = $data->username;
$password = $data->password; // يُفضّل تشفيره في نسخة الإنتاج
$full_name = $data->full_name;
$department_id = $data->department_id;
$status = isset($data->status) ? $data->status : 0;

$query = "INSERT INTO supervisor (username, password, full_name, department_id, status)
          VALUES ('$username', '$password', '$full_name', '$department_id', '$status')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Supervisor created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
