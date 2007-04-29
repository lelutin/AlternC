<?php
/*
 $Id: bro_pref.php,v 1.2 2003/06/10 06:45:16 root Exp $
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
 Purpose of file: Configuration of the file browser
 ----------------------------------------------------------------------
*/
require_once("../class/config.php");

$fields = array (
	"submit"          => array ("request", "string", ""),
	"editsizex"       => array ("request", "string", ""),
	"editsizey"       => array ("request", "string", ""),
	"listmode"        => array ("request", "string", ""),
	"showicons"       => array ("request", "string", ""),
	"downfmt"         => array ("request", "string", ""),
	"createfile"      => array ("request", "string", ""),
	"showtype"        => array ("request", "string", ""),
	"editor_font"     => array ("request", "string", ""),
	"editor_size"     => array ("request", "string", ""),
	"golastdir"       => array ("request", "string", ""),
);
getFields($fields);

if ($submit)
{
	$bro->SetPrefs($editsizex, $editsizey, $listmode, $showicons, $downfmt, $createfile, $showtype, $editor_font, $editor_size, $golastdir);
	$error = _("Your preferences have been updated.");
	include ("bro_main.php");
	exit;
}

$p = $bro->GetPrefs();

include_once("head.php");

if ($error)
	echo "<p class=\"error\">" . $error . "</p>";

?>
<h3><?php __("File editor preferences"); ?></h3>
<form action="bro_pref.php" method="post">
<table cellpadding="6" border="1" cellspacing="0">
	<tr>
		<td colspan="2"><h4><?php __("File editor preferences"); ?></h4></td>
	</tr>
	<tr>
		<td><?php __("Horizontal window size"); ?></td>
		<td><select class="inl" name="editsizex">
<?php

for ($i = 400; $i <= 1000; $i += 100)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["editsizex"] == $i) echo " selected=\"selected\"";
	echo ">" . $i . "</option>";
}

?>
</select></td>
</tr>
<tr>
<td><?php __("Vertical window size"); ?></td>
<td><select class="inl" name="editsizey">
<?php

for ($i = 200; $i <= 500; $i += 50)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["editsizey"] == $i)
		echo " selected=\"selected\"";
	echo ">" . $i . "</option>";
}

?>
</select></td>
</tr>
<tr>
<td><?php __("File editor font name"); ?></td>
<td><select class="inl" name="editor_font">
<?php

for ($i = 0; $i < count($bro->l_editor_font); $i++)
{
	echo "<option value=\"" . $bro->l_editor_font[$i] . "\"";
	if ($p["editor_font"] == $bro->l_editor_font[$i])
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_editor_font[$i]) . "</option>";
}

?>
</select></td>
</tr>
<tr>
<td><?php __("File editor font size"); ?></td>
<td><select class="inl" name="editor_size">
<?php

for ($i = 0; $i < count($bro->l_editor_size); $i++)
{
	echo "<option value=\"" . $bro->l_editor_size[$i] . "\"";
	if ($p["editor_size"] == $bro->l_editor_size[$i])
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_editor_size[$i]) . "</option>";
}

?>
</select>
</td>
</tr>
</table>

<p>&nbsp;</p>

<table cellpadding="6" border="1" cellspacing="0">
	<tr>
		<td colspan="2"><h4><?php __("File browser preferences"); ?></h4></td>
	</tr>
	<tr>
		<td><?php __("File list view"); ?></td>
		<td><select class="inl" name="listmode">
<?php

for ($i = 0; $i < count($bro->l_mode); $i++)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["listmode"] == $i)
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_mode[$i]) . "</option>";
}

?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php __("Downloading file format"); ?></td>
		<td><select class="inl" name="downfmt">
<?php

for($i = 0; $i <count($bro->l_tgz); $i++)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["downfmt"] == $i)
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_tgz[$i]) . "</option>";
}

?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php __("What to do after creating a file"); ?></td>
		<td><select class="inl" name="createfile">
<?php

for($i = 0; $i < count($bro->l_createfile); $i++)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["createfile"] == $i)
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_createfile[$i]) . "</option>";
}

?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php __("Show icons?"); ?></td>
		<td><select class="inl" name="showicons">
<?php

for($i = 0; $i < count($bro->l_icons); $i++)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["showicons"] == $i)
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_icons[$i]) . "</option>";
}

?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php __("Show file types?"); ?></td>
		<td><select class="inl" name="showtype">
<?php

for($i = 0; $i < count($bro->l_icons); $i++)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["showtype"] == $i)
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_icons[$i]) . "</option>";
}

?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php __("Remember last visited directory?"); ?></td>
		<td><select class="inl" name="golastdir">
<?php

for($i = 0; $i < count($bro->l_icons); $i++)
{
	echo "<option value=\"" . $i . "\"";
	if ($p["golastdir"] == $i)
		echo " selected=\"selected\"";
	echo ">" . _($bro->l_icons[$i]) . "</option>";
}

?>
			</select>
		</td>
	</tr>
</table>
<p><input type="submit" name="submit" class="inb" value="<?php __("Change my settings"); ?>" /></p>

</form>
<p>&nbsp;</p>
<a href="bro_main.php"><?php __("Back to the file browser"); ?></a>
<?php include_once("foot.php"); ?>