<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$password = $data['password'];
$full_name = $data['full_name'];

$stmt = $conn->prepare("INSERT INTO admin (username, password, full_name, status) VALUES (?, ?, ?, 0)");
$stmt->bind_param("sss", $username, $password, $full_name);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم إنشاء المدير بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في إنشاء المدير", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
