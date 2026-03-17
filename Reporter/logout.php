<?php
session_start();

// Get user_id before destroying session
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($_SESSION['reporter_user_id']) ? intval($_SESSION['reporter_user_id']) : 0);

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with user_id
$redirect = 'index.php';
if ($user_id > 0) {
    $redirect .= '?user_id=' . $user_id;
}
header("Location: " . $redirect);
exit();
?>
