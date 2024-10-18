<?php

/**
 * Function to send an email using PHP's mail() function.
 *
 * @param string $to      Recipient email address.
 * @param string $subject Email subject.
 * @param string $message Email message content (HTML).
 * @param string $headers Additional headers for the email (optional).
 * @return bool True if email was successfully sent, false otherwise.
 */
function sendEmail($to, $subject, $message, $headers = '') {
    // Set the headers if not provided
    if (empty($headers)) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'From: admission@digiminds.co.za' . "\r\n" .
                    'Reply-To: admission@digiminds.co.za' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
    }

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        return true;
    } else {
        return false;
    }
}
?>
