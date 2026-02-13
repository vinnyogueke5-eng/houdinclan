<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Only registered members may access this page
if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>CLASSIFIED</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{
 background:black;
 color:#00ff00;
 font-family:monospace;
 padding:40px;
}
</style>
</head>

<body>

<h1>CLASSIFIED ARCHIVE</h1>
<p>> Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>.</p>
<p>> Future modules coming soon.</p>

<p><a href="dashboard.php">Back to Dashboard</a> &nbsp;|&nbsp; <a href="logout.php">Logout</a></p>

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
