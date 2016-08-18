<?php
if (! isset($_COOKIE["aLevel"])) :
    header("location: ./");
    exit;
endif;

$aLogin = $_COOKIE["aLogin"];
$aLevel = 0 + $_COOKIE["aLevel"];
$aName = $_COOKIE["aName"];
$aInfo = $_COOKIE["aInfo"];

if (($aLevel & LevelLogin) == 0) :
    header("location: ./");
    exit;
endif;

$levelEditRun = ($aLevel & LevelRun) != 0;
$levelEditSys = ($aLevel & LevelSys) != 0;

?>
