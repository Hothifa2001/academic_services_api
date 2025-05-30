<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$role = $_GET['role'] ?? '';
$id = $_GET['id'] ?? null;

if (!$role || !$id) {
  echo json_encode(['status' => 'error', 'message' => 'بيانات ناقصة']);
  exit;
}

$table = $role === 'admin' ? 'admin' : 'supervisor';

$stmt = $conn->prepare("SELECT username, password FROM $table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode(['status' => 'success', 'data' => $row], JSON_UNESCAPED_UNICODE);
} else {
  echo json_encode(['status' => 'error', 'message' => 'المستخدم غير موجود']);
}
?>
