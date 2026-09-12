<?php
include '../config.php';
// Simple Admin Auth - তোমার Email দিয়ে Admin
$admin_email = "admin@gmail.com"; // এখানে তোমার Email দাও
if(!isset($_SESSION['email']) || $_SESSION['email'] != $admin_email){
    // যদি সাধারণ ইউজার Admin এ ঢুকতে চায়
    if(!isset($_SESSION['user_id'])){ header("Location: ../login.php"); exit; }
    // চাইলে এই চেক বন্ধ করে দিতে পারো
}
$users_count = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$videos_count = $conn->query("SELECT COUNT(*) as c FROM videos")->fetch_assoc()['c'];
$all_videos = $conn->query("SELECT videos.*, users.email FROM videos JOIN users ON videos.user_id=users.id ORDER BY videos.id DESC");
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin - VEXAI</title>
<style>body{margin:0;background:#020617;color:#fff;font-family:sans-serif} .nav{padding:20px;background:#1e293b;display:flex;justify-content:space-between} .grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:1000px;margin:20px auto;padding:20px} .box{background:rgba(255,255,255,0.06);padding:20px;border-radius:15px;text-align:center} table{width:100%;border-collapse:collapse;margin-top:20px} th,td{padding:10px;border-bottom:1px solid #334155;text-align:left} img,video{width:100px;border-radius:8px}</style>
</head><body>
<div class="nav"><b>VEXAI Admin Panel</b><a href="../index.php" style="color:#fff;text-decoration:none">Go to Site</a></div>
<div class="grid">
<div class="box"><h2><?=$users_count?></h2><p>Total Users</p></div>
<div class="box"><h2><?=$videos_count?></h2><p>Total Videos</p></div>
</div>
<div style="max-width:1000px;margin:auto;padding:20px">
<h3>All Generations</h3>
<table>
<tr><th>User</th><th>Image</th><th>Video</th><th>Prompt</th><th>Action</th></tr>
<?php while($row=$all_videos->fetch_assoc()){ ?>
<tr>
<td><?=$row['email']?></td>
<td><img src="../<?=$row['image_path']?>"></td>
<td><video src="../<?=$row['video_path']?>" muted></video></td>
<td><?=$row['prompt']?></td>
<td><a href="?delete=<?=$row['id']?>" style="color:#f87171">Delete</a></td>
</tr>
<?php } ?>
</table>
<?php
if(isset($_GET['delete'])){
    $id=intval($_GET['delete']);
    $conn->query("DELETE FROM videos WHERE id=$id");
    echo "<script>location.href='index.php'</script>";
}
?>
</div>
    </body></html>