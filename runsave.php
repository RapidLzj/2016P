<?php
require 'const.php';
require 'priv.php';
require 'conn.php';
require 'util.php';

if (! $levelEditRun) {
    header("location: ./");
    require "conx.php";
}

// collect form info
$mode = $_POST["savemode"];
$oldid = $_POST["oldid"];
$runid = $_POST["runid"];
$teles = $_POST["teles"];
$fdate = $_POST["fdate"];
$tdate = $_POST["tdate"];
$filts = $_POST["filters"];
$rnote = $_POST["rnote"];
$tcode = substr($teles, 0, 1);

// telescope timezone
$sqltel = "SELECT TimeZone FROM Telescope WHERE Telescope = '$teles'";
$rstel = $conn->query($sqltel);
$rowtel = $rstel->fetch_array();
$tz = $rowtel["TimeZone"];

if ($oldid != "") {
// collect old info
    $sqlrun = "SELECT RunID, FromJD, ToJD, Telescope " .
        "FROM ObsRun r WHERE RunID = '$oldid'";
    $rsrun = $conn->query($sqlrun);
    $rowrun = $rsrun->fetch_array();
    $ofjd = $rowrun["FromJD"];
    $otjd = $rowrun["ToJD"];
    $oteles = $rowrun["Telescope"];
    $otcode = substr($oteles, 0, 1);

    $sqlnight = "SELECT MIN(MJD) AS MinMJD, MAX(MJD) AS MaxMJD " .
        "FROM ObsNight WHERE RunID = '$oldid' AND Status > 0";
    $rsnight = $conn->query($sqlnight);
    $rownight = $rsnight->fetch_array();
    if ($rownight) {
        $minmjd = $rownight["MinMJD"];
        $maxmjd = $rownight["MaxMJD"];
    } else {
        $minmjd = 9999;
        $maxmjd = -1;
    }
    if (is_null($minmjd)) $minmjd = 9999;
    if (is_null($maxmjd)) $maxmjd = -1;
} else {
    $ofjd = 9999;
    $otjd = -1;
    $otcode = "";
    $minmjd = 9999;
    $maxmjd = -1;
}

$err = 0;
// check new runid length
if (strlen($runid) < 3 || strlen($runid) > 8)
    $err |= 1 << 0;
// check new runid not exist
if ($runid != $oldid) {
    $sqlcheck = "SELECT RunID FROM ObsRun WHERE RunID = '$runid'";
    $rscheck = $conn->query($sqlcheck);
    if ($rscheck->fetch_array()) {
        $err |= 1 << 1;
    }
}
// check from and to date format, and transfer to mjd
if (mode == "delete") {
    if ($maxmjd > 0)
        $err |= 1 << 7;
} else {
    $fjd = datetomjd($fdate, $tz);
    if ($fjd < 0)
        $err |= 1 << 2;
    $tjd = datetomjd($tdate, $tz);
    if ($tjd < 0)
        $err |= 1 << 3;
    if ($fjd > 0 && $tjd > 0) { // only if date is valid, check the following
        // check from date earlier than or equal to end date
        if ($fjd > $tjd)
            $err |= 1 << 4;
        // check data range not exclude existing log
        if ($oldid != "") {
            if ($fjd > $minmjd || $tjd < $maxmjd)
                $err |= 1 << 5;
        }
        // check date range not covered by other run at same telescope
        $sqlcheck = "SELECT RunID FROM ObsRun " .
            "WHERE RunID <> '$oldid' AND Telescope = '$teles' AND FromJD <= $tjd AND $fjd <= ToJD";
        $rscheck = $conn->query($sqlcheck);
        if ($rscheck->fetch_array()) {
            $err |= 1 << 6;
        }
    }
}

// return to editor or save data and goto rundetail
if ($err != 0) {
    setcookie("OldID",     $oldid);
    setcookie("RunID",     $runid);
    setcookie("Telescope", $teles);
    setcookie("FromDate",  $fdate);
    setcookie("ToDate",    $tdate);
    setcookie("Filters",   $filts);
    setcookie("Note",      $rnote);
    header("location: runedit.php?err=$err&id=$runid");
} else {
    if ($mode == "delete") {
        $sqldel = "DELETE FROM ObsNight WHERE RunID = '$oldid'";
        $conn->query($sqldel);
        $sqldel = "DELETE FROM ObsRun WHERE RunID = '$oldid'";
        $conn->query($sqldel);
        header("location: ./");
    } else {
        $filts = str_replace("'", "''", $filts);
        $rnote = str_replace("'", "''", $rnote);
        if ($oldid == "") {
            $sqlnew = "INSERT INTO ObsRun (RunID, Telescope, FromJD, ToJD, FromDate, ToDate, Filters, Note) " .
                "VALUES ('$runid', '$teles', $fjd, $tjd, '$fdate', '$tdate', '$filts', '$rnote')";
            $conn->query($sqlnew);
            for ($d = $fjd; $d <= $tjd; $d++) {
                $nid = sprintf("%04d%1s", $d, $tcode);
                $ds = mjdtodate($d, $tz);
                $sqlnew = "INSERT INTO ObsNight (NightID, MJD, RunID, DateStr, Status) " .
                    "VALUES ('$nid', $d, '$runid', '$ds', 0)";
                $conn->query($sqlnew);
            }
        } else {
            $sqlnew = "UPDATE ObsRun SET " .
                "RunID = '$runid', " .
                "Telescope = '$teles', " .
                "FromJD = $fjd, " .
                "ToJD = $tjd, " .
                "FromDate = '$fdate', " .
                "ToDate = '$tdate', " .
                "Filters = '$filts', " .
                "Note = '$rnote' " .
                "WHERE RunID = '$oldid'";
            //echo "$sqlnew<br>\n";
            $conn->query($sqlnew);
            for ($d = $ofjd; $d <= $otjd; $d++) {
                $nid = sprintf("%04d%1s", $d, $tcode);
                $onid = sprintf("%04d%1s", $d, $otcode);
                if ($fjd <= $d && $d <= $tjd) {
                    // old day in new run, keep row, but update nightid and runid, if necessory
                    $sqlnew = "UPDATE ObsNight SET " .
                        "NightID = '$nid', RunID = '$runid' " .
                        "WHERE NightID = '$onid'";
                    //echo "$sqlnew<br>\n";
                    $conn->query($sqlnew);
                    // change night id for existing log
                    $sqlnew = "UPDATE ObsLog SET NightID = '$nid' WHERE NightID = '$onid'";
                    //echo "$sqlnew<br>\n";
                    $conn->query($sqlnew);
                } else {
                    // old day out of new run, such night should not have log, so no need to delete
                    $sqlnew = "DELETE FROM ObsNight WHERE NightID = '$onid'";
                    //echo "$sqlnew<br>\n";
                    $conn->query($sqlnew);
                }
            }
            for ($d = $fjd; $d <= $tjd; $d++) {
                if ($ofjd < $d && $d > $otjd) {
                    $nid = sprintf("%04d%1s", $d, $tcode);
                    $ds = mjdtodate($d, $tz);
                    // only new days out of old plan need insert
                    $sqlnew = $sqlnew = "INSERT INTO ObsNight (NightID, MJD, RunID, DateStr, Status) " .
                        "VALUES ('$nid', $d, '$runid', '$ds', 0)";
                    //echo "$sqlnew<br>\n";
                    $conn->query($sqlnew);
                }
            }
        }
        header("location: rundetail.php?id=$runid");
    }
}
require "conx.php";

?>
