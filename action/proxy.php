<?php
include '../db.php';
header("Content-Type: application/json");

$query = $conn->prepare("SELECT keyPassword FROM confidential");
$query->execute();
$query->bind_result($APIKey);
$query->fetch();
$query->close();

//API key
$apiKey = $APIKey;

// Get POST data from frontend
$input = json_decode(file_get_contents("php://input"), true);

$ch = curl_init("https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=false&wait=true");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "content-type: application/json",
    "X-RapidAPI-Host: judge0-ce.p.rapidapi.com",
    "X-RapidAPI-Key: $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($input));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>