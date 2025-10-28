<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: admin_login.php'); exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><title>3D Referral Network</title>
  <link rel="stylesheet" href="../frontend/assets/css/style.css">
  <style>
    body,html{height:100%;margin:0}
    #wrap{display:grid;grid-template-columns:1fr 360px;grid-gap:12px;height:100vh;padding:12px}
    #3d-graph{border-radius:10px;background:#071026}
    .panel{padding:12px;background:#072033;border-radius:8px;color:#e6eef6}
    .btn{padding:8px 10px;border-radius:6px;border:none;background:#0b78b8;color:#fff;cursor:pointer}
  </style>
</head>
<body>
  <div id="wrap">
    <div id="3d-graph"></div>
    <div class="panel">
      <h3>Overview</h3>
      <div id="kTotal">Nodes: —</div>
      <div id="kLinks">Links: —</div>
      <hr>
      <div id="nodeDetail"><em>Select a node</em></div>
      <div style="margin-top:12px">
        <button class="btn" id="exportBtn">Export PNG→PDF</button>
        <button class="btn" onclick="location.href='admin_dashboard.php'">Back</button>
      </div>
    </div>
  </div>

<script src="https://unpkg.com/three@0.160.0/build/three.min.js"></script>
<script src="https://unpkg.com/three-spritetext"></script>
<script src="https://unpkg.com/3d-force-graph"></script>
<script>
// minimal 3D graph loader - fetches admin/referral_data.php
const container = document.getElementById('3d-graph');
container.style.width = '100%'; container.style.height = '100%';
let Graph;
fetch('referral_data.php', {credentials:'include'}).then(r=>r.json()).then(data=>{
  document.getElementById('kTotal').innerText = 'Nodes: ' + data.nodes.length;
  document.getElementById('kLinks').innerText = 'Links: ' + data.links.length;
  Graph = ForceGraph3D()(container)
    .graphData(data)
    .nodeLabel(n => `${n.name}\n${n.email}\n₹${(n.earnings||0).toFixed(2)}`)
    .nodeAutoColorBy('group')
    .linkDirectionalParticles(1)
    .onNodeClick(node => {
      document.getElementById('nodeDetail').innerHTML = '<b>' + node.name + '</b><br/>' + node.email + '<br/>Earnings: ₹' + (node.earnings||0);
    });
  Graph.cameraPosition({z:600});
}).catch(err=>{ console.error(err); document.getElementById('nodeDetail').innerText = 'Error loading'; });

// Export snapshot (simple)
document.getElementById('exportBtn').addEventListener('click', async ()=>{
  if(!Graph) return alert('Not ready');
  const dataUrl = Graph.toImage(1600,900,'image/png',2.0);
  // POST to export endpoint
  const res = await fetch('export_png_to_pdf.php', {
    method:'POST', credentials:'include',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({png_base64: dataUrl, filename: 'referral_network'})
  });
  if (res.ok) {
    // try download blob
    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = 'referral_network.pdf'; a.click();
  } else {
    const j = await res.json().catch(()=>null);
    alert('Export failed: ' + (j?.error || 'server error'));
  }
});
</script>
</body>
</html>