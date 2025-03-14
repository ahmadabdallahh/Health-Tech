<?php
session_start();
include 'config.php'; // لربط قاعدة البيانات

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($_POST['first_name'], $_POST['email'], $_POST['phone'], 
              $_POST['national_id'], $_POST['age'], $_POST['gender'], 
              $_POST['hospital'], $_POST['specialty'], $_POST['doctor'], 
              $_POST['appointment_date'])
    ) {
        $first_name = $_POST['first_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $national_id = $_POST['national_id'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $hospital = $_POST['hospital'];
        $specialty = $_POST['specialty'];
        $doctor = $_POST['doctor'];
        $appointment_date = $_POST['appointment_date'];

        if (!empty($first_name) && !empty($email) && !empty($phone) && 
            !empty($national_id) && !empty($age) && !empty($gender) && 
            !empty($hospital) && !empty($specialty) && !empty($doctor) && 
            !empty($appointment_date)) {
            
            $sql = "INSERT INTO appointments (first_name, email, phone, national_id, age, gender, hospital, specialty, doctor, appointment_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }

            // ربط المتغيرات بالاستعلام
            $stmt->bind_param("ssssisssss", $first_name, $email, $phone, $national_id, $age, $gender, $hospital, $specialty, $doctor, $appointment_date);

            // تنفيذ الاستعلام
            if ($stmt->execute()) {
                $message = "Successfully booked!";
            } else {
                $message = "Reservation failed. Try again.";
            }
            $stmt->close(); // اغلاق الstmt بعد الاستخدام
        } else {
            $message = "All fields must be filled in.";
        }
    } else {
        $message = "The data was not received correctly.";
    }

    // عرض رسالة التنبيه وإعادة تحميل الصفحة
    echo "<script>
        alert('$message');
        window.location.href = 'appointment.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Tech</title>
    <link rel="stylesheet" href="appointments.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        // Fetch and populate hospitals when the page loads
        window.onload = function() {
            fetch('get_hospitals.php')
                .then(response => response.json())
                .then(data => {
                    const hospitalSelect = document.querySelector('select[name="hospital"]');
                    data.forEach(hospital => {
                        const option = document.createElement('option');
                        option.value = hospital;
                        option.textContent = hospital;
                        hospitalSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching hospitals:', error));
        };

        // Update specialties based on the selected hospital
        function updateSpecialties() {
            const hospitalSelect = document.querySelector('select[name="hospital"]');
            const selectedHospital = hospitalSelect.value;

            // Clear the doctor select and specialty select
            const specialtySelect = document.querySelector('select[name="specialty"]');
            specialtySelect.innerHTML = '<option value="" disabled selected>Select a Specialty</option>'; // Reset specialties
            const doctorSelect = document.querySelector('select[name="doctor"]');
            doctorSelect.innerHTML = '<option value="" disabled selected>Select a Doctor</option>'; // Reset doctors

            fetch(`get_specialties.php?hospital=${encodeURIComponent(selectedHospital)}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(specialty => {
                        const option = document.createElement('option');
                        option.value = specialty;
                        option.textContent = specialty;
                        specialtySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching specialties:', error));
        }

        // Update doctors based on the selected hospital and specialty
        function updateDoctors() {
            const hospitalSelect = document.querySelector('select[name="hospital"]');
            const specialtySelect = document.querySelector('select[name="specialty"]');
            const selectedHospital = hospitalSelect.value;
            const selectedSpecialty = specialtySelect.value;

            // Clear the doctor select before fetching new doctors
            const doctorSelect = document.querySelector('select[name="doctor"]');
            doctorSelect.innerHTML = '<option value="" disabled selected>Select a Doctor</option>'; // Reset doctors

            if (selectedHospital && selectedSpecialty) { // Only fetch doctors if both hospital and specialty are selected
                fetch(`get_doctors.php?hospital=${encodeURIComponent(selectedHospital)}&specialty=${encodeURIComponent(selectedSpecialty)}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.name;
                            option.textContent = doctor.name;
                            doctorSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching doctors:', error));
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo.png" alt="HealthTech Logo">
            <h2>HealthTech</h2>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li class="home"><a href="appointment.php">Appointment</a></li>
                <li><a href="result.php">Results</a></li>
                <li><a href="EduCare.php">EduCare</a></li>
                <li><a href="Hospitally.php">Hospitally</a></li>
                <li><a href="forum.php">Forum</a></li> 
                <li><a href="about.html">About</a></li>
            </ul>
        </nav>
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['doctor_id'])): ?>
            <a href="logout.php" class="get-started">Log out</a>
        <?php else: ?>
            <a href="log-in.php" class="get-started">Log in</a>
        <?php endif; ?>
    </header>

    <div class="container">
        <div class="content">
            <form action="appointment.php" method="POST">
                <div class="fr-name">
                    <input type="text" name="first_name" placeholder="Name" required>
                </div>

                <div class="phone">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                
                <div class="phone">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                </div>

                <div class="phone">
                    <input type="text" name="national_id" placeholder="National ID" maxlength="14" required>
                </div>

                <div class="phone">
                    <input type="number" name="age" placeholder="Age" required>
                </div>

                <div class="phone">
                    <select name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="ls-namee">
                    <select name="hospital" required onchange="updateSpecialties()">
                        <option value="" disabled selected>Select a Hospital</option>
                        <!-- Options will be added dynamically using JavaScript -->
                    </select>
                </div>
                
                <div class="ls-namee">
                    <select name="specialty" required onchange="updateDoctors()">
                        <option value="" disabled selected>Select a Specialty</option>
                        <!-- Options will be populated dynamically by JavaScript -->
                    </select>
                </div>
                
                <div class="pass">
                    <select name="doctor" required>
                        <option value="" disabled selected>Select a Doctor</option>
                        <!-- Options will be populated dynamically by JavaScript -->
                    </select>
                </div>
                
                <div class="pass">
                    <input type="datetime-local" name="appointment_date" required>
                </div>

                <div class="log-in">
                    <input type="submit" value="Book Appointment">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
