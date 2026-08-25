<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $event = new mysqli('localhost', 'a17665e9_clubgoLive', '8Y8$lMBNHCS^', 'a17665e9_clubgoLive');
  $event->set_charset("utf8mb4");
} catch(Exception $e) {
  try {
    $event = new mysqli('127.0.0.1', 'root', '', 'a17665e9_clubgoLive');
    $event->set_charset("utf8mb4");
  } catch(Exception $e2) {
    try {
      $event = new mysqli('localhost', 'root', '', 'a17665e9_clubgoLive');
      $event->set_charset("utf8mb4");
    } catch(Exception $e3) {
      error_log($e3->getMessage());
    }
  }
}
    
$set = $event->query("SELECT * FROM `tbl_setting`")->fetch_assoc();
date_default_timezone_set($set['timezone']);
	
$validate = $event->query("SELECT * FROM `tbl_validate`")->fetch_assoc();

if (!defined('IMAGE_SERVER_URL')) {
    define('IMAGE_SERVER_URL', 'https://www.clubgo.in/v2/');
}

if (!function_exists('get_image_url')) {
    function get_image_url($path, $fallback = 'images/website/clubgoimg.webp') {
        if (empty($path)) {
            return $fallback;
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        $cleanPath = ltrim($path, '/');
        $localFile = dirname(__DIR__) . '/' . $cleanPath;
        if (file_exists($localFile) && !is_dir($localFile)) {
            return $cleanPath;
        }
        return IMAGE_SERVER_URL . $cleanPath;
    }
}
?>