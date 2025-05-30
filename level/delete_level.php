<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM level WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم حذف المستوى بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في الحذف", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
