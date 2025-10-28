<?php
// backend/referral_data.php - generic data endpoint (admin/session optional)
include 'db.php';
header('Content-Type: application/json');

$res = $conn->query("SELECT id,name,email,parent_id,earnings,ref_code,avatar_url FROM users");
$nodes = []; $links = [];
while ($r = $res->fetch_assoc()) {
  $nodes[] = [
    'id' => intval($r['id']),
    'name' => $r['name'],
    'email' => $r['email'],
    'earnings' => floatval($r['earnings']),
    'ref_code' => $r['ref_code'],
    'avatar_url' => $r['avatar_url']
  ];
  if ($r['parent_id']) $links[] = ['source' => intval($r['parent_id']), 'target' => intval($r['id'])];
}
echo json_encode(['nodes' => $nodes, 'links' => $links], JSON_UNESCAPED_UNICODE);
?>