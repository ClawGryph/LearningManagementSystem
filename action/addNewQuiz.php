<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['quizTitle'];
    $description = $_POST['quizDescription'];
    $deadline = $_POST['quizDeadline'];

    // Insert quiz metadata
    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, deadline) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $deadline);
    $stmt->execute();
    $quizID = $stmt->insert_id;
    $stmt->close();

    if (!isset($_POST['quizTypeSelect'])) {
        die("No quizTypeSelect array found in POST");
    }

    // Loop through quizTypeSelect to catch all questions
    foreach ($_POST['quizTypeSelect'] as $index => $questionType) {
        echo "Processing question #$index of type $questionType<br>";

        // debug line
        echo "Question: " . ($_POST['question'][$index] ?? 'N/A') . "<br>";
        echo "Correct: " . ($_POST['correctAnswer'][$index] ?? 'N/A') . "<br>";
        if ($questionType === 'multiple') {
            $questionText = $_POST['question'][$index];
            $correct = $_POST['correctAnswer'][$index];

            // Insert question
            $stmt = $conn->prepare("INSERT INTO quiz_questions (quizID, question, question_type, correct_answer) VALUES (?, ?, 'multiple', ?)");
            $stmt->bind_param("iss", $quizID, $questionText, $correct);
            $stmt->execute();
            $questionID = $stmt->insert_id;
            $stmt->close();

            // Insert options
            $options = ['A', 'B', 'C', 'D'];
            foreach ($options as $opt) {
                $field = $_POST[$opt][$index] ?? null;
                if (!empty($field)) {
                    $stmt = $conn->prepare("INSERT INTO quiz_choices (questionID, option_label, option_text) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $questionID, $opt, $field);
                    $stmt->execute();
                    $stmt->close();
                }
            }

        } elseif ($questionType === 'identification') {
            $q = $_POST['identificationQuestion'][$index] ?? '';
            $a = $_POST['identificationAnswer'][$index] ?? '';

            if (!empty($q) && !empty($a)) {
                $stmt = $conn->prepare("INSERT INTO quiz_questions (quizID, question, question_type, correct_answer) VALUES (?, ?, 'identification', ?)");
                $stmt->bind_param("iss", $quizID, $q, $a);
                $stmt->execute();
                $stmt->close();
            }

        } elseif ($questionType === 'truefalse') {
            $q = $_POST['tfQuestion'][$index] ?? '';
            $a = $_POST['tfAnswer'][$index] ?? '';

            if (!empty($q) && !empty($a)) {
                $stmt = $conn->prepare("INSERT INTO quiz_questions (quizID, question, question_type, correct_answer) VALUES (?, ?, 'truefalse', ?)");
                $stmt->bind_param("iss", $quizID, $q, $a);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    echo "<script>alert('Quiz saved successfully!'); window.location.href='../instructor/instructor-landingpage.php';</script>";
}
?>