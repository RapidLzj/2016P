<?php
if (! isset($_COOKIE["aLevel"])) :
    header("location: login.php");
    exit;
endif;

$aPid   = $_COOKIE["aPid"] + 0;
$aLogin = $_COOKIE["aLogin"];
$aLevel = $_COOKIE["aLevel"] + 0;
$aName  = $_COOKIE["aName"];
$aInfo  = $_COOKIE["aInfo"];

if (($aLevel & LevelLogin) == 0) :
    header("location: login.php");
    exit;
endif;

$levelEditRun = ($aLevel & LevelRun) != 0;
$levelEditSys = ($aLevel & LevelSys) != 0;

?>
