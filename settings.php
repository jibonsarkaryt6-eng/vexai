<?php include '../config.php';
$admin_email = "admin@gmail.com";
if(!isset($_SESSION['email'])){ header("Location: ../login.php"); exit; }

if(isset($_POST['save'])){
    $site_name = $_POST['site_name'];
    $api_key = $_POST['api_key'];
    // এই Settings টা file এ save হবে
    file_put_contents("settings.json", json_encode(['site_name'=>$site_name,'api_key'=>$api_key]));
    $msg = "Settings Saved!";
}
$settings = file_exists("settings.json") ? json_decode(file_get_contents("settings.json"), true) : ['site_name'=>'VEXAI','api_key'=>''];
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Settings</title>
<style>body{background:#020617;color:#fff;font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh} .card{background:#1e293b;padding:30px;border-radius:20px;width:90%;max-width:400px} input{width:90%;padding:12px;margin:10px 0;border-radius:10px;border:none;background:#0f172a;color:#fff} .btn{width:95%;padding:12px;background:linear-gradient(90deg,#8b5cf6,#ec4899);border:none;border-radius:10px;color:#fff}</style>
</head><body>
<div class="card">
<h3>Site Settings</h3>
<?php if(isset($msg)) echo "<p style='color:#4ade80'>$msg</p>"; ?>
<form method="post">
<input type="text" name="site_name" value="<?=$settings['site_name']?>" placeholder="Site Name" required>
<input type="text" name="api_key" value="<?=$settings['api_key']?>" placeholder="Replicate API Key (sk-...)" >
<button class="btn" name="save">Save Settings</button>
</form>
<br><a href="index.php" style="color:#a78bfa">Back to Admin</a>
</div>
    </body></html>