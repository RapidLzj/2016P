<?php
require 'const.php';
require 'priv.php';
require 'conn.php';
require 'util.php';

if (! $levelEditRun) {
    header("location: main.php");
    require "conx.php";
}

$runid = $_GET["id"];
if (! isset($runid)) { // new run
    $runid = "NEW";
}
$err = $_GET["err"];
if (! isset($err)) $err = 0;
$errmsg = array(
    "Run ID must be 3-8 characters.",
    "Run ID Already exists.",
    "Start date format invalid, must be yyyymmdd style.",
    "End date format invalid, must be yyyymmdd style.",
    "Start date is later than end date.",
    "New date range may exclude nights with existing log.",
    "New date range conflict with other run.",
    "Cannot delete run with observed log."
);

if ($err == 0) {
    // if err is 0, the request comes from directly url, not after save
    $sqlrun = "SELECT RunID, Telescope, FromJD, ToJD, FromDate, ToDate, " .
        "Filters, Note " .
        "FROM ObsRun r WHERE RunID = '$runid'";
    $rsrun = $conn->query($sqlrun);
    $rowrun = $rsrun->fetch_array();
    if ($rowrun) {
        $oldid = $rowrun["RunID"];
        $teles = $rowrun["Telescope"];
        $fdate = $rowrun["FromDate"];
        $tdate = $rowrun["ToDate"];
        $filts = str_replace("'", "''", $rowrun["Filters"]);
        $rnote = str_replace("'", "''", $rowrun["Note"]);
    } else {
        $oldid = "";
        $teles = "";
        $fdate = date("Ymd");
        $tdate = date("Ymd");
        $filts = "";
        $rnote = "";
    }
} else {
    // comes from save page, use data in session
    $oldid = $_COOKIE["OldID"];
    $runid = $_COOKIE["RunID"];
    $teles = $_COOKIE["Telescope"];
    $fdate = $_COOKIE["FromDate"];
    $tdate = $_COOKIE["ToDate"];
    $filts = str_replace("'", "''", $_COOKIE["Filters"]);
    $rnote = str_replace("'", "''", $_COOKIE["Note"]);
}

$pagetitle = "Edit Run $oldid";

$sqltel = "SELECT Telescope, FullName FROM Telescope";
$rstel = $conn->query($sqltel);

if ($oldid == "") {
    $mode = "nodel";
} else {
    $sqlnight = "SELECT NightID FROM ObsNight WHERE RunID = '$oldid' AND Status > 0";
    $rsnight = $conn->query($sqlnight);
    if ($rsnight->fetch_array()) {
        $mode = "nodel";
    } else {
        $mode = "candel";
    }
}



require 'head.php';

echo "<p class='title'>Run $runid</p>";

echo "<form action='runsave.php' method='post'>";

echo "<table class='editor'>\n";

echo "<tr>" .
    "<th class='field'>RunID</th>" .
    "<td class='value'>" .
    "<input type='text' class='tinytext' name='runid' value='$runid' maxlength='8' />" .
    "<input type='hidden' name='oldid' value='$oldid' />" .
    "<input type='hidden' name='savemode' value='edit' />" .
    "&emsp; <b>Local Dates</b> &nbsp;" .
    "<input type='text' class='tinytext' name='fdate' value='$fdate' maxlength='8' title='yyyymmdd Format' />" .
    " &rArr; " .
    "<input type='text' class='tinytext' name='tdate' value='$tdate' maxlength='8' title='yyyymmdd Format' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Filters</th>" .
    "<td class='value' title='Just for description, not connected to telescope'>" .
    "<input type='text' class='longtext' name='filters' value='$filts' maxlength='255' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Telescope</th>" .
    "<td class='value'>" .
    "<select name='teles' class='longtext'>";
while ($rowtel = $rstel->fetch_array()) {
    $telname = $rowtel["Telescope"];
    $telfull = $rowtel["FullName"];
    $s = ($teles == $telname) ? "selected" : "";
    echo "<option $s value='$telname'>$telname : $telfull</option>";
}
echo "</select>" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Note</th>" .
    "<td class='value'>" .
    "<input type='text' class='longtext' name='rnote' value='$rnote' maxlength='255' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'></th>" .
    "<td class='value'>" .
    "<button type='submit' onclick='savemode.value=\"edit\";'>Save</button> &emsp; " .
    ($mode == "candel" ? "<button type='submit' onclick='savemode.value=\"delete\";'>Delete</button>" : "") .
    "</td></tr>\n";
if ($err != 0) {
    echo "<tr>" .
        "<th class='field'><span class='error'>Error</span></th>" .
        "<td class='value'>";
    for ($e = 0; $e < count($errmsg); $e++) {
        if (($err & (1 << $e)) != 0) {
            echo "<span class='error'>$errmsg[$e]</span><br />";
        }
    }
    echo "</td></tr>\n";
}
echo "</table>\n";
echo "<p></p>";

echo "</form>";

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
