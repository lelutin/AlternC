<?php
/*
 $Id: hta_del.php,v 1.4 2003/08/20 13:08:28 root Exp $
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
 Original Author of file: Franck Missoum
 Purpose of file: Delete a username from a protected folder
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");

$fields = array (
	"d"    => array ("request", "array", array()),
);
getFields($fields);

if (!is_array($d))
{
	$tmp = array($d);
	$d = $tmp;
}
reset($d);

while (list($key, $val) = each($d))
{
	$r = $hta->DelDir($val);
	if (!$r)
	{
		$error .= $err->errstr() . "<br />";
	}
	else
	{
		$error .= sprintf(_("The protected folder %s has been successfully unprotected"), $val) . "<br />";
	}
}

include ("hta_list.php");
exit();

?>