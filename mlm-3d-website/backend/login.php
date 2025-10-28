<?php
// backend/login.php
session_start();
include 'db.php';

$email = $conn->real_escape_string($_POST['email'] ?? '');
$pwd = $_POST['password'] ?? '';
if (!$email || !$pwd) { die('Missing'); }

$res = $conn->query("SELECT id,password,name,email,ref_code FROM users WHERE email='$email' LIMIT 1");
if (!$res || $res->num_rows === 0) { die('Invalid'); }
$user = $res->fetch_assoc();
if (password_verify($pwd, $user['password'])) {
  $_SESSION['user_id'] = $user['id'];
  echo 'OK';
} else { echo 'Invalid'; }
?>