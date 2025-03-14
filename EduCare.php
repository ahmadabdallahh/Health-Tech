<?php
// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthtech"; // تأكد من اسم قاعدة البيانات

$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// بدء الجلسة
session_start();

// إضافة وظيفة تسجيل الخروج
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: log-in.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit();
}

// التحقق من وجود المستخدم في الجلسة
$loggedIn = isset($_SESSION['user_id']); // استبدل 'user_id' بالمتغير المناسب لجلسة المستخدم

$searchResult = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = trim($_POST['search']);
    $query = strtolower($query);

    // البحث في قاعدة البيانات عن المرض المدخل
    $sql = "SELECT * FROM diseases WHERE LOWER(disease_name_ar) LIKE ? OR LOWER(disease_name_en) LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchQuery = "%" . $query . "%";
    $stmt->bind_param("ss", $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResult .= "<div class='result'>
                <h3>{$row['disease_name_ar']} ({$row['disease_name_en']})</h3>
                <p><strong>الوصف:</strong> {$row['description']}</p>
                <p><strong>الوقاية:</strong> {$row['prevention']}</p>
                <p><strong>العلاج:</strong> {$row['treatment']}</p>
            </div>";
        }
    } else {
        $searchResult = "<p>لم يتم العثور على نتائج لهذا البحث.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Health Tech - EduCare</title>
    <link rel="stylesheet" href="EduCare.css">
    <style>
        @media (max-width: 768px) {
            /* تصغير حجم الخطوط والعناصر على الشاشات الصغيرة */
            header {
                flex-direction: column;
                height: auto;
                text-align: center;
            }

            .logo {
                justify-content: center;
                margin: 10px 0;
            }

            nav ul {
                flex-direction: column;
                gap: 10px;
                margin: 10px 0;
            }

            nav ul li a {
                font-size: 16px;
            }

            .get-started {
                font-size: 16px;
                padding: 8px 15px;
                margin: 10px 0;
            }
        }
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

        .logo {
            display: flex;
            align-items: center;
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
            justify-content: center;
            margin-top: 50px;
        }

        .search {
            text-align: center;
            margin-left:700px;
        }

        .search h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .search-ic input {
            width: 350px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-ic input:focus {
            border-color: #24a1d7;
            outline: none;
        }

        .search button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #24a1d7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .result {
            background-color: #ffffff;
            padding: 20px;
            margin: 20px 15%;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .result:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .result h3 {
            font-size: 24px;
            color: #24a1d7;
            margin-bottom: 10px;
            direction: rtl;
        }

        .result p {
    font-size: 18px;
    line-height: 1.8;
    color: #555;
    margin: 10px 0;
    direction: rtl;
    text-align: right;
}

.result p strong {
    color: #24a1d7;
    font-weight: 600;
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
                <li><a href="result.php">Results</a></li>
                <li class="home"><a href="EduCare.php">EduCare</a></li>
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
            <div class="search">
                <h2>Search for health information</h2>
                <form method="POST">
                    <div class="search-ic">
                        <input type="text" name="search" placeholder="Type the name of the disease" required>
                    </div>
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </section>

    <div class="results">
        <?= $searchResult ?>
    </div>
</body>
</html>

<?php
// إغلاق الاتصال بقاعدة البيانات
$conn->close(); 
?>
