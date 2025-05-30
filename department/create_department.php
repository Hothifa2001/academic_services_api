<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];

$stmt = $conn->prepare("INSERT INTO department (name) VALUES (?)");
$stmt->bind_param("s", $name);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم إنشاء القسم بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في إنشاء القسم", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
