<?php
session_start();

include_once("config.php");

//open db connection
$db = @mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
$db = @mysql_select_db(DB_NAME);
 

function saveAdmin($pass, $oldpass = '') {
    if (checkAdmin($oldpass) == '1') {
	$sql = "UPDATE dev.paypal_admins SET `password` = '".md5($pass)."' WHERE (password = '".md5($oldpass)."')";
    } else {
	$sql = "";	
    }
    

    if ($sql != "") {
	$result = mysql_query($sql);
        return true;
    } else {
	return false;
    }

}

function checkAdmin($pass) {
//checks admin login.  
$sql = "SELECT * FROM dev.paypal_admins WHERE `password` = '".md5($pass)."'";
$result = mysql_query($sql);
error_log("Password: ".$pass);
error_log(mysql_error());
//if successful, this will be 1, otherwise 0
return mysql_num_rows($result);

}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'check') {
	if (checkAdmin($_POST['password'])=='1') {
	    echo '1';
	} else {
	    echo '0';
	}
    }
    if ($_POST['action'] == 'change') {
	if (saveAdmin($_POST['password'], $_POST['old_password'])) {
	    echo '1';
	} else {
	    echo '0'; 
	}
    }
} else {

?>
<html>
<head><title>LM PayPal Admin Password</title></head>
<body>
<form action="paypal-password.php" method="POST">
    <input type="hidden" name="action" value="change" />
    <div><label for="old_password">Current Password:</label><br /><input type="password" name="old_password" value="" /></div>
    <div><label for="password">New Password:</label><br /><input type="password" name="password" value="" /></div>
    <div><input type="submit" name="submit" value="Update Password" />
</form>
<?php if ($_get['action']== 'checkpass') { ?>
<form action="paypal-password.php" method="POST">
    <input type="hidden" name="action" value="check" />
    <div><label for="password">New Password:</label><br /><input type="password" name="password" value="" /></div>
    <div><input type="submit" name="submit" value="Check Password" />
</form>
 <?php } ?>
</body>
</html>

<?php } ?>
