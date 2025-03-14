<?php
include 'config.php';

if ($conn) {
    echo "تم الاتصال بقاعدة البيانات بنجاح!";
} else {
    echo "فشل الاتصال بقاعدة البيانات!";
}
?>
