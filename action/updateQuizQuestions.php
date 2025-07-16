<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizID = $_POST['quizID'];
    $questionIDs = $_POST['questionID'];
    $questions = $_POST['questionText'];
    $answers = $_POST['correctAnswer'];
    $questionTypes = $_POST['questionType'];

    // Choice arrays – should be aligned in the form
    $choiceA = $_POST['choiceA'] ?? [];
    $choiceB = $_POST['choiceB'] ?? [];
    $choiceC = $_POST['choiceC'] ?? [];
    $choiceD = $_POST['choiceD'] ?? [];

    foreach ($questionIDs as $i => $questionID) {
        $questionText = $questions[$i];
        $correctAnswer = $answers[$i];
        $type = $questionTypes[$i];

        // Update the question and correct answer
        $stmt = $conn->prepare("UPDATE quiz_questions SET question = ?, correct_answer = ? WHERE questionID = ?");
        $stmt->bind_param("ssi", $questionText, $correctAnswer, $questionID);
        $stmt->execute();
        $stmt->close();

        // If it's a multiple choice question, update or insert choices
        if ($type === 'multiple') {
            $choices = [
                'A' => $choiceA[$i] ?? '',
                'B' => $choiceB[$i] ?? '',
                'C' => $choiceC[$i] ?? '',
                'D' => $choiceD[$i] ?? '',
            ];

            foreach ($choices as $label => $text) {
                // Check if the choice exists
                $checkStmt = $conn->prepare("SELECT choiceID FROM quiz_choices WHERE questionID = ? AND option_label = ?");
                $checkStmt->bind_param("is", $questionID, $label);
                $checkStmt->execute();
                $checkStmt->store_result();

                if ($checkStmt->num_rows > 0) {
                    // Update existing choice
                    $checkStmt->bind_result($choiceID);
                    $checkStmt->fetch();
                    $checkStmt->close();

                    $updateStmt = $conn->prepare("UPDATE quiz_choices SET option_text = ? WHERE choiceID = ?");
                    $updateStmt->bind_param("si", $text, $choiceID);
                    $updateStmt->execute();
                    $updateStmt->close();
                } else {
                    // Insert new choice
                    $checkStmt->close();
                    $insertStmt = $conn->prepare("INSERT INTO quiz_choices (questionID, option_label, option_text) VALUES (?, ?, ?)");
                    $insertStmt->bind_param("iss", $questionID, $label, $text);
                    $insertStmt->execute();
                    $insertStmt->close();
                }
            }
        }
    }

    echo "<script>alert('Questions updated successfully!'); window.location.href='../instructor/instructor-create-quiz.php';</script>";
}
?>