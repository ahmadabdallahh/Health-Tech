<?php
session_start();

// Check if the doctor is logged in
if (!isset($_SESSION['email'])) {
    // If the doctor is not logged in, redirect to the login page
    header('Location: log-in.php');
    exit;
}

include 'config.php';

// Get the doctor's email from the session
$email = $_SESSION['email'];

// Step 1: Select the doctor's details from the 'doctors' table using the email
$sql_doctor = "SELECT * FROM doctors WHERE email = ?";
$stmt_doctor = $conn->prepare($sql_doctor);
$stmt_doctor->bind_param("s", $email);
$stmt_doctor->execute();
$result_doctor = $stmt_doctor->get_result();

// Step 2: Check if the doctor details are found
if ($result_doctor->num_rows === 1) {
    $doctor = $result_doctor->fetch_assoc();
} else {
    echo "Doctor details not found.";
    exit;
}

// Handle the approval/rejection of appointments
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    if ($action === 'reject') {
        // Delete the appointment if rejected
        $sql_delete = "DELETE FROM appointments WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $appointment_id);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            echo "<script>alert('Appointment rejected and deleted from the database.');</script>";
        } else {
            echo "<script>alert('Failed to reject the appointment.');</script>";
        }
    } else {
        // Update the appointment status in the database to 'approved'
        $sql_update = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $action, $appointment_id);
        $stmt_update->execute();

        // Get the patient's email to send the notification
        $sql_appointment = "SELECT * FROM appointments WHERE id = ?";
        $stmt_appointment = $conn->prepare($sql_appointment);
        $stmt_appointment->bind_param("i", $appointment_id);
        $stmt_appointment->execute();
        $result_appointment = $stmt_appointment->get_result();
        $appointment = $result_appointment->fetch_assoc();

        $patient_email = $appointment['email'];

        // Define the notification message
        $notification_message = 'Your appointment has been approved.';

        // Insert the notification into the notifications table
        $sql_notification = "INSERT INTO notifications (user_email, message) VALUES (?, ?)";
        $stmt_notification = $conn->prepare($sql_notification);
        $stmt_notification->bind_param("ss", $patient_email, $notification_message);
        $stmt_notification->execute();

        if ($stmt_notification->affected_rows > 0) {
            echo "<script>alert('Notification sent to the patient.');</script>";
        } else {
            echo "<script>alert('Failed to send notification.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="doctor.css">
    <style>
        .appointment {
            padding: 20px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .appointment.approved {
            background-color: #d4edda; /* Light Green */
            border-color: #c3e6cb;
        }
        .appointment.rejected {
            background-color: #f8d7da; /* Light Red */
            border-color: #f5c6cb;
        }
        .forum-link {
            display: inline-block; /* لتغيير العرض */
            background-color: #24a1d7; /* لون الخلفية */
            color: #fff; /* لون النص */
            padding: 10px 20px; /* مسافات داخلية */
            text-decoration: none; /* إزالة التسطير */
            border-radius: 5px; /* جعل الزوايا مستديرة */
            font-weight: bold; /* جعل النص عريضًا */
            font-size: 18px; /* حجم النص */
            transition: 0.3s ease all; /* تأثير الانتقال عند التمرير */
        }

        .forum-link:hover {
            background-color: #167bc3; /* لون الخلفية عند التمرير */
            transform: scale(1.1); /* تكبير الرابط عند التمرير */
        }

    </style>
</head>
<body>
    <h2>Welcome, Dr. <?php echo htmlspecialchars($doctor['name']); ?></h2>
    <h3>Doctor Details</h3>
    <p><b>Name:</b> <?php echo htmlspecialchars($doctor['name']); ?></p>
    <p><b>Email:</b> <?php echo htmlspecialchars($doctor['email']); ?></p>
    <p><b>Phone:</b> <?php echo htmlspecialchars($doctor['phone']); ?></p>
    <p><b>Hospital:</b> <?php echo htmlspecialchars($doctor['hospital']); ?></p>
    <p><b>Specialty:</b> <?php echo htmlspecialchars($doctor['specialty']); ?></p>
    
    <!-- Link to Forum -->
    <a href="forum.php" class="forum-link">Go to Forum</a>
    
    <h2>Appointments</h2>
    <div class="appointments">
        <?php
        // Retrieve appointments for the logged-in doctor
        $sql_appointments = "SELECT * FROM appointments WHERE doctor = ? ORDER BY id DESC";
        $stmt_appointments = $conn->prepare($sql_appointments);
        $stmt_appointments->bind_param("s", $doctor['name']);
        $stmt_appointments->execute();
        $result_appointments = $stmt_appointments->get_result();

        if ($result_appointments->num_rows > 0) {
            while ($row = $result_appointments->fetch_assoc()) { ?>
                <div class="appointment <?php echo htmlspecialchars($row['status']); ?>">
                    <p><b>Patient Name:</b> <?php echo htmlspecialchars($row['first_name']); ?></p>
                    <p><b>Email:</b> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><b>Phone:</b> <?php echo htmlspecialchars($row['phone']); ?></p>
                    <p><b>National ID:</b> <?php echo htmlspecialchars($row['national_id']); ?></p>
                    <p><b>Age:</b> <?php echo htmlspecialchars($row['age']); ?></p>
                    <p><b>Gender:</b> <?php echo htmlspecialchars($row['gender']); ?></p>
                    <p><b>Hospital:</b> <?php echo htmlspecialchars($row['hospital']); ?></p>
                    <p><b>Specialty:</b> <?php echo htmlspecialchars($row['specialty']); ?></p>
                    <p><b>Doctor:</b> <?php echo htmlspecialchars($row['doctor']); ?></p>
                    <p><b>Appointment Date:</b> <?php echo htmlspecialchars($row['appointment_date']); ?></p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                        <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                    </form>
                </div>
                <hr>
            <?php }
        } else {
            echo "<p>No appointments found.</p>";
        }
        ?>
    </div>
</body>
</html>
