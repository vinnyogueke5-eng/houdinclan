<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Require login
if(!isset($_SESSION['user'])){
    header('Location: login.php');
    exit;
}
// Developer-friendly error reporting while debugging this page.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>HOUDINI CLAN Dashboard</title>
	<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@300;500;700&display=swap" rel="stylesheet">
	<style>
		body{background:#05070d;color:#00ffe1;font-family:Orbitron, 'Orbitron', sans-serif;padding:40px}
		.card{border:1px solid #00ffe1;padding:30px;margin:20px 0;background:#0b0f1c}
		a{color:#00ffe1}
		.nav{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
	</style>
</head>

<body>

<div class="nav">
		<div><strong>HOUDINI CLAN</strong></div>
		<div>
			<a href="BV.HTML">Home</a> &nbsp;|&nbsp; <a href="dashboard.html">Operations</a> &nbsp;|&nbsp; <a href="secret.php">Cyber Defense</a> &nbsp;|&nbsp; <a href="register.html">Development</a> &nbsp;|&nbsp; <a href="dashboard.html">Intelligence</a> &nbsp;|&nbsp; <a href="register.html">Join</a>
		</div>
</div>

<h1>WELCOME OPERATIVE</h1>
<p>Secure channel established.</p>

<div class="card">
	<h3>Mission Status</h3>
	<p>All systems operational.</p>
</div>

<div class="card">
	<h3>Internal Tools</h3>
	<p><a href="secret.php">Access Restricted Archive</a></p>
</div>

<a href="logout.php">Logout</a>

</body>
</html>
