<?php
/*
 $Id: aws_pass.php 19 2004-09-08 22:01:32Z anonymous $
 ----------------------------------------------------------------------
 AlternC - Web Hosting System
 Copyright (C) 2002 by the AlternC Development Team.
 http://alternc.org/
 ----------------------------------------------------------------------
 Based on:
 Valentin Lacambre's web hosting softwares: http://altern.org/
 ----------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
 ----------------------------------------------------------------------
 Original Author of file: Benjamin Sonntag
 Purpose of file: Change a user's password.
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");

$fields = array (
	"login" => array ("request", "string", ""),
	"pass"  => array ("request", "string", ""),
);

getFields($fields);

if (!$aws->login_exists($login)) {
	$error=$err->errstr();
	include("aws_users.php");
	exit();
}

if ($pass) {
	if (!$aws->change_pass($login,$pass)) {
		$error=$err->errstr();
	} else {
		include("aws_users.php");
		exit();
	}
}

include_once("head.php");

?>
<h3><?php __("Change a user's password"); ?></h3>
<?php
if ($error) {
?>
<p class="error"><?php echo $error ?></p>
<?php } ?>

<form method="post" action="aws_pass.php" name="main">
<table border="1" cellspacing="0" cellpadding="4">
<tr><th>
<?php __("Username"); ?></th><td>
	<code><?php echo $login; ?></code> <input type="hidden" name="login" value="<?php echo $login; ?>" />
</td></tr>
<tr><th><label for="pass"><?php __("New Password"); ?></label></th><td><input type="text" class="int" name="pass" id="pass" value="<?php echo $pass; ?>" size="20" maxlength="64" /></td></tr>
<tr><td colspan="2"><input type="submit" class="inb" name="submit" value="<?php __("Change this user's password"); ?>" /></td></tr>
</table>
</form>
<?php include_once("foot.php"); ?>