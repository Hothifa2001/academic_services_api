<?php
header('Content-Type: application/json');
include '../config.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(["status" => "error", "message" => "Missing student ID"]);
    exit;
}

$id = $data->id;
$academic_id = $data->Academic_ID ?? null;
$password = $data->password ?? null;
$full_name = $data->full_name ?? null;
$major_id = $data->major_id ?? null;
$level_id = $data->level_id ?? null;
$term_id = $data->term_id ?? null;
$status = $data->status ?? null;

$fields = [];
if ($academic_id !== null) $fields[] = "Academic_ID = '$academic_id'";
if ($password !== null) $fields[] = "password = '$password'";
if ($full_name !== null) $fields[] = "full_name = '$full_name'";
if ($major_id !== null) $fields[] = "major_id = '$major_id'";
if ($level_id !== null) $fields[] = "level_id = '$level_id'";
if ($term_id !== null) $fields[] = "term_id = '$term_id'";
if ($status !== null) $fields[] = "status = '$status'";

if (empty($fields)) {
    echo json_encode(["status" => "error", "message" => "No fields to update"]);
    exit;
}

$query = "UPDATE student SET " . implode(', ', $fields) . " WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success", "message" => "Student updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
