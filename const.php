<?php
// Level
define("LevelLogin", 1 << 0);
define("LevelRun", 1 << 14);
define("LevelSys", 1 << 15);
//Night Status
define("StatusDoing", 1 << 0);
define("StatusObsed", 1 << 1);
define("StatusNone", 1 << 2);
define("StatusShared", 1 << 3);
define("StatusWeather", 1 << 4);
define("StatusDevice", 1 << 5);
define("StatusOther", 1 << 6);
define("StatusElse", 1 << 7);
define("StatusPlan", 1 << 15);
$StatusArr = array(StatusDoing, StatusObsed, StatusNone, StatusShared,
    StatusWeather, StatusDevice, StatusOther, StatusElse, StatusPlan);
$StatusText = array("Obsing", "Obsed", "NoWork", "Shared",
    "Weather", "Device", "Other", "Else", "Plan");
$StatusTitle = array(
    "Observing. Log is editing but not submitted.",
    "Finished and the whole night is used.",
    "Nothing has been done.",
    "This night is shared with other proposal.",
    "Finished, but hours is lost for bad weather.",
    "Finished, but hours is lost for device problem.",
    "Finished, but hours used in other observation.",
    "Finished, but hours is lost for reasons else.",
    "This night is canceled.");
// Weather
$nStatus = count($StatusArr);
$Weather_Lightning = array("No", "Seldom", "&cross; Storm");
$Weather_Wind = array("Almost No", "Breeze", "High", "&cross; Strong", "&cross; Storm");
$Weather_Humidity = array("Fine & Dry", "Fine & Median Dry", "Partly Cloudy", "Cloudy", "Fog",
    "&cross; High Humidity",
    "&cross; Light rain/snow", "&cross; Heavy rain/snow", "&cross; Rain/Snow storm");
?>