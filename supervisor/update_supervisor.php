<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(["status" => "error", "message" => "Missing supervisor ID"]);
    exit;
}

$id = $data->id;
$username = $data->username ?? null;
$password = $data->password ?? null;
$full_name = $data->full_name ?? null;
$department_id = $data->department_id ?? null;
$status = $data->status ?? null;

$fields = [];
if ($username !== null) $fields[] = "username = '$username'";
if ($password !== null) $fields[] = "password = '$password'";
if ($full_name !== null) $fields[] = "full_name = '$full_name'";
if ($department_id !== null) $fields[] = "department_id = '$department_id'";
if ($status !== null) $fields[] = "status = '$status'";

if (empty($fields)) {
    echo json_encode(["status" => "error", "message" => "No fields to update"]);
    exit;
}

$query = "UPDATE supervisor SET " . implode(', ', $fields) . " WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Supervisor updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
