<?php
//forgot_password.php
include 'db_config.php';
include 'send_email.php'; // Ensure this includes the `sendEmail` function

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the email exists
        $stmt = $pdo->prepare('SELECT email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate a unique reset token
            $token = bin2hex(random_bytes(32));
           // Set the expiration time (1 hour from now) in MySQL DATETIME format
$expires = date("Y-m-d H:i:s", time() + 3600); // Adds 1 hour from now



            // Update the user's reset token and expiry
            $stmt = $pdo->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?');
            $stmt->execute([$token, $expires, $email]);

            // Send email with the reset link
            $resetLink = "https://digiminds.co.za/reset_password.php?token=$token";
            $subject = 'Password Reset Request';
            $message = '<html><body>';
            $message .= '<p>Click the following link to reset your password:</p>';
            $message .= '<p><a href="' . $resetLink . '">Reset Password</a></p>';
            $message .= '</body></html>';

            if (sendEmail($email, $subject, $message)) {
                echo '<script>alert("A reset link has been sent to your email address."); window.location.href="forgot_password.php";</script>';
            } else {
                echo 'Failed to send email. Please contact support.';
            }
        } else {
            echo '<script>alert("No account found with that email address."); window.location.href="forgot_password.php";</script>';
        }

    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    } finally {
        // Close the connection
        $pdo = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #c8f0ec;
            background-image: url('assets/images/bg_form1.png');
            background-size: cover;
            background-position: center;
            color: #1f272b;
        }
        .login-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 100px;
            background-image: url('assets/images/bg_form.png');
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .login-container h2 {
            font-weight: bold;
            color: #a12c2f;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            background-color: #e0e0e0;
            color: #2c2c2c;
            border: none;
            border-radius: 20px;
            box-shadow: none;
            font-size: 13px;
            font-weight: 500;
            padding: 0px 15px;
            height: 40px;
            outline: none;
        }
        .form-control:focus {
            box-shadow: none;
            background-color: #d0d0d0;
        }
        .btn-primary {
            background-color: #a12c2f;
            color: #fff;
            border: none;
            border-radius: 22px;
            padding: 12px 30px;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            transition: all .3s;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .back-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            color: #00a8f3;
        }
        .back-link a {
            color: #00a8f3;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Forgot Password</h2>
        <form method="post" action="forgot_password.php">
            <div class="form-group">
                <label for="email" style="font-weight: bold;">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
