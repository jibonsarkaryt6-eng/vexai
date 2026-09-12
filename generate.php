<?php
include 'config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }

$settings = json_decode(@file_get_contents("admin/settings.json"), true);
$hf_token = trim($settings['api_key'] ?? "");

if(isset($_FILES['image'])){
    $dir="uploads/"; if(!is_dir($dir)) mkdir($dir,0777,true);
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $name = time()."_".uniqid().".".$ext;
    $path = $dir.$name;
    move_uploaded_file($_FILES['image']['tmp_name'],$path);

    $prompt = $_POST['prompt'] ?? "intense drift motion";
    $uid = $_SESSION['user_id'];
    $videoPath = "";
    $status = "completed";

    if(!empty($hf_token)){
        $imageData = file_get_contents($path);
        // Model: stable-video-diffusion-img2vid-xt - এটা Real Motion বানায়
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

        if($code==200 && strlen($result) > 50000){
            $videoPath = $dir.time()."_real.mp4";
            file_put_contents($videoPath, $result);
        } else {
            // Error log save
            file_put_contents($dir."_error.txt", $result);
        }
    }

    $stmt=$conn->prepare("INSERT INTO videos (user_id,image_path,video_path,prompt,status) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss",$uid,$path,$videoPath,$prompt,$status);
    $stmt->execute();
    header("Location: result.php?id=".$stmt->insert_id);
    exit;
}
?>