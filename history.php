<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$uid = $_SESSION['user_id'] ?? 1;
$videos = [];

if($conn){
  try{
    $result = $conn->query("SELECT * FROM videos WHERE user_id=$uid ORDER BY id DESC");
    if($result){
      while($row = $result->fetch_assoc()){ $videos[] = $row; }
    }
  } catch(Exception $e){}
}

// DB না থাকলে Session থেকে একটা Item বানাও যাতে Page খালি না লাগে
if(empty($videos) && isset($_SESSION['last_image'])){
  $videos[] = [
    'id' => 0,
    'image_path' => $_SESSION['last_image'],
    'video_path' => $_SESSION['last_video'],
    'prompt' => $_SESSION['last_prompt'] ?? 'No prompt',
    'created_at' => date('Y-m-d H:i:s')
  ];
}
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>History - VEXAI</title>
<style>body{margin:0;background:#020617;color:#fff;font-family:sans-serif}.navbar{display:flex;justify-content:space-between;padding:20px 30px;background:rgba(255,255,255,0.05)}.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;max-width:1000px;margin:20px auto;padding:20px}.item{background:rgba(255,255,255,0.06);padding:15px;border-radius:15px;border:1px solid rgba(255,255,255,0.1)}video,img{width:100%;border-radius:10px}a{color:#a78bfa;text-decoration:none}</style>
</head><body>
<div class="navbar"><b><a href="index.php" style="color:#fff">VEXAI ✨</a></b><a href="index.php" style="color:#fff">Dashboard</a></div>
<h2 style="text-align:center">My Generation History</h2>
<div class="grid">
<?php if(empty($videos)): ?>
<p style="text-align:center;opacity:0.6">No videos yet. Go to <a href="generate.php">Generate</a></p>
<?php else: ?>
<?php foreach($videos as $row){ ?>
<div class="item">
<img src="<?php echo htmlspecialchars($row['image_path']); ?>">
<?php if(!empty($row['video_path'])){ ?><video src="<?php echo htmlspecialchars($row['video_path']); ?>" controls muted loop></video><?php } ?>
<p style="font-size:13px;opacity:0.8"><?php echo htmlspecialchars($row['prompt']); ?></p>
<small style="opacity:0.5"><?php echo htmlspecialchars($row['created_at'] ?? ''); ?></small>
<br><?php if($row['id']!=0){ ?><a href="result.php?id=<?php echo $row['id']; ?>">View Full →</a><?php } else { ?><a href="result.php?temp=1">View Full →</a><?php } ?>
</div>
<?php } ?>
<?php endif; ?>
</div>
</body></html>
