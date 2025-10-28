<?php
// admin/adjust_earnings.php (expects JSON POST)
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
include '../backend/db.php';
include 'csrf.php';
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$token = $input['csrf_token'] ?? '';
if (!validate_csrf($token)) { http_response_code(403); echo json_encode(['error'=>'Invalid CSRF']); exit; }
$user_id = intval($input['user_id'] ?? 0);
$amount = floatval($input['amount'] ?? 0);
$note = substr($input['note'] ?? '', 0, 255);
if ($user_id <= 0 || $amount == 0.0) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$conn->begin_transaction();
try {
  $stmt = $conn->prepare('UPDATE users SET earnings = earnings + ? WHERE id = ?');
  $stmt->bind_param('di', $amount, $user_id);
  $stmt->execute();
  $admin_id = intval($_SESSION['admin']);
  $stmt2 = $conn->prepare('INSERT INTO earnings_adjustments (admin_id,user_id,amount,note) VALUES (?,?,?,?)');
  $stmt2->bind_param('iids', $admin_id, $user_id, $amount, $note);
  $stmt2->execute();
  $conn->commit();
  echo json_encode(['ok'=>true]);
} catch (Exception $e) {
  $conn->rollback();
  http_response_code(500); echo json_encode(['error'=>'DB error','detail'=>$e->getMessage()]);
}
?>