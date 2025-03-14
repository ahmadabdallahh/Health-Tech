<?php
include 'config.php';

$result = $conn->query("SELECT DISTINCT hospital FROM doctors");
$hospitals = [];

while ($row = $result->fetch_assoc()) {
    $hospitals[] = $row['hospital'];
}

echo json_encode($hospitals);
?>
