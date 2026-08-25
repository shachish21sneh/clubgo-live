<?php
require dirname(__DIR__) . '/include/eventconfig.php';

$indexes = [
    ['tbl_user', 'idx_user_mobile', '`mobile`'],
    ['tbl_user', 'idx_user_email', '`email`'],
    ['tbl_user', 'idx_user_code', '`code`'],
    ['tbl_user', 'idx_user_status_mobile', '`status`, `mobile`'],

    ['tbl_ticket', 'idx_ticket_eid_uid', '`eid`, `uid`'],
    ['tbl_ticket', 'idx_ticket_uid', '`uid`'],
    ['tbl_ticket', 'idx_ticket_eid_type', '`eid`, `ticket_type`'],

    ['tbl_fav', 'idx_fav_uid_eid', '`uid`, `eid`'],
    ['tbl_fav_venue', 'idx_fav_venue_uid_vid', '`uid`, `vid`'],

    ['tbl_event', 'idx_event_status_sdate', '`status`, `sdate`'],
    ['tbl_event', 'idx_event_status_id', '`status`, `id`'],
    ['tbl_event', 'idx_event_loc_id', '`loc_id`, `status`'],

    ['tbl_type_price', 'idx_typeprice_eid_price', '`eid`, `price`'],

    ['tbl_sponsore', 'idx_sponsore_eid_status', '`eid`, `status`'],
    ['tbl_gallery', 'idx_gallery_eid_status', '`eid`, `status`'],
    ['tbl_cover', 'idx_cover_eid_status', '`eid`, `status`'],

    ['tbl_otp_verification', 'idx_otp_mobile', '`mobile`, `otp`'],
    ['tbl_veneu', 'idx_venue_status_id', '`loc_status`, `loc_id`'],
];

// Fix legacy datetime
$event->query("SET sql_mode=''");
$event->query("UPDATE tbl_event SET updated_at = CURRENT_TIMESTAMP WHERE updated_at IS NULL OR updated_at = '0000-00-00 00:00:00'");
$event->query("ALTER TABLE tbl_event MODIFY COLUMN updated_at datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

foreach ($indexes as $item) {
    list($table, $indexName, $columns) = $item;
    
    // Check if index exists
    $check = $event->query("SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND INDEX_NAME = '$indexName'");
    if ($check && $check->num_rows > 0) {
        echo "Index $indexName on $table already exists.\n";
    } else {
        $sql = "ALTER TABLE `$table` ADD INDEX `$indexName` ($columns)";
        if ($event->query($sql)) {
            echo "Successfully created index $indexName on $table ($columns).\n";
        } else {
            echo "Failed to create index $indexName on $table: " . $event->error . "\n";
        }
    }
}

echo "Database Index Optimization Complete!\n";
