<?php
include '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$title = $data['title'];
$content = $data['content'];
$date = $data['date'];
$target_is_student = $data['target_is_student'];
$department_id = !empty($data['department_id']) ? $data['department_id'] : null;
$level_id = !empty($data['level_id']) ? $data['level_id'] : null;

$query = "UPDATE notification 
          SET title=?, content=?, date=?, target_is_student=?, department_id=?, level_id=?
          WHERE id=?";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssiiii", $title, $content, $date, $target_is_student, $department_id, $level_id, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>