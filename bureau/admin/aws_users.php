<?php
/*
 $Id: aws_users.php 20 2004-09-08 22:01:58Z anonymous $
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
 Purpose of file: List awstats accounts of the user.
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");
include_once("head.php");

$nologin=false;
if (!$r=$aws->list_login()) {
	$nologin=true;
	$error=$err->errstr();
}

?>
<h3><?php __("Awstats allowed user list"); ?></h3>

<form method="post" action="aws_useradd.php" name="main">
<table border="1" cellspacing="0" cellpadding="4">
<tr><th>
<label for="login"><?php __("Username"); ?></label></th><td>
	<select class="inl" name="prefixe"><?php $aws->select_prefix_list($prefixe); ?></select>&nbsp;<b>_</b>&nbsp;<input type="text" class="int" name="login" id="login" value="" size="20" maxlength="64" />
</td></tr>
<tr><th><label for="pass"><?php __("Password"); ?></label></th><td><input type="text" class="int" name="pass" id="pass" value="" size="20" maxlength="64" /></td></tr>
<tr><td colspan="2"><input type="submit" class="inb" name="submit" value="<?php __("Create this new awstat account."); ?>" /></td></tr>
</table>
</form>

<?php


if ($error) {
?>
<p class="error"><?php echo $error ?></p>
<?php }

if (!$nologin) {
?>


<form method="post" action="aws_userdel.php">
<table cellspacing="0" cellpadding="4">
<tr><th colspan="2">&nbsp;</th><th><?php __("Username"); ?></th></tr>
<?php
$col=1;
foreach ($r as $val) {
	$col=3-$col;
?>
	<tr class="lst<?php echo $col; ?>">
		<td align="center"><input type="checkbox" class="inc" id="del_<?php echo $val; ?>" name="del_<?php echo $val; ?>" value="<?php echo $val; ?>" /></td>
		<td><a href="aws_pass.php?login=<?php echo $val ?>"><?php __("Change password"); ?></a></td>
		<td><label for="del_<?php echo $val; ?>"><?php echo $val ?></label></td>
	</tr>
<?php
	}
?>
<tr><td colspan="5"><input type="submit" name="submit" class="inb" value="<?php __("Delete checked accounts"); ?>" /></td></tr>
</table>
</form>
<?php
 }
?>
<?php include_once("foot.php"); ?>