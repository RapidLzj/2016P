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

$sqlsubmit = "UPDATE ObsNight SET " .
    "Status = Status & 65534, SubmitTime = CURRENT_TIMESTAMP " .
    "WHERE NightID = '$nid'";
$conn->query($sqlsubmit);

header("location: nightlog.php?id=$nid");
require "conx.php";
?>