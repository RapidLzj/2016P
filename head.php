<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?= $pagetitle ?> - SSSO Log</title>
<link rel="stylesheet" type="text/css" href="obslog.css">
</head>
<body>
<div id='page'>
<div id='head'>
<p class='title'><img src='lzjnamelogo.gif' alt='logo' /> SAGE Sky Survey Observation Log</p>
</div>
<?php if (isset($aLevel)) : ?>
<div id='menu'>
    <span><?="$aName/$aInfo"?></span>&emsp;
    <a href='main.php'>&check; Home</a> |
    <?php if ($aLevel & $levelRun != 0) : ?><a href='runedit.php'>&sect; New Run</a> | <?php endif; ?>
    <a href='selfedit.php'>&clubs; My Profile</a> |
    <?php if ($aLevel & $levelSys != 0) : ?><a href='userlist.php'>&malt; Persons</a> | <?php endif; ?>
    <a href='exit.php'>&cross; Logout</a>
</div>
<?php endif; ?>
<div id='main'>
