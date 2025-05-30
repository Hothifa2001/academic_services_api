<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$name = $data['name'];

$stmt = $conn->prepare("UPDATE term SET name = ? WHERE id = ?");
$stmt->bind_param("si", $name, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم تحديث الترم بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في التحديث", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
