<?php
session_start();
 
// Generate new session ID
$_SESSION['session_id'] = uniqid('chat_', true);
 
// Redirect to index
header('Location: index.php');
exit;
?>
