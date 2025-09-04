<?php
header("Content-Type: application/json");

//API key
$apiKey = "9c2e724a78mshdfce4f8721eb7f8p12c000jsn6fce8755d692";

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