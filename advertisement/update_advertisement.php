<?php
header('Content-Type: application/json');
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(["status" => "error", "message" => "Only POST allowed"]);
    exit;
}

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? null;
$content = $_POST['content'] ?? null;
$imageBase64 = $_POST['image_base64'] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Missing ad ID"]);
    exit;
}

$updateFields = [];
if ($title) $updateFields[] = "title = '$title'";
if ($content) $updateFields[] = "content = '$content'";
if ($imageBase64) $updateFields[] = "image_path = '$imageBase64'";

if (empty($updateFields)) {
    echo json_encode(["status" => "error", "message" => "No fields to update"]);
    exit;
}

$updateQuery = "UPDATE advertisement SET " . implode(', ', $updateFields) . " WHERE id = $id";

if (mysqli_query($conn, $updateQuery)) {
    echo json_encode(["status" => "success", "message" => "Ad updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>
