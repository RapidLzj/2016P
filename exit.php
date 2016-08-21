<?php
$cookieexpire = time()-3600;
setcookie('aPid',  '', $cookieexpire);
setcookie('aLogin',  '', $cookieexpire);
setcookie('aLevel',  '', $cookieexpire);
setcookie('aName',  '', $cookieexpire);
setcookie('aInfo',  '', $cookieexpire);

header("location: login.php");
exit();
?>
