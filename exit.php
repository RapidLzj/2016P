<?php
    $cookieexpire = time()-3600;
    setcookie('BonRapid',  '', $cookieexpire);

    header("location: ./");
    exit();
?>
