<?php
$pagetitle = 'Login';
require 'conn.php';
require 'const.php';

if (isset($_POST["myname"]) && !empty($_POST["myname"])) {
    $myname = $_POST["myname"];
    $mypin = $_POST["mypin"];
    $sql = "select * from Person where PLogin = '$myname' and PPswd = '$mypin'";
    $rs = $conn->query($sql);
    if ($row = $rs->fetch_array()){
        $cookieexpire = time()+13*24*60*60; //13 days
        setcookie("aLogin", $row["PLogin"], $cookieexpire);
        setcookie("aLevel", $row["PLevel"], $cookieexpire);
        setcookie("aName", $row["PName"], $cookieexpire);
        setcookie("aInfo", $row["PInfo"], $cookieexpire);

        header("location: main.php");
        require 'conx.php';
    } else {
        $msg = "<span class='error'>Login failed: username or password error</span>";
    }
} else {
    $msg = "<span class='note'>Please enter username and password to login</span>";
}
?>

<?php require 'head.php'; ?>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<form action='./' method='post'>
<p>
    Username: <input type='text' name='myname' value='<?= $myname ?>' />
    Password: <input type='password' name='mypin' value='' />
    <button type='submit'>Login</button>
    <?= $msg ?></p>
</form>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<?php require 'foot.php'; ?>
<?php require 'conx.php'; ?>
