<?php
/*
 $Id: m_mailman.php,v 1.4 2003/06/10 07:31:36 root Exp $
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
 Purpose of file: Manage mailing-lists with Mailman
 ----------------------------------------------------------------------
*/
/* 
   SQL STRUCTURE : 

CREATE TABLE mailman (
  id int(10) unsigned NOT NULL auto_increment,
  uid int(10) unsigned NOT NULL default '0',
  list varchar(128) NOT NULL default '',
  domain varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Listes de discussion Mailman';

*/

class m_mailman {
  
  var $uid=0;         /* Membre dont on souhaite gérer les listes de diffusions */
  var $lang="fr"; /* Default language for Mailman lists */
  
  /** Nom du quota utilisé */
  var $alternc_quota_name="mailman";

  /*****************************************************************************/
  function m_mailman($membre=0) { $this->uid=$membre; }
  
  /*****************************************************************************/
  /** Return the mailing-lists managed by this member : */
  function enum_ml() {
    global $err,$db;
    $err->log("mailman","enum_ml");
    $db->query("SELECT * FROM mailman WHERE uid=".$this->uid.";");
    if (!$db->num_rows()) {
      $err->raise("mailman",1);
      return false;
    }
    $mls=array();
    while ($db->next_record()) {
      $mls[]=$db->Record;
    }
    return $mls;
  }
  
  /*****************************************************************************/
  function prefix_list() {
    global $db,$err;
    $r=array();
    $db->query("SELECT domaine FROM domaines WHERE compte=".$this->uid." ORDER BY domaine;");
    while ($db->next_record()) {
      $r[]=$db->f("domaine");
    }
    return $r;
  }
  /*****************************************************************************/
  function select_prefix_list($current) {
    global $db,$err;
    $r=$this->prefix_list();
    reset($r);
    while (list($key,$val)=each($r)) {
      if ($current==$val) $c=" selected=\"selected\""; else $c="";
      echo "<option$c>$val</option>";
    }
    return true;
  }
  
  /*****************************************************************************/
  /** Create a new list for this member : */
  function add_lst($domain,$login,$owner,$password) {
    global $db,$err,$quota,$mail;
    $err->log("mailman","add_lst",$login."@".$domain." - ".$owner);
    $login=strtolower($login);
    // TODO : verifier que le domaine est bien hébergé.
    $domain=strtolower($domain);
    if ($login=="") {
      $err->raise("mailman",2);
      return false;
    }
    if (!$owner || !$password) {
      $err->raise("mailman",3);
      return false;
    }
    if (checkmail($owner)) {
      $err->raise("mailman",4);
      return false;
    }
    $r=$this->prefix_list();
    if (!in_array($domain,$r) || $domain=="") {
      $err->raise("mailman",5);
      return false;
    }
    /*
      if (strpos($login,"_")!==false) {
      $err->raise("mailman",8);
      return false;
      }
    */
    $db->query("SELECT COUNT(*) AS cnt FROM mailman WHERE list='$login';");
    $db->next_record();
    if ($db->f("cnt")) {
      $err->raise("mailman",10);
      return false;
    }
    // Prefixe OK, on verifie la non-existence des mails que l'on va créer...
    if (!$mail->available($login."@".$domain) || !$ma->available($login."-request@".$domain) || !$ma->available($login."-owner@".$domain) || !$ma->available($login."-admin@".$domain) || !$ma->available($login."-bounces@".$domain) || !$ma->available($login."-confirm@".$domain) || !$ma->available($login."-join@".$domain) || !$ma->available($login."-leave@".$domain) || !$ma->available($login."-subscribe@".$domain) || !$ma->available($login."-unsubscribe@".$domain)) {
      // This is a mail account already !!!
      $err->raise("mailman",6);
      return false;
    }
    // Le compte n'existe pas, on vérifie le quota et on le créé.
    if ($quota->cancreate("ml")) {
      $quota->inc("ml"); // incrémentation du quota
      // Creation de la liste : 1. recherche du nom de la liste 
      // CA NE MARCHE PAS !
      $name=$login; 
      $db->query("INSERT INTO mailman (uid,list,domain,name) VALUES (".$this->uid.",'$login','$domain','$name');");
      if (!$mail->add_wrapper($login,$domain,"/var/lib/mailman/mail/mailman post $name","mailman") || 
	  !$mail->add_wrapper($login."-request",$domain,"/var/lib/mailman/mail/mailman request $name","mailman") || 
	  !$mail->add_wrapper($login."-owner",$domain,"/var/lib/mailman/mail/mailman owner $name","mailman") || 
	  !$mail->add_wrapper($login."-admin",$domain,"/var/lib/mailman/mail/mailman admin $name","mailman") || 
	  !$mail->add_wrapper($login."-bounces",$domain,"/var/lib/mailman/mail/mailman bounces $name","mailman") || 
	  !$mail->add_wrapper($login."-confirm",$domain,"/var/lib/mailman/mail/mailman confirm $name","mailman") || 
	  !$mail->add_wrapper($login."-join",$domain,"/var/lib/mailman/mail/mailman join $name","mailman") ||
	  !$mail->add_wrapper($login."-leave",$domain,"/var/lib/mailman/mail/mailman leave $name","mailman") || 
	  !$mail->add_wrapper($login."-subscribe",$domain,"/var/lib/mailman/mail/mailman subscribe $name","mailman") || 
	  !$mail->add_wrapper($login."-unsubscribe",$domain,"/var/lib/mailman/mail/mailman unsubscribe $name","mailman")
	  ) {
	$mail->del_wrapper($login,$domain);	        $ma->del_wrapper($login."-request",$domain);
	$mail->del_wrapper($login."-owner",$domain);	$ma->del_wrapper($login."-admin",$domain);
	$mail->del_wrapper($login."-bounces",$domain);	$ma->del_wrapper($login."-confirm",$domain);	
	$mail->del_wrapper($login."-join",$domain);	$ma->del_wrapper($login."-leave",$domain);
	$mail->del_wrapper($login."-subscribe",$domain);	$ma->del_wrapper($login."-unsubscribe",$domain);
	$db->query("DELETE FROM mailman WHERE name='$name';");
	return false;
      }
      // Wrapper created, sql ok, now let's create the list :)
      exec("/usr/lib/alternc/mailman.create \"".escapeshellcmd($name."@".$domain)."\" \"".escapeshellcmd($owner)."\" \"".escapeshellcmd($password)."\"");
      return true;
    } else {
      $err->raise("mailman",7); // quota
      return false;
    }
  }
  
  /*****************************************************************************/
  function delete_lst($id) {
    global $db,$err,$quota,$mail;
    $err->log("mailman","delete_lst",$id);
    
    $db->query("SELECT * FROM mailman WHERE id=$id and uid=".$this->uid.";");
    $db->next_record();
    if (!$db->f("id")) {
      $err->raise("mailman",9);
      return false;
    }
    exec("/usr/lib/alternc/mailman.delete ".escapeshellarg($db->f("name")));
    $login=$db->f("list");
    $domain=$db->f("domain");
    $db->query("DELETE FROM mailman WHERE id=$id");
    $mail->del_wrapper($login,$domain);	        $ma->del_wrapper($login."-request",$domain);
    $mail->del_wrapper($login."-owner",$domain);	$ma->del_wrapper($login."-admin",$domain);
    $mail->del_wrapper($login."-bounces",$domain);	$ma->del_wrapper($login."-confirm",$domain);	
    $mail->del_wrapper($login."-join",$domain);	$ma->del_wrapper($login."-leave",$domain);
    $mail->del_wrapper($login."-subscribe",$domain);	$ma->del_wrapper($login."-unsubscribe",$domain);
    $quota->dec("ml");
    return $login."@".$domain;
  }

  /** ***************************************************************************
      del_dom($dom) Supprime un domaine a l'utilisateur
      $dom est le domaine concerne
      fonction appelée par m_domains
  *****************************************************************************/
  function alternc_del_domain($dom) {
    global $err;
    $err->log("mailman","del_domain",$dom);

    // Suppression des listes du domaine
    $listes=$this->enum_ml();
    if (is_array($listes)) {
      while (list($key,$val)=each($listes)) {
	$this->delete_lst($val["id"]);
      }
    }
    exec("/usr/lib/alternc/lst_deldom ".escapeshellarg($dom));
    return true;
  }

  /*****************************************************************************/
  function alternc_add_domain($dom) {
    global $err;
    $err->log("mailman","del_domain",$dom);
    return true;
  }


} /* Class m_mailman */

?>