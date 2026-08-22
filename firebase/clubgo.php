<?php
// Load your service account credentials
$serviceAccountFile = '/home1/a17665e9/clubgo.in/v2/firebase/clubgoapp-1619425914731-firebase-adminsdk-foydh-41460e8b32.json';
if (!file_exists($serviceAccountFile)) {
    die("Service account file not found.");
}

$serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error parsing service account file: " . json_last_error_msg());
}

$client_email = $serviceAccount['client_email'];
$private_key = $serviceAccount['private_key'];
$project_id = 'clubgoapp-1619425914731'; // Ensure this matches your Firebase project ID exactly
$device_token = 'er6i4DO-RSG8FZaczVYECL:APA91bGBpwO5jc5eTizUyZuEiyCt2cALri2XwuKqHgiUknE3IXvHrSMHRzDMxqNiRKkf72uyBX4CCA2ZeA4hEG_fM-A7Xn_glsr_8Lo5gVjw02z_dhW85VY';

// Function to log errors
function log_error($message) {
    echo $message . "\n";
    error_log($message);
}

// Function to create a JWT for OAuth 2.0
function create_jwt($client_email, $private_key) {
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    $now = time();
    $expires = $now + 3600; // Token valid for 1 hour

    $payload = [
        'iss' => $client_email,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $expires
    ];

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
    $signature = '';

    if (!openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $private_key, 'SHA256')) {
        log_error("OpenSSL error: " . openssl_error_string());
        return null;
    }

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

// Function to get access token
function get_access_token($jwt) {
    $url = 'https://oauth2.googleapis.com/token';
    $data = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        log_error("HTTP request failed. Error: " . $error['message']);
        return null;
    }

    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_error("Error parsing access token response: " . json_last_error_msg());
        return null;
    }

    if (isset($response['error'])) {
        log_error("Error getting access token: " . $response['error_description']);
        return null;
    }

    return $response['access_token'];
}

// Function to send FCM notification
function send_fcm_notification($access_token, $project_id, $device_token, $notification) {
    $url = "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send";
    log_error("URL being accessed: $url"); // Debugging line

    $data = [
        'message' => [
            'token' => $device_token,
            'notification' => $notification
        ]
    ];

    $options = [
        'http' => [
            'header' => "Authorization: Bearer {$access_token}\r\nContent-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        log_error("HTTP request failed. Error: " . $error['message']);
        return null;
    }

    $response = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_error("Error parsing FCM response: " . json_last_error_msg());
        return null;
    }

    if (isset($response['error'])) {
        log_error("Error sending FCM notification: " . $response['error']['message']);
        return null;
    }

    return $result;
}

// Generate JWT
$jwt = create_jwt($client_email, $private_key);
if (!$jwt) {
    die("Error generating JWT.");
}

// Get access token
$access_token = get_access_token($jwt);
if (!$access_token) {
    die("Error getting access token.");
}

// Prepare notification data
$notification = [
    'title' => 'Test Notification',
    'body' => 'This is a test message'
];

// Send FCM notification
$response = send_fcm_notification($access_token, $project_id, $device_token, $notification);
if (!$response) {
    die("Error sending FCM notification.");
}

// Display the response
echo $response;

?>