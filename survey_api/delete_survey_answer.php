<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? null;
if (!$id) {
  echo json_encode(['status' => 'error', 'message' => 'المعرف غير موجود']);
  exit;
}

$stmt = $conn->prepare("DELETE FROM survey_answer WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل في الحذف']);
}
?>
