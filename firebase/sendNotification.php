<?php
// sendNotification.php

// Resolve service account credentials path dynamically
$serviceAccountFile = __DIR__ . '/clubgoapp-1619425914731-firebase-adminsdk-foydh-41460e8b32.json';
if (!file_exists($serviceAccountFile)) {
    $jsonFiles = glob(__DIR__ . '/*firebase-adminsdk*.json');
    if (!empty($jsonFiles)) {
        $serviceAccountFile = $jsonFiles[0];
    }
}

$client_email = null;
$private_key = null;
$project_id = 'clubgoapp-1619425914731';

if (file_exists($serviceAccountFile)) {
    $serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($serviceAccount)) {
        $client_email = $serviceAccount['client_email'] ?? null;
        $private_key = $serviceAccount['private_key'] ?? null;
        if (!empty($serviceAccount['project_id'])) {
            $project_id = $serviceAccount['project_id'];
        }
    } else {
        error_log("Error parsing service account file: " . json_last_error_msg());
    }
} else {
    error_log("Service account file not found in " . __DIR__);
}

// Common error logger (does not echo to stdout to prevent breaking JSON responses)
function log_error($message) {
    error_log("[FCM Error] " . $message);
}

// Function to create a JWT for OAuth 2.0
function create_jwt($client_email, $private_key) {
    if (empty($client_email) || empty($private_key)) {
        log_error("Missing client_email or private_key for JWT generation.");
        return null;
    }

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];

    $now = time();
    $expires = $now + 3600; // Token valid for 1 hour

    $payload = [
        'iss'   => $client_email,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $expires
    ];

    $base64UrlHeader  = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
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
    if (empty($jwt)) {
        return null;
    }

    $url = 'https://oauth2.googleapis.com/token';
    $data = [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion'  => $jwt
    ];

    $options = [
        'http' => [
            'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'        => 'POST',
            'content'       => http_build_query($data),
            'ignore_errors' => true,
            'timeout'       => 10
        ]
    ];

    $context = stream_context_create($options);
    $result  = @file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        log_error("HTTP request failed. Error: " . ($error['message'] ?? 'Unknown error'));
        return null;
    }

    $response = json_decode($result, true);
    if (isset($response['error'])) {
        log_error("Error getting access token: " . ($response['error_description'] ?? $response['error']));
        return null;
    }

    return $response['access_token'] ?? null;
}

// Function to send FCM notification
function send_fcm_notification($device_token, $notification) {
    global $client_email, $private_key, $project_id;

    if (empty($device_token) || empty($client_email) || empty($private_key) || empty($project_id)) {
        log_error("Missing required credentials or device token to send FCM notification.");
        return null;
    }

    // Generate JWT
    $jwt = create_jwt($client_email, $private_key);
    if (!$jwt) {
        log_error("Error generating JWT.");
        return null;
    }

    // Get access token
    $access_token = get_access_token($jwt);
    if (!$access_token) {
        log_error("Error getting access token.");
        return null;
    }

    $url = "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send";
    $data = [
        'message' => [
            'token'        => $device_token,
            'notification' => $notification
        ]
    ];

    $options = [
        'http' => [
            'header'        => "Authorization: Bearer {$access_token}\r\nContent-Type: application/json\r\n",
            'method'        => 'POST',
            'content'       => json_encode($data),
            'ignore_errors' => true,
            'timeout'       => 10
        ]
    ];

    $context = stream_context_create($options);
    $result  = @file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        log_error("HTTP request failed. Error: " . ($error['message'] ?? 'Unknown error'));
        return null;
    }

    return $result;
}
