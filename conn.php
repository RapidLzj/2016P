<?php
$conn = new mysqli('uvbys', 'uvbys', 'uvbySurvey', 'surveylog');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>