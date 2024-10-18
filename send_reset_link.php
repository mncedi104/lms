<?php
include 'db_config.php';
include 'send_email.php'; // Ensure this function is defined to send emails

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if email exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32)); // Generate a secure token
            $userId = $user['id'];

            // Store token in the database
            $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token) VALUES (?, ?)');
            $stmt->execute([$userId, $token]);

            $resetLink = "https://yourdomain.com/reset_password.php?token=$token";

            // Send reset link to user's email
            $subject = 'Password Reset Request';
            $message = '<html><body>';
            $message .= '<h2>Password Reset Request</h2>';
            $message .= '<p>Click the link below to reset your password:</p>';
            $message .= '<p><a href="' . $resetLink . '">' . $resetLink . '</a></p>';
            $message .= '</body></html>';

            if (sendEmail($email, $subject, $message)) {
                echo 'Reset link sent to your email address.';
            } else {
                echo 'Failed to send reset link. Please contact support.';
            }
        } else {
            echo 'No user found with that email address.';
        }

    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    } finally {
        $pdo = null;
    }
}
?>
