<?php
/*
 $Id: sql_del.php,v 1.3 2003/06/10 07:20:29 root Exp $
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
 Purpose of file: Delete a mysql user database
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");
include_once ("head.php");

$fields = array (
	"confirm" => array ("request", "string", ""),
	"cancel"  => array ("request", "string", ""),
	"d"       => array ("request", "array", array()),
);
getFields($fields);

if ($cancel)
{
	include ("sql_list.php");
	exit();
}

if ($confirm == "y")
{
	foreach ($d as $val)
	{
		$r = $mysql->del_db($val);
		if (!$r)
		{
			$error .= $err->errstr() . "<br />";
		}
		else
		{
			$error .= sprintf(_("The database %s has been successfully deleted"), $mem->user["login"] . (($val) ? "_" : "") . $val) . "<br />";
		}
	}

	include ("sql_list.php");
	exit();
}

?>
<h3><?php __("MySQL Databases"); ?></h3>
<p class="error"><?php __("WARNING"); ?><br /><?php __("Confirm the deletion of the following SQL databases"); ?><br />
<?php __("This will delete all the tables currently in those db."); ?></p>
<form method="post" action="sql_del.php" id="main">
<p>
<input type="hidden" name="confirm" value="y" />
<?php

foreach ($d as $val)
{
	echo "<input type=\"hidden\" name=\"d[]\" value=\"" . $val . "\" />" . $mem->user["login"] . (($val) ? "_" : "") . $val . "<br />\n";
}

?>
<br />
<input type="submit" class="inb" name="sub" value="<?php __("Yes"); ?>" /> - <input type="submit" class="inb" name="cancel" value="<?php __("No"); ?>" />
</p>
</form>
<script type="text/javascript">
deploy("menu-sql");
</script>
<?php include_once("foot.php"); ?>>