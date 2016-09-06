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
    header("location: ./");
    require "conx.php";
}

$sqlnight = "SELECT RunID, NightID, MJD, DateStr, Status, Operator, " .
    "WeatherGeneral, WeatherDesc, Plan, Result, Note, SubmitTime, AcceptTime " .
    "FROM ObsNight n WHERE NightID = '$nid'";
$rsnight = $conn->query($sqlnight);
$rownight = $rsnight->fetch_array();
if (! $rownight) {
    header("location: ./");
    require "conx.php";
}
$runid = $rownight["RunID"];
$mjd = $rownight["MJD"];
$dates = $rownight["DateStr"];
$status = $rownight["Status"];
$operator = $rownight["Operator"];
$weatherg = $rownight["WeatherGeneral"];
$weatherl = intval($weatherg / 100);
$weatherw = intval($weatherg / 10) % 10;
$weatherh = $weatherg % 10;
$weatherd = $rownight["WeatherDesc"];
$cansubmit = $status > 1 && $operator != "";

$plan = $parse->text($rownight["Plan"]);
$result = $parse->text($rownight["Result"]);
$note = $parse->text($rownight["Note"]);
$status = $rownight["Status"];
$submittime = $rownight["SubmitTime"];
$accepttime = $rownight["AcceptTime"];

$sqlrun = "SELECT RunID, Telescope, Filters " .
          "FROM ObsRun r WHERE RunID = '$runid'";
$rsrun = $conn->query($sqlrun);
$rowrun = $rsrun->fetch_array();
$teles = $rowrun["Telescope"];
$filts = $rowrun["Filters"];

$sqltel = "SELECT FullName, LevelMask FROM Telescope WHERE Telescope = '$teles'";
$rstel = $conn->query($sqltel);
$rowtel = $rstel->fetch_array();
$levelTeles = ($aLevel & $rowtel["LevelMask"]) != 0;
$telen = $rowtel["FullName"];

$sqllog = "SELECT LineID, LogTime, FromSN, ToSN, Event, Note " .
    "FROM ObsLog WHERE NightID = '$nid' ORDER BY LogTime";
$rslog = $conn->query($sqllog);
$logcnt = $rslog->num_rows;

if ($preview && ! $levelTeles) {
    header("location: nightlog.php?id=$nid");
    require "conx.php";
}
$now = date("Y-m-d H:i:s");


$pagetitle = ($preview ? "Preview " : "") . "Log $dates (J$mjd)";

require 'head.php';

echo "<p class='title'>Observation Log of $dates (J$mjd) at $teles ".
    ($preview ? "<span class='note great'>preview</span>" : "" ) .
    (is_null($accepttime) ? "<span class='note great'>This is NOT FINAL version.</span>" : "") .
    "</p>";


echo "<table class='editor'>\n";
echo "<tr>" .
    "<td class='field'>Date &amp; Run</td>" .
    "<td class='value'>$dates (J$mjd) / <a href='rundetail.php?id=$runid'>$runid</a></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Telescope</td>" .
    "<td class='value'>$teles ($telen)</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Filters</td>" .
    "<td class='value'>$filts</td>" .
    "</tr>\n";
if ($status == 0) {
    echo "<tr>" .
        "<td class='field'></td>" .
        "<td class='value'><span class='note great'>NO LOG</span>" .
        "</td>" .
        "</tr>\n";
} elseif (($status & StatusDoing) != 0 && ! $preview) {
    echo "<tr>" .
        "<td class='field'></td>" .
        "<td class='value'><span class='note great'>Log is writing. This is NOT final version.</span>" .
        "</td>" .
        "</tr>\n";
}
echo "<tr>" .
    "<td class='field'>Operator</td>" .
    "<td class='value'>$operator</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Status</td>" .
    "<td class='value'>";
for ($s = 0; $s < $nStatus; $s++) {
    $c = ($status & $StatusArr[$s]) != 0;
    echo ($c ? "&check;" : "&cross;") .
        "<span class='" . ($c ? "checked" : "uncheck") . "'>$StatusText[$s]</span> &nbsp; ";
}
if ($status == StatusDoing) {
    echo "<br /><span class='error'>Please choose proper status.</span>";
}
echo "</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Weather General</td>" .
    "<td class='value'>" .
    "<b>Lightning:</b> $Weather_Lightning[$weatherl]\n" .
    "&nbsp;<b>Wind:</b> $Weather_Wind[$weatherw]\n" .
    "&nbsp;<b>Humidity:</b> $Weather_Humidity[$weatherh]\n" .
    "</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Weather Desc</td>" .
    "<td class='value'>$weatherd</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Plan</td>" .
    "<td class='value'>$plan</td>" .
    "</tr>\n";
echo "<tr><td class='field'>Log</td><td class='value'>\n";

if ($rslog->num_rows == 0) {
    echo "<span class='note'>No log items</span>";
} else {
    $rowid = 0;
    echo "<table id='tblog' class='tblist'>\n" .
        "<tr>\n" .
        "<th title='Log Serial Num'>&numero;</th>\n" .
        "<th title='Event Start Time'>Time</th>\n" .
        "<th title='File Serial Num Range'>File No</th>\n" .
        "<th title='Event/Action'>Event/Action</th>\n" .
        "<th title='Note'>Note</th>\n" .
        "</tr>\n";

    while ($row = $rslog->fetch_array()) {
        $rowalt = ++$rowid % 2;
        $lid = $row["LineID"];
        $ltime = $row["LogTime"];
        $lhour = intval($ltime / 3600);
        $lmin = intval($ltime / 60) % 60;
        $ltimes = sprintf("%02d:%02d", $lhour, $lmin);
        $fsn = $row["FromSN"];
        $tsn = $row["ToSN"];
        if (is_null($fsn)) $fsn = "&cross;";
        if (is_null($tsn)) $tsn = "&cross;";
        $event = $row["Event"];
        $lnote = $row["Note"];
        echo "<tr class='rowalt$rowalt'>\n" .
            "<td class='sn'>$rowid" .
            "</td>\n";
        echo "<td class='ctime'>$ltimes</td>\n";
        echo "<td class='cfileno'>$fsn &rArr; $tsn</td>\n";
        echo "<td class='cevent'>$event</td>\n";
        echo "<td class='clnote'>$lnote</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

echo "</td></tr>";
echo "<tr>" .
    "<td class='field'>Result</td>" .
    "<td class='value'>$result</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Note</td>" .
    "<td class='value'>$note</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td colspan='2' class='value note' style='text-align: right;'>" .
    "Submitted at $submittime. Accepted at $accepttime. Printed at $now (UTC)" .
    "</td>" .
    "</tr>\n";

if ($preview) {
    echo "<tr>" .
        "<td class='field'></td>" .
        "<td class='value' style='text-align: right;'>" .
        "<a href='nightedit.php?id=$nid'>&laquo; Back to Editor &raquo;</a> &emsp; " .
        ($cansubmit ?
            "<a href='nightsubmit.php?id=$nid'>&laquo; &#x1f310;Submit &raquo;</a>" :
            "<span class='error'>Correct error above before submit.</span>") .
        "</td>" .
        "</tr>";
} elseif (is_null($accepttime) && ! is_null($submittime)) {
    echo "<tr>" .
        "<td class='field'></td>" .
        "<td class='value' style='text-align: right;'>" .
        "<span class='great'>This is not a final version.</span>" .
        ($levelEditRun ? "<a href='nightaccept.php?id=$nid'>&laquo; &check;Accept &raquo;</a>" : "") .
        "</td>" .
        "</tr>";
}
echo "<tr>" .
    "<td class='field'></td>" .
    "<td class='value'>" .
    "<a href='nightfile.php?id=$nid'>Files on this night</a>" .
    "</td>" .
    "</tr>";
echo "</table>\n";
echo "<p></p>\n";

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
