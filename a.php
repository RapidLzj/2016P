<?php
$ds = '2016083x';
echo is_numeric($ds) ? "Y" : "N";
$yy = intval(substr($ds, 0, 4));
$mm = intval(substr($ds, 4, 2));
$dd = intval(substr($ds, 6, 2));
$md = array(0, 31,28,31,  30,31,30, 31,31,30, 31,30,31);
echo "$ds\n";
echo "$yy-$mm-$dd\n";
echo $md[$mm];
echo ($yy < 2000) ? "Y" : "N";
echo ($mm < 1 || $mm > 12)  ? "Y" : "N";
echo ($dd < 1 || $dd > $md[$mm]) ? "Y" : "N";
?>

