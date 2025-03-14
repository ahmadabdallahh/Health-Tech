<?php
session_start();
session_unset(); // حذف جميع المتغيرات في الجلسة
session_destroy(); // تدمير الجلسة
header('Location: index.php'); // إعادة توجيه المستخدم إلى الصفحة الرئيسية
exit();
?>
