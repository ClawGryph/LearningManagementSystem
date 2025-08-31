<?php
include '../db.php';
session_start();

if (!isset($_GET['activityID'])) {
    die("Activity not specified.");
}

$activityID = intval($_GET['activityID']);

// Fetch activity details
$sql = "SELECT pa.title, pa.instructions, pa.expected_output, pa.language, aa.assessment_time
        FROM programming_activity pa
        JOIN assessment_author aa ON pa.activityID = aa.assessment_refID
        WHERE activityID = ? AND aa.assessment_type = 'activity' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $activityID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Activity not found.");
}

$activity = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student | Activity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheer" href="../css/normalize.css">
</head>
<body>
    <div class="activity-container">
        <div class="instructions">
            <h2><?= htmlspecialchars($activity['title']) ?></h2>
            <h3>Instructions</h3>
            <p><?= nl2br(htmlspecialchars($activity['instructions'])) ?></p>

            <h3>Expected Output</h3>
            <pre><?= htmlspecialchars($activity['expected_output']) ?></pre>

            <h4>Language: <span id="lang"><?= htmlspecialchars($activity['language']) ?></span></h4>
        </div>

        <div class="compiler">
            <div class="compiler-header">
                <h2>Online Compiler</h2>
                <div id="timer" class="countdown-timer"></div>
                <button type="button" id="toggleFullscreen" title="Fullscreen"><i class="fa-solid fa-up-right-and-down-left-from-center"></i></button>
            </div>
            
            <form id="compilerForm" method="POST">
                <textarea id="compilerInput" name="code_submission" placeholder="Write your <?= htmlspecialchars($activity['language']) ?> code here..."></textarea>
                <input type="hidden" name="activityID" value="<?= $activityID ?>">
                <div class="output-box" id="output"></div>

                <div class="compiler-controls">
                    <button type="button" class="compiler-btn btn-drk-bg" id="runBtn">Run Code</button>
                    <button type="submit" class="compiler-btn btn-accent-bg">Submit Code</button>
                </div>
            </form>
        </div>
    </div>

    <!-- External JS -->
    <script>
        const activityLanguage = "<?= $activity['language'] ?>"; 
        const assessmentTimeMinutes = <?= (int)$activity['assessment_time'] ?>; 
    </script>
    <script src="../js/compiler.js"></script>
</body>
</html>