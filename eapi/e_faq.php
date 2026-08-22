<?php 
require dirname( dirname(__FILE__) ).'/include/eventconfig.php';
$data = json_decode(file_get_contents('php://input'), true);
header('Content-type: text/json');
if($data['uid'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $uid = strip_tags(mysqli_real_escape_string($event,$data['uid']));
    
    
$check = $event->query("select * from faq_cat where status=1");
$op = array();
$arr = array();
while($row = $check->fetch_assoc())
{
		$op['cat_id'] = $row['id'];
		$op['cat_name'] = $row['title'];
		$f= array();
		$p = array();
		$faq = $event->query("select * from tbl_faq where status=1 and fid=".$row['id']."");
		while($rowp = $faq->fetch_assoc())
{
	$p['faq_id'] = $rowp['id'];
	$p['faq_que'] = $rowp['question'];
	$p['faq_ans'] = $rowp['answer'];
	$f[] = $p;
}
$op['faq_list'] = $f;
$arr[] = $op;
}
$returnArr = array("FaqData"=>$arr,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Faq List Get Successfully!!");
}
echo json_encode($returnArr);