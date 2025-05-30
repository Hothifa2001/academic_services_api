<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(["status" => "error", "message" => "Missing major ID"]);
    exit;
}

$id = $data->id;
$name = $data->name ?? null;
$department_id = $data->department_id ?? null;

$fields = [];
if ($name !== null) $fields[] = "name = '$name'";
if ($department_id !== null) $fields[] = "department_id = '$department_id'";

if (empty($fields)) {
    echo json_encode(["status" => "error", "message" => "No fields to update"]);
    exit;
}

$query = "UPDATE major SET " . implode(', ', $fields) . " WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Major updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
