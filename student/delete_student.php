<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(["status" => "error", "message" => "Missing student ID"]);
    exit;
}

$id = $data->id;

$query = "DELETE FROM student WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Student deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
