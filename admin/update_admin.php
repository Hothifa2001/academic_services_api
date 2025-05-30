<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$username = $data['username'];
$password = $data['password'];
$full_name = $data['full_name'];
$status = $data['status'];

$stmt = $conn->prepare("UPDATE admin SET username = ?, password = ?, full_name = ?, status = ? WHERE id = ?");
$stmt->bind_param("sssii", $username, $password, $full_name, $status, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "تم تحديث المدير بنجاح"]);
} else {
    echo json_encode(["status" => "error", "message" => "فشل في التحديث", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
