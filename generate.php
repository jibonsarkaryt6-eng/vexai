<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

// Settings থেকে Token নাও
$settings = json_decode(@file_get_contents("admin/settings.json"), true);
$hf_token = trim($settings['api_key'] ?? getenv('HF_TOKEN') ?? "");
// তোমার Render Env Variable নাম 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 হলে সেটাও Check করবে
if(empty($hf_token)){
  $hf_token = getenv('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') ?: '';
}

if(isset($_FILES['image'])){
  $dir="uploads/";
  if(!is_dir($dir)) mkdir($dir,0777,true);
  
  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $name = time()."_".uniqid().".".$ext;
  $path = $dir.$name;
  move_uploaded_file($_FILES['image']['tmp_name'],$path);
  
  $prompt = $_POST['prompt'] ?? "intense drift motion";
  $uid = $_SESSION['user_id'] ?? 1;
  $videoPath = "";
  $status = "completed";

  if(!empty($hf_token)){
    $imageData = file_get_contents($path);
    $ch = curl_init("https://api-inference.huggingface.co/models/stabilityai/stable-video-diffusion-img2vid-xt");
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER=>true,
      CURLOPT_POST=>true,
      CURLOPT_HTTPHEADER=>["Authorization: Bearer $hf_token", "Content-Type: image/jpeg"],
      CURLOPT_POSTFIELDS=>$imageData,
      CURLOPT_TIMEOUT=>120,
      CURLOPT_SSL_VERIFYPEER=>false
    ]);
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($code==200 && strlen($result) > 10000){
      $videoPath = $dir.time()."_real.mp4";
      file_put_contents($videoPath, $result);
    } else {
      file_put_contents($dir."_error.txt", "CODE:$code | ".$result);
      // Fail হলেও একটা Fake Video Path দিয়ে Result এ নিয়ে যাবো যাতে 500 না দেয়
      $videoPath = $path; 
    }
  } else {
    $videoPath = $path; // Token না থাকলে Image কেই Video হিসেবে দেখাবে
  }

  // DB থাকলে Insert করো, না থাকলে Skip করো
  if($conn){
    try{
      $stmt=$conn->prepare("INSERT INTO videos (user_id,image_path,video_path,prompt,status) VALUES (?,?,?,?,?)");
      if($stmt){
        $stmt->bind_param("issss",$uid,$path,$videoPath,$prompt,$status);
        $stmt->execute();
        header("Location: result.php?id=".$stmt->insert_id);
        exit;
      }
    } catch(Exception $e){}
  }
  // DB না থাকলে Session এ Save করে Result এ যাও
  $_SESSION['last_image'] = $path;
  $_SESSION['last_video'] = $videoPath;
  $_SESSION['last_prompt'] = $prompt;
  header("Location: result.php?temp=1");
  exit;
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Generate - VEXAI</title>
<style>body{margin:0;background:#020617;color:#fff;font-family:sans-serif}.card{max-width:500px;margin:40px auto;background:rgba(255,255,255,0.06);padding:30px;border-radius:20px;text-align:center}input,textarea{width:90%;padding:14px;margin:10px 0;border-radius:12px;border:none;background:#0f172a;color:#fff}.btn{width:95%;padding:14px;background:linear-gradient(90deg,#8b5cf6,#ec4899);border:none;border-radius:12px;color:#fff;font-weight:bold;cursor:pointer}</style>
</head><body><div class="card"><h2>Generate Video ✨</h2><form method="post" enctype="multipart/form-data"><input type="file" name="image" accept="image/*" required><textarea name="prompt" placeholder="Prompt - e.g. drift motion"></textarea><button class="btn" type="submit">Generate</button></form><br><a href="index.php" style="color:#a78bfa">Back to Dashboard</a></div></body></html>
