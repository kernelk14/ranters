<?php
// I asked Brave Ask to help me create this script to auto cleanup timestamp based things.

// 1. Get the last cleanup time from the database
$env = parse_ini_file(".env");
$mysqli = new mysqli($env["MYSQL_HOST"], $env["MYSQL_USER"], $env["MYSQL_PASS"], $env["MYSQL_DB"]) or die("Cannot connect to server");
$result = $mysqli->query("SELECT setting_value FROM site_settings WHERE setting_name = 'last_cleanup'");
$row = $result->fetch_assoc();
$last_cleanup = (int)$row['setting_value'];

// 2. Check if 1 hour (3600 seconds) has passed
if (time() - $last_cleanup > 3600) {
    
    // Perform the deletion
    $deleteSql = "DELETE FROM rants WHERE time_created < NOW() - INTERVAL 1 DAY";
    if ($mysqli->query($deleteSql)) {
        $deletedCount = $mysqli->affected_rows;
        
        // Update the timestamp in the database
        $newTime = time();
        $updateSql = "UPDATE site_settings SET setting_value = '$newTime' WHERE setting_name = 'last_cleanup'";
        $mysqli->query($updateSql);
        
        // Optional: Log output for debugging
        error_log("Cleanup ran: Deleted $deletedCount rants.");
    }
}
?>   