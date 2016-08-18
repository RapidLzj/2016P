<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Log Preview';
require 'conn.php';
require 'util.php';

require 'parsedown/Parsedown.php';
$parse = new Parsedown();

$preview = isset($_GET["p"]);
$nid = $_GET["id"];
if (! isset($nid)) {
    header("location: main.php");
    require "conx.php";
}

$sqlsubmit = "UPDATE ObsNight SET Status = Status & 65534 WHERE NightID = '$nid'";
$conn->query($sqlsubmit);

header("location: nightlog.php?id=$nid");
require "conx.php";
?>