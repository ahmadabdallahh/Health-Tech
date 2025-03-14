<?php
include 'config.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'save') {
        // تحقق من البريد الإلكتروني في جدول users و doctors
        if (isset($_POST['email'])) {
            $email = $_POST['email'];

            // تحقق من وجود البريد في جدول المستخدمين
            $email_check_query_users = "SELECT id FROM users WHERE email = '$email'";  
            $email_check_result_users = $conn->query($email_check_query_users);

            // تحقق من وجود البريد في جدول الأطباء
            $email_check_query_doctors = "SELECT id FROM doctors WHERE email = '$email'";  
            $email_check_result_doctors = $conn->query($email_check_query_doctors);

            if ($email_check_result_users->num_rows > 0 || $email_check_result_doctors->num_rows > 0) {
                echo json_encode(["error" => "This email is already registered. Please use another one."]);
                exit();
            }
        }

        // تحقق من الرقم القومي في جدول users فقط
        if (isset($_POST['national_id'])) {
            $national_id = $_POST['national_id'];
            $national_id_check_query = "SELECT id FROM users WHERE national_id = '$national_id'";  
            $national_id_check_result = $conn->query($national_id_check_query);

            if ($national_id_check_result->num_rows > 0) {
                echo json_encode(["error" => "This national ID is already registered. Please use another one."]);
                exit();
            }
        }

        // إذا لم يكن البريد الإلكتروني والرقم القومي موجودين، نفذ عملية الحفظ في جدول users
        $columns = array_keys($_POST);
        $values = array_values($_POST);

        $columnsList = "`" . implode("`, `", $columns) . "`";
        $valuesList = "'" . implode("', '", array_map([$conn, 'real_escape_string'], $values)) . "'";

        $sql = "INSERT INTO `users` ($columnsList) VALUES ($valuesList)";  

        if ($conn->query($sql) === TRUE) {
            // استرجاع بيانات المستخدم المسجل حديثًا
            $user_id = $conn->insert_id;
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['first_name'] = $_POST['first_name'];
            $_SESSION['last_name'] = $_POST['last_name'];

            echo json_encode(["success" => "Data saved successfully."]);
        } else {
            echo json_encode(["error" => "Error during saving: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "Invalid action."]);
    }
} else {
    echo json_encode(["error" => "Action not specified."]);
}

$conn->close();
?>
