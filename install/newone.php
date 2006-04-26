#!/usr/bin/php4 -q
<?php
/*
 $Id: newone.php,v 1.6 2006/02/17 15:15:54 olivier Exp $
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
 Purpose of file: Create the first admin account on a new AlternC server
 ----------------------------------------------------------------------
*/

// Ne vérifie pas ma session :)
chdir("/var/alternc/bureau");
require("/var/alternc/bureau/class/config_nochk.php");

// On passe super-admin
$admin->enabled=1;

// On crée le compte admin : 
if (!$admin->add_mem("root","root","Administrateur", "Admin", "root@".$L_FQDN)) {
	echo $err->errstr()."\n";
	exit();
}

$db->query("update membres set su=1 where login='root';");

// On lui attribue des quotas par defaut
// 10 domains, 10 stats, 10 bases mysql, 20 ftp et 100 emails
$db->query("update quotas set total=10 where (name='stats' or name='mysql' or name='dom') and uid in (select uid from membres where login='root');");
$db->query("update quotas set total=20 where name='ftp' and uid in (select uid from membres where login='root');");
$db->query("update quotas set total=100 where name='mail' and uid in (select uid from membres where login='root');");

?>
