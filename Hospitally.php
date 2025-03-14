<?php
// Start session
session_start();

// Check for logout request
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: log-in.php"); // إعادة التوجيه إلى صفحة تسجيل الدخول
    exit();
}

// التحقق من وجود المستخدم في الجلسة
$loggedIn = isset($_SESSION['user_id']); // استبدل 'user_id' بالمتغير المناسب لجلسة المستخدم

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthtech";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests
if (isset($_POST['type'])) {
    $type = $_POST['type'];

    // Fetch governorates
    if ($type == 'governorates') {
        $query = "SELECT DISTINCT governorate FROM hospitals ORDER BY governorate";
        $result = $conn->query($query);
    
        $output = '<option value="">-- Select Governorate --</option>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $governorate = htmlspecialchars($row['governorate'], ENT_QUOTES, 'UTF-8');
                $output .= '<option value="' . $governorate . '">' . $governorate . '</option>';
            }
        }
        exit($output);
    }
    
    // Fetch centers
    if ($type == 'centers') {
        $governorate = $_POST['governorate'];
        $query = "SELECT DISTINCT center FROM hospitals WHERE governorate = ? ORDER BY center";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $governorate);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $output = '<option value="">-- Select Center --</option>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $center = htmlspecialchars($row['center'], ENT_QUOTES, 'UTF-8');
                $output .= '<option value="' . $center . '">' . $center . '</option>';
            }
        }
        exit($output);
    }
    
    // Fetch hospitals
    if ($type == 'hospitals') {
        $center = $_POST['center'];
        $query = "SELECT hospital_name FROM hospitals WHERE center = ? ORDER BY hospital_name";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $center);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $output = '<option value="">-- Select Hospital --</option>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hospital = htmlspecialchars($row['hospital_name'], ENT_QUOTES, 'UTF-8');
                $output .= '<option value="' . $hospital . '">' . $hospital . '</option>';
            }
        }
        exit($output);
    }

    // Fetch hospital details
    if ($type == 'hospitalDetails') {
        $hospital = $_POST['hospital'];
        $query = "SELECT * FROM hospitals WHERE hospital_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $hospital);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hospitalDetails = $result->fetch_assoc();
            $output = '<table>';
            $output .= '<tr><th>Hospital Name</th><td>' . htmlspecialchars($hospitalDetails['hospital_name']) . '</td></tr>';
            $output .= '<tr><th>Address</th><td>' . htmlspecialchars($hospitalDetails['address']) . '</td></tr>';
            $output .= '<tr><th>Phone</th><td>' . htmlspecialchars($hospitalDetails['phone']) . '</td></tr>';
            $output .= '<tr><th>Email</th><td>' . htmlspecialchars($hospitalDetails['email']) . '</td></tr>';
            $output .= '<tr><th>Specialties</th><td>' . htmlspecialchars($hospitalDetails['specialties']) . '</td></tr>';
            $output .= '</table>';
            exit($output);
        } else {
            exit("No details available for the selected hospital.");
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tech - Hospital Details</title>
    <link rel="stylesheet" href="details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .location {
            display: flex;
            flex-direction: column;
            max-width: 300px;
            margin: 20px auto;
        }
        select, button {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            width: 100%;
        }
        .submit input {
            background-color: #24a1d7;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .hospital-details {
            margin: 20px auto;
            max-width: 600px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            display: none;
            background-color: #f9f9f9;
        }
        .hospital-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .hospital-details th, .hospital-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .hospital-details th {
            width: 30%;
            background-color: #f5f5f5;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <li class="home"><a href="Hospitally.php">Hospitally</a></li>
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

    <main>
        <div class="container-hospital">
            <form>
                <div class="location">
                    <select id="governorate" name="governorate">
                        <option value="">-- Select Governorate --</option>
                    </select>

                    <select id="center" name="center" disabled>
                        <option value="">-- Select Center --</option>
                    </select>

                    <select id="hospital" name="hospital" disabled>
                        <option value="">-- Select Hospital --</option>
                    </select>
                </div>
                <div class="submit">
                    <input type="button" value="Show Hospital Details" onclick="showHospitalDetails()">
                </div>
            </form>
            <div class="hospital-details" id="hospitalDetails"></div>
        </div>
    </main>

    <script>
    $(document).ready(function() {
        // Load governorates on page load
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { type: 'governorates' },
            success: function(response) {
                $('#governorate').html(response);
            }
        });

        // Governorate change event
        $('#governorate').change(function() {
            const governorate = $(this).val();
            const $center = $('#center');
            const $hospital = $('#hospital');

            $center.prop('disabled', true).html('<option value="">-- Select Center --</option>');
            $hospital.prop('disabled', true).html('<option value="">-- Select Hospital --</option>');

            if (governorate) {
                $center.prop('disabled', false);
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: { 
                        type: 'centers',
                        governorate: governorate
                    },
                    success: function(response) {
                        $center.html(response);
                    }
                });
            }
        });

        // Center change event
        $('#center').change(function() {
            const center = $(this).val();
            const $hospital = $('#hospital');

            $hospital.prop('disabled', true).html('<option value="">-- Select Hospital --</option>');

            if (center) {
                $hospital.prop('disabled', false);
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: { 
                        type: 'hospitals',
                        center: center
                    },
                    success: function(response) {
                        $hospital.html(response);
                    }
                });
            }
        });
    });

    function showHospitalDetails() {
        const hospital = $('#hospital').val();
        if (!hospital) {
            alert('Please select a hospital first');
            return;
        }

        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { 
                type: 'hospitalDetails',
                hospital: hospital
            },
            success: function(response) {
                $('#hospitalDetails').html(response).show();
            }
        });
    }
    </script>
</body>
</html>