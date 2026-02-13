<?php 
// HOUDINI CLAN - Main Registration Endpoint
// Handles form-based registration with user-friendly responses

header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('ERROR: This endpoint only accepts POST requests');
}

// Include database connection
if (!file_exists('db.php')) {
    http_response_code(500);
    die('ERROR: Database configuration file not found');
}

include "db.php";

// Verify database connection
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    die('ERROR: Database connection failed');
}

// Get form data
$user = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$clan = strtoupper($_POST['clanname'] ?? '');

$message = '';
$messageClass = 'error';

// Validate clan
if ($clan !== 'HOUDINI') {
    $message = "Clan name must be HOUDINI";
} elseif (empty($user) || empty($email) || empty($pass)) {
    $message = "Missing required fields (username, email, password)";
} else {
    // Generate verification token
    $token = bin2hex(random_bytes(32));
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
    $user_esc = $conn->real_escape_string($user);
    $email_esc = $conn->real_escape_string($email);
    $fullname_esc = $conn->real_escape_string($fullname);

    // Check if username already exists
    $check = $conn->query("SELECT id FROM members WHERE username='$user_esc' OR email='$email_esc'");
    if ($check && $check->num_rows > 0) {
        $message = "Username or email already registered";
    } elseif (!$conn->query("INSERT INTO members(username,email,password,fullname,email_verified,verification_token) VALUES('$user_esc','$email_esc','$pass_hash','$fullname_esc',0,'$token')")) {
        $message = "Registration failed - " . $conn->error;
    } else {
        // Try to send verification email
        $verifyLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/hodini/verify_email.php?token=' . $token;
        $subject = 'HOUDINI CLAN - Email Verification';
        $emailBody = "Welcome to HOUDINI CLAN!\n\n";
        $emailBody .= "Click the link below to verify your email:\n";
        $emailBody .= $verifyLink . "\n\n";
        $emailBody .= "If you did not register, ignore this email.";
        $headers = "From: noreply@houdiniclan.local\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (@mail($email, $subject, $emailBody, $headers)) {
            $message = "Success! Check your email (" . htmlspecialchars($email) . ") to verify your account.";
        } else {
            $message = "Account created! Email notification could not be sent, but your account is active.";
        }
        $messageClass = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registration Result — HOUDINI CLAN</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#05070d;color:#00ffe1;margin:0;padding:20px}
.container{max-width:600px;margin:0 auto;background:#071028;padding:24px;border-radius:8px;text-align:center}
h1{margin:0 0 20px}
.message{padding:20px;border-radius:4px;margin:20px 0;font-size:16px}
.success{background:#0a2a1a;border:2px solid #2d9d5e;color:#a8ffcf}
.error{background:#2a0a0a;border:2px solid #9d2d2d;color:#ff6b6b}
a{color:#00ffe1;text-decoration:none;display:inline-block;margin-top:20px;padding:10px 20px;border:1px solid #00ffe1;border-radius:4px}
a:hover{background:#00ffe1;color:#05070d}
</style>
</head>
<body>
<div class="container">
<h1>HOUDINI CLAN Registration</h1>
<div class="message <?php echo $messageClass; ?>">
  <?php echo htmlspecialchars($message); ?>
</div>
<a href="register.html">← Back to Register</a>
</div>
</body>
</html>
