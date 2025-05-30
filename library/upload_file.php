<?php
// C:\xampp\htdocs\academic_services_api\library\upload_file.php

// =======================
// تهيئة إعدادات PHP للسماح بتحميل الملفات الكبيرة
// =======================
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');

header('Content-Type: application/json; charset=utf-8');

// 1) تأكد من أنّ الطلب POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Method not allowed, use POST'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) تحقق من وجود departmentName
if (empty($_POST['departmentName'])) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'departmentName is required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$deptName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u', '', $_POST['departmentName']);

// 3) قراءة المسار الفرعي إذا وُجد
$path = '';
if (!empty($_POST['path'])) {
    $raw   = str_replace(['..\\', '../'], '', $_POST['path']);
    $clean = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u', '', $raw);
    $path  = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($clean, "/\\"));
}

// 4) دليل المكتبة الجذري
$root = realpath(dirname(__DIR__, 2) . '/library');
if ($root === false) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Library root not found'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5) دليل القسم
$baseDept = $root . DIRECTORY_SEPARATOR . $deptName;
if (!is_dir($baseDept) && !mkdir($baseDept, 0777, true)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create department folder'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 6) دليل الهدف لتحميل الملفات
$targetDir = $path === ''
    ? $baseDept
    : $baseDept . DIRECTORY_SEPARATOR . $path;
if (!is_dir($targetDir)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Target folder does not exist'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 7) تحقق من وجود ملفات مرفوعة
if (empty($_FILES) || !isset($_FILES['files'])) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'No files uploaded'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 8) معالجة المصفوفة 'files' سواء مفردة أو متعددة
$uploaded = [];
$files = $_FILES['files'];
$count = is_array($files['name']) ? count($files['name']) : 1;

for ($i = 0; $i < $count; $i++) {
    $error    = is_array($files['error'])    ? $files['error'][$i]    : $files['error'];
    $tmpName  = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
    $origName = is_array($files['name'])     ? $files['name'][$i]     : $files['name'];

    if ($error !== UPLOAD_ERR_OK) {
        continue;
    }

    // تعقيم اسم الملف
    $fileName = preg_replace('/[^A-Za-z0-9_\-ا-ي\.\ ]/u', '', $origName);
    $destPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

    // فصل معالجة الملفات الكبيرة باستخدام دفق (stream)
    if (!move_uploaded_file($tmpName, $destPath)) {
        continue;
    }

    // إذا أرشيف ZIP: فك الضغط فوراً
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($ext === 'zip' && class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($destPath) === true) {
            $zip->extractTo($targetDir);
            $zip->close();
            unlink($destPath);
        }
    }

    $uploaded[] = $fileName;
}

// 9) بناء الاستجابة
if (empty($uploaded)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to upload any files'
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status'   => 'success',
        'uploaded' => $uploaded
    ], JSON_UNESCAPED_UNICODE);
}
