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

    $count = 1;

    // Now loop through grouped questions to generate output
    foreach ($questions as $qid => $qdata) {
        $output .= "<div class='edit-question-block quizInputsContainer'>";

        $output .= "<h3>Question # {$count}</h3>";

        $output .= "<input type='hidden' name='questionID[]' value='{$qid}'>";
        $output .= "<input type='hidden' name='questionType[]' value='" . htmlspecialchars($qdata['question_type']) . "'>";

        $output .= "<div class='inputGroup'>";
        $output .= "<input type='text' name='questionText[]' value='" . htmlspecialchars($qdata['question']) . "' required>";
        $output .= "<label for='questionText'>Question:</label>";
        $output .= "</div>";

        if ($qdata['question_type'] === 'multiple') {
            foreach (['A', 'B', 'C', 'D'] as $label) {
                $value = isset($qdata['choices'][$label]) ? htmlspecialchars($qdata['choices'][$label], ENT_QUOTES) : '';
                $output .= "<div class='inputGroup'>";
                $output .= "<input type='text' name='choice{$label}[]' value='{$value}' required>";
                $output .= "<label for='choice'>Choice $label:</label>";
                $output .= "</div>";
            }

            $output .= "<div class='inputGroup'>";
            $output .= "<input type='text' name='correctAnswer[]' value='" . htmlspecialchars($qdata['correct_answer']) . "' pattern='[a-dA-D]' maxlength='1' required>";
            $output .= "<label for='correctAnswer'>Correct Answer (A-D):</label>";
            $output .= "</div>";
        } elseif ($qdata['question_type'] === 'truefalse') {
            $output .= "<div class='inputNoToggle'>";
            $output .= "<label for='correctAnswer'>Correct Answer:</label>";
            $output .= "<select name='correctAnswer[]' required>";
            $output .= "<option value=''>--Select--</option>";
            $output .= "<option value='True'" . ($qdata['correct_answer'] === 'True' ? " selected" : "") . ">True</option>";
            $output .= "<option value='False'" . ($qdata['correct_answer'] === 'False' ? " selected" : "") . ">False</option>";
            $output .= "</select>";
            $output .= "</div>";

            // Maintain structure for alignment
            foreach (['A', 'B', 'C', 'D'] as $label) {
                $output .= "<input type='hidden' name='choice{$label}[]' value=''>";
            }
        } else {
            // Identification
            $output .= "<div class='inputGroup'>";
            $output .= "<input type='text' name='correctAnswer[]' value='" . htmlspecialchars($qdata['correct_answer']) . "' required>";
            $output .= "<label for='correctAnswer'>Correct Answer:</label>";
            $output .= "</div>";

            foreach (['A', 'B', 'C', 'D'] as $label) {
                $output .= "<input type='hidden' name='choice{$label}[]' value=''>";
            }
        }

        $output .= "</div>";
        $count++;
    }

    echo $output;
}
?>
