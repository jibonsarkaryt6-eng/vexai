<?php include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM videos WHERE user_id=$uid ORDER BY id DESC");
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>History - VEXAI</title>
<style>
body{margin:0;background:#020617;color:#fff;font-family:sans-serif}
.navbar{display:flex;justify-content:space-between;padding:20px 30px;background:rgba(255,255,255,0.05)}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;max-width:1000px;margin:20px auto;padding:20px}
.item{background:rgba(255,255,255,0.06);padding:15px;border-radius:15px;border:1px solid rgba(255,255,255,0.1)}
video,img{width:100%;border-radius:10px}
a{color:#a78bfa;text-decoration:none}
</style>
</head><body>
<div class="navbar"><b><a href="index.php" style="color:#fff">VEXAI ✨</a></b><a href="dashboard.php" style="color:#fff">Dashboard</a></div>
<h2 style="text-align:center">My Generation History</h2>
<div class="grid">
<?php while($row = $result->fetch_assoc()){ ?>
<div class="item">
<img src="<?=$row['image_path']?>">
<video src="<?=$row['video_path']?>" controls muted loop></video>
<p style="font-size:13px;opacity:0.8"><?=$row['prompt']?></p>
<small style="opacity:0.5"><?=$row['created_at']?></small>
<br><a href="result.php?id=<?=$row['id']?>">View Full →</a>
</div>
<?php } ?>
</div>
    </body></html>