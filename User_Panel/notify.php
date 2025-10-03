<?php
include_once '../Skill_Swap/code.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Admin_Panel/signin.php");
    exit;
}
$uid = intval($_SESSION['user_id']);
$id  = intval($_GET['id'] ?? 0);
$redirect = $_GET['redirect'] ?? 'public.php';

// Basic sanitize: disallow absolute external redirects
if (strpos($redirect, 'http://') === 0 || strpos($redirect, 'https://') === 0) {
    $redirect = 'public.php';
}

if ($id > 0) {
    mysqli_query($connection, "UPDATE notifications SET is_read=1 WHERE id=$id AND user_id=$uid");
}

// safe redirect
header("Location: $redirect");
exit;
