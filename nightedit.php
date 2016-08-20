<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Log Write';
require 'conn.php';
require 'util.php';

$nid = $_GET["id"];
if (! isset($nid)) {
    header("location: main.php");
    require "conx.php";
}

$sqlnight = "SELECT RunID, NightID, MJD, DateStr, Status, Operator, " .
    "WeatherGeneral, WeatherDesc, Plan, Result, Note, SubmitTime, AcceptTime " .
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
$plan = $rownight["Plan"];
$result = $rownight["Result"];
$note = $rownight["Note"];
$status = $rownight["Status"];
$submittime = $rownight["SubmitTime"];
$accepttime = $rownight["AcceptTime"];

if (! is_null($accepttime)) {
    // accepted log cannot be edited again
    header("location: nightlog.php?id=$nid");
    require "conx.php";
}

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

if (! $levelTeles) {
    header("location: nightlog.php?id=$nid");
    require "conx.php";
}

function selhour($selname, $cls, $value) {
    $res = "<select name='$selname' class='$cls'>";
    $res .= "<option value='0'" . ($value == 0 ? " selected" : "") . ">&cross;</option>";
    for ($v = 15; $v < 33; $v++) {
        $res .= "<option value='$v'" . ($v == $value ? " selected" : "") . ">$v</option>";
    }
    $res .= "</select>";
    return $res;
}
function selmin($selname, $cls, $value) {
    $res = "<select name='$selname' class='$cls'>";
    for ($v = 0; $v < 60; $v++) {
        $res .= "<option value='$v'" . ($v == $value ? " selected" : "") . ">$v</option>";
    }
    $res .= "</select>";
    return $res;
}
function seltext($selname, $cls, $value, $textarr) {
    $res  = "<select name='$selname' class='$cls'>";
    for ($v = 0; $v < count($textarr); $v++) {
        $res .= "<option value='$v'" . ($v == $value ? " selected" : "") . ">$textarr[$v]</option>";
    }
    $res .= "</select>";
    return $res;
}
function chkbox($chkname, $cls, $text, $value, $title) {
    $res = "<input type='checkbox' title='$title' name='$chkname' value='1' " .
        ($value ? "checked" : "") . " /><span title='$title'>$text</span>\n";
    return $res;
}


require 'head.php';

echo "<p class='title'>Observation Log of $dates(J$mjd)</p>";
if (! is_null($submittime))
    echo "<p class='note'>Edit after submit, last submitting time is $submittime .</p>";

echo "<form action='nightsave.php' method='post'>";
echo "<input type='hidden' name='nightid' value='$nid' />";
echo "<input type='hidden' name='logcnt' value='$logcnt' />";
echo "<input type='hidden' name='savemode' value='save' />";

echo "<table class='editor'>\n";
echo "<tr id='help'><td class='field'><button type='button' onclick='helpinfo.style.display = \"block\";'>Show Help</button></td>\n";
echo "<td class='value'><div id='helpinfo' style='display:none;'>\n" .
    "<p>About Observation Log Fields</p>" .
    "<p class='note'>\n" .
    "Status - AllOK: whole night is useful, no time lost.<br />\n" .
    "Status - Shared: This night is shared with other task, as planned.<br />\n" .
    "Status - None: Nothing has been done in this night.<br />\n" .
    "Status - Weather: Time lost due to bad weather, part or whole night.<br />\n" .
    "Status - Device: Time lost due to device problem, part or whole night.<br />\n" .
    "Status - Other: Other emergency observation happened, part or whole night.<br />\n" .
    "Status - Else: Time lost for reasons else to above.<br />\n" .
    "Status - Plan: Part or whole night is rescheduled.<br />\n" .
    "Weather: In general situations, select one most fits the worst situation.<br />\n" .
    "Weather: General situations with a `&cross;` indicate dome MUST close in that case.<br />\n" .
    "Plan: Original plan for this night. Especially test must be listed.<br />\n" .
    "Time: Local time. Hours from 24 to 33 means 0 to 9 in the next cal day.<br />\n" .
    "Time: Choose `&cross;` as hour means to DELETE this line." .
    "File No: File No related to this log item. If no exposure, this can be empty.<br />\n" .
    "Event: Any thing, operation or environment or device or other things.<br />\n" .
    "Empty lines: After current log, there are extra empty lines for new log, save log will get more." .
    "Result: What has been done in this night, according to original plan.<br />\n" .
    "</p><p><button type='button' onclick='helpinfo.style.display = \"none\";'>Hide Help</button></p>\n" .
    "</div></td></tr>\n";

echo "<tr>" .
    "<td class='field'>Date &amp; Run</td>" .
    "<td class='value'>$dates(J$mjd) / <a href='rundetail.php?id=$runid'>$runid</a></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Telescope</td>" .
    "<td class='value'>$teles ($telen)</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Filters</td>" .
    "<td class='value'>$filts</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Operator</td>" .
    "<td class='value'><input type='text' name='operator' value='$operator' class='longtext'  /></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Status</td>" .
    "<td class='value'>";
for ($s = 0; $s < $nStatus; $s++) {
    echo chkbox("s$StatusText[$s]", "", $StatusText[$s], ($status & $StatusArr[$s]) != 0, $StatusTitle[$s]);
}
echo "</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Weather General</td>" .
    "<td class='value'>" .
    "Lightning: " . seltext("weatherl", "shorttext", $weatherl, $Weather_Lightning) . "\n" .
    "&nbsp;Wind: " . seltext("weatherw", "shorttext", $weatherw, $Weather_Wind) . "\n" .
    "&nbsp;Humidity: " . seltext("weatherh", "shorttext", $weatherh, $Weather_Humidity) . "\n" .
    "</td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Weather Desc</td>" .
    "<td class='value'><input type='text' name='weatherd' value='$weatherd' class='longtext'/></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Plan</td>" .
    "<td class='value'><textarea name='plan' class='longbox'>$plan</textarea></td>" .
    "</tr>\n";
echo "<tr><td class='field'>Log</td><td class='value'>\n";

$rowid = 0;
echo "<table id='tblog'>\n" .
    "<tr>\n" .
    "<th title='Log Serial Num'>&#x1f5d1;</th>\n" .
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
    $fsn = $row["FromSN"];
    $tsn = $row["ToSN"];
    $event = $row["Event"];
    $lnote = $row["Note"];
    echo "<tr class='rowalt$rowalt'>\n" .
        "<td class='sn'>$rowid" .
        "<input type='hidden' name='lid[]' value='$lid' /></td>\n";
    echo "<td class='ctime'>" .
        selhour("lhour[]", "time", $lhour) . ":" . selmin("lmin[]", "time", $lmin) .
        "</td>\n";
    echo "<td class='cfileno'>" .
        "<input type='text' name='fsn[]' class='fileno' value='$fsn' maxlength='4' />&rArr;" .
        "<input type='text' name='tsn[]' class='fileno' value='$tsn' maxlength='4' />" .
        "</td>\n";
    echo "<td class='cevent'><input type='text' name='event[]' class='event' value='$event' maxlength='255' /></td>\n";
    echo "<td class='clnote'><input type='text' name='lnote[]' class='lnote' value='$lnote' maxlength='255' /></td>\n";
    echo "</tr>\n";
}
for ($i = 0; $i < 10; $i++) {
    $rowalt = ++$rowid % 2;
    echo "<tr class='rowalt$rowalt'>\n" .
        "<td class='sn'>$rowid" .
        "<input type='hidden' name='lid[]' value='0' /></td>\n";
    echo "<td class='ctime'>" .
        selhour("lhour[]", "time", 0) . ":" . selmin("lmin[]", "time", 0) .
        "</td>\n";
    echo "<td class='cfileno'>" .
        "<input type='text' name='fsn[]' class='fileno' value='' maxlength='4' />&rArr;" .
        "<input type='text' name='tsn[]' class='fileno' value='' maxlength='4' />" .
        "</td>\n";
    echo "<td class='cevent'><input type='text' name='event[]' class='event' value='' maxlength='255' /></td>\n";
    echo "<td class='clnote'><input type='text' name='lnote[]' class='lnote' value='' maxlength='255' /></td>\n";
    echo "</tr>\n";
}
echo "</table>\n<p></p>\n";

echo "</td></tr>";
echo "<tr>" .
    "<td class='field'>Result</td>" .
    "<td class='value'><textarea name='result' class='longbox'>$result</textarea></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'>Note</td>" .
    "<td class='value'><textarea name='note' class='longbox'>$note</textarea></td>" .
    "</tr>\n";
echo "<tr>" .
    "<td class='field'></td>" .
    "<td class='value'>" .
    "<button type='submit' title='Temporary save, will continue editing' onclick='savemode.value=\"save\";'>&#x1f4be;Save</button>" .
    "<button type='submit' title='Save and submit' onclick='savemode.value=\"submit\";'>&#x1f310;Submit</button>" .
    "</td>\n" .
    "</tr>";
echo "</table>\n";
echo "<p></p>\n";

echo "</form>";

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
