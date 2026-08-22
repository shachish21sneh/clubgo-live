<?php 
require dirname(dirname(__FILE__)).'/include/eventconfig.php';

$data = json_decode(file_get_contents('php://input'), true);

if($data['mobile'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Mobile number is required!");
}
else
{
    $mobile = strip_tags(mysqli_real_escape_string($event, $data['mobile']));
    $otp = rand(100000, 999999);

    $checkExist = $event->query("SELECT id FROM tbl_otp_verification WHERE mobile = '".$mobile."' ORDER BY id DESC LIMIT 1");

    if($checkExist->num_rows != 0){
        // Update existing row
        $event->query("UPDATE tbl_otp_verification SET otp = '".$otp."', status = 0, created_at = CURRENT_TIMESTAMP WHERE mobile = '".$mobile."'");
    } else {
        // Insert new row
        $event->query("INSERT INTO tbl_otp_verification (mobile, otp) VALUES ('".$mobile."', '".$otp."')");
    }

    // TODO: Send SMS here

    $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"OTP sent successfully!", "otp"=>$otp); // Remove 'otp' key in production
}

echo json_encode($returnArr);
?>
