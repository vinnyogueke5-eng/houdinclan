<?php 
// HOUDINI CLAN - Legacy JSON Registration Endpoint
// For form-based registration, use register-response.php instead
// This endpoint is kept for API/JSON requests

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "ERROR: Method not allowed. Use POST.";
    exit;
}

// For all practical purposes, redirect to the main registration endpoint
// which handles both forms and JSON
include 'register-response.php';


// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo "ERROR: This endpoint only accepts POST requests";
    exit;
}

// Include database connection
if (!file_exists('db.php')) {
    echo "ERROR: Database configuration file not found";
    exit;
}

include "db.php";

// If we get here, db.php loaded successfully
if (!isset($conn)) {
    echo "ERROR: Database connection not established";
    exit;
}

// Get form data - handle both form-encoded and JSON
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$user = '';
$email = '';
$pass = '';
$fullname = '';
$clan = '';

if (strpos($contentType, 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (is_array($data)) {
        $user = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $pass = $data['password'] ?? '';
        $fullname = $data['fullname'] ?? '';
        $clan = strtoupper($data['clanname'] ?? '');
    }
} else {
    $user = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $clan = strtoupper($_POST['clanname'] ?? '');
}

// Validate clan
if ($clan !== 'HOUDINI') {
    echo "ERROR: Clan name must be HOUDINI";
    exit;
}

// Validate required fields
if (empty($user) || empty($email) || empty($pass)) {
    echo "ERROR: Missing required fields (username, email, password)";
    exit;
}

// Generate verification token
$token = bin2hex(random_bytes(32));
$pass_hash = password_hash($pass, PASSWORD_DEFAULT);
$user_esc = $conn->real_escape_string($user);
$email_esc = $conn->real_escape_string($email);
$fullname_esc = $conn->real_escape_string($fullname);

// Check if username already exists
$check = $conn->query("SELECT id FROM members WHERE username='$user_esc' OR email='$email_esc'");
if ($check && $check->num_rows > 0) {
    echo "ERROR: Username or email already registered";
    exit;
}

// Insert member
if (!$conn->query("INSERT INTO members(username,email,password,fullname,email_verified,verification_token) VALUES('$user_esc','$email_esc','$pass_hash','$fullname_esc',0,'$token')")) {
    echo "ERROR: Registration failed - " . $conn->error;
    exit;
}

// Try to send verification email
$verifyLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/hodini/verify_email.php?token=' . $token;
$subject = 'HOUDINI CLAN - Email Verification';
$message = "Welcome to HOUDINI CLAN!\n\n";
$message .= "Click the link below to verify your email:\n";
$message .= $verifyLink . "\n\n";
$message .= "If you did not register, ignore this email.";
$headers = "From: noreply@houdiniclan.local\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (@mail($email, $subject, $message, $headers)) {
    echo "SUCCESS: Registration complete! Check your email to verify your account.";
} else {
    echo "SUCCESS: Account created! (Email notification could not be sent, but account is active.)";
}

?>
