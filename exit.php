<?php
$cookieexpire = time()-3600;
setcookie('aLogin',  '', $cookieexpire);
setcookie('aLevel',  '', $cookieexpire);
setcookie('aName',  '', $cookieexpire);
setcookie('aInfo',  '', $cookieexpire);

header("location: ./");
exit();
?>
