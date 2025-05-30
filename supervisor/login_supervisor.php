<?php
include '../config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $username = $data['username'];
    $password = $data['password'];

    // ربط جدول القسم لعرض اسمه
    $stmt = $conn->prepare("
        SELECT 
            supervisor.id, 
            supervisor.username, 
            supervisor.password,
            supervisor.full_name, 
            supervisor.status,
            supervisor.department_id,
            department.name AS department_name
        FROM supervisor
        JOIN department ON supervisor.department_id = department.id
        WHERE supervisor.username = ?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === $row['password']) {
            if ((int)$row['status'] === 1) {
                echo json_encode([
                    "status" => "success",
                    "message" => "تم تسجيل الدخول بنجاح",
                    "data" => [
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "full_name" => $row['full_name'],
                        "department_id" => $row['department_id'],
                        "department_name" => $row['department_name']
                    ]
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "الحساب غير نشط"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "كلمة المرور غير صحيحة"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "اسم المستخدم غير موجود"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "الطلب غير صالح"]);
}
