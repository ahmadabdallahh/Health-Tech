<?php
require 'config.php';

header('Content-Type: application/json');

// التحقق من الإجراء المطلوب
$action = isset($_GET['action']) ? $_GET['action'] : '';
$table = isset($_GET['table']) ? $_GET['table'] : '';

if (!$action || !$table) {
    echo json_encode(['error' => 'الإجراء أو الجدول غير محدد.']);
    exit;
}

switch ($action) {
    case 'fetch':
        // جلب البيانات من الجدول
        $query = "SELECT * FROM $table";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo json_encode([]);
        }
        break;

        case 'get':
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0; // تأكد أن id يتم تمريره كعدد صحيح
            if (!$id) {
                echo json_encode(['error' => 'لم يتم تحديد رقم السجل.']);
                exit;
            }
        
            $query = "SELECT * FROM $table WHERE id = $id"; // تأكد أن العمود id موجود
            $result = $conn->query($query);
        
            if ($result->num_rows > 0) {
                echo json_encode($result->fetch_assoc());
            } else {
                echo json_encode(['error' => 'لم يتم العثور على السجل.']);
            }
            break;
        

    case 'getColumns':
        // جلب أسماء الأعمدة للجدول
        $query = "SHOW COLUMNS FROM $table";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $columns = [];
            while ($column = $result->fetch_assoc()) {
                $columns[] = $column['Field'];
            }
            echo json_encode($columns);
        } else {
            echo json_encode(['error' => 'لا توجد أعمدة في الجدول.']);
        }
        break;

    case 'add':
        // إضافة سجل جديد
        $fields = [];
        $values = [];

        foreach ($_POST as $key => $value) {
            $fields[] = $key;
            $values[] = "'" . $conn->real_escape_string($value) . "'";
        }

        $query = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES (" . implode(',', $values) . ")";
        if ($conn->query($query)) {
            echo json_encode(['success' => 'تمت الإضافة بنجاح.']);
        } else {
            echo json_encode(['error' => 'خطأ أثناء الإضافة: ' . $conn->error]);
        }
        break;

        case 'update':
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            if (!$id) {
                echo json_encode(['error' => 'لم يتم تحديد رقم السجل.']);
                exit;
            }
        
            $updates = [];
            foreach ($_POST as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = '" . $conn->real_escape_string($value) . "'";
                }
            }
        
            $query = "UPDATE $table SET " . implode(',', $updates) . " WHERE id = $id";
            if ($conn->query($query)) {
                echo json_encode(['success' => 'تم التحديث بنجاح.']);
            } else {
                echo json_encode(['error' => 'خطأ أثناء التحديث: ' . $conn->error]);
            }
            break;
        
            case 'delete':
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0; // تحقق من وجود id
                if (!$id) {
                    echo json_encode(['error' => 'لم يتم تحديد رقم السجل.']);
                    exit;
                }
            
                $query = "DELETE FROM $table WHERE id = $id";
                if ($conn->query($query)) {
                    echo json_encode(['success' => 'تم الحذف بنجاح.']);
                } else {
                    echo json_encode(['error' => 'خطأ أثناء الحذف: ' . $conn->error]);
                }
                break;
            
        

    default:
        echo json_encode(['error' => 'إجراء غير معروف.']);
        break;
}

$conn->close();
?>
