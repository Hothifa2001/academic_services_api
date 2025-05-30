<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || !isset($data->department_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$name = $data->name;
$department_id = $data->department_id;

$query = "INSERT INTO major (name, department_id) VALUES ('$name', '$department_id')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Major created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
