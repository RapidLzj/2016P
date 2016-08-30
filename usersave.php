<?php
require 'const.php';
require 'priv.php';
require 'conn.php';
require 'util.php';

//if (! $levelEditSys) {
//    header("location: ./");
//    require "conx.php";
//}

$sqltel = "SELECT Telescope, LevelMask FROM Telescope";
$rstel = $conn->query($sqltel);
$tel = array();
while ($row =$rstel->fetch_array()) {
    $tel[$row["Telescope"]] = $row["LevelMask"];
}

// collect form info
$mode = $_POST["savemode"];
$pswd = $_POST["pswd"];
$pid = $_POST["pid"];
$plogin = $_POST["plogin"];
$ppswd1 = $_POST["ppswd1"];
$ppswd2 = $_POST["ppswd2"];
$pname = $_POST["pname"];
$pinfo = $_POST["pinfo"];
$plevel = (isset($_POST["plevellogin"]) ? LevelLogin : 0) +
    (isset($_POST["plevelrun"]) ? LevelRun: 0) +
    (isset($_POST["plevelsys"]) ? LevelSys: 0);
foreach ($tel as $tn => $tm) {
    $plevel += (isset($_POST["pleveltel$tn"]) ? $tm: 0);
}

if (!$levelEditSys && $pid != $aPid) {
    header("location: ./");
    require "conx.php";
}

$err = 0;
// check current user's password
$sqlcheck = "SELECT PID FROM Person WHERE PLogin = '$aLogin' AND PPswd = '$pswd'";
$rscheck = $conn->query($sqlcheck);
if ($rscheck->num_rows == 0) {
    $err |= 1 << 5;
}
if ($mode != "delete") {
// check new login length
    if (strlen($plogin) < 3 || strlen($plogin) > 20)
        $err |= 1 << 0;
// check new runid not exist
    $sqlcheck = "SELECT PID FROM Person WHERE PLogin = '$plogin' AND PID <> $pid";
    $rscheck = $conn->query($sqlcheck);
    if ($rscheck->fetch_array()) {
        $err |= 1 << 1;
    }
// user name
    if ($pname == "")
        $err |= 1 << 2;
// user new password
    if ($ppswd1 != $ppswd2)
        $err |= 1 << 3;
    if ($pid == 0 && $ppswd1 == "")
        $err |= 1 << 4;
}

// return to editor or save data and goto rundetail
if ($err != 0) {
    setcookie("PID",    $pid);
    setcookie("PLogin", $plogin);
    setcookie("PLevel", $plevel);
    setcookie("PName",  $pname);
    setcookie("PInfo",  $pinfo);
    header("location: useredit.php?err=$err&id=$pid");
} else {
    if ($mode == "delete") {
        $sqldel = "DELETE FROM Person WHERE PID = $pid";
        $conn->query($sqldel);
        header("location: userlist.php");
    } else {
        if ($pid == 0) {
            $sqlnew = "INSERT INTO Person (PLogin, PPswd, PLevel, PName, PInfo) " .
                "VALUES ('$plogin', '$ppswd1', $plevel, '$pname', '$pinfo')";
            $conn->query($sqlnew);
        } else {
            $sqlnew = "UPDATE Person SET " .
                ($pid != $aPid ? "PLogin = '$plogin', PLevel = $plevel, " :"") .
                ($ppswd1 != "" ? "PPswd = '$ppswd1', " : "").
                "PName = '$pname', " .
                "PInfo = '$pinfo'  " .
                "WHERE PID = $pid";
            $conn->query($sqlnew);
        }
        header("location: userlist.php");
    }
}
require "conx.php";

?>
