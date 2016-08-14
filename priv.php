<?php
if (! isset($_COOKIE["aLevel"])) {
    header("location: ./");
    exit;
}
if ($_COOKIE["aLogin"] == '') {
    header("location: ./");
    exit;
}
$aLogin = $_COOKIE["aLogin"];
$aLevel = $_COOKIE["aLevel"];
$aName = $_COOKIE["aName"];
$aInfo = $_COOKIE["aInfo"];
?>
