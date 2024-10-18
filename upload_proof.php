<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Proof of Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #c8f0ec;
            background-image: url('assets/images/bg_form1.png'); /* Replace 'your-background-image-url.jpg' with your actual image URL */
            background-size: cover;
            background-position: center;
            color: #1f272b; /* Text color */
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 50px;
            background-image: url('assets/images/bg_form.png');
            background-size: cover;
            background-position: center;
            color: #fff; /* Text color for the form */
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            background-color: #f7f7f7; /* Matches the style for input fields */
            color: #7a7a7a; /* Matches the style for input fields */
            border: none; /* Removes default border */
            border-radius: 20px; /* Rounded corners */
            box-shadow: none; /* Removes any box-shadow */
            font-size: 13px; /* Matches the font-size */
            font-weight: 500; /* Matches the font-weight */
            padding: 0px 15px; /* Matches the padding */
            height: 40px; /* Matches the height */
            outline: none; /* Removes outline on focus */
        }
        .form-control:focus {
            box-shadow: none; /* Keeps box-shadow off even when focused */
        }
        .btn-primary {
            background-color: #a12c2f; /* Matches the button background color */
            color: #fff; /* Matches the button text color */
            border: none; /* Removes default button border */
            border-radius: 22px; /* Rounded corners */
            padding: 12px 30px; /* Matches the padding */
            font-size: 13px; /* Matches the font-size */
            font-weight: 500; /* Matches the font-weight */
            text-transform: uppercase; /* Matches the text-transform */
            transition: all .3s; /* Smooth transition on hover */
        }
        .btn-primary:hover {
            opacity: 0.9; /* Matches the hover opacity effect */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Proof of Payment</h2>

        <?php
        include 'db_config.php';

        // Initialize variables for form values
        $id_number = $contact_number = '';

        // Check if form submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get form data
            $id_number = htmlspecialchars($_POST['id_number']);
            $contact_number = htmlspecialchars($_POST['contact_number']);

            // File upload handling
            $targetDir = "uploads/";
            $fileName = basename($_FILES["proof_of_payment"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Allow certain file formats
            $allowTypes = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
            if (!in_array(strtolower($fileType), $allowTypes)) {
                echo '<div class="alert alert-danger" role="alert">Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed.</div>';
            } elseif ($_FILES['proof_of_payment']['size'] > 5242880) { // Check file size (5MB)
                echo '<div class="alert alert-danger" role="alert">Sorry, your file is too large. It should be less than 5MB.</div>';
            } else {
                // Rename file to prevent overwriting existing files
                $newFileName = uniqid() . '_' . $fileName;
                $targetFilePath = $targetDir . $newFileName;

                // Upload file to server
                if (move_uploaded_file($_FILES["proof_of_payment"]["tmp_name"], $targetFilePath)) {
                    // File uploaded successfully, now insert data into database
                    try {
                        // Connect to the database
                        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Prepare an SQL statement to insert proof of payment data
                        $stmt = $pdo->prepare('INSERT INTO proof_of_payment (id_number, contact_number, proof_file) VALUES (?, ?, ?)');
                        $stmt->execute([$id_number, $contact_number, $targetFilePath]);

                        echo '<div class="alert alert-success" role="alert">Proof of payment uploaded successfully.</div>';
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger" role="alert">Connection failed: ' . $e->getMessage() . '</div>';
                    } finally {
                        // Close the connection
                        $pdo = null;
                    }
                } else {
                    echo '<div class="alert alert-danger" role="alert">Sorry, there was an error uploading your file.</div>';
                }
            }
        }
        ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="id_number">ID Number:</label>
                <input type="text" class="form-control" id="id_number" name="id_number" value="<?php echo htmlspecialchars($id_number); ?>" required>
                <small id="id_number_help" class="form-text text-muted">Enter a valid South African ID number.</small>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>" required>
            </div>
            <div class="form-group">
                <label for="proof_of_payment">Proof of Payment (Image or PDF):</label>
                <input type="file" class="form-control-file" id="proof_of_payment" name="proof_of_payment" accept="image/*, .pdf" required>
                <small id="proof_help" class="form-text text-muted">Upload a file (image or PDF) less than 5MB.</small>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
