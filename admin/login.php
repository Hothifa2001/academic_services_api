<?php
header("Content-Type: application/json; charset=UTF-8");
require_once("../config.php"); // تأكد من المسار الصحيح لملف الاتصال

// استقبال بيانات JSON
$data = json_decode(file_get_contents("php://input"), true);
$username = $conn->real_escape_string($data["username"]);
$password = $conn->real_escape_string($data["password"]);

// ✅ تحقق من جدول الادمن
$sql_admin = "SELECT id, username, password, full_name, status 
              FROM admin 
              WHERE username = '$username' AND password = '$password' 
              LIMIT 1";
$result_admin = $conn->query($sql_admin);

if ($result_admin && $result_admin->num_rows > 0) {
    $admin = $result_admin->fetch_assoc();

    if ((int)$admin['status'] !== 1) {
        echo json_encode([
            "status" => "error",
            "message" => "الحساب غير مفعل من قبل الإدارة"
        ]);
        exit();
    }

    echo json_encode([
        "status" => "success",
        "role" => "admin",
        "data" => [
            "id" => (int)$admin['id'],
            "username" => $admin['username'],
            "password" => $admin['password'],
            "full_name" => $admin['full_name'],
            "status" => (int)$admin['status']
        ]
    ]);
    exit();
}

// ✅ تحقق من جدول المشرف
$sql_sup = "SELECT s.id, s.username, s.password, s.full_name, s.status, 
                   s.department_id, d.name AS department_name
            FROM supervisor s
            JOIN department d ON s.department_id = d.id
            WHERE s.username = '$username' AND s.password = '$password'
            LIMIT 1";
$result_sup = $conn->query($sql_sup);

if ($result_sup && $result_sup->num_rows > 0) {
    $sup = $result_sup->fetch_assoc();

    if ((int)$sup['status'] !== 1) {
        echo json_encode([
            "status" => "error",
            "message" => "الحساب غير مفعل من قبل الإدارة"
        ]);
        exit();
    }

    echo json_encode([
        "status" => "success",
        "role" => "supervisor",
        "data" => [
            "id" => (int)$sup['id'],
            "username" => $sup['username'],
            "password" => $sup['password'],
            "full_name" => $sup['full_name'],
            "status" => (int)$sup['status'],
            "department_id" => (int)$sup['department_id'],
            "department_name" => $sup['department_name']
        ]
    ]);
    exit();
}

// ❌ إذا لم يتم العثور على المستخدم
echo json_encode([
    "status" => "error",
    "message" => "بيانات الدخول غير صحيحة"
]);
exit();
?>
