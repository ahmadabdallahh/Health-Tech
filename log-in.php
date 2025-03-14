<?php
session_start();
include 'config.php'; // Include your database connection

$email_error = ""; // متغير لرسالة الخطأ
$email_value = ""; // متغير للحفاظ على قيمة الإيميل المدخل

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $email_value = $email; // حفظ قيمة البريد المدخل

    // Check the 'doctors' table
    $sqlDoctor = "SELECT * FROM doctors WHERE email = ?";
    $stmtDoctor = $conn->prepare($sqlDoctor);
    $stmtDoctor->bind_param("s", $email);
    $stmtDoctor->execute();
    $resultDoctor = $stmtDoctor->get_result();

    if ($resultDoctor->num_rows == 1) {
        $doctor = $resultDoctor->fetch_assoc();
        
        // Doctor found, log them in
        $_SESSION['doctor_id'] = $doctor['id']; // Store doctor ID in session
        $_SESSION['doctor_name'] = $doctor['name']; // Store doctor name
        $_SESSION['email'] = $doctor['email']; // Store email in session
        
        header('Location: doctor-page.php'); // Redirect to doctor page
        exit;
    }

    // If not found in 'doctors', check the 'users' table
    $sqlUser = "SELECT * FROM users WHERE email = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("s", $email);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    if ($resultUser->num_rows == 1) {
        $user = $resultUser->fetch_assoc();
        
        // User found, log them in
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name']; // Store user full name
        $_SESSION['email'] = $user['email']; // Store email in session
        
        header('Location: index.php'); // Redirect to main page
        exit;
    }

    // If neither login is successful, show error
    $email_error = "Email not found."; // تعيين رسالة الخطأ
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tech</title>
    <link rel="stylesheet" href="log-in.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .googlee{
            width: 20px;
            height: 20px;
            position: relative;
            top: 4px;
            right: 5px;
        }

    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo.png" alt="HealthTech Logo">
            <h2>HealthTech</h2>
        </div>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="appointment.php">Appointment</a></li>
                <li><a href="result.php">Results</a></li>
                <li><a href="EduCare.php">EduCare</a></li>
                <li><a href="Hospitally.php">Hospitally</a></li>
                <li><a href="forum.php">Forum</a></li> 
                <li><a href="about.html">About</a></li>
            </ul>
        </nav>
        <a href="log-in.php" class="get-started">log in</a>
    </header>

    <div class="container">
        <div class="content">
            <div class="cont-text">
                <h2>Welcome Back!</h2>
                <h5>Start your health care journey today.</h5>
            </div>

            <form id="loginForm" method="POST" action="log-in.php">
                <div class="email">
                    <input type="email" name="email" id="email" placeholder="Email" value="<?= $email_value; ?>" class="<?= $email_error ? 'error-border' : ''; ?>" required>
                    <?php if ($email_error): ?>
                        <span class="error-message"><?= $email_error; ?></span>
                    <?php endif; ?>
                </div>

                <div class="pass">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="cont-for">
                    <div class="keep-log">
                        <input type="checkbox" name="Keep in log in" id="keep">
                        <label for="keep">keep in log in</label>
                    </div>

                    <div class="for-get">
                        <a href="#for-get">Forget your Password ?</a>
                    </div>
                </div>

                <div class="log-in">
                    <input type="submit" value="Log in">
                </div>

                <div class="container-hr">
                    <hr class="hr-text" data-content="OR">
                </div>
            </form>

            <div class="google-icon">
                <div class="google">
                    <button><img src="images/google-icon.png" class="googlee" alt=""><a href="#google">Log in with Google</a></button>
                </div>
            </div>

            <div class="agree">
                <h5>By signing up, you agree to the Terms & Conditions and Privacy Policy</h5>
            </div>

            <div class="container-hr and">
                <hr class="hr-text" data-content="Do not have an account">
            </div>

            <div class="register">
                <a href="sign up/sign-up.html">Create new account</a>
            </div>
        </div>

        <div class="doctor-img">
            <img src="images/log-in-img.png" alt="">
        </div>
    </div>

    <style>
        .error-border {
            border: 2px solid red;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: block; /* تأكد من ظهور الرسالة في سطر منفصل */
        }
    </style>
</body>
</html>
