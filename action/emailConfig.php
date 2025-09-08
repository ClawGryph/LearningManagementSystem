<?php
    define('EMAIL_SALT', 'R4nd0mV@lu3T9b4!.');
    function hashEmail($email) {
        return hash('sha256', $email . EMAIL_SALT);
    }
?>