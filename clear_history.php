<?php
session_start();
 
// Check if session exists
if (!isset($_SESSION['session_id'])) {
    header('Location: index.php');
    exit;
}
 
// Database connection
$host = 'localhost';
$dbname = 'rsoa_rsoa276_77';
$username = 'rsoa_rsoa276_77';
$password = '123456';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    // Delete chat history for current session
    $stmt = $pdo->prepare("DELETE FROM chat_history WHERE session_id = ?");
    $stmt->execute([$_SESSION['session_id']]);
 
} catch(PDOException $e) {
    // Handle error silently
}
 
// Redirect back to index
header('Location: index.php');
exit;
?>
