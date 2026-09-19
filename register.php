<?php
include 'config.php';
if(session_status()===PHP_SESSION_NONE) session_start();
if(isset($_SESSION['user_id'])){ header("Location: index.php"); exit; }
$error="";
if(isset($_POST['register'])){
 $email = $_POST['email'];
 if($conn){
   $email_safe = $conn->real_escape_string($_POST['email']);
   $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
   $check = $conn->query("SELECT id FROM users WHERE email='$email_safe'");
   if($check && $check->num_rows>0){ $error="Email already exists!"; }
   else { $conn->query("INSERT INTO users (email, password) VALUES ('$email_safe', '$password')"); header("Location: login.php?success=1"); exit; }
 } else {
   $_SESSION['user_id']=1; $_SESSION['email']=$email; $_SESSION['user_email']=$email;
   header("Location: index.php"); exit;
 }
}
?>
<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1"><title>Register</title><style>*{font-family:sans-serif}body{margin:0;background:radial-gradient(circle at top,#1e1b4b,#020617);color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center}.card{width:90%;max-width:400px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);padding:30px;border-radius:20px;text-align:center}input{width:90%;padding:14px;margin:10px 0;border-radius:12px;border:none;background:#0f172a;color:#fff}.btn{width:95%;padding:14px;background:linear-gradient(90deg,#8b5cf6,#ec4899);border:none;border-radius:12px;color:#fff;font-weight:bold;cursor:pointer}a{color:#a78bfa;text-decoration:none}</style></head><body><div class="card"><h2>Create Account ✨</h2><?php if($error) echo "<p style='color:#f87171'>$error</p>"; ?><form method="post"><input type="email" name="email" required placeholder="Email"><input type="password" name="password" required placeholder="Password"><button class="btn" name="register" type="submit">Sign Up</button></form><p>Already have? <a href="login.php">Login</a></p></div></body></html>
