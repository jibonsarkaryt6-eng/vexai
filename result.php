<?php
include 'config.php';
$id=intval($_GET['id']);
$q=$conn->query("SELECT * FROM videos WHERE id=$id");
$v=$q->fetch_assoc();
$settings=json_decode(@file_get_contents("admin/settings.json"),true);
$token=trim($settings['api_key']??"");
?>
<div style="background:#111; color:white; text-align:center; padding:20px; min-height:100vh;">
<img src="<?=$v['image_path']?>" id="srcImg" style="width:90%; max-width:500px; border-radius:12px;">
<div id="status" style="margin:15px; color:#facc15;">Real AI Video Generating... Please wait 30 sec...</div>
<video id="realVideo" autoplay loop muted controls style="width:90%; max-width:500px; display:none; border-radius:12px;"></video>
<br>
<a id="dl" style="display:none; margin-top:15px; background:linear-gradient(to right, #a855f7, #ec4899); padding:12px 25px; border-radius:25px; color:white; text-decoration:none;">⬇️ Download Real Video</a>

<script>
async function makeReal(){
  const img=document.getElementById('srcImg');
  const res=await fetch(img.src);
  const blob=await res.blob();
  const status=document.getElementById('status');

  try{
    const hfRes=await fetch("https://api-inference.huggingface.co/models/stabilityai/stable-video-diffusion-img2vid-xt",{
      method:"POST",
      headers:{"Authorization":"Bearer <?=$token?>"},
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

  }catch(e){
    status.innerHTML="Error: "+e.message;
  }
}
makeReal();
</script>
</div>