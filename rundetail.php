<?php
require 'const.php';
require 'priv.php';
require 'conn.php';
require 'util.php';

$runid = $_GET["id"];
if (! isset($runid)) {
    header("location: main.php");
    require "conx.php";
}
$pagetitle = 'Run $runid Info';

$sqlrun = "SELECT RunID, Telescope, FromDate, ToDate, Filters, Note, ToJD-FromJD+1 AS Nights " .
          "FROM ObsRun r WHERE RunID = '$runid'";
$rsrun = $conn->query($sqlrun);
if ($rsrun->num_rows == 0) {
    header("location: main.php");
    require "conx.php";
}
$rowrun = $rsrun->fetch_array();
$teles = $rowrun["Telescope"];
$fdate = $rowrun["FromDate"];
$tdate = $rowrun["ToDate"];
$filts = $rowrun["Filters"];
$rnote  = $rowrun["Note"];
$nights = $rowrun["Nights"];

$sqltel = "SELECT FullName, LevelMask FROM Telescope WHERE Telescope = '$teles'";
$rstel = $conn->query($sqltel);
$rowtel = $rstel->fetch_array();
$levelTeles = ($aLevel & $rowtel["LevelMask"]) != 0;
$telen = $rowtel["FullName"];

$sqlnight = "SELECT NightID, MJD, DateStr, Status, " .
            "(SELECT COUNT(*) FROM ObsLog WHERE NightID = n.NightID) AS LogCnt ".
            "FROM ObsNight n WHERE RunID = '$runid'";
$rsnight = $conn->query($sqlnight);


require 'head.php';

echo "<p class='title'>Run $runid" .
    ($levelEditRun ? "<a href='runedit.php?id=$runid'>&#x1f4dd;</a>" : "") .
    "</p>\n";

echo "<table id='tbrund'>\n" .
    "<tr><td>Date: $fdate &rArr; $tdate | ".
    "Filters:  $filts | " .
    "<span class='note'>Telescope: $telen</span></td>" .
    "<tr><td>Note: <span class='note'>$rnote</span></td></tr>\n" .
    "</table>\n";
echo "<p></p>";

$rowid = 0;
echo "<table id='tbnight'>\n" .
    "<tr>\n" .
    "<th title='Line No'>&numero;</th>\n" .
    "<th title='Local Date and Julian Day'>Date</th>\n";
for ($i = 1; $i < $nStatus; $i++) {
    echo "<th title='$StatusTitle[$i]'>$StatusText[$i]</th>";
}
echo "<th title='Log Items Count'>Log</th>\n";
if ($levelTeles) {
    echo "<th title='Write log'>Edit</th>\n";
}
echo "</tr>\n";

while ($row = $rsnight->fetch_array()) {
    $rowalt = ++$rowid % 2;
    $nid = $row["NightID"];
    $dates = $row["DateStr"];
    $mjd = $row["MJD"];
    $status = $row["Status"];
    $logcnt = $row["LogCnt"];
    echo "<tr class='rowalt$rowalt'>\n" .
        "<td class='sn'>$rowid</td>\n" .
        "<td class='date'><a href='nightlog.php?id=$nid' title='View log'>$dates/J$mjd</a></td>\n";
    for ($i = 1; $i < $nStatus; $i++) {
        if ($status == 0)
            $ss = ".";
        elseif (($status & StatusDoing) != 0)
            $ss = "&#x1f51b;";
        elseif (($status & $StatusArr[$i]) != 0)
            $ss = "$StatusText[$i]";
        else
            $ss = "-";
        echo "<td class='status' title='$StatusTitle[$i]'>$ss</td>\n";
    }
    echo "<td class='num'>$logcnt</td>\n";
    if ($levelTeles) {
        echo "<td class='write'><a href='nightedit.php?id=$nid' title='Write log'>&#x1f4dd;</a></td>";
    }
    echo "</tr>\n";
}

echo "</table>\n<p></p>\n";

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
