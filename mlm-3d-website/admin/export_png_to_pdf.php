<?php
// admin/export_png_to_pdf.php
session_start();
if (!isset($_SESSION['admin'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['png_base64'])) { http_response_code(400); echo json_encode(['error'=>'Missing png_base64']); exit; }
$data = $input['png_base64'];
$filename = preg_replace('/[^a-zA-Z0-9_\-]/','', ($input['filename'] ?? 'export')) ?: 'export';
$png = base64_decode(preg_replace('#^data:image/\w+;base64,#i','',$data));
if ($png === false) { http_response_code(400); echo json_encode(['error'=>'Invalid base64']); exit; }
$tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename . '_' . time() . '.png';
file_put_contents($tmp, $png);
if (class_exists('Imagick')) {
  try {
    $im = new Imagick();
    $im->readImage($tmp);
    $im->setImageFormat('pdf');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    echo $im->getImagesBlob();
    unlink($tmp);
    exit;
  } catch (Exception $e) {
    unlink($tmp);
    http_response_code(500); echo json_encode(['error'=>'Imagick failed','detail'=>$e->getMessage()]); exit;
  }
} else {
  header('Content-Type: image/png');
  header('Content-Disposition: attachment; filename="' . $filename . '.png"');
  readfile($tmp);
  unlink($tmp);
  exit;
}
?>