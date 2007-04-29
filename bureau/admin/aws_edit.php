<?php
/*
 $Id: aws_edit.php 8 2004-09-08 14:20:03Z anonymous $
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
 Purpose of file: Edit a statistic set
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");

$fields = array (
	"id" => array ("request", "integer", 0),
);
getFields($fields);

if (!$id)
{
	$error = _("No Statistics selected!");
}
else
{
	$r = $aws->get_stats_details($id);
	if (!$r)
	{
		$error = $err->errstr();
	}
}

$_REQUEST["id"] = $r["id"];
$_REQUEST["hostname"] = $r["hostname"];

include ("aws_add.php");
exit();

?>