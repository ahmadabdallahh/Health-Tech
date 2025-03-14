
<?php
/*
// Include the database connection
include('config.php');

// Retrieve questions with user names
$query = "SELECT q.id AS question_id, q.question_text, q.question_date, u.first_name, u.last_name 
          FROM questions q 
          JOIN users u ON q.user_id = u.id 
          ORDER BY q.question_date DESC";
$result = mysqli_query($conn, $query);

// Check if there are any questions
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='question-box'>
                <h3>" . $row['first_name'] . " " . $row['last_name'] . ":</h3>
                <p>" . $row['question_text'] . "</p>
                <p class='date'>" . $row['question_date'] . "</p>
                <div class='answer-box'>
                    <h4>Answers:</h4>";
                
                // Get answers for this question
                $question_id = $row['question_id'];
                $answerQuery = "SELECT a.answer_text, a.answer_date, u.first_name, u.last_name 
                                FROM answers a 
                                JOIN users u ON a.user_id = u.id 
                                WHERE a.question_id = $question_id 
                                ORDER BY a.answer_date DESC";
                $answerResult = mysqli_query($conn, $answerQuery);
                
                if (mysqli_num_rows($answerResult) > 0) {
                    while ($answer = mysqli_fetch_assoc($answerResult)) {
                        echo "<div class='answer-box'>
                                <h4>" . $answer['first_name'] . " " . $answer['last_name'] . ":</h4>
                                <p>" . $answer['answer_text'] . "</p>
                                <p class='date'>" . $answer['answer_date'] . "</p>
                            </div>";
                    }
                } else {
                    echo "<p>No answers yet.</p>";
                }

        echo "</div>
            </div>";
    }
} else {
    echo "<p>No questions found.</p>";
}

// Close database connection
mysqli_close($conn);
?>
