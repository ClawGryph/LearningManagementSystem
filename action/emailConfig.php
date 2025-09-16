<?php
    include './db.php';

    $query = $conn->prepare("SELECT saltValue FROM confidential");
    $query->execute();
    $query->bind_result($saltValue);
    $query->fetch();
    $query->close();

    define('EMAIL_SALT', $saltValue);
    function hashEmail($email) {
        return hash('sha256', $email . EMAIL_SALT);
    }
?>