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

$sqlnight = "SELECT RunID, NightID, MJD, DateStr, Status, Operator, " .
    "WeatherGeneral, WeatherDesc, Plan, Result, Note, LastEdit " .
    "FROM ObsNight n WHERE NightID = '$nid'";
$rsnight = $conn->query($sqlnight);
$rownight = $rsnight->fetch_array();
if (! $rownight) {
    header("location: main.php");
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

$plan = $parse->text($rownight["Plan"]);
$result = $parse->text($rownight["Result"]);
$note = $parse->text($rownight["Note"]);
$status = $rownight["Status"];
$lastedit = $rownight["LastEdit"];

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


$pagetitle = ($preview ? "Preview " : "") . "Log $dates(J$mjd)";

require 'head.php';

echo "<p class='title'>Observation Log of $dates(J$mjd) ".
    ($preview ? "<span class='note'>preview</span>" : "" ) .
    "</p>";


echo "<table class='nightinfo'>\n";
echo "<tr>" .
    "<td class='field'>Run</td><td class='value'><a href='rundetail.php?id=$runid'>$runid</a></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Telescope</td><td class='value'>$telen</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Filters</td><td class='value'>$filts</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Operator</td>" .
    "<td class='value'>$operator</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Status</td>" .
    "<td class='value'>";
for ($s = 1; $s < $nStatus; $s++) {
    $c = ($status & $StatusArr[$s]) != 0;
    echo ($c ? "&check;" : "&cross;") .
        " <span class='".($c ? "checked" : "uncheck")."'>$StatusText[$s]</span> &nbsp; ";
}
echo "</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Weather General</td>" .
    "<td class='value'>" .
    "Lightning: $Weather_Lightning[$weatherl]\n" .
    "&nbsp;Wind: $Weather_Wind[$weatherw]\n" .
    "&nbsp;Humidity: $Weather_Humidity[$weatherh]\n" .
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
    echo "<span class='note'>No log</span>";
} else {
    $rowid = 0;
    echo "<table id='tblog'>\n" .
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
    "<td class='field'></td>" .
    "<td class='value note' style='text-align: right;'>" .
    "Last submit time: $lastedit. Print time: $now (UTC)" .
    "</td>" .
    "</tr>\n";
if ($preview) {
    echo "<tr>" .
        "<td class='field'></td>" .
        "<td class='value' style='text-align: right;'>" .
        "<a href='nightedit.php?id=$nid'>Back to Editor</a> &emsp; " .
        "<a href='nightsubmit.php?id=$nid'>&#x1f310;Submit</a>" .
        "</td>" .
        "</tr>";
}
echo "</table>\n";
echo "<p></p>\n";

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
