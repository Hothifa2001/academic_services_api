<?php
// delete.php
header('Content-Type: application/json; charset=utf-8');

// 1) قراءة JSON من الطلب
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['departmentName']) || !isset($input['itemName'])) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'departmentName and itemName are required'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2) تعقيم المدخلات
$dept     = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u','',$input['departmentName']);
$item     = preg_replace('/[^A-Za-z0-9_\-ا-ي \.\-]/u','',$input['itemName']);
$path     = '';
if (!empty($input['path'])) {
    $raw   = str_replace(['..\\','../'], '', $input['path']);
    $clean = preg_replace('/[^A-Za-z0-9_\-ا-ي\/ ]/u','',$raw);
    $path  = trim($clean, "/\\");
}

// 3) بناء المسار الكامل
$root      = realpath(dirname(__DIR__,2).'/library');
$baseDept  = $root . DIRECTORY_SEPARATOR . $dept;
$targetDir = $path === ''
    ? $baseDept
    : $baseDept . DIRECTORY_SEPARATOR . $path;
$target    = $targetDir . DIRECTORY_SEPARATOR . $item;

if (!file_exists($target)) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Item not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 4) دالة لحذف مجلد مع جميع محتوياته
function rrmdir($dir) {
    $items = array_diff(scandir($dir), ['.','..']);
    foreach ($items as $entry) {
        $full = $dir . DIRECTORY_SEPARATOR . $entry;
        is_dir($full) ? rrmdir($full) : unlink($full);
    }
    return rmdir($dir);
}

// 5) نفذ الحذف
if (is_dir($target)) {
    $ok = rrmdir($target);
} else {
    $ok = unlink($target);
}

if ($ok) {
    echo json_encode(['status'=>'success','message'=>'Deleted successfully'], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Delete failed'], JSON_UNESCAPED_UNICODE);
}
