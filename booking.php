<?php
session_start();
include 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // التأكد من تسجيل الدخول
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $hospital = $_POST['hospital'];
    $specialty = $_POST['specialty'];
    $experience = $_POST['experience']; // إضافة الخبرة
    $doctor = $_POST['doctor'];
    $appointment_date = $_POST['appointment_date'];

    // إضافة الموعد لقاعدة البيانات
    $sql = "INSERT INTO appointments (first_name, hospital, specialty, experience, doctor, appointment_date, user_email) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $first_name, $hospital, $specialty, $experience, $doctor, $appointment_date, $_SESSION['email']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // إرسال التفاصيل بالبريد الإلكتروني
        $to = $_SESSION['email'];
        $subject = "تأكيد حجز الموعد";
        $message = "مرحبا، $first_name.\n\n تم حجز موعدك في المستشفى: $hospital \n التخصص: $specialty \n الخبرة المطلوبة: $experience \n الطبيب: $doctor \n التاريخ: $appointment_date.";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($to, $subject, $message, $headers)) {
            echo "تم إرسال تفاصيل الموعد إلى بريدك الإلكتروني.";
        } else {
            echo "تم الحجز بنجاح، لكن تعذر إرسال التفاصيل إلى بريدك الإلكتروني.";
        }
    } else {
        echo "حدث خطأ أثناء الحجز. حاول مرة أخرى.";
    }
}
?>

<!-- نموذج الحجز -->
<form method="POST" action="booking.php">
    <input type="text" name="first_name" placeholder="الاسم الأول" required>
    <input type="text" name="hospital" placeholder="اسم المستشفى" required>
    <input type="text" name="specialty" placeholder="التخصص" required>
    <input type="text" name="experience" placeholder="الخبرة المطلوبة" required> <!-- إضافة حقل الخبرة -->
    <input type="text" name="doctor" placeholder="اسم الطبيب" required>
    <input type="date" name="appointment_date" required>
    <button type="submit">احجز الموعد</button>
</form>
