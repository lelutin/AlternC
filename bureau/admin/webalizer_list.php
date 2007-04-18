<?php
/*
 $Id: webalizer_list.php 83 2006-03-29 23:23:12Z benjamin $
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
 Original Author of file:
 Purpose of file:
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");
include_once("head.php");

$nosta=false;
if (!$r=$webalizer->get_list()) {
	$error=$err->errstr();
	$nosta=true;
}

?>
<h3><?php __("Statistics List"); ?></h3>
<?php
	if ($quota->cancreate("stats")) { ?>
<p>
		- <a href="webalizer_add.php"><?php __("Create new Statistics"); ?></a><br />
</p>
<?php  	}

	if ($error) {
		echo "<p class=\"error\">$error</p>";
	}

if (!$nosta) {
?>

<p>
    <?php __("Here is the list of the statistics sets installed in your account :<br />Click on 'Modify' to change the statistics configuration<br />To delete a stats set, check the corresponding checkbox and click on 'Delete the checked Statistics'"); ?>
</p>
<form method="post" action="webalizer_del.php">
<table cellspacing="0" cellpadding="4">
<tr><th colspan="2">&nbsp;</th><th><?php __("Domain name"); ?></th><th><?php __("Language"); ?></th><th><?php __("Folder"); ?></th><th><?php __("View"); ?></th></tr>
<?php
reset($r);
$col=1;
while (list($key,$val)=each($r))
	{
	$col=3-$col;
?>
	<tr class="lst<?php echo $col; ?>">
		<td><input type="checkbox" class="inc" id="del_<?php echo $val["id"]; ?>" name="del_<?php echo $val["id"]; ?>" value="<?php echo $val["id"]; ?>" /></td>
		<td class="center"><a href="webalizer_edit.php?id=<?php echo $val["id"] ?>"><img src="images/edit.png" alt="<?php __("Edit"); ?>" /></a></td>
		<td><label for="del_<?php echo $val["id"]; ?>"><?php echo $val["hostname"] ?></label></td>
		<td><?php echo _($webalizer->langname[$val["lang"]]); ?></td>
		<td><code>/<?php echo $val["dir"] ?></code></td>
		<td><?php
	if ($uv=$bro->viewurl($val["dir"],"")) echo "<a href=\"$uv\">"._("View")."</a>";
?>&nbsp;</td>
	</tr>
<?php
	}

?>

<tr><td colspan="5"><input type="submit" class="inb" name="submit" value="<?php __("Delete the checked Statistics"); ?>" /></td></tr>
</table>
</form>

<?php $mem->show_help("webalizer_list");

}

?>
<?php include_once("foot.php"); ?>