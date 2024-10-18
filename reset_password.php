<?php
include 'db_config.php'; // Ensure this file sets up the $pdo variable correctly

// Retrieve the token from the URL
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Output the token to check its value (for debugging)
echo htmlspecialchars($token);

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $token = $_POST['token'];

    try {
        // Ensure $pdo is defined correctly in db_config.php
        if (!$pdo) {
            throw new Exception("Database connection not initialized.");
        }

     

        // Prepare and execute query to check if the token exists and is valid
        $stmt = $pdo->prepare('SELECT email, reset_expires FROM users WHERE reset_token = ?');
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

     

        if ($user && strtotime($user['reset_expires']) > time()) { // Use strtotime for comparison
            $email = $user['email'];
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Prepare and execute query to update the user's password and clear reset fields
            $stmt = $pdo->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?');
            $stmt->execute([$hashed_password, $email]);

            echo '<script>alert("Password has been reset successfully. Redirecting to login page."); window.location.href="login.php";</script>';
        } else {
            echo '<script>alert("Invalid or expired token."); window.location.href="forgot_password.php";</script>';
        }

    } catch (PDOException $e) {
        echo '<script>alert("Connection failed: ' . $e->getMessage() . '"); window.location.href="forgot_password.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '"); window.location.href="forgot_password.php";</script>';
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
    <title>Reset Password</title>
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
        <h2>Reset Password</h2>
        <form method="post" action="reset_password.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="password" style="font-weight: bold;">New Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
