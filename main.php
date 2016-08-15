<?php
require 'priv.php';
$pagetitle = 'Home';
require 'conn.php';
require 'const.php';
require 'util.php';

$sqldate = "select event_date, count(*) as cnt from event_log " .
    "group by event_date order by event_date desc limit 15";
$rsdate = $conn->query($sqldate);

$sqltype = "select event_type, count(*) as cnt from event_log " .
    "group by event_type";
$rstype = $conn->query($sqltype);

require 'head.php';

echo "<p class='title'>Bonjour, Rapid!</p>";

$datecnt = $rsdate->num_rows;
echo "<p class='title'>Event by dates</p>";
if ($datecnt == 0) {
    echo "<p>No event now.</p>";
} else {
    echo "<p>📅  ";
    while ($row = $rsdate->fetch_array()) {
        echo " | <a href='day.php?day=" . $row["event_date"] . "'>" .
            datestr($row["event_date"]) . "<sup>(" . $row["cnt"] . ")</sup></a>";
    }
    echo "</p>";
    //echo " || <a href='list.php'>ALL <sup>($papercnt)</sup></a></p>";
}

$typecnt = $rstype->num_rows;
echo "<p class='title'>Event by types</p>";
if ($typecnt == 0) {
    echo "<p>No event now.</p>";
} else {
    echo "<p>$typecnt 🏷  ";
    while ($row = $rstype->fetch_array()) {
        echo " | <a href='day.php?type=" . $row["event_type"] . "'>" .
            $row["event_type"] . "<sup>(" . $row["cnt"] . ")</sup></a>";
    }
    echo "</p>";
    //echo " || <a href='list.php'>ALL <sup>($papercnt)</sup></a></p>";
}

require 'searchbox.php';
?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
