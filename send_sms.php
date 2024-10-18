<?php
// send_sms.php

// Function to send SMS via BulkSMS
function sendSMS($contact, $message) {
    // Replace these values with your actual BulkSMS credentials and endpoint
    $bulksmsUsername = 'Benni_mk_';
    $bulksmsPassword = 'Mhlangu@08';
    $bulksmsEndpoint = 'https://api.bulksms.com/v1/messages?';

    // Format phone number (add country code and remove leading 0 if necessary)
    $phoneNumber = '+27' . ltrim($contact, '0');

    // Prepare the data for the API request
    $bulksmsData = [
        'username' => $bulksmsUsername,
        'password' => $bulksmsPassword,
        'body' => $message,
        'to' => $phoneNumber,
    ];

    // Initialize cURL session
    $ch = curl_init($bulksmsEndpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bulksmsData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Add Basic Authentication headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode($bulksmsUsername . ':' . $bulksmsPassword),
    ]);

    // Execute the request and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    return $response;
}
?>
