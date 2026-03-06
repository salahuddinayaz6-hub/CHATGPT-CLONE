<?php
session_start();
 
// Generate or get session ID
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid('chat_', true);
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
    die("Connection failed: " . $e->getMessage());
}
 
// Get chat history for current session
$stmt = $pdo->prepare("SELECT * FROM chat_history WHERE session_id = ? ORDER BY created_at ASC");
$stmt->execute([$_SESSION['session_id']]);
$chat_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot - ChatGPT Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
 
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
 
        .chat-container {
            width: 100%;
            max-width: 1200px;
            height: 90vh;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
 
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
 
        .chat-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
 
        .chat-header p {
            font-size: 14px;
            opacity: 0.9;
        }
 
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
 
        .message {
            margin-bottom: 20px;
            display: flex;
            animation: fadeIn 0.3s ease;
        }
 
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
 
        .message.user {
            justify-content: flex-end;
        }
 
        .message.bot {
            justify-content: flex-start;
        }
 
        .message-content {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 20px;
            font-size: 15px;
            line-height: 1.5;
            position: relative;
        }
 
        .user .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }
 
        .bot .message-content {
            background: white;
            color: #333;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
 
        .message-time {
            font-size: 11px;
            margin-top: 5px;
            opacity: 0.7;
        }
 
        .user .message-time {
            text-align: right;
            color: rgba(255,255,255,0.7);
        }
 
        .bot .message-time {
            color: #999;
        }
 
        .typing-indicator {
            display: none;
            padding: 15px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: fit-content;
            margin-bottom: 20px;
        }
 
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #999;
            margin: 0 2px;
            animation: typing 1s infinite;
        }
 
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
 
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
 
        @keyframes typing {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }
 
        .chat-input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
 
        .chat-input {
            flex: 1;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s;
        }
 
        .chat-input:focus {
            border-color: #667eea;
        }
 
        .send-button {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
 
        .send-button:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
 
        .send-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
 
        .clear-button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background: #f0f0f0;
            color: #666;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
 
        .clear-button:hover {
            background: #e0e0e0;
            color: #333;
        }
 
        .welcome-message {
            text-align: center;
            color: #999;
            padding: 40px;
            font-size: 16px;
        }
 
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
 
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
 
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
 
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
 
        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
 
            .chat-container {
                height: 95vh;
            }
 
            .message-content {
                max-width: 85%;
                font-size: 14px;
                padding: 12px 15px;
            }
 
            .chat-header h1 {
                font-size: 20px;
            }
        }
 
        /* Error message styling */
        .error-message {
            background: #fee;
            color: #c00;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            text-align: center;
        }
 
        /* New chat button */
        .new-chat-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.5);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            transition: all 0.3s;
        }
 
        .new-chat-btn:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h1>🤖 AI Chatbot</h1>
            <p>Your intelligent conversation partner</p>
            <button class="new-chat-btn" onclick="window.location.href='new_chat.php'">➕ New Chat</button>
        </div>
 
        <div class="chat-messages" id="chatMessages">
            <?php if (empty($chat_history)): ?>
                <div class="welcome-message">
                    <h3>👋 Welcome!</h3>
                    <p>Start a conversation by typing a message below.</p>
                </div>
            <?php else: ?>
                <?php foreach ($chat_history as $chat): ?>
                    <div class="message user">
                        <div class="message-content">
                            <?php echo htmlspecialchars($chat['user_message']); ?>
                            <div class="message-time"><?php echo date('h:i A', strtotime($chat['created_at'])); ?></div>
                        </div>
                    </div>
                    <div class="message bot">
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($chat['bot_response'])); ?>
                            <div class="message-time"><?php echo date('h:i A', strtotime($chat['created_at'])); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
 
        <div class="typing-indicator" id="typingIndicator">
            <span></span>
            <span></span>
            <span></span>
        </div>
 
        <div class="chat-input-container">
            <input type="text" 
                   class="chat-input" 
                   id="userInput" 
                   placeholder="Type your message here..." 
                   onkeypress="handleKeyPress(event)">
            <button class="send-button" id="sendButton" onclick="sendMessage()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
 
    <script>
        const chatMessages = document.getElementById('chatMessages');
        const userInput = document.getElementById('userInput');
        const sendButton = document.getElementById('sendButton');
        const typingIndicator = document.getElementById('typingIndicator');
 
        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
 
        // Initial scroll
        scrollToBottom();
 
        // Handle Enter key press
        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }
 
        // Send message function
        async function sendMessage() {
            const message = userInput.value.trim();
 
            if (!message) return;
 
            // Disable input and button while sending
            userInput.disabled = true;
            sendButton.disabled = true;
 
            // Add user message to chat
            addMessage(message, 'user');
 
            // Clear input
            userInput.value = '';
 
            // Show typing indicator
            typingIndicator.style.display = 'block';
            scrollToBottom();
 
            try {
                // Send message to server
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                });
 
                const data = await response.json();
 
                // Hide typing indicator
                typingIndicator.style.display = 'none';
 
                if (data.success) {
                    // Add bot response to chat
                    addMessage(data.response, 'bot');
                } else {
                    // Show error message
                    addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                }
            } catch (error) {
                console.error('Error:', error);
                typingIndicator.style.display = 'none';
                addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
 
            // Re-enable input and button
            userInput.disabled = false;
            sendButton.disabled = false;
            userInput.focus();
 
            scrollToBottom();
        }
 
        // Add message to chat
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
 
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
 
            messageDiv.innerHTML = `
                <div class="message-content">
                    ${text.replace(/\n/g, '<br>')}
                    <div class="message-time">${timeString}</div>
                </div>
            `;
 
            chatMessages.appendChild(messageDiv);
            scrollToBottom();
        }
 
        // Remove welcome message if it exists
        function removeWelcomeMessage() {
            const welcomeMessage = document.querySelector('.welcome-message');
            if (welcomeMessage) {
                welcomeMessage.remove();
            }
        }
    </script>
</body>
</html>
