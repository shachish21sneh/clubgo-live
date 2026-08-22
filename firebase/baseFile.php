<?php
require_once __DIR__ . '/sendNotification.php';

/**
 * Base function to send notification
 * 
 * @param string $device_token  The device FCM token
 * @param array  $notification  Notification array ["title" => "...", "body" => "..."]
 * @return string|null          FCM response or null on failure
 */
function sendNotificationBase($device_token, $notification) {
    return send_fcm_notification($device_token, $notification);
}

// $token = 'er6i4DO-RSG8FZaczVYECL:APA91bGBpwO5jc5eTizUyZuEiyCt2cALri2XwuKqHgiUknE3IXvHrSMHRzDMxqNiRKkf72uyBX4CCA2ZeA4hEG_fM-A7Xn_glsr_8Lo5gVjw02z_dhW85VY';
// $notification = [
//     'title' => 'Test Notification',
//     'body' => 'This is a test message'
// ];
// echo $response = sendNotificationBase($token, $notification);