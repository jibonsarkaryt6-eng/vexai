<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid = $_SESSION['user_id'] ?? 1;
$total = 0;
if($conn){
  try{
    $res = $conn->query("SELECT COUNT(*) as c FROM videos WHERE user_id=$uid");
    if($res){ $row = $res->fetch_assoc(); $total = $row['c'] ?? 0; }
  } catch(Exception $e){ $total = 0; }
}
$email = $_SESSION['email'] ?? $_SESSION['user_email'] ?? 'User';
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Dashboard - VEXAI</title>
<style>body{margin:0;background:#020617;color:#fff;font-family:sans-serif}.navbar{display:flex;justify-content:space-between;padding:20px 30px;background:rgba(255,255,255,0.05)}.card{max-width:800px;margin:30px auto;background:rgba(255,255,255,0.06);padding:25px;border-radius:20px;border:1px solid rgba(255,255,255,0.1)}.stat{display:inline-block;background:linear-gradient(90deg,#8b5cf6,#ec4899);padding:20px 30px;border-radius:15px;margin:10px}a.btn{padding:12px 20px;background:#1e293b;border-radius:10px;color:#fff;text-decoration:none;margin:5px;display:inline-block}</style>
</head><body>
<div class="navbar"><b>VEXAI ✨</b><span><?php echo htmlspecialchars($email); ?> | <a href="logout.php" style="color:#f87171;text-decoration:none">Logout</a></span></div>
<div class="card">
<h2>Dashboard</h2>
<div class="stat"><h3><?php echo $total; ?></h3><p>Total Videos</p></div>
<div class="stat" style="background:linear-gradient(90deg,#06b6d4,#3b82f6)"><h3>Premium</h3><p>Plan Active</p></div>
<br><br>
<a class="btn" href="generate.php">+ New Video</a>
<a class="btn" href="history.php">My History</a>
</div>
</body></html>
