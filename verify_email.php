<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Email Verification</title>
  <style>
    body{background:#02070d;color:#00ffe1;font-family:Orbitron;text-align:center;padding:60px 20px}
    a{color:#00ffe1}
  </style>
</head>
<body>
  <h2>Email Verification</h2>

<?php
$token = $_GET['token'] ?? '';
if(empty($token)){
  echo "<p style='color:#ff6b6b'>Invalid verification link.</p>";
}else{
  $token_esc = $conn->real_escape_string($token);
  $result = $conn->query("SELECT * FROM members WHERE verification_token='$token_esc' AND email_verified=0");
  
  if($result && $result->num_rows > 0){
    $row = $result->fetch_assoc();
    $id = $row['id'];
    
    // Mark email as verified
    if($conn->query("UPDATE members SET email_verified=1, verification_token=NULL WHERE id=$id")){
      echo "<p style='color:#a8ffcf'><strong>Email verified successfully!</strong></p>";
      echo "<p>Your account is now active. You can <a href='login.php'>login here</a>.</p>";
    }else{
      echo "<p style='color:#ff6b6b'>Verification failed: " . $conn->error . "</p>";
    }
  }else{
    echo "<p style='color:#ff6b6b'>Invalid or expired verification token.</p>";
  }
}
?>

  <p><a href="BV.HTML">Back to Home</a></p>

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
