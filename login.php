<?php include 'config.php';
if(isset($_SESSION['user_id'])){ header("Location: index.php"); exit; }
if(isset($_POST['login'])){
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if(password_verify($_POST['password'], $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit;
        } else { $error = "Wrong Password!"; }
    } else { $error = "User Not Found!"; }
}
?>
<!DOCTYPE html>
<html><head><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - VEXAI</title>
<style>
*{font-family:sans-serif} body{margin:0;background:radial-gradient(circle at top,#1e1b4b,#020617);color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{width:90%;max-width:400px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);padding:30px;border-radius:20px;text-align:center}
input{width:90%;padding:14px;margin:10px 0;border-radius:12px;border:none;background:#0f172a;color:#fff}
.btn{width:95%;padding:14px;background:linear-gradient(90deg,#8b5cf6,#ec4899);border:none;border-radius:12px;color:#fff;font-weight:bold;cursor:pointer}
a{color:#a78bfa;text-decoration:none}
</style>
</head><body>
<div class="card">
<h2>Welcome Back 👋</h2>
<?php if(isset($_GET['success'])) echo "<p style='color:#4ade80'>Registered! Please Login</p>"; ?>
<?php if(isset($error)) echo "<p style='color:#f87171'>$error</p>"; ?>
<form method="post">
<input type="email" name="email" placeholder="Email Address" required>
<input type="password" name="password" placeholder="Password" required>
<button class="btn" name="login" type="submit">Login</button>
</form>
<p>Don't have account? <a href="register.php">Register</a></p>
</div>
    </body></html>