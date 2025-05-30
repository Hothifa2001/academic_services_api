<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$major_id = $data['major_id'];
$level_id = $data['level_id'];
$term_id = $data['term_id'];

$stmt = $conn->prepare("INSERT INTO course (name, major_id, level_id, term_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siii", $name, $major_id, $level_id, $term_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم إنشاء المادة بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في الإنشاء", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
