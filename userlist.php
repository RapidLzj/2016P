<?php
require 'const.php';
require 'priv.php';
$pagetitle = 'Persons';
require 'conn.php';
require 'util.php';

if (! $levelEditSys) {
    header("location: main.php");
    require "conx.php";
}

$sqluser = "SELECT PID, PLogin, PLevel, PName, PInfo " .
          "FROM Person p ORDER BY PLogin";
$rsuser = $conn->query($sqluser);

$sqltel = "SELECT Telescope, LevelMask FROM Telescope";
$rstel = $conn->query($sqltel);
$tt = $rstel->num_rows + 3;
$tel = array();
while ($row =$rstel->fetch_array()) {
    $tel[$row["Telescope"]] = $row["LevelMask"];
}


require 'head.php';

echo "<p class='title'>System Users</p>";

$usercnt = $rsuser->num_rows;
$rowid = 0;
if ($usercnt == 0) {
    echo "<p class='note'>No other user in system now.</p>";
} else {
    echo "<table id='tbuser'>\n";
    echo "<tr>\n" .
        "<th rowspan='2' title='Line No'>&numero;</th>\n" .
        "<th rowspan='2' title='Login name'>Login</th>\n" .
        "<th rowspan='2' title='Full name'>Full Name</th>\n" .
        "<th rowspan='2'title='Additional Info'>Extra Info</th>\n" .
        "<th colspan='$tt' title='User level'>User Privileges</th>\n" .
        "<th rowspan='2'title='Edit'>&#x1f4dd;</th>\n" .
        "</tr>\n";
    echo "<tr>\n" .
        "<th title='Can login to system'>Active</th>\n" .
        "<th title='Run Edit'>Run</th>\n" .
        "<th title='User Control'>User</th>\n";
    foreach ($tel as $tn => $tm)
        echo "<th title='Telescope operator'>$tn</th>\n";
    echo "</tr>\n";
    while ($row = $rsuser->fetch_array()) {
        $rowalt = ++$rowid % 2;
        $pid = $row["PID"];
        $plogin = $row["PLogin"];
        $ppswd  = $row["PPswd"];
        $plevel = $row["PLevel"];
        $pname  = $row["PName"];
        $pinfo  = $row["PInfo"];
        $plevellogin = ($plevel & LevelLogin) != 0 ? "&check;" : "&cross;";
        $plevelrun = ($plevel & LevelRun) != 0 ? "&check;" : "&cross;";
        $pleveluser = ($plevel & LevelSys) != 0 ? "&check;" : "&cross;";
        echo "<tr class='rowalt$rowalt'>\n" .
            "<td class='sn'>$rowid</td>\n" .
            "<td class='login'>$plogin</td>\n" .
            "<td class='name'>$pname</td>\n" .
            "<td class='info'>$pinfo</td>\n";
        echo "<td class='level'>$plevellogin</td>\n" .
            "<td class='level'>$plevelrun</td>\n" .
            "<td class='level'>$pleveluser</td>\n";
        foreach ($tel as $tn => $tm)
            echo "<td class='level'>" . (($plevel & $tm) != 0 ? "&check;" : "&cross;") . "</td>\n";
        echo "<td title='Edit run'><a href='useredit.php?id=$pid'>&#x1f4dd;</a></td>\n";
        echo "</tr>\n";
    }
    $rowalt = ++$rowid % 2;
    echo "<tr class='rowalt$rowalt'>" .
        "<td class='sn'>+</td>\n" .
        "<td class='login'></td>\n" .
        "<td class='name'>New User</td>\n" .
        "<td class='info'></td>\n";
    echo "<td class='level'></td>\n" .
        "<td class='level'></td>\n" .
        "<td class='level'></td>\n";
    foreach ($tel as $tn => $tm)
        echo "<td class='level'></td>\n";
    echo "<td title='Edit run'><a href='useredit.php'>&#x1f4dd;</a></td>\n";
        "</tr>";
    echo "</table>\n";
    echo "<p></p>";
}

?>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
