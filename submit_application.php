<?php
include 'db_config.php';
include 'send_email.php';
include 'send_sms.php'; // Assuming you have this function in send_sms.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $dob = htmlspecialchars($_POST['dob']);
    $id_number = htmlspecialchars($_POST['id_number']);
    $address = htmlspecialchars($_POST['address']);
    $contact_number = htmlspecialchars($_POST['contact_number']);
    $employment_status = htmlspecialchars($_POST['employment_status']);
    $course = htmlspecialchars($_POST['course']);

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if email already exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            // Email already exists
            echo '<script>alert("User already exists. Press OK to go back."); window.location.href="https://digiminds.co.za/application_form.php";</script>';
        } else {
            // Prepare and execute insert query for applications
            $stmt = $pdo->prepare('INSERT INTO applications (first_name, last_name, email, dob, id_number, address, contact_number, employment_status, course_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$first_name, $last_name, $email, $dob, $id_number, $address, $contact_number, $employment_status, $course]);

            // Generate a random password and hash it
            $password = bin2hex(random_bytes(6)); // 12-character password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into the users table
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, role_id, active_status, is_registered, course_id, password, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$first_name . ' ' . $last_name, $email, 6, 1, 0, $course, $hashed_password, 0]);

            // Get the newly inserted user's ID
            $user_id = $pdo->lastInsertId();

            // Insert login data into the privacy table
            $stmt = $pdo->prepare('INSERT INTO privacy (user_id, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$user_id, $email,  $password]);

            // Prepare email content
            $to = $email;
            $subject = 'Application Submitted Successfully';
            $message = '<html><body>';
            $message .= '<h2>Dear ' . $first_name . ' ' . $last_name . ',</h2>';
            $message .= '<p>Your application has been submitted successfully.</p>';
            $message .= '<p>Your username is: ' . htmlspecialchars($email) . '</p>';
            $message .= '<p>Your password is: ' . htmlspecialchars($password) . '</p>';
            $message .= '<p>Log in here: <a href="https://digiminds.co.za/login.php">Login</a></p>';
            $message .= '<p>Please note that you need to pay the admission fee of R110. Use your ID NUMBER as REFERENCE. Bank details are as follows:</p>';
            $message .= '<p><strong>Bank Name:</strong> ABC Bank <br>';
            $message .= '<strong>Account Number:</strong> 1234567890 <br>';
            $message .= '<strong>Branch Code:</strong> 123456 <br>';
            $message .= 'Upload proof of payment <a href="https://digiminds.co.za/upload_proof.php">here</a>.</p>';
            $message .= '<p>Kind regards,<br>Admission</p>';
            $message .= '</body></html>';

            // Send email
            if (sendEmail($to, $subject, $message)) {
                // Prepare SMS content
                $smsMessage = "Dear $first_name $last_name, your application has been submitted successfully. Your username is $email and password is $password. Log in at https://digiminds.co.za/login.php.";
                
                // Send SMS
                $smsResponse = sendSMS($contact_number, $smsMessage);

                // Process SMS API response
                $smsResponseArray = json_decode($smsResponse, true);

                if (is_array($smsResponseArray) && !empty($smsResponseArray[0]['status']['type']) && $smsResponseArray[0]['status']['type'] === 'ACCEPTED') {
                    // Redirect to thank you page
                    header('Location: thank_you.php');
                    exit();
                } else {
                    // Email sent but SMS failed
                    echo 'Email sent but failed to send SMS. Please contact support.';
                }
            } else {
                // Email sending failed
                echo 'Failed to send email. Please contact support.';
            }
        }

    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    } catch (Exception $e) {
        echo $e->getMessage();
    } finally {
        // Close the connection
        $pdo = null;
    }
}
?>
