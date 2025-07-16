<?php
include '../db.php';

if (isset($_GET['quizID'])) {
    $quizID = intval($_GET['quizID']);

    $stmt = $conn->prepare("SELECT c.option_label, c.option_text, q.question, q.correct_answer, q.question_type, q.questionID FROM quizzes qz JOIN quiz_questions q ON qz.quizID = q.quizID LEFT JOIN quiz_choices c ON q.questionID = c.questionID WHERE qz.quizID = ? ORDER BY q.questionID, c.option_label");
    $stmt->bind_param("i", $quizID);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = '';
    $questions = [];

    while ($row = $result->fetch_assoc()) {
        $qid = $row['questionID'];

        // Initialize question if not yet added
        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'question' => $row['question'],
                'question_type' => $row['question_type'],
                'correct_answer' => $row['correct_answer'],
                'choices' => [] // to store A, B, C, D
            ];
        }

        // Add choice if it's a multiple-choice type
        if ($row['question_type'] === 'multiple' && $row['option_label']) {
            $questions[$qid]['choices'][$row['option_label']] = $row['option_text'];
        }
    }

    // Now loop through grouped questions to generate output
    foreach ($questions as $qid => $qdata) {
        $output .= "<div class='edit-question-block'>";
        $output .= "<input type='hidden' name='questionID[]' value='{$qid}'>";
        $output .= "<input type='hidden' name='questionType[]' value='" . htmlspecialchars($qdata['question_type']) . "'>";

        $output .= "<label>Question:</label>";
        $output .= "<input type='text' name='questionText[]' value='" . htmlspecialchars($qdata['question']) . "' required>";

        if ($qdata['question_type'] === 'multiple') {
            foreach (['A', 'B', 'C', 'D'] as $label) {
                $value = isset($qdata['choices'][$label]) ? htmlspecialchars($qdata['choices'][$label], ENT_QUOTES) : '';
                $output .= "<label>Choice $label:</label>";
                $output .= "<input type='text' name='choice{$label}[]' value='{$value}' required>";
            }

            $output .= "<label>Correct Answer (A-D):</label>";
            $output .= "<input type='text' name='correctAnswer[]' value='" . htmlspecialchars($qdata['correct_answer']) . "' pattern='[a-dA-D]' maxlength='1' required>";
        } elseif ($qdata['question_type'] === 'truefalse') {
            $output .= "<label>Correct Answer:</label>";
            $output .= "<select name='correctAnswer[]' required>";
            $output .= "<option value=''>--Select--</option>";
            $output .= "<option value='True'" . ($qdata['correct_answer'] === 'True' ? " selected" : "") . ">True</option>";
            $output .= "<option value='False'" . ($qdata['correct_answer'] === 'False' ? " selected" : "") . ">False</option>";
            $output .= "</select>";

            // Maintain structure for alignment
            foreach (['A', 'B', 'C', 'D'] as $label) {
                $output .= "<input type='hidden' name='choice{$label}[]' value=''>";
            }
        } else {
            // Identification
            $output .= "<label>Correct Answer:</label>";
            $output .= "<input type='text' name='correctAnswer[]' value='" . htmlspecialchars($qdata['correct_answer']) . "' required>";

            foreach (['A', 'B', 'C', 'D'] as $label) {
                $output .= "<input type='hidden' name='choice{$label}[]' value=''>";
            }
        }

        $output .= "</div>";
    }

    echo $output;
}
?>
