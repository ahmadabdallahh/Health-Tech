<?php
// بدء الجلسة
session_start();

// تضمين الاتصال بقاعدة البيانات
include('config.php');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    // إذا لم يكن المستخدم مسجل دخوله، إعادة توجيهه لصفحة تسجيل الدخول
    header("Location: login.php");
    exit();
}

// جلب تفاصيل المستخدم بناءً على الـ user_id المخزن في الجلسة
$user_id = $_SESSION['user_id'];

// استعلام لجلب بيانات المستخدم من قاعدة البيانات
$query = "SELECT first_name, last_name, phone_number, email, national_id FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

// التحقق من وجود البيانات
if (mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
} else {
    // في حال لم يتم العثور على البيانات
    echo "User not found!";
    exit();
}

// استعلام لجلب المواعيد للمستخدم
$appointment_query = "SELECT * FROM appointments WHERE email = '".$user_data['email']."'";
$appointment_result = mysqli_query($conn, $appointment_query);

// التعامل مع عملية التحديث
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // تعديل البيانات الشخصية
    if (isset($_POST['update_profile'])) {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $update_query = "UPDATE users SET first_name = '$first_name', last_name = '$last_name', phone_number = '$phone_number', email = '$email' WHERE id = '$user_id'";
        mysqli_query($conn, $update_query);
    }

    // حذف الموعد
    if (isset($_POST['delete_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $delete_query = "DELETE FROM appointments WHERE id = '$appointment_id'";
        mysqli_query($conn, $delete_query);
    }

    // إعادة تحميل الصفحة بعد التعديل أو الحذف
    header("Location: profile.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-image: linear-gradient(to right, #24a1d7, #167bc3);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            margin: 20px auto;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 30px;
        }

        .profile-item {
            margin-bottom: 15px;
        }

        .profile-item label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .profile-item span,
        .profile-item input {
            display: block;
            width: 97%;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .profile-item input {
            display: none;
        }

        .profile-item button {
            margin-top: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .profile-item button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .actions button {
            margin: 2px 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #007BFF;
            color: white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <header>
        <h1>Profile</h1>
    </header>
    <div class="container">
        <div class="section">
            <h2>Account Details</h2>
            <form method="POST" action="profile.php">
                <div class="profile-item">
                    <label>Name:</label>
                    <span id="name-display"><?php echo $user_data['first_name'] . ' ' . $user_data['last_name']; ?></span>
                    <input type="text" name="first_name" id="name-input" value="<?php echo $user_data['first_name']; ?>" style="display: none;">
                    <input type="text" name="last_name" id="last-name-input" value="<?php echo $user_data['last_name']; ?>" style="display: none;">
                    <button type="button" onclick="editField('name')">Edit</button>
                </div>
                <div class="profile-item">
                    <label>Phone Number:</label>
                    <span id="phone-display"><?php echo $user_data['phone_number']; ?></span>
                    <input type="text" name="phone_number" id="phone-input" value="<?php echo $user_data['phone_number']; ?>" style="display: none;">
                    <button type="button" onclick="editField('phone')">Edit</button>
                </div>
                <div class="profile-item">
                    <label>Email:</label>
                    <span id="email-display"><?php echo $user_data['email']; ?></span>
                    <input type="email" name="email" id="email-input" value="<?php echo $user_data['email']; ?>" style="display: none;">
                    <button type="button" onclick="editField('email')">Edit</button>
                </div>
                <div class="profile-item">
                    <label>National ID:</label>
                    <span><?php echo $user_data['national_id']; ?></span>
                </div>
                <div class="profile-item">
                    <button type="submit" name="update_profile">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="section">
            <h2>Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Hospital</th>
                        <th>Specialty</th>
                        <th>Doctor</th>
                        <th>Appointment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($appointment_result) > 0) {
                        while ($appointment = mysqli_fetch_assoc($appointment_result)) {
                            echo '<tr>';
                            echo '<td>' . $user_data['first_name'] . ' ' . $user_data['last_name'] . '</td>';
                            echo '<td>' . $appointment['hospital'] . '</td>';
                            echo '<td>' . $appointment['specialty'] . '</td>';
                            echo '<td>' . $appointment['doctor'] . '</td>';
                            echo '<td>' . $appointment['appointment_date'] . '</td>';
                            echo '<td class="actions">
                                    <form method="POST" action="profile.php" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="' . $appointment['id'] . '">
                                        <button type="submit" class="delete-btn" name="delete_appointment">Delete</button>
                                    </form>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No appointments found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editField(field) {
            document.getElementById(field + '-display').style.display = 'none';
            document.getElementById(field + '-input').style.display = 'block';
        }
    </script>
</body>

</html>
