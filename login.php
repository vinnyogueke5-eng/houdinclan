<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . "/db.php";

if(isset($_POST['login'])){
  $user = $_POST['username'] ?? '';
  $pass = $_POST['password'] ?? '';

  $result = $conn->query("SELECT * FROM members WHERE username='". $conn->real_escape_string($user) ."'");
  $data = $result ? $result->fetch_assoc() : null;

  if($data && password_verify($pass,$data['password'])){
    if($data['email_verified'] == 0){
      $error = 'Please verify your email before logging in. Check your inbox for the verification link.';
    }else{
      $_SESSION['user'] = $user;
      header('Location: dashboard.php');
      exit;
    }
  }else{
    $error = 'ACCESS DENIED';
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Secure Login</title>
  <style>
    body{background:#02030a;color:#00ffe1;font-family:Orbitron,text-align:center;padding-top:100px}
    input{padding:10px;margin:10px;background:black;border:1px solid #00ffe1;color:#00ffe1}
    button{padding:10px 20px;background:black;border:1px solid #00ffe1;color:#00ffe1}
  </style>
</head>
<body>

<h2>Member Login</h2>

<form method="POST">
  <input name="username" placeholder="Username" required>
  <input name="password" type="password" placeholder="Password" required>
  <button name="login">ENTER</button>
</form>

<p style="color:red;"><?php if(isset($error)) echo $error; ?></p>

<p><a href="register.html">Create account</a></p>

<nav>
  <div>HOUDINI CLAN</div>
  <div>
    <a href="BV.HTML">Home</a>
    <a href="dashboard.html">Operations</a>
    <a href="secret.php">Cyber Defense</a>
    <a href="register.html">Development</a>
    <a href="dashboard.html">Intelligence</a>
    <a href="register.html">Join</a>
  </div>
</nav>

</body>
</html>
