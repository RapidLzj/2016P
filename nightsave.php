<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Log Save';
require 'conn.php';
require 'util.php';

$nid = $_POST["nightid"];
$savemode = $_POST["savemode"];
$operator = str_replace("'", "''", $_POST["operator"]);
$weatherg = $_POST["weatherl"] * 100 + $_POST["weatherw"] * 10 + $_POST["weatherh"];
$weatherd = str_replace("'", "''", $_POST["weatherd"]);
$status = StatusDoing +
    (isset($_POST["sObsed"]) ? StatusObsed : 0) +
    (isset($_POST["sNoWork"]) ? StatusNone : 0) +
    (isset($_POST["sShared"]) ? StatusShared : 0) +
    (isset($_POST["sWeather"]) ? StatusWeather : 0) +
    (isset($_POST["sDevice"]) ? StatusDevice : 0) +
    (isset($_POST["sOther"]) ? StatusOther : 0) +
    (isset($_POST["sElse"]) ? StatusElse : 0) +
    (isset($_POST["sPlan"]) ? StatusPlan : 0);
$plan = $_POST["plan"];
$result = $_POST["result"];
$note = $_POST["note"];

$lid = $_POST["lid"];
$lhour = $_POST["lhour"];
$lmin = $_POST["lmin"];
$fsn = $_POST["fsn"];
$tsn = $_POST["tsn"];
$event = $_POST["event"];
$lnote = $_POST["lnote"];

if (! isset($nid)) {
    header("location: main.php");
    require "conx.php";
}

$sqlnight = "UPDATE ObsNight SET " .
    "Operator = '$operator', " .
    "WeatherGeneral = $weatherg, " .
    "WeatherDesc = '$weatherd', " .
    "Plan = '$plan', " .
    "Result = '$result', " .
    "Note = '$note', " .
    "Status = $status " .
    "WHERE NightID = '$nid'";
$conn->query($sqlnight);

for ($i = 0; $i < count($lid); $i++) {
    $ltime = $lhour[$i] * 3600 + $lmin[$i] * 60;
    $event2 = str_replace("'", "''", $event[$i]);
    $lnote2 = str_replace("'", "''", $lnote[$i]);
    $fsn2 = is_int($fsn[$i]) ? $fsn[$i] : "NULL";
    $tsn2 = is_int($tsn[$i]) ? $tsn[$i] : "NULL";

    if ($lid[$i] > 0 && $lhour[$i] > 0) {
        $sqllog = "UPDATE ObsLog SET " .
            "LogTime = $ltime, " .
            "FromSN = $fsn2, " .
            "ToSN = $tsn2, " .
            "Event = '$event2', " .
            "Note = '$lnote2' " .
            "WHERE LineID = $lid[$i]";
        $conn->query($sqllog);
    } elseif ($lid[$i] > 0 && $lhour[$i] == 0) {
        $sqllog = "DELETE FROM ObsLog WHERE LineID = $lid[$i]";
        $conn->query($sqllog);
    } elseif ($lid[$i] == 0 && $lhour[$i] > 0) {
        $sqllog = "INSERT INTO ObsLog (NightID, LogTime, FromSN, ToSN, Event, Note) " .
            "VALUES ('$nid', $ltime, $fsn2, $tsn2, '$event2', '$lnote2')";
        $conn->query($sqllog);
    }
}

if ($savemode == "submit") {
    header("location: nightlog.php?p=p&id=$nid");
} else {
    header("location: nightedit.php?id=$nid");
}

require 'conx.php';
?>
