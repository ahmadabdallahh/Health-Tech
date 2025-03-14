<?php
session_start(); // Start the session to track the user login status

// Check if the user is logged in, otherwise redirect to login page


// Add logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: log-in.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit();
}

// التحقق من وجود المستخدم في الجلسة
$loggedIn = isset($_SESSION['user_id']); // استبدل 'user_id' بالمتغير المناسب لجلسة المستخدم


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tech - Results</title>
    <!-- Main CSS file -->
    <link rel="stylesheet" href="result.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f4f4f4;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #ffffff20;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
        }

        .logo h2 {
            margin-left: 10px;
            font-size: 24px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
        }

        .get-started {
            padding: 10px 20px;
            background-color: #24a1d7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .search-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .search-id input {
            width: 350px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }

        .search-con p {
            font-size: 16px;
            color: #d8d8d8;
        }

        .image-girl img {
            width: 400px;
        }

        .btn {
            padding: 5px;
            width: 70px;
            margin-left: 180px;
            margin-top: 10px;
            background-color: #24a1d7;
            border-radius: 40px;
            border: none;
            color: white;
        }

        .logout {
            padding: 10px 20px;
            background-color: #24a1d7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo.png" alt="HealthTech Logo" height="50">
            <h2>HealthTech</h2>
        </div>

        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="appointment.php">Appointment</a></li>
                <li class="home"><a href="result.php">Results</a></li>
                <li><a href="EduCare.php">EduCare</a></li>
                <li><a href="Hospitally.php">Hospitally</a></li>
                <li><a href="forum.php">Forum</a></li>
                <li><a href="about.html">About</a></li>
            </ul>
        </nav>
        <?php if ($loggedIn): ?>
            <!-- إذا كان المستخدم قد سجل الدخول، عرض زر تسجيل الخروج -->
            <a href="?logout=true" class="get-started">Log out</a>
        <?php else: ?>
            <!-- إذا لم يكن المستخدم قد سجل الدخول، عرض زر تسجيل الدخول -->
            <a href="log-in.php" class="get-started">Log in</a>
        <?php endif; ?>
    </header>

    <section>
        <div class="container">
            <div class="content">
                <div class="search-content">
                    <div class="search-id">
                        <input type="text" name="search-id" id="nationalId" placeholder="National ID" maxlength="14">
                    </div>

                    <div class="search-con">
                        <p>Please make sure the ID is correct before pressing search.<br> Test results will only be displayed if they are ready.</p>
                    </div>

                    <button onclick="checkId()" class="btn">Search</button>
                </div>
            </div>

            <div class="image-girl">
                <img src="images/image-result.png" alt="Result Image">
            </div>
        </div>
    </section>

    <script>
        function checkId() {
            var id = document.getElementById('nationalId').value;
            if (id.length === 14) {
                window.location.href = 'results-page.html'; // Redirect to results page after validation
            } else {
                alert('Please enter a valid 14-digit National ID');
            }
        }
    </script>
</body>
</html>
