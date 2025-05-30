<?php
// search_library.php
header('Content-Type: application/json; charset=utf-8');

// 1) قراءة الطلب
$input = json_decode(file_get_contents('php://input'), true);
if (empty($input['departmentName']) || empty($input['query'])) {
    http_response_code(400);
    echo json_encode([
        'status'=>'error',
        'message'=>'departmentName and query are required'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
$deptName = preg_replace('/[^A-Za-z0-9_\-ا-ي ]/u','',$input['departmentName']);
$query    = mb_strtolower($input['query'],'UTF-8');

// 2) تحديد مجلّد القسم
$root = realpath(dirname(__DIR__,2).'/library');
$baseDept = $root . DIRECTORY_SEPARATOR . $deptName;
if (!is_dir($baseDept)) {
    http_response_code(404);
    echo json_encode(['status'=>'error','message'=>'Department not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 3) مسح شجري لإيجاد كل الملفات/المجلدات المطابقة
$results = [];
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDept, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($it as $fileinfo) {
    $name = $fileinfo->getFilename();
    if (mb_stripos(mb_strtolower($name,'UTF-8'), $query, 0, 'UTF-8') !== false) {
        // مسار نسبي من داخل مجلد القسم
        $rel = str_replace($baseDept . DIRECTORY_SEPARATOR, '', $fileinfo->getPathname());
        $results[] = [
            'name'         => $name,
            'relativePath' => str_replace(DIRECTORY_SEPARATOR, '/', $rel),
            'type'         => $fileinfo->isDir() ? 'dir' : 'file',
            'size'         => $fileinfo->isFile() ? $fileinfo->getSize() : null,
        ];
    }
}

// 4) إعادة النتيجة
echo json_encode(['status'=>'success','data'=>$results], JSON_UNESCAPED_UNICODE);
