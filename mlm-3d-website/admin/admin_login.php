<?php
// admin/admin_login.php
session_start();
include '../backend/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $res = $conn->query("SELECT * FROM admin WHERE username='" . $conn->real_escape_string($username) . "' LIMIT 1");
  if ($res && $res->num_rows) {
    $a = $res->fetch_assoc();
    if (hash('sha256', $password) === $a['password']) {
      $_SESSION['admin'] = $a['id'];
      header('Location: admin_dashboard.php');
      exit;
    } else { $err = 'Invalid'; }
  } else { $err = 'Not found'; }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Login</title><link rel="stylesheet" href="../frontend/assets/css/style.css"></head>
<body><div class="container"><h2>Admin Login</h2>
<?php if(!empty($err)) echo '<div style="color:red">' . htmlspecialchars($err) . '</div>'; ?>
<form method="POST">
  <input name="username" placeholder="Username" required><br>
  <input name="password" type="password" placeholder="Password" required><br>
  <button>Login</button>
</form>
</div></body></html>