<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Log Submit';
require 'conn.php';
require 'util.php';

$nid = $_GET["id"];
if (! isset($nid)) {
    header("location: ./");
    require "conx.php";
}

if (! $levelEditRun) {
    header("location: nightlog.php?id=$nid");
    require "conx.php";
}

$sqlaccept = "UPDATE ObsNight SET " .
    "AcceptTime = CURRENT_TIMESTAMP " .
    "WHERE NightID = '$nid' AND AcceptTime IS NULL";
$conn->query($sqlaccept);

header("location: nightlog.php?id=$nid");
require "conx.php";
?>