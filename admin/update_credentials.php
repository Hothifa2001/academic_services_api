<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

// ✅ دعم GET: لجلب بيانات المستخدم
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $id = $_GET['id'] ?? null;
  $role = $_GET['role'] ?? '';

  if (!$id || !$role) {
    echo json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة']);
    exit;
  }

  $table = $role === 'admin' ? 'admin' : 'supervisor';
  $query = "SELECT username, password FROM $table WHERE id = $id";

  $result = $conn->query($query);
  if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'username' => $row['username'], 'password' => $row['password']]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'لم يتم العثور على المستخدم']);
  }

  exit;
}

// ✅ دعم POST: لتحديث اسم المستخدم وكلمة المرور
$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$username = $data['username'] ?? null;
$password = $data['password'] ?? null;
$role = $data['role'] ?? '';

if (!$id || !$username || !$password || !$role) {
  echo json_encode(['status' => 'error', 'message' => 'البيانات غير مكتملة']);
  exit;
}

$table = $role === 'admin' ? 'admin' : 'supervisor';
$stmt = $conn->prepare("UPDATE $table SET username = ?, password = ? WHERE id = ?");
$stmt->bind_param("ssi", $username, $password, $id);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'فشل التحديث']);
}
?>
