<?php
date_default_timezone_set('UTC'); //America/Phoenix
define("TIMEZERO", 1346630400);
define("MJD0", mktime(0, 0, 0, 10, 10, 1995)); //Timestamp for MJD 2450000
define("Y2K", 1544); // MJD of 2000-01-01

function datetomjd($ds, $tz) {
    if (strlen($ds) != 8)
        return -1;

    $yy = intval(substr($ds, 0, 4));
    $mm = intval(substr($ds, 4, 2));
    $dd = intval(substr($ds, 6, 2));

    $md = array(0, 31,28,31,  30,31,30, 31,31,30, 31,30,31);

    //check format
    if ($yy < 2000 || $mm < 1 || $mm > 12 || $dd < 1 || $dd > $md[$mm])
        return -1;

    $yd = $dd - 1; // day of year, Jan 01 as 0
    for ($m = 0; $m < $mm; $m++) $yd += $md[$m];
    if ($yy % 4 == 0 && $mm > 2) $yd++;
    $pd = 365 * ($yy - 2000) + ceil(($yy - 2000) / 4) + $yd;
    if ($tz <= -7) $pd++; // adjust for timezone
    return $pd + 1544; //J1544 is 2000-01-01
}

function mjdtodate($mjd, $tz) {
    $t = MJD0 + $mjd * 24 * 3600 + $tz * 3600;
    $ds = date("Ymd", $t);
    return $ds;
}

function today_date () {
    return datetosn(time() - 7*60*60);
}

function datetosn ( $d ) {
    return intval(($d - TIMEZERO) / (24*60*60));
}

function sntodate ( $d ) {
    return $d * 24*60*60 + TIMEZERO;
}

function datestr ( $d ) {
    return date("Y-m-d", sntodate($d));// + 7*60*60
}

function timestr ( $t, $second=false ) {
    return date(($second ? "H:i:s" : "H:i"), $t);
}

function spanstr( $t ) {
    return date(($t >= 3600 ? "H:i:s" : "i:s"), $t);
}

function monthspan( $month ) {
    $yr = intval(substr($month, 0, 4));
    $mn = intval(substr($month, 4, 2));
    if ($month != 2 || $yr % 4 != 0) {
        $daycnt = array(31,28,31, 30,31,30, 31,31,30, 31,30,31);
        $daycnt = $daycnt[$mn-1];
    } else {
        $daycnt = 29;
    }
    $d0 = mktime(0, 0, 0, $mn, 1, $yr);
    //echo $d0;
    $d0 = datetosn($d0);
    $d1 = $d0 + $daycnt - 1;
    //echo "//$month: $yr - $mn : $d0 $d1";
    return array($d0, $d1);
}

function deg2hms($d) {
    $h = $d / 15.0;
    $hh = intval($h);
    $mm = intval(($h - $hh) * 60.0);
    $ss = ($h - $hh - $mm / 60.0) * 3600.0;
    return sprintf("%02d:%02d:%05.2f", $hh, $mm, $ss);
}

function deg2dms($d) {
    $si = $d < 0.0 ? "-" : "+";
    $ad = abs($d);
    $dd = intval($ad);
    $mm = intval(($ad - $dd) * 60.0);
    $ss = ($ad - $dd - $mm / 60.0) * 3600.0;
    return sprintf("%1s%02d:%02d:%04.1f", $si, $dd, $mm, $ss);
}

function sec2hms($s) {
    $hh = intval($s / 3600.0);
    $mm = intval($s % 60 / 60.0);
    $ss = intval($s % 60);
    return sprintf("%02d:%02d:%02d", $hh, $mm, $ss);
}

?>