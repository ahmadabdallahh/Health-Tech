


<?php
session_start();
include 'config.php';

// Get user details if logged in
$user = null;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sqlUser = "SELECT * FROM users WHERE email = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("s", $email);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    $user = $resultUser->fetch_assoc();

    // Get notifications
    $sqlNotifications = "SELECT * FROM notifications WHERE user_email = ? ORDER BY created_at DESC LIMIT 1";
    $stmtNotifications = $conn->prepare($sqlNotifications);
    $stmtNotifications->bind_param("s", $email);
    $stmtNotifications->execute();
    $resultNotifications = $stmtNotifications->get_result();
    $notification = $resultNotifications->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tech</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        /* Previous styles remain */
        .get-started{
            margin: 0 30px 0 0;
        }
        .notification-box {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            text-align: center;
            z-index: 1000;
        }

        .notification-box h3 {
            margin: 0;
            font-size: 20px;
        }

        .close-btn {
            background-color: #ff4c4c;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            padding: 5px 10px;
        }

        .close-btn:hover {
            background-color: #ff1a1a;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 999;
        }

        .notification {
            position: absolute;
            top: 30px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #ff9900;
        }

        .user-menu {
            position: relative;
            z-index: 999;
            right: 35px;
            font-size: 24px;
            cursor: pointer;
        }

        .user-icon {
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 176px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown-menu a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }

        .show {
            display: block;
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
                <li class="home"><a href="index.php">Home</a></li>
                <li><a href="appointment.php">Appointment</a></li>
                <li><a href="result.php">Results</a></li>
                <li><a href="EduCare.php">EduCare</a></li>
                <li><a href="Hospitally.php">Hospitally</a></li>
                <li><a href="forum.php">Forum</a></li> 
                <li><a href="about.html">About</a></li>
            </ul>
        </nav>

        <?php if (isset($_SESSION['email'])): ?>
            <div class="user-menu">
                <i class="fas fa-user user-icon" onclick="toggleMenu()"></i>
                <div class="dropdown-menu" id="userMenu">
                    <a href="profile.php">Show Profile</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
            <a href="#" class="notification" onclick="openNotification()">
                <i class="fas fa-bell"></i>
            </a>
        <?php else: ?>
            <a href="log-in.php" class="get-started">Log In</a>
        <?php endif; ?>
    </header>

    <a href="#" class="notification" onclick="openNotification()">
            <i class="fas fa-bell"></i>
        </a>
    </header>

    <!-- الطبقة الخلفية (Overlay) -->
    <div class="overlay" id="overlay"></div>

    <!-- نافذة الإشعارات -->
    <div class="notification-box" id="notificationBox">
        <h3>
            <?php
                // عرض رسالة الإشعار إذا كانت موجودة
                echo isset($notification['message']) ? htmlspecialchars($notification['message']) : 'No new notifications';
            ?>
        </h3>
        <button class="close-btn" onclick="closeNotification()">Close</button>
    </div>
    <!-- Previous notification box and overlay code remains the same -->

    <script>
        function toggleMenu() {
            document.getElementById("userMenu").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.user-icon')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
        function openNotification() {
            document.getElementById('notificationBox').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // دالة لإغلاق نافذة الإشعار والطبقة الخلفية
        function closeNotification() {
            document.getElementById('notificationBox').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        // Previous notification functions remain the same
    </script>
    <main id="home">
        <section class="hero">
            <div class="hero-content">
                <h2>Your health care. <br> starts here...</h2>
                <p>Integrated medical services that allow you to book appointments, follow up on tests, get medical consultations from anywhere, anytime and more.</p>
            </div>

            <div class="hero-image">
                <a href="#home"><img src="images/doctor-home.png" alt="Doctor Image"></a>
            </div>
        </section>
    </main>
    <!-- Previous main content remains the same -->
</body>
</html>
