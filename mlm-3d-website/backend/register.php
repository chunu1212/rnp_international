<?php
// backend/register.php
include 'db.php';

$name = $conn->real_escape_string($_POST['name'] ?? '');
$email = $conn->real_escape_string($_POST['email'] ?? '');
$pwd = $_POST['password'] ?? '';
$referred_by = $conn->real_escape_string($_POST['referred_by'] ?? '');

if (!$name || !$email || !$pwd) {
  die('Missing fields');
}

$hash = password_hash($pwd, PASSWORD_BCRYPT);
$ref_code = substr(md5($email.time()), 0, 8);

$parent_id = NULL;
if ($referred_by) {
  $r = $conn->query("SELECT id FROM users WHERE ref_code='{$referred_by}' LIMIT 1");
  if ($r && $r->num_rows) { $parent_id = intval($r->fetch_assoc()['id']); }
}

$stmt = $conn->prepare("INSERT INTO users (name,email,password,ref_code,referred_by,parent_id) VALUES (?,?,?,?,?,?)");
$stmt->bind_param('sssssi', $name, $email, $hash, $ref_code, $referred_by, $parent_id);
if ($stmt->execute()) {
  // distribute simple commission
  if ($parent_id) {
    $conn->query("UPDATE users SET earnings = earnings + 50, level1_income = level1_income + 50 WHERE id = $parent_id");
    $res = $conn->query("SELECT parent_id FROM users WHERE id=$parent_id");
    $lvl2 = $res->fetch_assoc()['parent_id'] ?? null;
    if ($lvl2) { $conn->query("UPDATE users SET earnings = earnings + 30, level2_income = level2_income + 30 WHERE id = $lvl2"); }
    if ($lvl2) {
      $res3 = $conn->query("SELECT parent_id FROM users WHERE id=$lvl2");
      $lvl3 = $res3->fetch_assoc()['parent_id'] ?? null;
      if ($lvl3) { $conn->query("UPDATE users SET earnings = earnings + 20, level3_income = level3_income + 20 WHERE id = $lvl3"); }
    }
  }
  echo 'OK';
} else {
  echo 'Error: ' . $conn->error;
}
?>