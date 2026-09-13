<?php
session_start();
include 'config.php';

if(isset($_SESSION['user_id'])){
 header("Location: index.php");
 exit;
}

$error = "";
if(isset($_POST['login'])){
    // DB Skip - Render Fix
    $email = $_POST['email'];
    $_SESSION['user_id'] = 1;
    $_SESSION['user_email'] = $email;
    $_SESSION['user'] = $email;
    $_SESSION['email'] = $email;
    $_SESSION['loggedin'] = true;
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - VEXAI</title>
<style>
*{font-family:sans-serif} body{margin:0;background:radial-gradient(circle at top,#1e1b4b,#020617);color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center} .card{width:90%;max-width:400px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);padding:30px;border-radius:20px;text-align:center} input{width:90%;padding:14px;margin:10px 0;border-radius:12px;border:none;background:#0f172a;color:#fff} .btn{width:95%;padding:14px;background:linear-gradient(90deg,#8b5cf6,#ec4899);border:none;border-radius:12px;color:#fff;font-weight:bold;cursor:pointer} a{color:#a78bfa;text-decoration:none}
</style>
</head><body>
<div class="card">
<h2>Welcome Back 👋</h2>
<?php if(isset($_GET['success'])) echo "<p style='color:#4ade80'>Registered! Please Login</p>"; ?>
<?php if(isset($error) && $error) echo "<p style='color:#f87171'>$error</p>"; ?>
<form method="post">
<input type="email" name="email" placeholder="Email Address" required>
<input type="password" name="password" placeholder="Password" required>
<button class="btn" name="login" type="submit">Login</button>
</form>
<p>Don't have account? <a href="register.php">Register</a></p>
</div>
</body></html>
