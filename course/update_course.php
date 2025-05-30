<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$name = $data['name'];
$major_id = $data['major_id'];
$level_id = $data['level_id'];
$term_id = $data['term_id'];

$stmt = $conn->prepare("UPDATE course SET name = ?, major_id = ?, level_id = ?, term_id = ? WHERE id = ?");
$stmt->bind_param("siiii", $name, $major_id, $level_id, $term_id, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم تحديث المادة بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في التحديث", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
