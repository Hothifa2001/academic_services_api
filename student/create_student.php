<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->Academic_ID) || !isset($data->password) || !isset($data->full_name) 
    || !isset($data->major_id) || !isset($data->level_id) || !isset($data->term_id)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$academic_id = $data->Academic_ID;
$password = $data->password; 
$full_name = $data->full_name;
$major_id = $data->major_id;
$level_id = $data->level_id;
$term_id = $data->term_id;
$status = isset($data->status) ? $data->status : 0; // افتراضي 0

$query = "INSERT INTO student (Academic_ID, password, full_name, major_id, level_id, term_id, status)
          VALUES ('$academic_id', '$password', '$full_name', '$major_id', '$level_id', '$term_id', '$status')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Student created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
