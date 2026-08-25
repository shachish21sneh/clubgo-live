<?php
require_once 'include/eventconfig.php';
require_once 'include/eventmania.php';

header('Content-Type: application/json; charset=utf-8');

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true) ?? [];

$action = $_GET['action'] ?? $data['action'] ?? '';

// 1. User Login
if ($action === 'login') {
    $mobile = trim($data['mobile'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($mobile) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide both mobile number and password.']);
        exit;
    }

    $escapedMobile = $event->real_escape_string($mobile);
    $md5Pass = md5($password);

    $check = $event->query("SELECT * FROM tbl_user WHERE (mobile='$escapedMobile' OR email='$escapedMobile') AND status=1");
    if ($check && $check->num_rows > 0) {
        $user = $check->fetch_assoc();
        if ($user['password'] === $md5Pass || $user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_mobile'] = $user['mobile'];
            $_SESSION['user_wallet'] = $user['wallet'];
            $_SESSION['user_pic'] = $user['pro_pic'] ?? 'images/profile/pic1.jpg';

            echo json_encode(['status' => 'success', 'message' => 'Logged in successfully!', 'user' => $user]);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password. Please try again.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found or account is deactivated.']);
        exit;
    }
}

// 2. User Registration
if ($action === 'register') {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $mobile = trim($data['mobile'] ?? '');
    $password = trim($data['password'] ?? '');
    $referral = trim($data['referral'] ?? '');

    if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $escapedMobile = $event->real_escape_string($mobile);
    $escapedEmail = $event->real_escape_string($email);
    $escapedName = $event->real_escape_string($name);
    $md5Pass = md5($password);

    $exist = $event->query("SELECT id FROM tbl_user WHERE mobile='$escapedMobile' OR email='$escapedEmail'");
    if ($exist && $exist->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Mobile number or email already registered. Please sign in.']);
        exit;
    }

    // Generate user referral code
    $myCode = rand(1000, 9999);
    $initialWallet = (float)($set['scredit'] ?? 0);
    $now = date('Y-m-d H:i:s');

    // Insert user
    $insertSql = "INSERT INTO tbl_user (name, email, mobile, password, ccode, code, refercode, wallet, status, rdate) 
                  VALUES ('$escapedName', '$escapedEmail', '$escapedMobile', '$md5Pass', '+91', $myCode, '$referral', '$initialWallet', 1, '$now')";
    
    if ($event->query($insertSql)) {
        $userId = $event->insert_id;

        // Welcome wallet credit report
        if ($initialWallet > 0) {
            $event->query("INSERT INTO wallet_report (uid, message, status, amt, tdate) VALUES ($userId, 'Welcome SignUp Bonus', 'Credit', '$initialWallet', '$now')");
        }

        // Check if referral code was provided
        if (!empty($referral)) {
            $refCheck = $event->query("SELECT id, wallet FROM tbl_user WHERE code='" . $event->real_escape_string($referral) . "'");
            if ($refCheck && $refCheck->num_rows > 0) {
                $refUser = $refCheck->fetch_assoc();
                $rcredit = (float)($set['rcredit'] ?? 0);
                if ($rcredit > 0) {
                    $newWallet = $refUser['wallet'] + $rcredit;
                    $event->query("UPDATE tbl_user SET wallet=$newWallet WHERE id=" . $refUser['id']);
                    $event->query("INSERT INTO wallet_report (uid, message, status, amt, tdate) VALUES (" . $refUser['id'] . ", 'Referral Bonus for inviting $name', 'Credit', '$rcredit', '$now')");
                }
            }
        }

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_mobile'] = $mobile;
        $_SESSION['user_wallet'] = $initialWallet;
        $_SESSION['user_pic'] = 'images/profile/pic1.jpg';

        echo json_encode(['status' => 'success', 'message' => 'Account created successfully!']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create account. Please try again.']);
        exit;
    }
}

// 3. User Logout
if ($action === 'logout') {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_mobile'], $_SESSION['user_wallet'], $_SESSION['user_pic']);
    echo json_encode(['status' => 'success']);
    exit;
}

// 4. Toggle Bookmark
if ($action === 'toggle_bookmark') {
    if (empty($_SESSION['user_id'])) {
        echo json_encode(['status' => 'need_login', 'message' => 'Please sign in to save your favorites.']);
        exit;
    }

    $uid = (int)$_SESSION['user_id'];
    $type = $data['type'] ?? 'event';
    $id = (int)($data['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        exit;
    }

    if ($type === 'event') {
        $check = $event->query("SELECT id FROM tbl_fav WHERE uid=$uid AND eid=$id");
        if ($check && $check->num_rows > 0) {
            $event->query("DELETE FROM tbl_fav WHERE uid=$uid AND eid=$id");
            echo json_encode(['status' => 'success', 'bookmarked' => false]);
        } else {
            $event->query("INSERT INTO tbl_fav (uid, eid) VALUES ($uid, $id)");
            echo json_encode(['status' => 'success', 'bookmarked' => true]);
        }
        exit;
    } else {
        $check = $event->query("SELECT id FROM tbl_fav_venue WHERE uid=$uid AND vid=$id");
        if ($check && $check->num_rows > 0) {
            $event->query("DELETE FROM tbl_fav_venue WHERE uid=$uid AND vid=$id");
            echo json_encode(['status' => 'success', 'bookmarked' => false]);
        } else {
            $event->query("INSERT INTO tbl_fav_venue (uid, vid) VALUES ($uid, $id)");
            echo json_encode(['status' => 'success', 'bookmarked' => true]);
        }
        exit;
    }
}

// 5. Search Suggestions
if ($action === 'search_suggest') {
    $q = trim($_GET['q'] ?? '');
    $results = [];

    if (strlen($q) >= 2) {
        $escaped = $event->real_escape_string($q);
        
        // Search Events
        $events = $event->query("SELECT id, title, img, place_name, sdate FROM tbl_event WHERE status=1 AND (title LIKE '%$escaped%' OR place_name LIKE '%$escaped%') LIMIT 5");
        while ($ev = $events->fetch_assoc()) {
            $date = date_create($ev['sdate']);
            $results[] = [
                'type' => 'Event',
                'title' => $ev['title'],
                'subtitle' => ($ev['place_name'] ?: 'Event') . ' • ' . date_format($date, 'd M'),
                'img' => get_image_url($ev['img']),
                'url' => 'event_detail.php?id=' . $ev['id']
            ];
        }

        // Search Venues
        $venues = $event->query("SELECT loc_id, loc_title, loc_image, loc_customer_headlines FROM tbl_veneu WHERE loc_status='A' AND (loc_title LIKE '%$escaped%' OR loc_customer_headlines LIKE '%$escaped%') LIMIT 4");
        while ($ve = $venues->fetch_assoc()) {
            $results[] = [
                'type' => 'Club/Venue',
                'title' => $ve['loc_title'],
                'subtitle' => $ve['loc_customer_headlines'] ?: 'Club & Lounge',
                'img' => get_image_url($ve['loc_image']),
                'url' => 'venue_detail.php?id=' . $ve['loc_id']
            ];
        }
    }

    echo json_encode(['status' => 'success', 'results' => $results]);
    exit;
}

// 6. Check Coupon Code
if ($action === 'check_coupon') {
    $code = trim($data['code'] ?? '');
    $orderAmt = (float)($data['amount'] ?? 0);

    if (empty($code)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a coupon code.']);
        exit;
    }

    $escapedCode = $event->real_escape_string($code);
    $check = $event->query("SELECT * FROM tbl_coupon WHERE c_title='$escapedCode' OR ctitle='$escapedCode' LIMIT 1");
    
    if ($check && $check->num_rows > 0) {
        $coupon = $check->fetch_assoc();
        $minAmt = (float)($coupon['min_amt'] ?? 0);
        $discountVal = (float)$coupon['c_value'];

        if ($orderAmt < $minAmt) {
            echo json_encode(['status' => 'error', 'message' => 'Minimum order amount for this coupon is ₹' . $minAmt]);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'discount' => $discountVal,
            'coupon_id' => $coupon['id'],
            'title' => $coupon['ctitle'] ?: $coupon['c_title'],
            'message' => 'Coupon applied! You saved ₹' . $discountVal
        ]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired coupon code.']);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
