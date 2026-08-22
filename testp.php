<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require __DIR__ . '/include/eventconfig.php';

header('Content-Type: application/json; charset=utf-8');

// 1) validate connection object
if (!isset($event)) {
    die("ERROR: \$event is not defined. Check include/top.php — it must create a mysqli object named \$event.\n");
}
if (!($event instanceof mysqli)) {
    die("ERROR: \$event is not a mysqli object. Type: " . gettype($event) . "\n");
}

function run($conn, $sql, $limitPrint = 5) {
    echo "----\nSQL: $sql\n";
    $res = $conn->query($sql);
    if ($res === false) {
        echo "MYSQL ERROR: " . $conn->error . "\n";
        return false;
    }
    // If it's a result set, print count + few rows
    if ($res instanceof mysqli_result) {
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        echo "ROWS: " . count($rows) . "\n";
        if (count($rows) > 0) {
            $slice = array_slice($rows, 0, $limitPrint);
            echo "SAMPLE:\n";
            print_r($slice);
        }
    } else {
        // boolean (true) for non-select queries
        echo "OK (non-select)\n";
    }
    return true;
}

// Check for table 'venue' and related tables
function checkTableExists($conn, $table) {
    $sql = "SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'";
    $result = $conn->query($sql);
    return $result && $result->num_rows > 0;
}

// List of tables to check
$tables_to_check = ['tbl_veneu', 'tbl_veneu', 'venue', 'venues'];

foreach ($tables_to_check as $t) {
    echo "\nChecking table: $t\n";
    if (checkTableExists($event, $t)) {
        run($event, "DESCRIBE $t");
    } else {
        echo "Table '$t' does not exist.\n";
    }
}

// Check for tbl_event and related tables
if (checkTableExists($event, 'tbl_event')) {
    run($event, "DESCRIBE tbl_event");
} else {
    echo "Table 'tbl_event' does not exist.\n";
}

// Basic counts
run($event, "SELECT COUNT(*) as c FROM tbl_event");
foreach ($tables_to_check as $t) {
    if (checkTableExists($event, $t)) {
        run($event, "SELECT COUNT(*) as c FROM $t");
    }
}

// Join SELECT to check event-venue relationship (limit 50)
run($event, "SELECT tbl_event.id AS event_id, tbl_event.loc_id AS event_loc, tbl_veneu.loc_id AS venue_loc, tbl_veneu.loc_title AS venue_name
             FROM tbl_event
             LEFT JOIN tbl_veneu ON tbl_event.loc_id = tbl_veneu.loc_id
             LIMIT 50");

// Events with no matching venue (loc_id != 0)
run($event, "SELECT id, loc_id FROM tbl_event WHERE loc_id != 0 AND loc_id NOT IN (SELECT loc_id FROM tbl_veneu) LIMIT 50");

// Show raw rows from event and venue tables
run($event, "SELECT * FROM tbl_event LIMIT 10");
foreach ($tables_to_check as $t) {
    if (checkTableExists($event, $t)) {
        run($event, "SELECT * FROM $t LIMIT 10");
    }
}

echo "\nDiagnostic finished.\n</pre>";
?>
