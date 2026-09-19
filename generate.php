<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

// Token - Env থেকে নেবে, না পেলে এখান থেকে নেবে (Test এর জন্য)
$settings = json_decode(@file_get_contents("admin/settings.json"), true);
$hf_token = trim($settings['api_key'] ?? "");
if(empty($hf_token)){
  $hf_token = getenv('HF_TOKEN');
}
if(empty($hf_token)){
  $hf_token = "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"; // তোমার Token - Test এর জন্য Direct দিলাম
}

$msg = "";
if(isset($_FILES['image'])){
  $dir="uploads/";
  if(!is_dir($dir)) mkdir($dir,0777,true);
  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $name = time()."_".uniqid().".".$ext;
  $path = $dir.$name;
  move_uploaded_file($_FILES['image']['tmp_name'],$path);
  $prompt = $_POST['prompt'] ?? "vegetable moving";

  if(empty($hf_token)){
    $msg = "HF Token পাওয়া যায়নি!";
  } else {
    $imageData = file_get_contents($path);
    $ch = curl_init("https://router.huggingface.co/hf-inference/models/stabilityai/stable-video-diffusion-img2vid-xt");
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER=>true,
      CURLOPT_POST=>true,
      CURLOPT_HTTPHEADER=>["Authorization: Bearer $hf_token", "Content-Type: image/jpeg"],
      CURLOPT_POSTFIELDS=>$imageData,
      CURLOPT_TIMEOUT=>120
    ]);
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if($code==200 && strlen($result) > 10000){
      $videoPath = $dir.time()."_real.mp4";
      file_put_contents($videoPath, $result);
      $_SESSION['last_image']=$path;
      $_SESSION['last_video']=$videoPath;
      $_SESSION['last_prompt']=$prompt;
      if(isset($conn) && $conn){
        try{
          $stmt=$conn->prepare("INSERT INTO videos (user_id,image_path,video_path,prompt,status) VALUES (?,?,?,?,?)");
          $uid=$_SESSION['user_id']??1; $st="completed";
          $stmt->bind_param("issss",$uid,$path,$videoPath,$prompt,$st);
          $stmt->execute();
          header("Location: result.php?id=".$stmt->insert_id); exit;
        }catch(Exception $e){}
      }
      header("Location: result.php?temp=1"); exit;
    } else {
      $msg = "HF Error Code $code: ".substr($result,0,400)." Curl: $err";
      file_put_contents($dir."_last_error.txt",$msg);
    }
  }
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Generate - VEXAI</title>
<style>body{margin:0;background:#020617;color:#fff;font-family:sans-serif}.card{max-width:500px;margin:40px auto;background:rgba(255,255,255,0.06);padding:30px;border-radius:20px;text-align:center}input,textarea{width:90%;padding:14px;margin:10px 0;border-radius:12px;border:none;background:#0f172a;color:#fff}.btn{width:95%;padding:14px;background:linear-gradient(90deg,#8b5cf6,#ec4899);border:none;border-radius:12px;color:#fff;font-weight:bold;cursor:pointer}</style>
</head><body><div class="card"><h2>Generate Video ✨</h2>
<?php if($msg) echo "<p style='color:#f87171;background:rgba(248,113,113,0.1);padding:10px;border-radius:8px;'>$msg</p>"; ?>
<form method="post" enctype="multipart/form-data"><input type="file" name="image" accept="image/*" required><textarea name="prompt" placeholder="লাউটা নড়বে"></textarea><button class="btn" type="submit">Generate</button></form><br><a href="index.php" style="color:#a78bfa">Back</a>
<?php if(file_exists("uploads/_last_error.txt")) echo "<br><small style='opacity:0.5'>Last Error: ".htmlspecialchars(file_get_contents("uploads/_last_error.txt"))."</small>"; ?>
</div></body></html>
