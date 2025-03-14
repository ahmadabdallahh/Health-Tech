<?php
include 'config.php'; // لربط قاعدة البيانات

if (isset($_GET['hospital'])) {
    $hospital = $_GET['hospital'];

    // استعلام لجلب التخصصات من قاعدة البيانات بناءً على المستشفى
    $sql = "SELECT DISTINCT specialty FROM doctors WHERE hospital = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hospital);
    $stmt->execute();
    $result = $stmt->get_result();

    $specialties = [];
    while ($row = $result->fetch_assoc()) {
        $specialties[] = $row['specialty'];
    }

    // إرجاع البيانات كـ JSON
    echo json_encode($specialties);
}
?>
