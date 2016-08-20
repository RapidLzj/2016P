<?php
require 'const.php';
require 'priv.php';
require 'conn.php';
require 'util.php';

if (! $levelEditSys) {
    header("location: main.php");
    require "conx.php";
}

$pid = $_GET["id"];
if (! isset($pid) || ! is_numeric($pid)) { // new run
    $runid = "0";
}
$err = $_GET["err"];
if (! isset($err)) $err = 0;
$errmsg = array(
    "User login must be 3 to 20 characters.",
    "User login exists.",
    "User name cannot be empty.",
    "New password do not match.",
    "New password must set for new user.",
    "Your password is wrong.",
);

if ($err == 0) {
    // if err is 0, the request comes from directly url, not after save
    $sqluser = "SELECT PID, PLogin, PLevel, PName, PInfo " .
        "FROM Person p WHERE PID = '$pid'";
    $rsuser = $conn->query($sqluser);
    $rowuser = $rsuser->fetch_array();
    if ($rowuser) {
        $plogin = $rowuser["PLogin"];
        $plevel = $rowuser["PLevel"];
        $pname = $rowuser["PName"];
        $pinfo = $rowuser["PInfo"];
    } else {
        $pid = 0;
        $plogin = "";
        $plevel = 1;
        $pname = "New User";
        $pinfo = "";
    }
} else {
    // comes from save page, use data in session
    $pid = $_COOKIE["PID"];
    $plogin = $_COOKIE["PLogin"];
    $plevel = $_COOKIE["PLevel"];
    $pname = $_COOKIE["PName"];
    $pinfo = $_COOKIE["PInfo"];
}

$plevellogin = ($plevel & LevelLogin) != 0;
$plevelrun = ($plevel & LevelRun) != 0;
$plevelsys = ($plevel & LevelSys) != 0;
$sqltel = "SELECT Telescope, LevelMask FROM Telescope";
$rstel = $conn->query($sqltel);
$tel = array();
while ($row =$rstel->fetch_array()) {
    $tel[$row["Telescope"]] = ($plevel & $row["LevelMask"]) != 0;
}

function chkbox($chkname, $cls, $text, $value, $title) {
    $res = "<input type='checkbox' title='$title' name='$chkname' value='1' " .
        ($value ? "checked" : "") . " /><span title='$title'>$text</span>\n";
    return $res;
}

$pagetitle = "Edit User $pname";




require 'head.php';

echo "<p class='title'>User $pname</p>";

echo "<form action='usersave.php' method='post'>";

echo "<table class='editor'>\n";

echo "<tr>" .
    "<th class='field'>Your Password</th>" .
    "<td class='value' title=''>" .
    "<input type='password' class='shorttext' name='pswd' value='' maxlength='255' />" .
    "<span class='note'>Input YOUR password to verify your identity.</span>" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Login</th>" .
    "<td class='value'>" .
    "<input type='hidden' name='pid' value='$pid' />" .
    "<input type='hidden' name='savemode' value='edit' />" .
    "<input type='text' class='shorttext' name='plogin' value='$plogin' maxlength='20' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Full Name</th>" .
    "<td class='value' title=''>" .
    "<input type='text' class='longtext' name='pname' value='$pname' maxlength='255' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>New Password</th>" .
    "<td class='value' title=''>" .
    "<input type='password' class='shorttext' name='ppswd1' value='' maxlength='255' />" .
    "<input type='password' class='shorttext' name='ppswd2' value='' maxlength='255' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Extra Info</th>" .
    "<td class='value'>" .
    "<input type='text' class='longtext' name='pinfo' value='$pinfo' maxlength='255' />" .
    "</td></tr>\n";
echo "<tr>" .
    "<th class='field'>Privileges</th>" .
    "<td class='value'>";
echo chkbox("plevellogin", "", "Active", $plevellogin, "Can login") . "&emsp;";
echo chkbox("plevelrun", "", "Run", $plevelrun, "Edit or add run") . "&emsp;";
echo chkbox("plevelsys", "", "User", $plevelsys, "User control") . "&emsp;";
echo "Telescopes: ";
foreach ($tel as $tn => $tl)
    echo chkbox("pleveltel$tn", "", $tn, $tl, "Operator of $tn") . "&nbsp;";
echo "</td></tr>\n";
echo "<tr>" .
    "<th class='field'></th>" .
    "<td class='value'>" .
    "<button type='submit' onclick='savemode.value=\"edit\";'>Save</button> &emsp; " .
    ($pid != 0 ? "<button type='submit' onclick='savemode.value=\"delete\";'>Delete</button>" : "") .
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
