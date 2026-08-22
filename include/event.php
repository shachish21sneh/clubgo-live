<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require 'eventconfig.php';
require 'eventmania.php';
if (isset($_POST['type'])) {
	if ($_POST['type'] == 'login') {
		$username = $_POST['username'];
		$password = $_POST['password'];


		$h = new Eventmania();

		$count = $h->eventlogin($username, $password, 'admin');
		if ($count != 0) {
			$_SESSION['eventname'] = $username;
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Login Successfully!", "message" => "welcome admin!!", "action" => "dashboard.php");
		} else {
			$returnArr = array("ResponseCode" => "200", "Result" => "false", "title" => "Please Use Valid Data!!", "message" => "Invalid Data!!", "action" => "index.php");
		}
	} else if ($_POST['type'] == 'add_code') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);


		$table = "tbl_code";
		$field_values = array("ccode", "status");
		$data_values = array("$title", "$okey");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Country Code Add Successfully!!", "message" => "Country Code section!", "action" => "list_code.php");
		}
	} else if ($_POST['type'] == 'update_status') {
		$id = $_POST['id'];
		$status = $_POST['status'];
		$coll_type = $_POST['coll_type'];

		if ($coll_type == 'user') {
			$table = "tbl_user";
			$field = "status=" . $status . "";
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData_single($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "User Status Change Successfully!!", "message" => "User section!", "action" => "userlist.php");
			}
		} else {
			$returnArr = array("ResponseCode" => "200", "Result" => "false", "title" => "Option Not There!!", "message" => "Error!!", "action" => "dashboard.php");
		}
	} else if ($_POST['type'] == 'add_fcat') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);


		$table = "faq_cat";
		$field_values = array("title", "status");
		$data_values = array("$title", "$okey");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "FAQ Category Add Successfully!!", "message" => "FAQ Category section!", "action" => "list_fcat.php");
		}
	} else if ($_POST['type'] == 'add_category') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);
		$target_dir = dirname(dirname(__FILE__)) . "/images/category/";
		$url = "images/category/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		$target_dirs = dirname(dirname(__FILE__)) . "/images/category/";
		$urls = "images/category/";
		$temps = explode(".", $_FILES["cover_img"]["name"]);
		$newfilenames = uniqid() . round(microtime(true)) . '.' . end($temps);
		$target_files = $target_dirs . basename($newfilenames);
		$urls = $urls . basename($newfilenames);


		move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
		move_uploaded_file($_FILES["cover_img"]["tmp_name"], $target_files);
		$table = "tbl_cat";
		$field_values = array("img", "status", "title", "cover_img");
		$data_values = array("$url", "$okey", "$title", "$urls");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Category Add Successfully!!", "message" => "Category section!", "action" => "list_category.php");
		}
	} else if ($_POST['type'] == 'edit_category') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);
		$id = $_POST['id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/category/";
		$url = "images/category/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		$target_dirs = dirname(dirname(__FILE__)) . "/images/category/";
		$urls = "images/category/";
		$temps = explode(".", $_FILES["cover_img"]["name"]);
		$newfilenames = uniqid() . round(microtime(true)) . '.' . end($temps);
		$target_files = $target_dirs . basename($newfilenames);
		$urls = $urls . basename($newfilenames);

		if ($_FILES["cat_img"]["name"] != '' and $_FILES["cover_img"]["name"] == '') {

			move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
			$table = "tbl_cat";
			$field = array('status' => $okey, 'img' => $url, 'title' => $title);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Category Update Successfully!!", "message" => "Category section!", "action" => "list_category.php");
			}
		} else if ($_FILES["cat_img"]["name"] == '' and $_FILES["cover_img"]["name"] != '') {

			move_uploaded_file($_FILES["cover_img"]["tmp_name"], $target_files);
			$table = "tbl_cat";
			$field = array('status' => $okey, 'cover_img' => $urls, 'title' => $title);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Category Update Successfully!!", "message" => "Category section!", "action" => "list_category.php");
			}
		} else if ($_FILES["cat_img"]["name"] != '' and $_FILES["cover_img"]["name"] != '') {

			move_uploaded_file($_FILES["cover_img"]["tmp_name"], $target_files);
			move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
			$table = "tbl_cat";
			$field = array('status' => $okey, 'cover_img' => $urls, 'img' => $url, 'title' => $title);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Category Update Successfully!!", "message" => "Category section!", "action" => "list_category.php");
			}
		} else {
			$table = "tbl_cat";
			$field = array('status' => $okey, 'title' => $title);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Category Update Successfully!!", "message" => "Category section!", "action" => "list_category.php");
			}
		}
	} else if ($_POST['type'] == 'edit_code') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);
		$id = $_POST['id'];
		$table = "tbl_code";
		$field = array('status' => $okey, 'ccode' => $title);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Country Code Update Successfully!!", "message" => "Country Code section!", "action" => "list_code.php");
		}
	} else if ($_POST['type'] == 'edit_fcat') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);
		$id = $_POST['id'];
		$table = "faq_cat";
		$field = array('status' => $okey, 'title' => $title);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "FAQ Category Update Successfully!!", "message" => "FAQ Category section!", "action" => "list_fcat.php");
		}
	} else if ($_POST['type'] == 'add_coupon') {
		$ccode = $event->real_escape_string($_POST['ccode']);
		$cdate = $_POST['cdate'];
		$minamt = $_POST['minamt'];
		$ctitle = $event->real_escape_string($_POST['ctitle']);
		$subtitle = $event->real_escape_string($_POST['subtitle']);
		$cstatus = $_POST['cstatus'];
		$cvalue = $_POST['cvalue'];
		$cdesc = $event->real_escape_string($_POST['cdesc']);
		$target_dir = dirname(dirname(__FILE__)) . "/images/coupon/";
		$url = "images/coupon/";
		$temp = explode(".", $_FILES["f_up"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["f_up"]["tmp_name"], $target_file);
		$table = "tbl_coupon";
		$field_values = array("c_img", "c_desc", "c_value", "c_title", "status", "cdate", "ctitle", "min_amt", "subtitle");
		$data_values = array("$url", "$cdesc", "$cvalue", "$ccode", "$cstatus", "$cdate", "$ctitle", "$minamt", "$subtitle");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Coupon Add Successfully!!", "message" => "Coupon section!", "action" => "list_ccode.php");
		}
	} else if ($_POST['type'] == 'edit_coupon') {
		$ccode = $event->real_escape_string($_POST['ccode']);
		$id = $_POST['id'];
		$cdate = $_POST['cdate'];
		$minamt = $_POST['minamt'];
		$ctitle = $event->real_escape_string($_POST['ctitle']);
		$subtitle = $event->real_escape_string($_POST['subtitle']);
		$cstatus = $_POST['cstatus'];
		$cvalue = $_POST['cvalue'];
		$cdesc = $event->real_escape_string($_POST['cdesc']);
		$restid = implode(',', $_POST['restsearch']);
		$target_dir = dirname(dirname(__FILE__)) . "/images/coupon/";
		$url = "images/coupon/";
		$temp = explode(".", $_FILES["f_up"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["f_up"]["name"] != '') {

			move_uploaded_file($_FILES["f_up"]["tmp_name"], $target_file);
			$table = "tbl_coupon";
			$field = array('c_img' => $url, 'c_desc' => $cdesc, 'c_value' => $cvalue, 'c_title' => $ccode, 'status' => $cstatus, 'cdate' => $cdate, 'ctitle' => $ctitle, 'min_amt' => $minamt, 'subtitle' => $subtitle);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Coupon Update Successfully!!", "message" => "Coupon section!", "action" => "list_ccode.php");
			}
		} else {
			$table = "tbl_coupon";
			$field = array('c_desc' => $cdesc, 'c_value' => $cvalue, 'c_title' => $ccode, 'status' => $cstatus, 'cdate' => $cdate, 'ctitle' => $ctitle, 'min_amt' => $minamt, 'subtitle' => $subtitle);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Coupon Update Successfully!!", "message" => "Coupon section!", "action" => "list_ccode.php");
			}
		}
	} else if ($_POST['type'] == 'add_page') {
		$ctitle = $event->real_escape_string($_POST['ctitle']);
		$cstatus = $_POST['cstatus'];
		$cdesc = $event->real_escape_string($_POST['cdesc']);
		$table = "tbl_page";

		$field_values = array("description", "status", "title");
		$data_values = array("$cdesc", "$cstatus", "$ctitle");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Page Add Successfully!!", "message" => "Page section!", "action" => "list_pages.php");
		}
	} else if ($_POST['type'] == 'edit_page') {
		$id = $_POST['id'];
		$ctitle = $event->real_escape_string($_POST['ctitle']);
		$cstatus = $_POST['cstatus'];
		$cdesc = $event->real_escape_string($_POST['cdesc']);

		$table = "tbl_page";
		$field = array('description' => $cdesc, 'status' => $cstatus, 'title' => $ctitle);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Page Update Successfully!!", "message" => "Page section!", "action" => "list_pages.php");
		}
	} else if ($_POST['type'] == 'code_delete') {
		$id = $_POST['id'];

		$table = "tbl_code";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Country Code Delete Successfully!!", "message" => "Country Code section!", "action" => "list_code.php");
		}
	} else if ($_POST['type'] == 'coupon_delete') {
		$id = $_POST['id'];

		$table = "tbl_coupon";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Coupon Delete Successfully!!", "message" => "Coupon section!", "action" => "list_ccode.php");
		}
	} else if ($_POST['type'] == 'page_delete') {
		$id = $_POST['id'];

		$table = "tbl_page";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Page Delete Successfully!!", "message" => "Page  section!", "action" => "list_pages.php");
		}
	} else if ($_POST['type'] == 'faqc_delete') {
		$id = $_POST['id'];

		$table = "faq_cat";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);

		$table = "tbl_faq";
		$where = "where fid=" . $id . "";
		$h = new Eventmania();
		$h->eventDeleteData($where, $table);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "FAQ Category Delete Successfully!!", "message" => "FAQ Category  section!", "action" => "list_fcat.php");
		}
	} else if ($_POST['type'] == 'edit_payment') {
		$dname = mysqli_real_escape_string($event, $_POST['cname']);
		$attributes = mysqli_real_escape_string($event, $_POST['p_attr']);
		$ptitle = mysqli_real_escape_string($event, $_POST['ptitle']);
		$okey = $_POST['status'];
		$id = $_POST['id'];
		$p_show = $_POST['p_show'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/payment/";
		$url = "images/payment/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["cat_img"]["name"] != '') {

			move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
			$table = "tbl_payment_list";
			$field = array('title' => $dname, 'status' => $okey, 'img' => $url, 'attributes' => $attributes, 'subtitle' => $ptitle, 'p_show' => $p_show);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Payment Gateway Update Successfully!!", "message" => "Payment Gateway section!", "action" => "payment-list.php");
			}
		} else {
			$table = "tbl_payment_list";
			$field = array('title' => $dname, 'status' => $okey, 'attributes' => $attributes, 'subtitle' => $ptitle, 'p_show' => $p_show);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "payment Update Successfully!!", "message" => "payment section!", "action" => "payment-list.php");
			}
		}
	} else if ($_POST['type'] == 'add_faq') {
		$question = mysqli_real_escape_string($event, $_POST['question']);
		$answer = mysqli_real_escape_string($event, $_POST['answer']);
		$okey = $_POST['status'];
		$fid = $_POST['fid'];




		$table = "tbl_faq";
		$field_values = array("question", "answer", "status", "fid");
		$data_values = array("$question", "$answer", "$okey", "$fid");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Faq Add Successfully!!", "message" => "Faq section!", "action" => "list_faq.php");
		}
	} else if ($_POST['type'] == 'edit_faq') {
		$question = mysqli_real_escape_string($event, $_POST['question']);
		$answer = mysqli_real_escape_string($event, $_POST['answer']);
		$okey = $_POST['status'];
		$fid = $_POST['fid'];
		$id = $_POST['id'];

		$table = "tbl_faq";
		$field = array('question' => $question, 'status' => $okey, 'answer' => $answer, 'fid' => $fid);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Faq Update Successfully!!", "message" => "Faq section!", "action" => "list_faq.php");
		}
	} else if ($_POST['type'] == 'faq_delete') {
		$id = $_POST['id'];

		$table = "tbl_faq";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Faq Delete Successfully!!", "message" => "Faq section!", "action" => "list_faq.php");
		}
	} else if ($_POST['type'] == 'edit_setting') {
		$webname = mysqli_real_escape_string($event, $_POST['webname']);
		$timezone = $_POST['timezone'];
		$currency = $_POST['currency'];

		$id = $_POST['id'];

		$one_key = $_POST['one_key'];

		$one_hash = $_POST['one_hash'];

		$scredit = $_POST['scredit'];
		$rcredit = $_POST['rcredit'];


		$target_dir = dirname(dirname(__FILE__)) . "/images/website/";
		$url = "images/website/";
		$temp = explode(".", $_FILES["weblogo"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["weblogo"]["name"] != '') {

			move_uploaded_file($_FILES["weblogo"]["tmp_name"], $target_file);
			$table = "tbl_setting";
			$field = array('timezone' => $timezone, 'weblogo' => $url, 'webname' => $webname, 'currency' => $currency, 'one_key' => $one_key, 'one_hash' => $one_hash, 'scredit' => $scredit, 'rcredit' => $rcredit);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Setting Update Successfully!!", "message" => "Setting section!", "action" => "setting.php");
			}
		} else {
			$table = "tbl_setting";
			$field = array('timezone' => $timezone, 'webname' => $webname, 'currency' => $currency, 'one_key' => $one_key, 'one_hash' => $one_hash, 'scredit' => $scredit, 'rcredit' => $rcredit);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Setting Update Successfully!!", "message" => "Offer section!", "action" => "setting.php");
			}
		}
	} else if ($_POST['type'] == 'edit_profile') {
		$dname = $_POST['email'];
		$dsname = $_POST['password'];
		$id = $_POST['id'];
		$table = "admin";
		$field = array('username' => $dname, 'password' => $dsname);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Profile Update Successfully!!", "message" => "Profile  section!", "action" => "profile.php");
		}
	} else if ($_POST['type'] == 'add_events') {
		try {
    // Code that might throw an exception


		$title = $event->real_escape_string($_POST['title']);
		$address = $event->real_escape_string($_POST['address']);
		$description = $event->real_escape_string($_POST['cdesc']);
		$disclaimer = $event->real_escape_string($_POST['disclaimer']);
		$menudescription = $event->real_escape_string($_POST['menudesc']);
		$headline_json = $event->real_escape_string($_POST['headline_list_json']);
		$terms = $event->real_escape_string($_POST['terms']);

		$status = $_POST['status'];
		$place_name = $event->real_escape_string($_POST['pname']);
		$sdate = $_POST['sdate'];
		$edate = $_POST['edate'];
		$stime = $_POST['stime'];
		$etime = $_POST['etime'];
		//$cid = $_POST['cid'];
		$latitude = $_POST['latitude'];
		$longtitude = $_POST['longtitude'];
		$cid = isset($_POST['cid']) ? implode(',', $_POST['cid']) : '';
		$price_status = $_POST['price_status'];
		$non_booking = $_POST['non_booking'];
		$user_number = $_POST['user_number'];
		$user_link = $_POST['user_link'];
		$loc_id = $_POST['loc_id'];
		if ($loc_id == "") {
			$loc_id = 0;
		}
		$similar_events = isset($_POST['smiler_event_id']) ? implode(',', $_POST['smiler_event_id']) : '';

		$target_dir = dirname(dirname(__FILE__)) . "/images/event/";
		$url = "images/event/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		$target_dirs = dirname(dirname(__FILE__)) . "/images/event/";
		$urls = "images/event/";
		$temps = explode(".", $_FILES["cover_img"]["name"]);
		$newfilenames = uniqid() . round(microtime(true)) . '.' . end($temps);
		$target_files = $target_dirs . basename($newfilenames);
		$urls = $urls . basename($newfilenames);


		$target_dird = dirname(dirname(__FILE__)) . "/images/dress/";
		$urld = "images/dress/";
		$tempd = explode(".", $_FILES["dress_img"]["name"]);
		$newfilenamed = uniqid() . round(microtime(true)) . '.' . end($tempd);
		$target_filed = $target_dird . basename($newfilenamed);
		$urld = $urld . basename($newfilenamed);


		$target_dirf = dirname(dirname(__FILE__)) . "/images/floor/";
		$urlf = "images/floor/";
		$tempf = explode(".", $_FILES["floor_img"]["name"]);
		$newfilenamef = uniqid() . round(microtime(true)) . '.' . end($tempf);
		$target_filef = $target_dirf . basename($newfilenamef);
		$urlf = $urlf . basename($newfilenamef);



		move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
		move_uploaded_file($_FILES["cover_img"]["tmp_name"], $target_files);
		move_uploaded_file($_FILES["dress_img"]["tmp_name"], $target_filed);
		move_uploaded_file($_FILES["floor_img"]["tmp_name"], $target_filef);
		$table = "tbl_event";

		$field_values = array("cid", "title", "img", "cover_img", "sdate", "stime", "etime", "address", "status", "description", "disclaimer", "latitude", "longtitude", "place_name", "dress_img", "floor_img", "payment_type", "non_booking", "user_number", "user_link", "similer_event", "loc_id", "edate", "menu_description", "headline_json", "term_and_condition");
		$data_values = array("$cid", "$title", "$url", "$urls", "$sdate", "$stime", "$etime", "$address", "$status", "$description", "$disclaimer", "$latitude", "$longtitude", "$place_name", "$urld", "$urlf", "$price_status", "$non_booking", "$user_number", "$user_link", "$similar_events", "$loc_id", "$edate", $menudescription, $headline_json, $terms);

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		print_r($check); exit;

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Event Add Successfully!!", "message" => "Event section!", "action" => "list_events.php");
		}
	} catch (Exception $e) {
	// Handle the exception
	$returnArr = array("ResponseCode" => "500", "Result" => "false", "title" => "Error Occurred!!", "message" => $e->getMessage(), "action" => "list_events.php");

	} 
}else if ($_POST['type'] == 'edit_event') {
		$title = $event->real_escape_string($_POST['title']);
		$id = $_POST['id'];
		$address = $event->real_escape_string($_POST['address']);
		$description = $event->real_escape_string($_POST['cdesc']);
		$disclaimer = $event->real_escape_string($_POST['disclaimer']);
		$menudescription = $event->real_escape_string($_POST['menudesc']);
		$headline_json = $event->real_escape_string($_POST['headline_list_json']);
		$terms = $event->real_escape_string($_POST['terms']);
		$status = $_POST['status'];
		$place_name = $event->real_escape_string($_POST['pname']);
		$sdate = $_POST['sdate'];
		$edate = $_POST['edate'];
		$stime = $_POST['stime'];
		$etime = $_POST['etime'];
		//$cid = $_POST['cid'];
		$loc_id = $_POST['loc_id'];
		if ($loc_id == "") {
			$loc_id = 0;
		}
		$latitude = $_POST['latitude'];
		$longtitude = $_POST['longtitude'];
		$cid = isset($_POST['cid']) ? implode(',', $_POST['cid']) : '';

		$price_status = $_POST['price_status'];
		$non_booking = $_POST['non_booking'];
		$user_number = $_POST['user_number'];
		$user_link = $_POST['user_link'];

		$similar_events = isset($_POST['smiler_event_id']) ? implode(',', $_POST['smiler_event_id']) : '';
		// Prepare directories and filenames for images
		$images = [
			'cat_img' => 'event',
			'cover_img' => 'event',
			'dress_img' => 'dress',
			'floor_img' => 'floor',
		];

		$uploadedFiles = [];

		foreach ($images as $key => $folder) {
			if (!empty($_FILES[$key]["name"])) {
				$target_dir = dirname(dirname(__FILE__)) . "/images/$folder/";
				$url = "images/$folder/";
				$temp = explode(".", $_FILES[$key]["name"]);
				$newfilename = uniqid() . round(microtime(true)) . '.' . end($temp);
				$target_file = $target_dir . basename($newfilename);
				$url = $url . basename($newfilename);

				if (move_uploaded_file($_FILES[$key]["tmp_name"], $target_file)) {
					$uploadedFiles[$key] = $url;
				}
			}
		}

		// Build field array
		$field = array(
			'place_name' => $place_name,
			'status' => $status,
			'title' => $title,
			'cid' => $cid,
			'sdate' => $sdate,
			'edate' => $edate,
			'stime' => $stime,
			'etime' => $etime,
			'address' => $address,
			'description' => $description,
			'disclaimer' => $disclaimer,
			'latitude' => $latitude,
			'longtitude' => $longtitude,
			"similer_event" => $similar_events,
			"loc_id" => $loc_id,
			"menu_description"=>$menudescription,
			"headline_json"=>$headline_json,
			"term_and_condition"=>$terms,
			"payment_type" => $price_status,
			"non_booking" => $non_booking,
			"user_number" => $user_number,
			"user_link" => $user_link,
		);

		// Add uploaded file paths to the field array if available
		foreach ($uploadedFiles as $key => $url) {
			if ($key == 'cat_img') {
				$field['img'] = $url;  // Map cat_img to img field
			} elseif ($key == 'cover_img') {
				$field['cover_img'] = $url;  // Map cover_img to cover_img field
			} elseif (in_array($key, ['dress_img', 'floor_img'])) {
				$field[$key] = $url;
			}
		}

		// Update the database
		$table = "tbl_event";
		$where = "where id=" . $id;
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array(
				"ResponseCode" => "200",
				"Result" => "true",
				"title" => "Event Update Successfully!!",
				"message" => "Event section!",
				"action" => "list_events.php"
			);
		} else {
			$table = "tbl_event";
			$field = array('place_name' => $place_name, 'status' => $status, 'title' => $title, 'cid' => $cid, 'sdate' => $sdate, 'stime' => $stime, 'etime' => $etime, 'address' => $address, 'description' => $description, 'disclaimer' => $disclaimer, 'latitude' => $latitude, 'longtitude' => $longtitude);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Event Update Successfully!!", "message" => "Event section!", "action" => "list_events.php");
			}
		}
	} else if ($_POST['type'] == 'add_gallery') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/gallery/";
		$url = "images/gallery/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
		$table = "tbl_gallery";
		$field_values = array("img", "status", "eid");
		$data_values = array("$url", "$okey", "$eid");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Add Successfully!!", "message" => "Gallery section!", "action" => "list_gallery.php");
		}
	} else if ($_POST['type'] == 'add_cover') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/cover/";
		$url = "images/cover/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
		$table = "tbl_cover";
		$field_values = array("img", "status", "eid");
		$data_values = array("$url", "$okey", "$eid");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Cover Image Add Successfully!!", "message" => "Cover Image section!", "action" => "list_cover.php");
		}
	} else if ($_POST['type'] == 'add_sponsore') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$title = $event->real_escape_string($_POST['title']);
		$target_dir = dirname(dirname(__FILE__)) . "/images/sponsore/";
		$url = "images/sponsore/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
		$table = "tbl_sponsore";
		$field_values = array("img", "status", "eid", "title");
		$data_values = array("$url", "$okey", "$eid", "$title");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Sponsore Add Successfully!!", "message" => "Sponsore section!", "action" => "list_sponsore.php");
		}
	} else if ($_POST['type'] == 'add_type') {
		$okey = $_POST['status'];
		// $eid = $_POST['eid'];
		//$etype = $event->real_escape_string($_POST['etype']);
		$etype = "";
		$tlimit = $_POST['tlimit'];

		$name = $_POST['name'];
		$detail = $_POST['detail'];
		$entrytype = $_POST['entrytype'];

		// Single Entry Fields
		$price = $_POST['price'] ?? 0;
		$discount_price = $_POST['discount_price'] ?? '';
		$ticket_description = $_POST['ticket_description'] ?? '';
		$single_name = $_POST['single_name'] ?? '';

		// Couple Entry Fields
		$t_price_c = $_POST['t_price_c'] ?? '';
		$discount_price_c = $_POST['discount_price_c'] ?? '';
		$ticket_description_c = $_POST['ticket_description_c'] ?? '';

		// Female Fields
		$t_price_f = $_POST['t_price_f'] ?? '';
		$discount_price_f = $_POST['discount_price_f'] ?? '';
		$ticket_description_f = $_POST['ticket_description_f'] ?? '';

		// Male Fields
		$t_price_m = $_POST['t_price_m'] ?? '';
		$discount_price_m = $_POST['discount_price_m'] ?? '';
		$ticket_description_m = $_POST['ticket_description_m'] ?? '';

		// Other Fields
		$couple_ratio = $_POST['couple_ratio'];
		$tstarttime = $_POST['t_start_time'];
		$tendtime = $_POST['t_end_time'];

		if ($entrytype == "couple") {
			$price = 0;
			$discount_price = '';
			$ticket_description = '';
			$single_name = '';
		}
		if ($entrytype == "single") {
			$t_price_c = '';
			$discount_price_c = '';
			$ticket_description_c = '';
			$t_price_f = '';
			$discount_price_f = '';
			$ticket_description_f = '';
			$t_price_m = '';
			$discount_price_m = '';
			$ticket_description_m = '';

		}

		$table = "tbl_type_price";
		$field_values = array(
			"status",
			"eid",
			"type",
			"price",
			"tlimit",
			"couple_ratio",
			"entry_type",
			"name",
			"details",
			"start_time",
			"end_time",
			"discount_price",
			"couple_price",
			"discount_couple_price",
			"description_couple",
			"single_name",
			"female_price",
			"discount_female_price",
			"description_female",
			"male_price",
			"discount_male_price",
			"description_male",
			"description"
		);

		$all_success = true;
		$eid_array = is_array($_POST['eid']) ? $_POST['eid'] : [$_POST['eid']];
		$h = new Eventmania();


		foreach ($eid_array as $eid) {
			$data_values = array(
				"$okey",
				"$eid",
				"$etype",
				"$price",
				"$tlimit",
				"$couple_ratio",
				"$entrytype",
				"$name",
				"$detail",
				"$tstarttime",
				"$tendtime",
				"$discount_price",
				"$t_price_c",
				"$discount_price_c",
				"$ticket_description_c",
				"$single_name",
				"$t_price_f",
				"$discount_price_f",
				"$ticket_description_f",
				"$t_price_m",
				"$discount_price_m",
				"$ticket_description_m",
				"$ticket_description"
			);

			$check = $h->eventinsertdata($field_values, $data_values, $table);

			if ($check != 1) {
				$all_success = false;
				break; // stop loop if any insertion fails
			}
		}


		// $field_values = array("status", "eid", "type", "price", "tlimit");
		// $data_values = array("$okey", "$eid", "$etype", "$price", "$tlimit","$couple_ratio",
		//   "$entrytype","$name","$detail","$tstarttime","$tendtime","$discount_price","$t_price_c","$discount_price_c","$ticket_description_c",
		//   "$single_name","$t_price_f","$discount_price_f","$ticket_description_f","$t_price_m","$discount_price_m","$ticket_description_m","$ticket_description"
		// );

		// $check = $h->eventinsertdata($field_values, $data_values, $table);
		// if ($check == 1) {
		// 	$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Type & Price Add Successfully!!", "message" => "Type & Price section!", "action" => "list_type.php");
		// }
// Final response
		if ($all_success) {
			$returnArr = array(
				"ResponseCode" => "200",
				"Result" => "true",
				"title" => "Type & Price Added Successfully!!",
				"message" => "Type & Price added for all selected events!",
				"action" => "list_type.php"
			);
		} else {
			$returnArr = array(
				"ResponseCode" => "500",
				"Result" => "false",
				"title" => "Insertion Failed",
				"message" => "One or more entries failed to save.",
			);
		}

	} else if ($_POST['type'] == 'edit_type') {
		$id = $_POST['id'];
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		//$etype = $event->real_escape_string($_POST['etype']);
		$etype = "";
		$tlimit = $_POST['tlimit'];

		$name = $_POST['name'];
		$detail = $_POST['detail'];
		$entrytype = $_POST['entrytype'];

		// Single Entry Fields
		$price = $_POST['price'] ?? 0;
		$discount_price = $_POST['discount_price'] ?? '';
		$ticket_description = $_POST['ticket_description'] ?? '';
		$single_name = $_POST['single_name'] ?? '';

		// Couple Entry Fields
		$t_price_c = $_POST['t_price_c'] ?? '';
		$discount_price_c = $_POST['discount_price_c'] ?? '';
		$ticket_description_c = $_POST['ticket_description_c'] ?? '';

		// Female Fields
		$t_price_f = $_POST['t_price_f'] ?? '';
		$discount_price_f = $_POST['discount_price_f'] ?? '';
		$ticket_description_f = $_POST['ticket_description_f'] ?? '';

		// Male Fields
		$t_price_m = $_POST['t_price_m'] ?? '';
		$discount_price_m = $_POST['discount_price_m'] ?? '';
		$ticket_description_m = $_POST['ticket_description_m'] ?? '';

		// Other Fields
		$couple_ratio = $_POST['couple_ratio'];
		$tstarttime = $_POST['t_start_time'];
		$tendtime = $_POST['t_end_time'];

		if ($entrytype == "couple") {
			$price = '';
			$discount_price = '';
			$ticket_description = '';
			$single_name = '';
		}
		if ($entrytype == "single") {
			$t_price_c = '';
			$discount_price_c = '';
			$ticket_description_c = '';
			$t_price_f = '';
			$discount_price_f = '';
			$ticket_description_f = '';
			$t_price_m = '';
			$discount_price_m = '';
			$ticket_description_m = '';
		}


		$table = "tbl_type_price";
		//$field = array('status' => $okey, 'price' => $price, 'eid' => $eid, 'tlimit' => $tlimit, 'type' => $etype);
		$field = array(
			'status' => $okey,
			'eid' => $eid,
			'type' => $etype,
			'price' => $price,
			'tlimit' => $tlimit,
			'couple_ratio' => $couple_ratio,
			'entry_type' => $entrytype,
			'name' => $name,
			'details' => $detail,
			'start_time' => $tstarttime,
			'end_time' => $tendtime,
			'discount_price' => $discount_price,
			'couple_price' => $t_price_c,
			'discount_couple_price' => $discount_price_c,
			'description_couple' => $ticket_description_c,
			'single_name' => $single_name,
			'female_price' => $t_price_f,
			'discount_female_price' => $discount_price_f,
			'description_female' => $ticket_description_f,
			'male_price' => $t_price_m,
			'discount_male_price' => $discount_price_m,
			'description_male' => $ticket_description_m,
			'description' => $ticket_description
		);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Type & Price Edit Successfully!!", "message" => "Type & Price section!", "action" => "list_type.php");
		}
	} else if ($_POST['type'] == 'edit_gallery') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$id = $_POST['id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/gallery/";
		$url = "images/gallery/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["cat_img"]["name"] != '') {

			move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
			$table = "tbl_gallery";
			$field = array('status' => $okey, 'img' => $url, 'eid' => $eid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Update Successfully!!", "message" => "Gallery section!", "action" => "list_gallery.php");
			}
		} else {
			$table = "tbl_gallery";
			$field = array('status' => $okey, 'eid' => $eid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Update Successfully!!", "message" => "Gallery section!", "action" => "list_gallery.php");
			}
		}
	} else if ($_POST['type'] == 'edit_sponsore') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$id = $_POST['id'];
		$title = $event->real_escape_string($_POST['title']);
		$target_dir = dirname(dirname(__FILE__)) . "/images/sponsore/";
		$url = "images/sponsore/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["cat_img"]["name"] != '') {

			move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
			$table = "tbl_sponsore";
			$field = array('status' => $okey, 'img' => $url, 'eid' => $eid, 'title' => $title);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Sponsore Update Successfully!!", "message" => "Sponsore section!", "action" => "list_sponsore.php");
			}
		} else {
			$table = "tbl_sponsore";
			$field = array('status' => $okey, 'eid' => $eid, 'title' => $title);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Sponsore Update Successfully!!", "message" => "Sponsore section!", "action" => "list_sponsore.php");
			}
		}
	} else if ($_POST['type'] == 'edit_cover') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$id = $_POST['id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/cover/";
		$url = "images/cover/";
		$temp = explode(".", $_FILES["cat_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["cat_img"]["name"] != '') {

			move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
			$table = "tbl_cover";
			$field = array('status' => $okey, 'img' => $url, 'eid' => $eid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Cover Image Update Successfully!!", "message" => "Cover Image section!", "action" => "list_cover.php");
			}
		} else {
			$table = "tbl_cover";
			$field = array('status' => $okey, 'eid' => $eid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Cover Image Update Successfully!!", "message" => "Cover Image section!", "action" => "list_cover.php");
			}
		}
	} else if ($_POST['type'] == 'gallery_delete') {
		$id = $_POST['id'];

		$table = "tbl_gallery";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Delete Successfully!!", "message" => "Gallery  section!", "action" => "list_gallery.php");
		}
	} else if ($_POST['type'] == 'cover_delete') {
		$id = $_POST['id'];

		$table = "tbl_cover";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Cover Image Delete Successfully!!", "message" => "Cover Image  section!", "action" => "list_gallery.php");
		}
	} else if ($_POST['type'] == 'sponsore_delete') {
		$id = $_POST['id'];

		$table = "tbl_sponsore";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Sponsore Delete Successfully!!", "message" => "Sponsore  section!", "action" => "list_sponsore.php");
		}
	} else if ($_POST['type'] == 'type_delete') {
		$id = $_POST['id'];

		$table = "tbl_type_price";
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventDeleteData($where, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Type & Price Delete Successfully!!", "message" => "Type & Price  section!", "action" => "list_type.php");
		}
	} else if ($_POST['type'] == 'add_cuisines') {


		$ctitle = $event->real_escape_string($_POST['title']);

		$cstatus = $_POST['status'];

		$table = "tbl_cuisines";
		$field_values = array("name", "status");
		$data_values = array("$ctitle", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "cuisines Add Successfully!!", "message" => "cuisines section!", "action" => "list_cuisines.php");
		}
	} else if ($_POST['type'] == 'edit_cuisines') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$cstatus = $_POST['status'];

		$table = "tbl_cuisines";
		$field = array('name' => $ctitle, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "cuisines Update Successfully!!", "message" => "cuisines section!", "action" => "list_cuisines.php");
		}
	} else if ($_POST['type'] == 'add_facilities') {


		$ctitle = $event->real_escape_string($_POST['title']);

		$cstatus = $_POST['status'];

		$table = "tbl_facilities";
		$field_values = array("name", "status");
		$data_values = array("$ctitle", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "facilities Add Successfully!!", "message" => "facilities section!", "action" => "list_facilities.php");
		}
	} else if ($_POST['type'] == 'edit_facilities') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$cstatus = $_POST['status'];

		$table = "tbl_facilities";
		$field = array('name' => $ctitle, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "facilities Update Successfully!!", "message" => "facilities section!", "action" => "list_cuisines.php");
		}
	} else if ($_POST['type'] == 'add_knowfor') {


		$ctitle = $event->real_escape_string($_POST['title']);

		$cstatus = $_POST['status'];

		$table = "tbl_known_for";
		$field_values = array("name", "status");
		$data_values = array("$ctitle", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "know for Add Successfully!!", "message" => "know for section!", "action" => "list_knowfor.php");
		}
	} else if ($_POST['type'] == 'edit_knowfor') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$cstatus = $_POST['status'];

		$table = "tbl_known_for";
		$field = array('name' => $ctitle, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "know for Update Successfully!!", "message" => "know for section!", "action" => "list_knowfor.php");
		}
	} else if ($_POST['type'] == 'add_city') {


		$ctitle = $event->real_escape_string($_POST['title']);

		$cstatus = $_POST['status'];

		$table = "tbl_city";
		$field_values = array("name", "status");
		$data_values = array("$ctitle", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "city Add Successfully!!", "message" => "city section!", "action" => "list_city.php");
		}
	} else if ($_POST['type'] == 'edit_city') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$cstatus = $_POST['status'];

		$table = "tbl_city";
		$field = array('name' => $ctitle, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "city Update Successfully!!", "message" => "city section!", "action" => "list_city.php");
		}
	} else if ($_POST['type'] == 'add_venue_category') {


		$ctitle = $event->real_escape_string($_POST['title']);

		$cstatus = $_POST['status'];

		$table = "tbl_venue_category";
		$field_values = array("name", "status");
		$data_values = array("$ctitle", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Venue Category Add Successfully!!", "message" => "city section!", "action" => "list_venue_category.php");
		}
	} else if ($_POST['type'] == 'edit_venue_category') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$cstatus = $_POST['status'];

		$table = "tbl_venue_category";
		$field = array('name' => $ctitle, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Venue Category Update Successfully!!", "message" => "city section!", "action" => "list_venue_category.php");
		}
	} else if ($_POST['type'] == 'add_Package') {


		$ctitle = $event->real_escape_string($_POST['title']);
		$price = $event->real_escape_string($_POST['price']);

		$cstatus = $_POST['status'];

		$table = "tbl_package";
		$field_values = array("title", "price", "status");
		$data_values = array("$ctitle", "$price", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "package Add Successfully!!", "message" => "package section!", "action" => "list_package.php");
		}
	} else if ($_POST['type'] == 'edit_Package') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$price = $event->real_escape_string($_POST['price']);
		$cstatus = $_POST['status'];

		$table = "tbl_package";
		$field = array('title' => $ctitle, 'price' => $price, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "package Update Successfully!!", "message" => "package section!", "action" => "list_package.php");
		}
	} else if ($_POST['type'] == 'add_Package_item') {


		$ctitle = $event->real_escape_string($_POST['title']);
		$pck_id = $event->real_escape_string($_POST['package_id']);
		$cstatus = $_POST['status'];

		$table = "tbl_package_items";
		$field_values = array("name", "package_id", "status");
		$data_values = array("$ctitle", "$pck_id", "$cstatus");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "item Add Successfully!!", "message" => "item section!", "action" => "list_package_item.php");
		}
	} else if ($_POST['type'] == 'edit_Package_item') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$pck_id = $event->real_escape_string($_POST['package_id']);
		$cstatus = $_POST['status'];

		$table = "tbl_package_items";
		$field = array('name' => $ctitle, 'package_id' => $pck_id, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "item Update Successfully!!", "message" => "item section!", "action" => "list_package_item.php");
		}
	} else if ($_POST['type'] == 'add_venue') {
		$ctitle = $event->real_escape_string($_POST['title']);
		$vdesc = $event->real_escape_string($_POST['vdesc']);

		//$loc_open_close = $event->real_escape_string($_POST['loc_open_close']);
		$loc_open_close = "";

		$loc_cus_headline = $event->real_escape_string($_POST['loc_cus_headline']);
		$cid = $event->real_escape_string($_POST['cityid']);
		$status = $event->real_escape_string($_POST['status']);
		$featuredorder = $event->real_escape_string($_POST['featuredorder']);
		// $sdate = $event->real_escape_string($_POST['sdate']);
		// $edate = $event->real_escape_string($_POST['edate']);
		// $stime = $event->real_escape_string($_POST['stime']);
		// $etime = $event->real_escape_string($_POST['etime']);
		$sdate = "";
		$edate = "";
		$stime = "00:00:00";
		$etime = "00:00:00";

		// Handling multiple selections
		$categories = isset($_POST['cid']) ? implode(',', $_POST['cid']) : '';
		$cuisines = isset($_POST['cusid']) ? implode(',', $_POST['cusid']) : '';
		$facilities = isset($_POST['facid']) ? implode(',', $_POST['facid']) : '';
		$known_for = isset($_POST['knowforid']) ? implode(',', $_POST['knowforid']) : '';
		$packages = isset($_POST['pkgitemid']) ? implode(',', $_POST['pkgitemid']) : '';
		$days = isset($_POST['daysid']) ? implode(',', $_POST['daysid']) : '';
		$similar_venues = isset($_POST['similar_venue']) ? implode(',', $_POST['similar_venue']) : '';

		if (isset($_POST['days'])) {
			$days_data = [];

			foreach ($_POST['days'] as $day => $times) {
				if (isset($times['open'])) { // If day is checked
					$days_data[$day] = [
						'start' => $times['start'],
						'end' => $times['end']
					];
				}
			}

			// Convert to JSON and include in your insert query
			$loc_days_json = json_encode($days_data);
			// Add this to your INSERT query for tbl_veneu
		}

		// Handling image upload
		if (!empty($_FILES['v_img']['name'])) {

			$target_dirs = dirname(dirname(__FILE__)) . "/images/venue/";
			$urls = "images/venue/";
			$temps = explode(".", $_FILES["v_img"]["name"]);
			$newfilenames = uniqid() . round(microtime(true)) . '.' . end($temps);
			$target_file = $target_dirs . basename($newfilenames);
			$urls = $urls . basename($newfilenames);
			move_uploaded_file($_FILES["v_img"]["tmp_name"], $target_file);
			$image = $urls;
		} else {
			$image = "";
		}

		$table = "tbl_veneu";
		$field_values = array(
			"loc_title",
			"loc_description",
			"loc_open_close",
			"loc_customer_headlines",
			"loc_city_id",
			"loc_category_id",
			"loc_cuisines_id",
			"loc_facilities_id",
			"loc_known_for",
			"loc_package_id",
			"loc_days",
			"loc_similer_venue",
			"loc_from_date",
			"loc_to_date",
			"loc_start_time",
			"loc_end_time",
			"loc_status",
			"is_featured",
			"loc_image",
			"loc_days_json"
		);
		$data_values = array(
			"$ctitle",
			"$vdesc",
			"$loc_open_close",
			"$loc_cus_headline",
			"$cid",
			"$categories",
			"$cuisines",
			"$facilities",
			"$known_for",
			"$packages",
			"$days",
			"$similar_venues",
			"$sdate",
			"$edate",
			"$stime",
			"$etime",
			"$status",
			"$featuredorder",
			"$image",
			"$loc_days_json"
		);

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Venue Added Successfully!", "message" => "Venue section!", "action" => "list_venue.php");
		} else {
			$returnArr = array("ResponseCode" => "500", "Result" => "false", "title" => "Error!", "message" => "Failed to add venue.");
		}
	} else if ($_POST['type'] == 'edit_venue') {
		$id = $event->real_escape_string($_POST['venue_id']);

		$ctitle = $event->real_escape_string($_POST['title']);
		$vdesc = $event->real_escape_string($_POST['vdesc']);

		//$loc_open_close = $event->real_escape_string($_POST['loc_open_close']);
		$loc_open_close = "";

		$loc_cus_headline = $event->real_escape_string($_POST['loc_cus_headline']);
		$cid = $event->real_escape_string($_POST['cityid']);
		$status = $event->real_escape_string($_POST['status']);
		$featuredorder = $event->real_escape_string($_POST['featuredorder']);
		// $sdate = $event->real_escape_string($_POST['sdate']);
		// $edate = $event->real_escape_string($_POST['edate']);
		// $stime = $event->real_escape_string($_POST['stime']);
		// $etime = $event->real_escape_string($_POST['etime']);
		$sdate = "";
		$edate = "";
		$stime = "00:00:00";
		$etime = "00:00:00";

		// Handling multiple selections
		$categories = isset($_POST['cid']) ? implode(',', $_POST['cid']) : '';
		$cuisines = isset($_POST['cusid']) ? implode(',', $_POST['cusid']) : '';
		$facilities = isset($_POST['facid']) ? implode(',', $_POST['facid']) : '';
		$known_for = isset($_POST['knowforid']) ? implode(',', $_POST['knowforid']) : '';
		$packages = isset($_POST['pkgitemid']) ? implode(',', $_POST['pkgitemid']) : '';
		$days = isset($_POST['daysid']) ? implode(',', $_POST['daysid']) : '';
		$similar_venues = isset($_POST['similar_venue']) ? implode(',', $_POST['similar_venue']) : '';


		if (isset($_POST['days'])) {
			$days_data = [];

			foreach ($_POST['days'] as $day => $times) {
				if (isset($times['open'])) { // If day is checked
					$days_data[$day] = [
						'start' => $times['start'],
						'end' => $times['end']
					];
				}
			}

			// Convert to JSON and include in your update query
			$loc_days_json = json_encode($days_data);
			// Add this to your UPDATE query for tbl_veneu
		}
		// Handling image upload
		if (!empty($_FILES['v_img']['name'])) {
			$target_dirs = dirname(dirname(__FILE__)) . "/images/venue/";
			$urls = "images/venue/";
			$temps = explode(".", $_FILES["v_img"]["name"]);
			$newfilenames = uniqid() . round(microtime(true)) . '.' . end($temps);
			$target_file = $target_dirs . basename($newfilenames);
			$urls = $urls . basename($newfilenames);
			move_uploaded_file($_FILES["v_img"]["tmp_name"], $target_file);
			$image = $urls;
		} else {
			$image = ""; // Keep the existing image if no new image is uploaded
		}

		$table = "tbl_veneu";
		$fields = array(
			"loc_title" => $ctitle,
			"loc_description" => $vdesc,
			"loc_open_close" => $loc_open_close,
			"loc_customer_headlines" => $loc_cus_headline,
			"loc_city_id" => $cid,
			"loc_category_id" => $categories,
			"loc_cuisines_id" => $cuisines,
			"loc_facilities_id" => $facilities,
			"loc_known_for" => $known_for,
			"loc_package_id" => $packages,
			"loc_days" => $days,
			"loc_similer_venue" => $similar_venues,
			"loc_from_date" => $sdate,
			"loc_to_date" => $edate,
			"loc_start_time" => $stime,
			"loc_end_time" => $etime,
			"loc_status" => $status,
			"is_featured" => $featuredorder,
			"loc_days_json" => $loc_days_json
		);

		// Update the image only if a new image is uploaded
		if (!empty($image)) {
			$fields["loc_image"] = $image;
		}

		$where = "WHERE loc_id=" . $id;
		$h = new Eventmania();
		$check = $h->eventupdateData($fields, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Venue Updated Successfully!", "message" => "Venue section!", "action" => "list_venue.php");
		} else {
			$returnArr = array("ResponseCode" => "500", "Result" => "false", "title" => "Error!", "message" => "Failed to update venue.");
		}
	} else if ($_POST['type'] == 'edit_Package_item') {
		$id = $_POST['id'];

		$ctitle = $event->real_escape_string($_POST['title']);
		$pck_id = $event->real_escape_string($_POST['package_id']);
		$cstatus = $_POST['status'];

		$table = "tbl_package_items";
		$field = array('name' => $ctitle, 'package_id' => $pck_id, 'status' => $cstatus);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);

		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "item Update Successfully!!", "message" => "item section!", "action" => "list_package_item.php");
		}
	} else if ($_POST['type'] == 'add_menu_cat') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);


		$table = "menu_category";
		$field_values = array("title", "status");
		$data_values = array("$title", "$okey");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Category Add Successfully!!", "message" => "Menu Category section!", "action" => "list_menu_cat.php");
		}
	} else if ($_POST['type'] == 'edit_menu_cat') {
		$okey = $_POST['status'];
		$title = $event->real_escape_string($_POST['title']);
		$id = $_POST['id'];
		$table = "menu_category";
		$field = array('status' => $okey, 'title' => $title);
		$where = "where id=" . $id . "";
		$h = new Eventmania();
		$check = $h->eventupdateData($field, $table, $where);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Category Update Successfully!!", "message" => "Menu Category section!", "action" => "list_menu_cat.php");
		}
	} else if ($_POST['type'] == 'add_menu') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$mid = $_POST['menu_cat_id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/menu/";
		$url = "images/menu/";
		$temp = explode(".", $_FILES["menu_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["menu_img"]["tmp_name"], $target_file);
		$table = "tbl_menu";
		$field_values = array("img", "status", "eid", "menu_cat_id");
		$data_values = array("$url", "$okey", "$eid", $mid);

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Add Successfully!!", "message" => "Menu section!", "action" => "list_menu.php");
		}
	} else if ($_POST['type'] == 'edit_menu') {
		$okey = $_POST['status'];
		$eid = $_POST['eid'];
		$mid = $_POST['menu_cat_id'];
		$id = $_POST['id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/menu/";
		$url = "images/menu/";
		$temp = explode(".", $_FILES["menu_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["menu_img"]["name"] != '') {

			move_uploaded_file($_FILES["menu_img"]["tmp_name"], $target_file);
			$table = "tbl_menu";
			$field = array('status' => $okey, 'img' => $url, 'eid' => $eid, 'menu_cat_id' => $mid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Update Successfully!!", "message" => "menu section!", "action" => "list_menu.php");
			}
		} else {
			$table = "tbl_menu";
			$field = array('status' => $okey, 'eid' => $eid, 'menu_cat_id' => $mid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Update Successfully!!", "message" => "Menu section!", "action" => "list_menu.php");
			}
		}
	} else if ($_POST['type'] == 'add_venue_gallery') {
		$okey = $_POST['status'];
		$vid = $_POST['vid'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/gallery/";
		$url = "images/gallery/";
		$temp = explode(".", $_FILES["venue_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["venue_img"]["tmp_name"], $target_file);
		$table = "tbl_venue_gallery";
		$field_values = array("img", "status", "vid");
		$data_values = array("$url", "$okey", "$vid");

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Add Successfully!!", "message" => "Gallery section!", "action" => "list_venue_gallery.php");
		}
	} else if ($_POST['type'] == 'edit_venue_gallery') {
		$okey = $_POST['status'];
		$vid = $_POST['vid'];
		$id = $_POST['id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/gallery/";
		$url = "images/gallery/";
		$temp = explode(".", $_FILES["venue_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["venue_img"]["name"] != '') {

			move_uploaded_file($_FILES["venue_img"]["tmp_name"], $target_file);
			$table = "tbl_venue_gallery";
			$field = array('status' => $okey, 'img' => $url, 'vid' => $vid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Update Successfully!!", "message" => "Gallery section!", "action" => "list_venue_gallery.php");
			}
		} else {
			$table = "tbl_venue_gallery";
			$field = array('status' => $okey, 'vid' => $vid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Update Successfully!!", "message" => "Gallery section!", "action" => "list_venue_gallery.php");
			}
		}
	} else if ($_POST['type'] == 'add_venue_menu') {
		$okey = $_POST['status'];
		$vid = $_POST['vid'];
		$mid = $_POST['menu_cat_id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/menu/";
		$url = "images/menu/";
		$temp = explode(".", $_FILES["menu_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);

		move_uploaded_file($_FILES["menu_img"]["tmp_name"], $target_file);
		$table = "tbl_venue_menu";
		$field_values = array("img", "status", "vid", "menu_cat_id");
		$data_values = array("$url", "$okey", "$vid", $mid);

		$h = new Eventmania();
		$check = $h->eventinsertdata($field_values, $data_values, $table);
		if ($check == 1) {
			$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Add Successfully!!", "message" => "Menu section!", "action" => "list_venue_menu.php");
		}
	} else if ($_POST['type'] == 'edit_venue_menu') {
		$okey = $_POST['status'];
		$vid = $_POST['vid'];
		$mid = $_POST['menu_cat_id'];
		$id = $_POST['id'];
		$target_dir = dirname(dirname(__FILE__)) . "/images/menu/";
		$url = "images/menu/";
		$temp = explode(".", $_FILES["menu_img"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		$target_file = $target_dir . basename($newfilename);
		$url = $url . basename($newfilename);
		if ($_FILES["menu_img"]["name"] != '') {

			move_uploaded_file($_FILES["menu_img"]["tmp_name"], $target_file);
			$table = "tbl_venue_menu";
			$field = array('status' => $okey, 'img' => $url, 'vid' => $vid, 'menu_cat_id' => $mid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);

			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Update Successfully!!", "message" => "menu section!", "action" => "list_venue_menu.php");
			}
		} else {
			$table = "tbl_venue_menu";
			$field = array('status' => $okey, 'vid' => $vid, 'menu_cat_id' => $mid);
			$where = "where id=" . $id . "";
			$h = new Eventmania();
			$check = $h->eventupdateData($field, $table, $where);
			if ($check == 1) {
				$returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Menu Update Successfully!!", "message" => "Menu section!", "action" => "list_venue_menu.php");
			}
		}
	}
}
echo json_encode($returnArr);