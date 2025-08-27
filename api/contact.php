<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate input
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    if (empty($message)) {
        $errors[] = 'Message is required';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }

    // Basic spam protection
    if (strlen($message) < 10) {
        echo json_encode([
            'success' => false,
            'message' => 'Message too short'
        ]);
        exit;
    }

    // Check for spam patterns
    $spamWords = ['viagra', 'casino', 'lottery', 'cheap', 'free money'];
    $messageText = strtolower($message . ' ' . $subject);

    foreach ($spamWords as $word) {
        if (strpos($messageText, $word) !== false) {
            echo json_encode([
                'success' => false,
                'message' => 'Message appears to be spam'
            ]);
            exit;
        }
    }

    // Rate limiting (simple implementation)
    $clientIP = $_SERVER['REMOTE_ADDR'];
    $rateLimit = $db->fetchOne(
        "SELECT COUNT(*) as count FROM contact_messages 
         WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) 
         AND (email = ? OR subject LIKE ?)",
        [$email, '%' . $clientIP . '%']
    );

    if ($rateLimit['count'] >= 5) {
        echo json_encode([
            'success' => false,
            'message' => 'Too many messages sent. Please try again later.'
        ]);
        exit;
    }

    // Insert message into database
    $messageId = $db->insert('contact_messages', [
        'name' => $name,
        'email' => $email,
        'subject' => $subject ?: 'Portfolio Contact',
        'message' => $message,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    if ($messageId) {
        // Optional: Send email notification to admin
        $adminEmail = $db->fetchOne("SELECT setting_value FROM site_settings WHERE setting_key = 'contact_email'");

        if ($adminEmail && !empty($adminEmail['setting_value'])) {
            $adminEmailAddress = $adminEmail['setting_value'];
            $emailSubject = "New Contact Message: " . $subject;
            $emailBody = "
                New contact message received from your portfolio:
                
                Name: {$name}
                Email: {$email}
                Subject: {$subject}
                
                Message:
                {$message}
                
                Sent at: " . date('Y-m-d H:i:s') . "
                IP Address: {$clientIP}
            ";

            $headers = [
                'From: ' . FROM_EMAIL,
                'Reply-To: ' . $email,
                'X-Mailer: PHP/' . phpversion(),
                'Content-Type: text/plain; charset=UTF-8'
            ];

            // Send email (if mail function is available)
            if (function_exists('mail')) {
                mail($adminEmailAddress, $emailSubject, $emailBody, implode("\r\n", $headers));
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your message! I will get back to you soon.'
        ]);
    } else {
        throw new Exception('Failed to save message');
    }
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. Please try again.'
    ]);
}
