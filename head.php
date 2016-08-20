<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=$pagetitle?> - SSSO Log</title>
<link rel="stylesheet" type="text/css" media="screen" href="obslog.css" />
<link rel="stylesheet" type="text/css" media="print" href="obslogprint.css" />
</head>

<body>
<div id='page'>
<div id='head'>
<p class='title'>&#x1f52d; SAGE Sky Survey Observation Log</p>
</div>
<?php if (isset($aLevel)) : ?>
<div id='menu'>
    <span><?="$aName/$aInfo"?></span>&emsp;
    <a href='main.php'>&#x1f310; Home</a> |
    <?php if ($levelEditRun) : ?><a href='runedit.php'>&#x1f4c5; New Run</a> | <?php endif; ?>
    <a href='selfedit.php'>&#x1f5dd; My Profile</a> |
    <?php if ($levelEditSys) : ?><a href='userlist.php'>&#x1f465; Persons</a> | <?php endif; ?>
    <a href='exit.php'>&#x1f6aa; Logout</a> |
    <a href='help.php'>Help</a>
</div>
<?php endif; ?>
<div id='main'>
