<?php
session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
function validate_csrf($token) {
  if (empty($_SESSION['csrf_token'])) return false;
  return hash_equals($_SESSION['csrf_token'], $token);
}
if (isset($_GET['fetch'])) {
  header('Content-Type: application/json');
  echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
}
?>