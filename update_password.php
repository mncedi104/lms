<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $token = htmlspecialchars($_POST['token']);
    $newPassword = htmlspecialchars($_POST['password']);

    // Validate token and new password
    if (empty($token) || empty($newPassword)) {
        die('Invalid request.');
    }

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verify token
        $stmt = $pdo->prepare('SELECT user_id FROM password_resets WHERE token = ? AND expires_at > ?');
        $stmt->execute([$token, date('Y-m-d H:i:s')]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            die('Invalid or expired token.');
        }
        $userId = $reset['user_id'];

        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hashedPassword, $userId]);

        // Delete the reset token
        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE token = ?');
        $stmt->execute([$token]);

        // Redirect to login page with success message
        echo '<script>alert("Password has been reset successfully. Redirecting to login page."); window.location.href="login.php";</script>';

    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    } finally {
        // Close the database connection
        $pdo = null;
    }
}
?>
