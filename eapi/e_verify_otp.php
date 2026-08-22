<?php
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
require dirname(dirname(__FILE__)) . '/firebase/baseFile.php';
$data = json_decode(file_get_contents('php://input'), true);

if ($data['mobile'] == '' || $data['otp'] == '') {
    $returnArr = array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Mobile and OTP are required!");
} else {
    $mobile = strip_tags(mysqli_real_escape_string($event, $data['mobile']));
    $otp = strip_tags(mysqli_real_escape_string($event, $data['otp']));
    $device_token = strip_tags(mysqli_real_escape_string($event, $data['device_token']));
    $device_type = strip_tags(mysqli_real_escape_string($event, $data['device_type']));
    $check = $event->query("SELECT * FROM tbl_otp_verification 
        WHERE mobile = '" . $mobile . "' 
        AND otp = '" . $otp . "' 
        AND status = 0 
        ORDER BY created_at DESC LIMIT 1");

    if ($check->num_rows != 0) {
        $data = $check->fetch_assoc();
        $createdTime = strtotime($data['created_at']);
        $now = time();
        $difference = $now - $createdTime;
        // if ($difference <= 300) // 5 minutes
        // {
            // Valid OTP
            $event->query("UPDATE tbl_otp_verification SET status = 1 WHERE id = '" . $data['id'] . "'");
            $status = $event->query("select * from tbl_user where mobile='" . $mobile . "' and status = 1");
            if ($status->num_rows != 0) {

                if ($device_token != '' and $device_type != '') {
                    $event->query("update tbl_user set device_token='" . $device_token . "', device_type='" . $device_type . "' where mobile='" . $mobile . "'");
                }

                $c = $event->query("select * from tbl_user where mobile='" . $mobile . "'  and status = 1");
                $c = $c->fetch_assoc();

                $name = $c['name'];

                $notification = [
                    'title' => 'Account Login',
                    'body' => ucfirst($name) . ", Welcome in ClubGo"
                ];
                if ($device_token != '' and $device_type != '') {
                    sendNotificationBase($device_token, $notification);
                }


                $returnArr = array("UserLogin" => $c, "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Login successfully!");
            } else {
                $returnArr = array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Your Status Deactivate!!!");
            }
            //$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"OTP Verified Successfully!");
        // } else {
        //     // Expired
        //     $event->query("UPDATE tbl_otp_verification SET status = 2 WHERE id = '" . $data['id'] . "'");
        //     $returnArr = array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "OTP Expired!");
        // }
    } else {
        $returnArr = array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Invalid OTP or Already Verified!","res"=>$difference);
    }
}

echo json_encode($returnArr);
