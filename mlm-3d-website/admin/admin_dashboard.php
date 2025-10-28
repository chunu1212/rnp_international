<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: admin_login.php'); exit; }
include '../backend/db.php';
$users = $conn->query('SELECT * FROM users ORDER BY id DESC');
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Dashboard</title><link rel="stylesheet" href="../frontend/assets/css/style.css"></head>
<body>
<div class="container">
  <h2>Admin Dashboard</h2>
  <p><a href="referral_3d.php">View 3D Referral Network</a> • <a href="logout.php">Logout</a></p>
  <table border="1" cellpadding="8" cellspacing="0">
  <tr><th>ID</th><th>Name</th><th>Email</th><th>Ref Code</th><th>Parent</th><th>Earnings</th></tr>
  <?php while($u = $users->fetch_assoc()): ?>
  <tr>
    <td><?php echo $u['id']?></td>
    <td><?php echo htmlspecialchars($u['name'])?></td>
    <td><?php echo htmlspecialchars($u['email'])?></td>
    <td><?php echo htmlspecialchars($u['ref_code'])?></td>
    <td><?php echo htmlspecialchars($u['referred_by'])?></td>
    <td><?php echo $u['earnings']?></td>
  </tr>
  <?php endwhile; ?>
  </table>
</div>
</body></html>