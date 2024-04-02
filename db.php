<?php
// Filename: db.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Facilitates the connection to the database
if (!$settings) {
    $settings = parse_ini_file('environment.ini.php', true);
}

if ($settings && !$link) {
    $link = mysqli_connect($settings['database']['db_hostname'], $settings['database']['db_user'], $settings['database']['db_pw'], $settings['database']['db_to_select']);
}

if (!$link) {
    error_log("TrackIt - Database doesn't connect. Error Message: ", $link->connect_error);
    die("Fatal Error: Unable to Connect to Database.");
}
?>
