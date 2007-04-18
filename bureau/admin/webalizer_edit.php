<?php
/*
 $Id: webalizer_edit.php 83 2006-03-29 23:23:12Z benjamin $
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

$fields = array (
	"id"     => array ("request", "integer", 0),
);
getFields($fields);

if (!$id) {
	$error=_("No Statistics selected!");
} else {
	$r=$webalizer->get_stats_details($id);
	if (!$r) {
		$error=$err->errstr();
	}
}

?>
<h3><?php __("Change the Statistics"); ?></h3>
<?php
	if ($error) {
		echo "<p class=\"error\">$error</p>";
		include_once ("foot.php");
		exit();
	}
?>
<form method="post" action="webalizer_doedit.php" id="main" name="main">
<table border="1" cellspacing="0" cellpadding="4">
<tr><th><input type="hidden" name="id" value="<?php echo $id ?>" />
        <?php __("Domain name"); ?></th><td>
	<?php echo $r["hostname"]; ?>
</td></tr>
<tr><th><label for="stalang"><?php __("Language"); ?></label></th><td><select class="inl" name="stalang" id="stalang"><?php $webalizer->select_lang_list($r["lang"]) ?></select></td></tr>
<tr><th><label for="dir"><?php __("Folder"); ?></label></th><td><input type="text" class="int" name="dir" id="dir" value="/<?php echo $r["dir"]; ?>" size="20" maxlength="255" />
<script type="text/javascript">
<!--
  document.write("&nbsp;<input type=\"button\" name=\"bff\" onclick=\"browseforfolder('main.dir');\" value=\" ... \" class=\"inb\" />");
//  -->
</script>
</td></tr>
<tr><td colspan="2"><input type="submit" class="inb" name="submit" value="<?php __("Change those Statistics."); ?>" /></td></tr>
</table>
</form>
<?php include_once ("foot.php"); ?>