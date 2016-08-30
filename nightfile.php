<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Files on this night';
require 'conn.php';
require 'util.php';

$nid = $_GET["id"];
if (! isset($nid)) {
    header("location: ./");
    require "conx.php";
}

$sqlnight = "SELECT RunID, NightID, MJD, DateStr " .
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

$sqlfile = "SELECT FileID, SN, ObsTime, Type, FilterCode, ExpTime, Object, RADeg, DecDeg " .
    "FROM FileBasic WHERE SUBSTR(FileID, 1, 5) = '$nid' ORDER BY FileID";
$rsfile = $conn->query($sqlfile);
$filecnt = $rsfile->num_rows;
$types = array(
    "B" => "Bias",
    "F" => "Flat",
    "D" => "Dark",
    "S" => "Survey",
    "O" => "Other",
    "T" => "Test",
);


$pagetitle = "Files on $dates(J$mjd)";

require 'head.php';

echo "<p class='title'>Observation Files on $dates(J$mjd)</p>";


if ($filecnt == 0) {
    echo "<span class='note'>No files on this night</span>";
} else {
    $rowid = 0;
    echo "<table id='tbfile' class='tblist'>\n" .
        "<tr>\n" .
        "<th title='File Serial Num'>&numero;</th>\n" .
        "<th title='File ID'>FileID</th>\n" .
        "<th title='Observation Time'>Time</th>\n" .
        "<th title='File Type'>Type</th>\n" .
        "<th title='Filter'>Filter</th>\n" .
        "<th title='Exposure time (seconds)'>ExpT</th>\n" .
        "<th title='Object'>Object</th>\n" .
        "<th title='RA (hms)'>RA</th>\n" .
        "<th title='Dec (dms)'>Dec</th>\n" .
        "</tr>\n";

    while ($row = $rsfile->fetch_array()) {
        $rowalt = ++$rowid % 2;
        $fid = $row["FileID"];
        $sn = $row["SN"];
        $otime = sec2hms($row["ObsTime"]);
        $ftype = $types[$row["Type"]];
        $filter = $row["FilterCode"];
        $expt = $row["ExpTime"];
        $obj = $row["Object"];
        $ras = deg2hms($row["RADeg"]);
        $des = deg2dms($row["DecDeg"]);

        echo "<tr class='rowalt$rowalt'>\n" .
            "<td class='sn'>$sn</td>\n";
        echo "<td class=''>$fid</td>\n";
        echo "<td class=''>$otime</td>\n";
        echo "<td class=''>$ftype</td>\n";
        echo "<td class=''>$filter</td>\n";
        echo "<td class=''>$expt</td>\n";
        echo "<td class=''>$obj</td>\n";
        echo "<td class=''>$ras</td>\n";
        echo "<td class=''>$des</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}
echo "<p></p>\n";

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
