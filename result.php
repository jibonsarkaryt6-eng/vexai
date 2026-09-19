<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$settings=json_decode(@file_get_contents("admin/settings.json"),true);
$token=trim($settings['api_key'] ?? getenv('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') ?? "");

// Default value
$image_path = $_SESSION['last_image'] ?? '';
$video_path = $_SESSION['last_video'] ?? '';
$prompt = $_SESSION['last_prompt'] ?? '';

if(isset($_GET['id']) && $conn){
  try{
    $id=intval($_GET['id']);
    $q=$conn->query("SELECT * FROM videos WHERE id=$id");
    if($q && $q->num_rows>0){
      $v=$q->fetch_assoc();
      $image_path = $v['image_path'];
      $video_path = $v['video_path'];
      $prompt = $v['prompt'];
    }
  } catch(Exception $e){}
}
?>
<div style="background:#020617; color:white; text-align:center; padding:20px; min-height:100vh; font-family:sans-serif;">
 <h2>VEXAI Result ✨</h2>
 <p style="color:#94a3b8"><?php echo htmlspecialchars($prompt); ?></p>
 <img src="<?php echo htmlspecialchars($image_path); ?>" id="srcImg" style="width:90%; max-width:500px; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">
 <div id="status" style="margin:15px; color:#facc15;">
  <?php if(strpos($video_path, '.mp4')!==false && file_exists($video_path)) echo "✅ Video Ready From Backend"; else echo "Real AI Video Generating... Please wait 30 sec..."; ?>
 </div>
 <video id="realVideo" autoplay loop muted controls style="width:90%; max-width:500px; <?php echo (strpos($video_path, '.mp4')!==false && file_exists($video_path)) ? '' : 'display:none;'; ?> border-radius:12px;" src="<?php echo (strpos($video_path, '.mp4')!==false) ? htmlspecialchars($video_path) : ''; ?>"></video>
 <br>
 <a id="dl" href="<?php echo htmlspecialchars($video_path); ?>" download style="<?php echo (strpos($video_path, '.mp4')!==false) ? 'display:inline-block;' : 'display:none;'; ?> margin-top:15px; background:linear-gradient(to right, #a855f7, #ec4899); padding:12px 25px; border-radius:25px; color:white; text-decoration:none;">⬇️ Download Video</a>

 <?php if(!(strpos($video_path, '.mp4')!==false && file_exists($video_path))): ?>
 <script>
 async function makeReal(){
  const img=document.getElementById('srcImg');
  if(!img.src) return;
  const res=await fetch(img.src);
  const blob=await res.blob();
  const status=document.getElementById('status');
  try{
   const hfRes=await fetch("https://api-inference.huggingface.co/models/stabilityai/stable-video-diffusion-img2vid-xt",{
    method:"POST",
    headers:{"Authorization":"Bearer <?php echo $token; ?>"},
    body:blob
   });
   if(!hfRes.ok){
    const err=await hfRes.text();
    status.innerHTML="Model Loading... HF says: "+err.substring(0,200)+"<br>20 sec পর Auto Refresh হবে";
    setTimeout(()=>location.reload(),20000);
    return;
   }
   const videoBlob=await hfRes.blob();
   const url=URL.createObjectURL(videoBlob);
   const v=document.getElementById('realVideo');
   v.src=url; v.style.display="block";
   document.getElementById('dl').href=url;
   document.getElementById('dl').download="vexai_real.mp4";
   document.getElementById('dl').style.display="inline-block";
   status.innerHTML="✅ Real Video Ready! Zoom না, Real Motion!";
   status.style.color="#22c55e";
  }catch(e){ status.innerHTML="Error: "+e.message; }
 }
 makeReal();
 </script>
 <?php endif; ?>
 <br><br><a href="index.php" style="color:#a78bfa; text-decoration:none;">Back to Dashboard</a>
</div>
