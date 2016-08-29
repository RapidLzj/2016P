<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Home';
require 'conn.php';
require 'util.php';

// order: 0, default, by date desc, 1, by telescope, 2 by status (ing-ed-to)
$order = $_GET["order"];
if (! isset($order) || ! is_numeric($order)) $order = 0;
if ($order != 1 && $order != 2) $order = 0;

$sqlrun = "SELECT RunID, Telescope, FromDate, ToDate, Filters, Note, ToJD-FromJD+1 AS Nights, ".
          "(SELECT count(*) FROM ObsNight WHERE RunID = r.RunID AND (Status = 0  OR Status % 2 = 1)) AS Night0, " .
          "(SELECT count(*) FROM ObsNight WHERE RunID = r.RunID AND (Status > 0 AND Status % 2 = 0)) AS Night1  " .
          "FROM ObsRun r";
$orderby = array("1 DESC", "2, 1 DESC", "10 DESC, 1 DESC")[$order];
$sqlrun = "SELECT RunID, Telescope, FromDate, ToDate, Filters, Note, " .
    "Nights, Night0, Night1, IF(Night1=0, 0, IF(Night0=0, 1, 2)) AS Status " .
    "FROM ($sqlrun) rr ORDER BY $orderby";
$rsrun = $conn->query($sqlrun);

require 'head.php';

echo "<p class='title'>Salut, $aName!</p>";

$runcnt = $rsrun->num_rows;
$rowid = 0;
echo "<p class='title'>$runcnt Runs</p>";

$statust = array("&#x0262f;", "&#x02714;", "&#x1f52d;");
$statuss = array("&#x1f51c; Not Start", "&#x1f51a; Finished", "&#x1f51b; Observing");

if ($runcnt == 0) {
    echo "<p class='note'>No run in system now.</p>";
} else {
    echo "<table id='tbrun' class='tblist'>\n";
    echo "<tr><td colspan='8'>Arrange By: " .
        ($order == 0 ? "<b>&check; &#x1f4c5; Date</b>" : "<a href='./?order=0'>&#x1f4c5; Date</a>") .
        " | " .
        ($order == 1 ? "<b>&check; &#x1f52d; Telescope</b>" : "<a href='./?order=1'>&#x1f52d; Telescope</a>") .
        " | " .
        ($order == 2 ? "<b>&check; &#x1f5c2; Status</b>" : "<a href='./?order=2'>&#x1f5c2; Status</a>") .
        "</td></tr>";
    echo "<tr>\n" .
        "<th title='Line No'>&numero;</th>\n" .
        "<th title='ID'>&#x1f3f7;</th>\n" .
        "<th title='Telescope'>&#x1f52d;</th>\n" .
        "<th title='Start and End Date'>&#x1f4c5;</th>\n" .
        "<th title='Night Count'>&#x2728;</th>\n" .
        "<th title='Status'>&#x1f5c2;</th>\n" .
        "<th title='Note'>&#x1f4dc;</th>\n" .
        ($levelEditRun ? "<th title='Edit'>&#x1f4dd;</th>\n" : "") .
        "</tr>\n";
    while ($row = $rsrun->fetch_array()) {
        $rowalt = ++$rowid % 2;
        $runid = $row["RunID"];
        $teles = $row["Telescope"];
        $fdate = $row["FromDate"];
        $tdate = $row["ToDate"];
        $filts = $row["Filters"];
        $note  = $row["Note"];
        $nights = $row["Nights"];
        $night0 = $row["Night0"];
        $night1 = $row["Night1"];
        $status = $row["Status"];
        //$statust = ($night1 == 0 ? "&#x0262f;" :
        //           ($night0 == 0 ? "&#x02714;" :
        //                           "&#x1f52d;"));
        //$statuss = ($night1 == 0 ? "&#x1f51c; Not Start" :
        //           ($night0 == 0 ? "&#x1f51a; Finished" :
        //                          "&#x1f51b; Observing"));
        echo "<tr class='rowalt$rowalt'>\n" .
            "<td class='sn'>$rowid</td>\n" .
            "<td class='id' title='Run ID'>$statust[$status] " .
            "<a href='rundetail.php?id=$runid' title='View detail info'>$runid</a>" .
            "</td>\n" .
            "<td class='tel'>" .
            "<a href='rundetail.php?id=$runid' title='View detail info'>$teles</a>" .
            "</td>\n" .
            "<td class='date'>" .
            "<a href='rundetail.php?id=$runid' title='View detail info'>$fdate &rArr; $tdate</a>" .
            "</td>\n" .
            "<td class='num'>$nights</td>\n" .
            "<td class='status'>$statuss[$status]</td>\n" .
            "<td class='note'>$note</td>\n" .
            ($levelEditRun ? "<td title='Edit run'><a href='runedit.php?id=$runid'>&#x1f4dd;</a></td>\n" : "") .
            "</tr>\n";
    }
    echo "</table>\n";
    echo "<p></p>";
}

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
