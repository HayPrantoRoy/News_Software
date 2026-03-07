<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$userRole = $_SESSION['role'] ?? 'super_admin';
if ($userRole !== 'super_admin') {
    if ($userRole === 'editor') {
        header('Location: index.php?editor');
    } else {
        header('Location: index.php');
    }
    exit;
}
?>
