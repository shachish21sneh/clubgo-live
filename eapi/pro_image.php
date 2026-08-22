<?php 
require dirname(dirname(__FILE__)) . '/include/eventconfig.php';
$data = json_decode(file_get_contents('php://input'), true);
header('Content-type: application/json');

if (empty($data['uid']) || empty($data['img'])) {
    $returnArr = array(
        "ResponseCode" => "401",
        "Result" => "false",
        "ResponseMsg" => "User ID or image data missing!"
    );
} else {
    $uid = mysqli_real_escape_string($event, strip_tags($data['uid']));
    $imgData = $data['img'];

    // Generate unique file name
    $imgName = "profile_" . uniqid() . ".png";
    $folderPath = dirname(dirname(__FILE__)) . "/images/profile/";
    $filePath = $folderPath . $imgName;

    // Decode base64 and save image
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    $imgContent = base64_decode($imgData);

    if (file_put_contents($filePath, $imgContent)) {
        // Save only relative path or filename
        $imgDbPath = "images/profile/" . $imgName;

        $event->query("UPDATE tbl_user SET pro_pic = '$imgDbPath' WHERE id = '$uid'");


         $c = $event->query("select * from tbl_user where  `id`=".$uid."")->fetch_assoc();
        $returnArr = array("UserLogin"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Profile Image Uploaded successfully!");

        // $returnArr = array(
        //     "ResponseCode" => "200",
        //     "Result" => "true",
        //     "ResponseMsg" => "Profile Image Uploaded Successfully!",
        //     "ImagePath" => $imgDbPath
        // );
    } else {
        $returnArr = array(
            "ResponseCode" => "500",
            "Result" => "false",
            "ResponseMsg" => "Failed to save the image."
        );
    }
}

echo json_encode($returnArr);
?>
