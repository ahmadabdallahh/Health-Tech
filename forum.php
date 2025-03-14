<?php
// Start the session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "healthtech");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: log-in.php");
    exit();
}

// Retrieve the logged-in user's information
$email = $_SESSION['email'];

// Check if user is a doctor or regular user and set appropriate variables
if (isset($_SESSION['doctor_id'])) {
    $user_id = $_SESSION['doctor_id'];
    $user_name = isset($_SESSION['doctor_name']) ? $_SESSION['doctor_name'] : "Doctor";
    $source_table = 'doctors';
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "User";
    $source_table = 'users';
} else {
    echo "Error: User ID is not defined.";
    exit();
}

// Handle new question submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question_text'])) {
    $question_text = $conn->real_escape_string($_POST['question_text']);
    $question_date = date("Y-m-d H:i:s");

    // Insert question
    $insert_question = "INSERT INTO questions (question_text, question_date, user_id, source_table) 
                        VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_question);
    $stmt->bind_param("ssis", $question_text, $question_date, $user_id, $source_table);
    
    if ($stmt->execute()) {
        header("Location: forum.php");
        exit();
    } else {
        echo "Error while adding the question: " . $stmt->error;
    }
    $stmt->close();
}

// Handle new answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer_text']) && isset($_POST['question_id'])) {
    $answer_text = $conn->real_escape_string($_POST['answer_text']);
    $question_id = intval($_POST['question_id']);
    $answer_date = date("Y-m-d H:i:s");

    // Insert answer
    $insert_answer = "INSERT INTO answers (answer_text, answer_date, question_id, submitted_by, source_table) 
                      VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_answer);
    $stmt->bind_param("ssiis", $answer_text, $answer_date, $question_id, $user_id, $source_table);
    
    if ($stmt->execute()) {
        header("Location: forum.php");
        exit();
    } else {
        echo "Error while adding the answer: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all questions with answers
$questions_query = "SELECT 
    q.id, 
    q.question_text, 
    q.question_date,
    q.source_table,
    CASE 
        WHEN q.source_table = 'doctors' THEN d.name
        WHEN q.source_table = 'users' THEN u.first_name
    END AS user_name
    FROM questions q 
    LEFT JOIN users u ON q.user_id = u.id AND q.source_table = 'users'
    LEFT JOIN doctors d ON q.user_id = d.id AND q.source_table = 'doctors'
    ORDER BY q.question_date DESC";

$questions_result = $conn->query($questions_query);

if ($questions_result === false) {
    echo "Error in fetching questions: " . $conn->error;
    exit();
}

// Rest of your existing code...

    if ($questions_result->num_rows === 0) {
        $no_questions = "No questions currently in the forum.";
    } else {
        $no_questions = "";
    }

    // Handle logout
    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header("Location: log-in.php");
        exit();
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forum</title>
        <style>
            /* CSS remains the same */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Quicksand", sans-serif;
            }

            body {
                background-color: #f4f4f4;
                min-width: 100%;
                height: 100%;
            }

            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #ffffff20;
                padding: 20px;
                height: 90px;
                box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .logo {
                opacity: 0.9;
                display: flex;
                align-items: center;
            }

            .logo img {
                height: 50px;
                margin: 0 10px 0 17px;
                transition: 0.5s ease all;
            }

            .logo img:hover {
                transform: scale(1.2);
            }

            header .logo h2 {
                font-size: 25px;
                font-weight: 600;
            }

            nav ul {
                list-style-type: none;
                display: flex;
                gap: 30px;
                margin: 0 0 0 50px;
            }

            nav ul li a {
                text-decoration: none;
                color: #333;
                font-weight: 600;
                font-size: 20px;
            }

            nav ul .home {
                background-color: #24a1d7;
                height: 28px;
                color: white;
                padding: 3px;
                border-radius: 5px;
            }

            nav ul li a:hover {
                padding: 0 0 10px 0;
                border-bottom: 2px solid #0056b3;
            }

            .login-btn {
                width: 150px;
            }

            .get-started {
                background-image: linear-gradient(to right , #24a1d7 , #167bc3);
                color: #fff;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                transition: 0.5s ease all;
                font-weight: 500;
                font-size: 20px;
                margin: 0 20px 0 0 ;
                cursor: pointer;
            }

            .get-started:hover {
                transform: scale(1.1);
            }
            
            .container {
                width: 80%;
                margin: 0 auto;
                padding-top: 30px;
            }

            h1 {
                text-align: center;
                color: #333;
                margin-bottom:40px;
            }

            .question-box {
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .question-box h3 {
                font-size: 20px;
                color: #333;
            }

            .date {
                color: #777;
                font-size: 14px;
            }

            .answer-box {
                background-color: #f9f9f9;
                margin-top: 20px;
                padding: 15px;
                border-left: 5px solid #007BFF;
            }

            .answer-box h4 {
                font-size: 18px;
                color: #007BFF;
            }

            .comment-form {
                margin-top: 20px;
            }

            textarea {
                width: 100%;
                height: 100px;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                margin-bottom: 10px;
            }

            button {
                background-color: #007BFF;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }

            button:hover {
                background-color: #0056b3;
            }

            .question-box, .answer-box {
                margin-bottom: 20px;
            }


        </style>
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
                    <li><a href="appointment.php">Appointments</a></li>
                    <li><a href="result.php">Results</a></li>
                    <li><a href="EduCare.php">EduCare</a></li>
                    <li><a href="Hospitally.php">Hospitally</a></li>
                    <li><a class="home" href="forum.php">Forum</a></li>
                    <li><a href="about.html">About</a></li>
                </ul>
            </nav>
            <a href="forum.php?logout=true" class="get-started">Log out</a>
        </header>

        <main>
            <div class="container">
                <h1>Discussion Forum between Users and Doctors</h1>

                <!-- Add question -->
                <div class="question-box">
                    <h3>Hello <?php echo htmlspecialchars($user_name); ?>:</h3>
                    <form method="POST" action="">
                        <textarea name="question_text" placeholder="Write your question here..." required></textarea>
                        <button type="submit">Submit Question</button>
                    </form>
                </div>

                <!-- Display questions and answers -->
                <div id="questions-container">
                    <?php if (!empty($no_questions)) : ?>
                        <p><?php echo $no_questions; ?></p>
                    <?php else: ?>
                        <?php while ($question = $questions_result->fetch_assoc()): ?>
                            <div class="question-box">
                                <h3><?php echo htmlspecialchars($question['user_name']); ?>:</h3>
                                <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                                <span class="date">Question Date: <?php echo htmlspecialchars($question['question_date']); ?></span>

                                <!-- Fetch and display answers for the question -->
                                <?php
                                $question_id = $question['id'];
                                $answers_query = "SELECT a.answer_text, a.answer_date, 
                                                        COALESCE(u.first_name, d.name) AS user_name 
                                                FROM answers a 
                                                LEFT JOIN users u ON a.submitted_by = u.id 
                                                LEFT JOIN doctors d ON a.submitted_by = d.id 
                                                WHERE a.question_id = $question_id";
                                $answers_result = $conn->query($answers_query);
                                ?>

                                <div class="answers-container">
                                    <?php while ($answer = $answers_result->fetch_assoc()): ?>
                                        <div class="answer-box">
                                            <h4>Answer by <?php echo htmlspecialchars($answer['user_name']); ?>:</h4>
                                            <p><?php echo htmlspecialchars($answer['answer_text']); ?></p>
                                            <span class="date">Answer Date: <?php echo htmlspecialchars($answer['answer_date']); ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>

                                <!-- Add answer -->
                                <div class="comment-form">
                                    <form method="POST" action="">
                                        <textarea name="answer_text" placeholder="Write your answer here..." required></textarea>
                                        <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                                        <button type="submit">Submit Answer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </body>
    </html>
