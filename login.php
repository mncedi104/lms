<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    try {
        $stmt = $pdo->prepare('SELECT id, password, role_id, is_registered, is_admin, active_status FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                if ($user['active_status'] != 1) {
                    header("Location: disabledAccounts.html");
                    exit;
                }

                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['is_registered'] = $user['is_registered'];
                $_SESSION['is_admin'] = $user['is_admin'];

                if ($user['role_id'] == 6 && $user['is_registered'] == 0 && $user['is_admin'] == 0) {
                    header("Location: reg.php");
                } elseif ($user['role_id'] == 6 && $user['is_registered'] == 1 && $user['is_admin'] == 0) {
                    header("Location: student/dmPortal.php");
                } elseif ($user['role_id'] == 1 && $user['is_admin'] == 1) {
                    header("Location: admin/adminDM.php");
                } else {
                    header("Location: login.php?error=invalid_role");
                }
                exit;
            } else {
                echo '<script>alert("Invalid password."); window.location.href="login.php";</script>';
            }
        } else {
            echo '<script>alert("Invalid email address."); window.location.href="login.php";</script>';
        }

    } catch (PDOException $e) {
        echo '<script>alert("Connection failed: ' . $e->getMessage() . '"); window.location.href="login.php";</script>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <h2>Login</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="email" style="font-weight: bold;">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password" style="font-weight: bold;">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <div class="back-link">
                <a href="forgot_password.php">Forgot Password?</a>
                <a href="https://digiminds.co.za">Main Site</a>
            </div>
        </form>
    </div>
</body>
</html>
