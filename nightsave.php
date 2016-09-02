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
$status = StatusDoing;
for ($s = 0; $s < $nStatus; $s++)
    $status += (isset($_POST["s$StatusText[$s]"]) ? $StatusArr[$s] : 0);
$plan = str_replace("'", "''", $_POST["plan"]);
$result = str_replace("'", "''", $_POST["result"]);
$note = str_replace("'", "''", $_POST["note"]);

$lid = $_POST["lid"];
$lhour = $_POST["lhour"];
$lmin = $_POST["lmin"];
$fsn = $_POST["fsn"];
$tsn = $_POST["tsn"];
$event = $_POST["event"];
$lnote = $_POST["lnote"];

if (! isset($nid)) {
    header("location: ./");
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
    $fsn2 = is_numeric($fsn[$i]) ? $fsn[$i] : "NULL";
    $tsn2 = is_numeric($tsn[$i]) ? $tsn[$i] : "NULL";

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
