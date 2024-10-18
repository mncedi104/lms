<?php
session_start();
include 'db_config.php';

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user's full name
    $stmt = $pdo->prepare('SELECT full_name FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // If user does not exist, log out and redirect to login page
        session_destroy();
        header('Location: login.php');
        exit();
    }
    $full_name = $user['full_name'];

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit();
    }

    $user_email = htmlspecialchars($_POST['user_email']);
    $contact_info = htmlspecialchars($_POST['contact_info']);
    $message = htmlspecialchars($_POST['message']);
    
    // Ensure the email is valid
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Please enter a valid email address."); window.location.href="reg.php";</script>';
        exit();
    }

    try {
        // Insert data into admin_queries table
        $stmt = $pdo->prepare("INSERT INTO admin_queries (user_id, user_name, user_email, contact_info, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $full_name, $user_email, $contact_info, $message]);

        echo '<script>alert("Your query has been submitted successfully."); window.location.href="reg.php";</script>';
    } catch (PDOException $e) {
        echo '<script>alert("Failed to submit your query. Please try again later."); window.location.href="reg.php";</script>';
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Incomplete</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">


   <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #c8f0ec;
            background-image: url('assets/images/bg_form1.png');
            background-size: cover;
            background-position: center;
            color: #1f272b;
        }
        .info {
            max-width: 800px;
            margin: 50px auto 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.85);
            text-align: center;
            color: #1f272b;
        }
        .container {
            max-width: 800px;
            margin: 30px auto 50px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.85);
            color: #1f272b;
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
    <div class="info">
        <h1>Oops, <?php echo htmlspecialchars($full_name); ?>, Your registration is pending or incomplete.</h1>
        <p style="font-weight: bold; color: #a12c2f">
            To complete your registration, please pay the <strong>R110.00 </strong> application fee to the banking details below: <br> <br>
<p style="border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
    <strong>Bank Name: ABC Bank</strong> <br>
    <strong>Account Number: 1234567890</strong> <br>
    <strong>Branch Code: 123456</strong> <br>
    Use your ID number as reference <br> <br>
</p>

            Upload your proof of payment here <a href="https://digiminds.co.za/upload_proof.php" style="color: #00a8f3">here</a>.
        </p>
        
<form method="post" action="logout.php">
    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Logout</button>
</form>

        
    </div>
    
    <div class="container">
        <h3>Already paid the admission fee? Contact Admin</h3>
        <form method="post" action="">
            <br>
            <div class="form-group">
                <label for="user_email" style="font-weight: bold;">Your Email:</label>
                <input type="email" class="form-control" id="user_email" name="user_email" required>
            </div>
            <div class="form-group">
                <label for="contact_info" style="font-weight: bold;">Contact Number:</label>
                <input type="text" class="form-control" id="contact_info" name="contact_info" required>
            </div>
            <div class="form-group">
                <label for="message" style="font-weight: bold;">Message:</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>



        <div class="back-link">
            <a href="login.php">Go Back</a>
        </div>
    </div>
</body>
</html>
