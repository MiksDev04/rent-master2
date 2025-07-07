<?php
// Initialize variables
$user_id = null;
$user = null;
$user_role = 'visitor';
$chat_contacts = array();
$is_logged_in = false;

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $is_logged_in = true;

    // Get user information
    $sql = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $user_role = $user['user_role'];

        if ($user_role == 'tenant') {
            // Get tenant's landlord information
            $sql = "SELECT l.landlord_id, u.user_name, u.user_image, u.user_id 
                    FROM tenants t 
                    JOIN landlords l ON t.landlord_id = l.landlord_id 
                    JOIN users u ON l.user_id = u.user_id 
                    WHERE t.user_id = $user_id AND t.tenant_status = 'active'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $chat_contacts[] = array(
                        'id' => $row['user_id'],
                        'name' => $row['user_name'],
                        'image' => $row['user_image'],
                        'type' => 'landlord'
                    );
                }
            }

            $chat_contacts[] = array(
                'id' => 'admin123',
                'name' => 'The Rent Master',
                'image' => 'assets/images/image.png',
                'type' => 'admin'
            );
        } else {
            $chat_contacts[] = array(
                'id' => 'admin123',
                'name' => 'The Rent Master',
                'image' => 'assets/images/image.png',
                'type' => 'admin'
            );
        }
    }
} else {
    $chat_contacts[] = array(
        'id' => 'admin123',
        'name' => 'The Rent Master',
        'image' => 'assets/images/image.png',
        'type' => 'admin'
    );
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Master - Chat System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }

        /* Floating Chat Button */
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
            border: none;
        }

        .chat-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(0, 123, 255, 0.4);
        }

        .chat-button svg {
            width: 30px;
            height: 30px;
            fill: white;
        }

        /* Chat Container */
        .chat-container {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 400px;
            height: 450px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            z-index: 999999999;
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .close-chat {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-chat:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Contact List */
        .contact-list {
            flex: 1;
            overflow-y: auto;
            border-bottom: 1px solid #e0e0e0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }

        .contact-item:hover {
            background-color: #f8f9fa;
        }

        .contact-item.active {
            background-color: #e3f2fd;
            border-left: 4px solid #007bff;
        }

        .contact-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .contact-info h4 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .contact-info p {
            margin: 0;
            font-size: 12px;
            color: #666;
            text-transform: capitalize;
        }

        /* Chat Messages */
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            max-height: 300px;
            display: none;
        }

        .message {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            align-items: flex-start;
        }

        .message.sent {
            align-items: flex-end;
        }

        .message.received {
            align-items: flex-start;
        }

        .message-content {
            max-width: 50%;
            /* Reduced from 70% to make messages smaller */
            padding: 10px 14px;
            /* Reduced padding for smaller size */
            border-radius: 16px;
            /* Slightly smaller border radius */
            word-wrap: break-word;
            font-size: 14px;
            /* Explicit font size for message text */
        }

        .message.sent .message-content {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.received .message-content {
            background: #f1f3f4;
            color: #333;
            border-bottom-left-radius: 5px;
        }

        .message-time {
            font-size: 10px;
            /* Even smaller font for timestamp */
            opacity: 0.6;
            margin-top: 3px;
        }

        .message.sent .message-time {
            text-align: right;
        }

        .message.received .message-time {
            text-align: left;
        }

        /* Message Input */
        .message-input {
            display: none;
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
            background: white;
        }

        /* .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        } */

        .message-text {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
            resize: none;
            min-height: 36px;
            max-height: 80px;
        }

        .message-text:focus {
            border-color: #007bff;
        }

        .send-button {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
        }

        .send-button:hover {
            background: linear-gradient(135deg, #0056b3, #004494);
        }

        .back-button {
            background: none;
            border: none;
            color: rgb(237, 246, 255);
            font-size: 14px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .back-button:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .chat-container {
                width: 100%;
                bottom: 10px;
                right: 0;
            }

            .chat-button {
                bottom: 20px;
                right: 20px;
            }

            .contact-avatar {
                width: 40px;
                height: 40px;
            }

            .contact-item {
                padding: 12px 15px;
            }

            .message-content {
                max-width: 85%;
            }
        }

        @media (max-width: 480px) {
            .chat-container {
                width: 100%;
                bottom: 10px;
                right: 0;
            }

            .chat-button {
                bottom: 15px;
                right: 15px;
                width: 50px;
                height: 50px;
            }

            .chat-button svg {
                width: 25px;
                height: 25px;
            }
        }
    </style>
</head>

<body>
    <!-- Your existing page content goes here -->

    <!-- Floating Chat Button -->
    <button class="chat-button" id="chatButton">
        <svg viewBox="0 0 24 24">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" />
        </svg>
    </button>

    <!-- Chat Container -->
    <div class="chat-container" id="chatContainer">
        <div class="chat-header">
            <div>
                <button class="back-button" id="backButton" style="display: none;">← Back</button>
                <h3 id="chatTitle">Messages</h3>
            </div>
            <button class="close-chat" id="closeChat">×</button>
        </div>

        <!-- Contact List -->
        <div class="contact-list" id="contactList">
            <?php if (!$is_logged_in): ?>
                <div class="login-prompt" style="padding: 20px; text-align: center; color: #666;">
                    <p style="margin-bottom: 15px;">Please login to start chatting</p>
                    <button onclick="window.location.href='?page=src/login'" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer;">Login</button>
                </div>
            <?php elseif (empty($chat_contacts)): ?>
                <div class="no-contacts" style="padding: 20px; text-align: center; color: #666;">
                    <p>No contacts available</p>
                </div>
            <?php else: ?>
                <?php foreach ($chat_contacts as $contact): ?>
                    <div class="contact-item" data-contact-id="<?php echo $contact['id']; ?>" data-contact-name="<?php echo htmlspecialchars($contact['name']); ?>" data-contact-type="<?php echo $contact['type']; ?>">
                        <img src="<?php echo $contact['image'] ? htmlspecialchars($contact['image']) : 'https://via.placeholder.com/45x45?text=' . substr($contact['name'], 0, 1); ?>" alt="Avatar" class="contact-avatar">
                        <div class="contact-info">
                            <h4><?php echo htmlspecialchars($contact['name']); ?></h4>
                            <p><?php echo $contact['type']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be loaded here -->
        </div>

        <!-- Message Input -->
        <div class="message-input" id="messageInput">
            <div class="input-group">
                <textarea class="message-text" id="messageText" placeholder="Type your message..." rows="1"></textarea>
                <button class="send-button" id="sendButton">Send</button>
            </div>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>

    <script>
        // Firebase Configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
            apiKey: "AIzaSyANSowcukFyt_9kQXRh_lIbGVNgSz8M9P0",
            authDomain: "rent-master-ddbd2.firebaseapp.com",
            projectId: "rent-master-ddbd2",
            storageBucket: "rent-master-ddbd2.firebasestorage.app",
            messagingSenderId: "96578548722",
            appId: "1:96578548722:web:1ac0f88c84a79b59ce316b",
            measurementId: "G-FE8EDXWQKB"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const db = firebase.firestore();

        // Current user and chat data
        const currentUserId = <?php echo $is_logged_in ? $user_id : 'null'; ?>;
        const currentUserName = "<?php echo $is_logged_in ? addslashes($user['user_name']) : 'Guest'; ?>";
        const currentUserImage = "<?php echo $is_logged_in ? addslashes($user['user_image']) : ''; ?>";
        const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
        let currentChatId = null;
        let currentContactId = null;
        let unsubscribe = null;

        // DOM Elements
        const chatButton = document.getElementById('chatButton');
        const chatContainer = document.getElementById('chatContainer');
        const closeChat = document.getElementById('closeChat');
        const contactList = document.getElementById('contactList');
        const chatMessages = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const messageText = document.getElementById('messageText');
        const sendButton = document.getElementById('sendButton');
        const backButton = document.getElementById('backButton');
        const chatTitle = document.getElementById('chatTitle');

        // Event Listeners
        chatButton.addEventListener('click', toggleChat);
        closeChat.addEventListener('click', toggleChat);
        sendButton.addEventListener('click', sendMessage);
        backButton.addEventListener('click', showContactList);

        // Contact item click listeners
        document.querySelectorAll('.contact-item').forEach(item => {
            item.addEventListener('click', function() {
                const contactId = this.getAttribute('data-contact-id');
                const contactName = this.getAttribute('data-contact-name');
                openChat(contactId, contactName);
            });
        });

        // Enter key to send message
        messageText.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-resize textarea
        messageText.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        function toggleChat() {
            if (chatContainer.style.display === 'none' || chatContainer.style.display === '') {
                chatContainer.style.display = 'flex';
                showContactList();
            } else {
                chatContainer.style.display = 'none';
                if (unsubscribe) {
                    unsubscribe();
                }
            }
        }

        function showContactList() {
            contactList.style.display = 'block';
            chatMessages.style.display = 'none';
            messageInput.style.display = 'none';
            backButton.style.display = 'none';
            chatTitle.textContent = 'Messages';

            if (unsubscribe) {
                unsubscribe();
            }
        }

        function openChat(contactId, contactName) {
            currentContactId = contactId;
            currentChatId = generateChatId(currentUserId, contactId);

            // Update UI
            contactList.style.display = 'none';
            chatMessages.style.display = 'block';
            messageInput.style.display = 'block';
            backButton.style.display = 'inline-block';
            chatTitle.textContent = contactName;

            // Clear previous messages
            chatMessages.innerHTML = '';

            // Load messages
            loadMessages();
        }

        function generateChatId(userId1, userId2) {
            // Create consistent chat ID regardless of order
            return userId1 < userId2 ? `${userId1}_${userId2}` : `${userId2}_${userId1}`;
        }

        function loadMessages() {
            if (unsubscribe) {
                unsubscribe();
            }

            unsubscribe = db.collection('chats')
                .doc(currentChatId)
                .collection('messages')
                .orderBy('timestamp', 'asc')
                .onSnapshot((snapshot) => {
                    chatMessages.innerHTML = '';
                    snapshot.forEach((doc) => {
                        const message = doc.data();
                        displayMessage(message);
                    });
                    scrollToBottom();
                });
        }

        // Updated displayMessage function - replace the existing one
        function displayMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${message.senderId == currentUserId ? 'sent' : 'received'}`;

            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';
            messageContent.textContent = message.text;

            const messageTime = document.createElement('div');
            messageTime.className = 'message-time';
            if (message.timestamp) {
                messageTime.textContent = new Date(message.timestamp.toDate()).toLocaleTimeString();
            }

            // Add content first, then time outside the bubble
            messageDiv.appendChild(messageContent);
            messageDiv.appendChild(messageTime); // Time is now outside the message-content div

            chatMessages.appendChild(messageDiv);
        }

        function sendMessage() {
            const text = messageText.value.trim();
            if (!text || !currentChatId) return;

            const message = {
                text: text,
                senderId: currentUserId,
                senderName: currentUserName,
                senderImage: currentUserImage,
                receiverId: currentContactId,
                timestamp: firebase.firestore.FieldValue.serverTimestamp()
            };

            // Add message to Firestore
            db.collection('chats')
                .doc(currentChatId)
                .collection('messages')
                .add(message)
                .then(() => {
                    messageText.value = '';
                    messageText.style.height = 'auto';
                })
                .catch((error) => {
                    console.error('Error sending message:', error);
                    alert('Failed to send message. Please try again.');
                });

            // Update chat metadata
            db.collection('chats').doc(currentChatId).set({
                participants: [currentUserId, currentContactId],
                lastMessage: text,
                lastMessageTime: firebase.firestore.FieldValue.serverTimestamp(),
                lastMessageSender: currentUserId
            }, {
                merge: true
            });
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Close chat when clicking outside
        document.addEventListener('click', function(e) {
            if (!chatContainer.contains(e.target) && !chatButton.contains(e.target)) {
                if (chatContainer.style.display === 'flex') {
                    chatContainer.style.display = 'none';
                    if (unsubscribe) {
                        unsubscribe();
                    }
                }
            }
        });
    </script>
</body>

</html>