<?php
include 'config.php'; // لربط قاعدة البيانات

if (isset($_GET['hospital']) && isset($_GET['specialty'])) {
    $hospital = $_GET['hospital'];
    $specialty = $_GET['specialty'];

    // استعلام لجلب الأطباء المتخصصين في التخصص المحدد
    $sql = "SELECT name FROM doctors WHERE hospital = ? AND specialty = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hospital, $specialty);
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    // إرجاع البيانات كـ JSON
    echo json_encode($doctors);
}
?>
