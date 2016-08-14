<?php
date_default_timezone_set('UTC'); //America/Phoenix
define("TIMEZERO", 1346630400);

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
    if ($on != 2 || $yr % 4 != 0) {
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

?>