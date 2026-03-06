<?php
session_start();
 
header('Content-Type: application/json');
 
// Check if session exists
if (!isset($_SESSION['session_id'])) {
    echo json_encode(['success' => false, 'error' => 'No session found']);
    exit;
}
 
// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim($input['message']) : '';
 
if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message is empty']);
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
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}
 
// Generate AI response (simulated for demo - replace with actual AI API)
function getAIResponse($userMessage) {
    // This is a simulated AI response. Replace with actual API call
    $responses = [
        "That's an interesting question! Let me think about it...",
        "I understand what you're asking. Here's what I think:",
        "Great question! Based on my knowledge, I would say that",
        "I appreciate your curiosity. The answer to your question is",
        "Let me help you with that. Here's my response:"
    ];
 
    $randomResponse = $responses[array_rand($responses)];
 
    // Simple response logic based on keywords
    $userMessage = strtolower($userMessage);
 
    if (strpos($userMessage, 'hello') !== false || strpos($userMessage, 'hi') !== false) {
        return "Hello! How can I assist you today?";
    } elseif (strpos($userMessage, 'how are you') !== false) {
        return "I'm doing great, thank you for asking! How can I help you?";
    } elseif (strpos($userMessage, 'name') !== false) {
        return "I'm an AI chatbot created to assist you with your questions!";
    } elseif (strpos($userMessage, 'help') !== false) {
        return "I'd be happy to help! What do you need assistance with?";
    } elseif (strpos($userMessage, 'weather') !== false) {
        return "I'm sorry, I don't have access to real-time weather data. You might want to check a weather website or app for that information.";
    } elseif (strpos($userMessage, 'time') !== false) {
        return "I don't have access to the current time, but you can check your device's clock!";
    } elseif (strpos($userMessage, 'thank') !== false) {
        return "You're welcome! Is there anything else I can help you with?";
    } else {
        return "$randomResponse \n\nI'm still learning and improving. For more accurate information, please consult reliable sources or ask a more specific question.";
    }
}
 
// Get AI response
$botResponse = getAIResponse($message);
 
// Save to database
try {
    $stmt = $pdo->prepare("INSERT INTO chat_history (session_id, user_message, bot_response) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['session_id'], $message, $botResponse]);
 
    echo json_encode([
        'success' => true,
        'response' => $botResponse
    ]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to save message']);
}
?>
