<?php 
error_reporting(E_ALL);
ini_set('display_errors', 0);
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
require dirname( dirname(__FILE__) ) . '/firebase/baseFile.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['mobile']) || empty($data['password'])) {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Mobile number and password are required!"]);
    exit;
}

$mobile = strip_tags(mysqli_real_escape_string($event, $data['mobile']));
$password = md5(strip_tags(mysqli_real_escape_string($event, $data['password'])));
$device_token = isset($data['device_token']) ? strip_tags(mysqli_real_escape_string($event, $data['device_token'])) : '';
$device_type = isset($data['device_type']) ? strip_tags(mysqli_real_escape_string($event, $data['device_type'])) : '';

// 1 single indexed query against 105k+ rows
$userQuery = $event->query("SELECT * FROM tbl_user WHERE mobile='$mobile' LIMIT 1");
if ($userQuery && $userQuery->num_rows > 0) {
    $user = $userQuery->fetch_assoc();
    
    if ((int)$user['status'] !== 1) {
        echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Your Account is Deactivated!"]);
        exit;
    }
    
    if ($user['password'] !== $password) {
        echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Invalid Email/Mobile No or Password!!!"]);
        exit;
    }
    
    // Update device token if provided
    if (!empty($device_token) && !empty($device_type)) {
        $event->query("UPDATE tbl_user SET device_token='$device_token', device_type='$device_type' WHERE id=" . (int)$user['id']);
        $user['device_token'] = $device_token;
        $user['device_type'] = $device_type;
    }
    
    // Notification
    if (!empty($device_token) && function_exists('sendNotificationBase')) {
        $notification = [
            'title' => 'Account Login',
            'body' => ucfirst($user['name']) . ", Welcome in ClubGo"
        ];
        sendNotificationBase($device_token, $notification);
    }
    
    echo json_encode(["UserLogin" => $user, "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Login successfully!"]);
} else {
    echo json_encode(["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Invalid Email/Mobile No or Password!!!"]);
}