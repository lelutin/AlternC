<?php
/*
 $Id: aws_list.php 23 2004-10-23 15:44:12Z anonymous $
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
 Purpose of file: List awstats statistics and manage them.
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");
include_once("head.php");

$nosta=false;
if (!$r=$aws->get_list()) {
	$error=$err->errstr();
	$nosta=true;
}

?>
<h3><?php __("Statistics List"); ?></h3>
<p>
		- <a href="aws_users.php"><?php __("Manage allowed users' accounts"); ?></a><br />
<?php
	if ($quota->cancreate("aws")) { ?>
		- <a href="aws_add.php"><?php __("Create new Statistics"); ?></a><br />
<?php  	}
?>
</p>
<?php
	if ($error) {
		echo "<p class=\"error\">$error</p>";
	}

if (!$nosta) {
?>

<form method="post" action="aws_del.php">
<table cellspacing="0" cellpadding="4">
<tr><th colspan="2">&nbsp;</th><th><?php __("Domain name"); ?></th><th>Allowed Users</th><th><?php __("View"); ?></th></tr>
<?php

reset($r);
$col = 1;
$i = 0;
while (list($key, $val) = each($r))
{
	$col = 3 - $col;
	$altImg = ($i % 2 == 0 ? "" : "alt");
	$i++;
?>
	<tr class="lst<?php echo $col; ?>">
		<td><input type="checkbox" class="inc" id="del_<?php echo $val["id"]; ?>" name="del_<?php echo $val["id"]; ?>" value="<?php echo $val["id"]; ?>" /></td>
		<td><a href="aws_edit.php?id=<?php echo $val["id"] ?>"><img src="images/edit<?php echo $altImg; ?>.png" alt="<?php __("Edit"); ?>" title="<?php __("Edit"); ?>" /></a></td>
		<td><label for="del_<?php echo $val["id"]; ?>"><?php echo $val["hostname"] ?></label></td>
		<td><?php echo $val["users"] ?></td>
		<td><a href="/cgi-bin/awstats.pl?config=<?php echo $val["hostname"]; ?>"><?php __("View"); ?></a></td>
	</tr>
<?php
	}

?>

<tr><td colspan="5"><input type="submit" class="inb" name="submit" value="<?php __("Delete the checked Statistics"); ?>" /></td></tr>
</table>
</form>
<?php
}

?>
<?php include_once("foot.php"); ?>