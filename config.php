<?php
// إعدادات الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";  // اسم المستخدم الافتراضي لـ XAMPP
$password = "";      // كلمة المرور الافتراضية تكون فارغة
$dbname = "healthtech";  // اسم قاعدة البيانات اللي أنشأتها

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>
