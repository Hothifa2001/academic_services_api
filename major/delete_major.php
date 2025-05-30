<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));
$id = $data->id ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Missing major ID"]);
    exit;
}

$query = "DELETE FROM major WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Major deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
