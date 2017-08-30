<?php

ini_set('display_errors', 'stdout');

$SMBWEBCLIENT_CLASS = 'smbwebclient_moodle';

global $smb_cfg, $CFG, $USER, $site;

require_once('../../config.php');
require_once('../../auth/ldap/auth.php');
include('config_smb_web_client.php'); // config for this block only
include('class_smbwebclient.php');
include('class_smbwebclient_moodle.php');

$PAGE->set_context(context_system::instance());
require_login();

if (!confirm_sesskey()) {
    print_error("Error - No session key");
    die();
}

if (!$site = get_site()) {
    print_error("Could not find site-level course");
    die();
}


$swc = new smbwebclient_moodle;
if ($swc->criticalError){

    $PAGE->set_title("$site->shortname: Error");
    $PAGE->set_heading("$site->shortname: Error");
    $PAGE->set_url('/blocks/smb_web_client/smbwebclient_moodle.php');

    echo ("<h1>Sorry, you cannot view your homedirectory online</h1>");
    echo ("<p>Error Message: $swc->criticalError</p>");
    die();
} else {
    $swc->Run();
}

?>
