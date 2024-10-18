<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form</title>
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
            color: #fff;
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
            margin-bottom: 5px;
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
    <div class="container">
        <h2>Application Form</h2>
        <p style="font-weight: bold; color: #a12c2f">Admission Fee: R110</p>
        <form method="post" action="submit_application.php" onsubmit="return validateForm()">
            <div class="row">
                <div class="col-md-6">
                    <!-- Personal details -->
                    <div class="form-group">
                        <label for="first_name" style="font-weight: bold;">First Name:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name" style="font-weight: bold;">Last Name:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email" style="font-weight: bold;">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="dob" style="font-weight: bold;">Date of Birth:</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="id_number" style="font-weight: bold;">ID Number:</label>
                        <input type="text" class="form-control" id="id_number" name="id_number" required>
                        <small id="id_number_help" class="form-text text-muted">Enter a valid South African ID number.</small>
                    </div>
                    <div class="form-group">
                        <label for="address" style="font-weight: bold;">Address:</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact_number" style="font-weight: bold;">Contact Number:</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="form-group">
                        <label for="employment_status" style="font-weight: bold;">Employment Status:</label>
                        <select class="form-control" id="employment_status" name="employment_status" required>
                            <option value="">Select Employment Status</option>
                            <option value="Employed">Employed</option>
                            <option value="Unemployed">Unemployed</option>
                            <option value="Self-employed">Self-employed</option>
                            <option value="Student">Student</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Course selection dropdown -->
                    <div class="form-group">
                        <label for="course" style="font-weight: bold;">Course:</label>
                        <select class="form-control" id="course" name="course" required>
                            <option value="0">Select Course</option>
                            <option value="1">Entrepreneurship</option>
                            <option value="2">Scents Course</option>
                            <option value="3">Soap Making Course</option>
                            <option value="4">Body Care Course</option>
                            <option value="5">Basic IT Course</option>
                            <option value="6">Molding Course</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </div>
        </form>

        <div class="back-link">
            <a href="https://digiminds.co.za/">Go Back to Main Site</a>
        </div>
    </div>

    <script>
        function validateForm() {
            var idNumber = document.getElementById('id_number').value.trim();
            var regex = /^\d{13}$/; // Regex pattern for South African ID number

            if (!regex.test(idNumber) || !validateIDNumber(idNumber)) {
                alert('Please enter a valid South African ID number.');
                return false;
            }

            return true;
        }

        function validateIDNumber(idNumber) {
            // Check if the length is exactly 13
            if (idNumber.length !== 13) {
                return false;
            }

            // Validate using Luhn algorithm
            var sum = 0;
            var shouldDouble = false;

            for (var i = idNumber.length - 1; i >= 0; i--) {
                var digit = parseInt(idNumber.charAt(i), 10);

                if (shouldDouble) {
                    digit *= 2;
                    if (digit > 9) {
                        digit -= 9;
                    }
                }

                sum += digit;
                shouldDouble = !shouldDouble;
            }

            return (sum % 10 === 0);
        }
    </script>

</body>
</html>
