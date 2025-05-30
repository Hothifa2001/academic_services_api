<?php
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$date = $data['date'];
$title = $data['title'];
$content = $data['content'];
$sender_id = $data['sender_id'];
$sender_is_admin = $data['sender_is_admin'];
$target_is_student = $data['target_is_student'];
$department_id = !empty($data['department_id']) ? $data['department_id'] : null;
$level_id = !empty($data['level_id']) ? $data['level_id'] : null;

$query = "INSERT INTO notification (date, title, content, sender_id, sender_is_admin, target_is_student, department_id, level_id)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssiiiii", $date, $title, $content, $sender_id, $sender_is_admin, $target_is_student, $department_id, $level_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>
