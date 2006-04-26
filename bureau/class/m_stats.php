<?php
/*
 $Id: m_stats.php,v 1.10 2004/05/19 14:23:06 benjamin Exp $
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
 Purpose of file: Gestion des statistiques web par Webalizer.
 ----------------------------------------------------------------------
*/
/**
* Classe de gestion des statistiques web webalizer / apache des hébergés
* 
* Cette classe permet de gérer les statistiques web générées par webalizer
* dans la langue de votre choix, ainsi que les fichiers logs bruts d'apache.<br />
* Copyleft {@link http://alternc.net/ AlternC Team}
* 
* @copyright    AlternC-Team 2002-11-01 http://alternc.net/
* 
*/
class m_stats {

  /** Emplacement des fichiers de conf webalizer
   * @access private 
   */
  var $CONFDIR="/etc/webalizer/";

  /** Emplacement du fichier patron pour recopie.
   * @access private 
   */
  var $TEMPLATEFILE="/etc/webalizer/template.conf";

  /** Nom des langues disponibles */
  var $langname=array(
		      "FR"=>"French",
		      "EN"=>"English",
		      "DE"=>"German",
		      "ES"=>"Spanish",
		      );

  /* ----------------------------------------------------------------- */
  /**
   * Constructeur
   */
  function m_stats() {
  }

  /* ----------------------------------------------------------------- */
  /**
   * Nom du quota
   */
  function alternc_quota_names() {
    return "stats";
  } 

  /* ----------------------------------------------------------------- */
  /**
   * Retourne un tableau contenant les jeux de statistiques d'un membre.
   *
   * @return array retourne un tableau indexé de tableaux associatif de la 
   *  forme : 
   *  $r[0-n]["id"] = numéros du jeu
   *  $r[0-n]["hostname"]= domaine concerné
   *  $r[0-n]["dir"]= Répertoire destination (dans le dossier du membre)
   *  $r[0-n]["lang"]= Langue de production des statistiques
   */
  function get_list() {
    global $db,$err,$cuid;
    $err->log("stats","get_list");
    $r=array();
    $db->query("SELECT id, hostname, dir, lang FROM stats WHERE uid='$cuid' ORDER BY hostname;");
    if ($db->num_rows()) {
      while ($db->next_record()) {
	// On passe /var/alternc/html/u/user
	preg_match("/^\/var\/alternc\/html\/.\/[^\/]*\/(.*)/", $db->f("dir"),$match);
	$r[]=array(
		   "id"=>$db->f("id"),
		   "hostname"=>$db->f("hostname"),
		   "lang"=>$db->f("lang"),
		   "dir"=>$match[1]
		   );
      }
      return $r;
    } else {
      $err->raise("stats",1);
      return false;
    }
  }

  /* ----------------------------------------------------------------- */
  /**
   * Retourne un tableau contenant les détails d'un jeu de statistiques
   *  d'un membre.
   *
   * @param integer $id Numéro du jeu de stats dont on veut les infos.
   * @return array retourne un tableau associatif de la forme : 
   *  $r["id"] = numéros du jeu
   *  $r["hostname"]= domaine concerné
   *  $r["dir"]= Répertoire destination (dans le dossier du membre)
   *  $r["lang"]= Langue de production des statistiques
   */
  function get_stats_details($id) {
    global $db,$err,$cuid;
    $err->log("stats","get_stats_details",$id);
    $r=array();
    $db->query("SELECT id, hostname, dir, lang FROM stats WHERE uid='$cuid' AND id='$id';");
    if ($db->num_rows()) {
      $db->next_record();
      // On passe /var/alternc/html/u/user
      preg_match("/^\/var\/alternc\/html\/.\/[^\/]*\/(.*)/", $db->f("dir"),$match);
      return array(
		   "id"=>$db->f("id"),
		   "hostname"=> $db->f("hostname"),
		   "lang"=>$db->f("lang"),
		   "dir"=>$match[1]
		   );
    } else {
      $err->raise("stats",2);
      return false;
    }
  }


  /* ----------------------------------------------------------------- */
  /** Retourne la liste des domaines / sous-domaines autorisés pour le membre.
   * 
   * @return array retourne un tableau indexé des domaines / sous-domaines utilisables.
   */
  function host_list() {
    global $db,$err,$cuid;
    $r=array();
    $db->query("SELECT domaine,sub FROM sub_domaines WHERE compte='$cuid' ORDER BY domaine,sub;");
    while ($db->next_record()) {
      if ($db->f("sub")) {
	$r[]=$db->f("sub").".".$db->f("domaine");
      } else {
	$r[]=$db->f("domaine");
      }
    }
    return $r;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Affiche des options select de la liste des noms de domaines autorisés pour
   * le membre. sous forme de champs d'options (select)
   */
  function select_host_list($current) {
    $r=$this->host_list();
    reset($r);
    while (list($key,$val)=each($r)) {
      if ($current==$val) $c=" selected=\"selected\""; else $c="";
      echo "<option$c>$val</option>";
    }
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Affiche des options select de la liste des langues autorisées pour
   * le membre. sous forme de champs d'options (select)
   */
  function select_lang_list($current) {
    reset($this->langname);
    while (list($key,$val)=each($this->langname)) {
      if ($current==$val) $c=" selected=\"selected\""; else $c="";
      echo "<option value=\"$key\"$c>"._($val)."</option>";
    }
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Modifie un jeu de statistiques existant
   * @param integer $id est le numéro du jeu de statistiques
   * @param string $dir est un chemin relatif à "/var/alternc/html/u/user"
   * @param string $stlang est la langue de production des statistiques (code à 2 lettres)
   */
  function put_stats_details($id,$dir,$stlang) {
    global $db,$err,$bro,$mem,$cuid;
    $err->log("stats","put_stats_details",$id);
    $db->query("SELECT count(*) AS cnt FROM stats WHERE id='$id' and uid='$cuid';");
    $db->next_record();
    if (!$db->f("cnt")) {
      $err->raise("stats",2);
      return false;
    }
    $dir=$bro->convertabsolute($dir);
    if (substr($dir,0,1)=="/") {
      $dir=substr($dir,1);
    }
    // On a épuré $dir des problèmes eventuels ... On est en DESSOUS du dossier de l'utilisateur.
    if (!$this->langname[$stlang]) {
      $err->raise("stats",6);
      return false;
    }
    $lo=$mem->user["login"];
    $l=substr($lo,0,1);
    $db->query("UPDATE stats SET lang='$stlang', dir='/var/alternc/html/$l/$lo/$dir', uid='$cuid' WHERE id='$id';");
    $this->_createconf($id);
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Efface un jeu de statistiques existant.
   * @param integer $id est le numéro du jeu de statistiques à supprimer
   * @return string le nom du domaine du jeu ainsi effacé, ou FALSE si une erreur est survenue.
   */
  function delete_stats($id) {
    global $db,$err,$quota,$cuid;
    $err->log("stats","delete_stats",$id);
    $db->query("SELECT hostname FROM stats WHERE id='$id' and uid='$cuid';");
    if (!$db->num_rows()) {
      $err->raise("stats",2);
      return false;
    }
    $db->next_record();
    $this->_delconf($db->f("hostname"));
    $db->query("DELETE FROM stats WHERE id='$id'");
    $quota->dec("stats");
    return $name;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Cree un nouveau jeu de statistiques
   * @param string $dir est le chemin d'accès racine du compte ftp dans le compte du membre
   * @param string $lang est la langue choisie
   * @param string $hostname est le nom de domaine sur lequel on fait des stats
   * @return boolean TRUE si le jeu de stats a été créé avec succès, FALSE sinon.
   */
  function add_stats($hostname,$dir,$lang) {
    global $db,$err,$quota,$bro,$mem,$cuid;
    $err->log("stats","add_stats",$hostname);
    $dir=$bro->convertabsolute($dir);
    if (substr($dir,0,1)=="/") {
      $dir=substr($dir,1);
    }
    // On a épuré $dir des problèmes eventuels ... On est en DESSOUS du dossier de l'utilisateur.
    $r=$this->host_list();
    if (!in_array($hostname,$r) || $hostname=="") {
      $err->raise("stats",3);
      return false;
    }
    if (!$this->langname[$lang]) {
      $err->raise("stats",6);
      return false;
    }
    $lo=$mem->user[login];
    $l=substr($lo,0,1);
    // Le compte n'existe pas, on le crée.
    if ($quota->cancreate("stats")) {
      $quota->inc("stats");
      $db->query("INSERT INTO stats (hostname,lang,dir,uid) VALUES ('$hostname','$lang','/var/alternc/html/$l/$lo/$dir','$cuid')");
      $this->_createconf($db->lastid());
      return true;
    } else {
      $err->raise("stats",5);
      return false;
    }
  }

  function alternc_del_member() {
    global $db,$quota,$err,$cuid;
    $err->log("stats","del_member");
    $db->query("SELECT * FROM stats WHERE uid='$cuid';");
    $cnt=0;
    $t=array();
    while ($db->next_record()) {
      $cnt++;
      $t[]=$db->f("hostname");
    }
    $db->query("DELETE FROM stats WHERE uid='$cuid';");
    for($i=0;$i<cnt;$i++) {
      $this->_delconf($t[$i]);
    }
    $db->query("DELETE FROM stats2 WHERE mid='$cuid';");
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Fonction appellée par m_dom lorsqu'un domaine est supprimé.
   * @param string $dom est le domaine à supprimer.
   */
  function alternc_del_domain($dom) {
    global $db,$quota,$err,$cuid;
    $err->log("stats","del_dom",$dom);
    // on remonte les quotas ;)
    $db=new DB_System();
    $db->query("SELECT * FROM stats WHERE uid='$cuid' AND hostname like '%$dom'");
    $cnt=0;
    $t=array();
    while ($db->next_record()) {
      $cnt++;
      $t[]=$db->f("hostname");
    }
    $r=$quota->getquota("stats");
    $quota->setquota("stats",$r["u"]-$cnt,1);
    // on détruit les jeux de stats associés au préfixe correspondant :
    for($i=0;$i<cnt;$i++) {
      $db->query("DELETE FROM stats WHERE uid='$cuid' AND hostname='".$t[$i]."';");
      $this->_delconf($t[$i]);
    }
    // Suppression des stats apache brutes : 
    $db->query("SELECT * FROM stats2 WHERE mid='$cuid' AND hostname like '%$dom'");
    $cnt=0;
    $t=array();
    while ($db->next_record()) {
      $cnt++;
      $t[]=$db->f("hostname");
    }
    $r=$quota->getquota("stats");
    $quota->setquota("stats",$r["u"]-$cnt,1);
    // on détruit les jeux de stats associés au préfixe correspondant :
    for($i=0;$i<cnt;$i++) {
      $db->query("DELETE FROM stats2 WHERE mid='$cuid' AND hostname='".$t[$i]."';");
    }
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Recalcule le quota complet de l'utilisateur courant, ou de l'utilisateur $id
   * @param integer $id Numéro de l'utilisateur (facultatif)
   */
  function alternc_quota_check() {
    global $db,$err,$quota,$cuid;
    $err->log("stats","checkquota");
    $db->query("SELECT COUNT(*) AS cnt FROM stats WHERE uid='$cuid'");
    $db->next_record();
    $ss=intval($db->f("cnt"));
    $db->query("SELECT COUNT(*) AS cnt FROM stats2 WHERE mid='$cuid'");
    $db->next_record();
    $ss+=intval($db->f("cnt"));
    $quota->setquota("stats",$ss,1);
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** Retourne un tableau contenant les jeux de stats APACHE 
   * d'un membre. Le tableau est de la forme
   * $r[0-n]["id"] = numéros du jeu
   * $r[0-n]["hostname"]= domaine concerné
   * $r[0-n]["folder"]= Répertoire destination (dans le dossier du membre)
   * 
   * @return array Tableau de résultat, ou FALSE si une erreur est survenue
   */
  function get_list_raw() {
    global $db,$err,$cuid;
    $err->log("stats","get_list_raw");
    $r=array();
    $db->query("SELECT id, hostname, folder FROM stats2 WHERE mid='$cuid' ORDER BY hostname;");
    if ($db->num_rows()) {
      while ($db->next_record()) {
	// On passe /var/alternc/html/u/user
	preg_match("/^\/var\/alternc\/html\/.\/[^\/]*\/(.*)/", $db->f("folder"),$match);
	$r[]=array(
		   "id"=>$db->f("id"),
		   "hostname"=>$db->f("hostname"),
		   "folder"=>$match[1]
		   );
      }
      return $r;
    } else {
      $err->raise("stats",7);
      return false;
    }
  }

  /* ----------------------------------------------------------------- */
  /** 
   * retourne un tableau contenant les details d'un
   * jeu de statistiques apache brut géré par le membre.
   * $id est un id de jeu de stats. Retourne un tableau associatif sous la forme :
   * "id" = numéro du jeu
   * "hostname"= domaine concerné
   * "folder"= Répertoire destination (dans le dossier du membre)
   * @param string $id Numéro du jeu de stats brutes dont on veut les détails
   * @return array Tableau contenant les détails du jeu, ou FALSE en cas d'erreur
   */
  function get_stats_details_raw($id) {
    global $db,$err,$cuid;
    $err->log("stats","get_stats_details_raw",$id);
    $r=array();
    $db->query("SELECT id, hostname, folder FROM stats2 WHERE mid='$cuid' AND id='$id';");
    if ($db->num_rows()) {
      $db->next_record();
      // On passe /var/alternc/html/u/user
      preg_match("/^\/var\/alternc\/html\/.\/[^\/]*\/(.*)/", $db->f("folder"),$match);
      return array(
		   "id"=>$db->f("id"),
		   "hostname"=> $db->f("hostname"),
		   "folder"=>$match[1]
		   );
    } else {
      $err->raise("stats",8);
      return false;
    }
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Modifie un jeu de statistiques apache brutes existant
   * $id est le numéro du jeu de statistiques
   * $folder est un chemin relatif à "/var/alternc/html/u/user"
   * @param integer $id Numéro du jeu de stats brutes à modifier 
   * @param string $folder Dossier destination des stats
   * @return boolean TRUE si le jeu a été modifié, FALSE sinon.
   */
  function put_stats_details_raw($id,$folder) {
    global $db,$err,$bro,$mem,$cuid;
    $err->log("stats","put_stats_details_raw",$id);
    $db->query("SELECT count(*) AS cnt FROM stats2 WHERE id='$id' and mid='$cuid';");
    $db->next_record();
    if (!$db->f("cnt")) {
      $err->raise("stats",8);
      return false;
    }
    // TODO : replace with ,1 on convertabsolute call, and delete "/Var/alternc.../" at the query.
    $folder=$bro->convertabsolute($folder);
    if (substr($folder,0,1)=="/") {
      $folder=substr($folder,1);
    }
    $lo=$mem->user["login"];
    $l=substr($lo,0,1);
    $db->query("UPDATE stats2 SET folder='/var/alternc/html/$l/$lo/$folder', mid='$cuid' WHERE id='$id';");
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Efface un jeu de statistiques apache brut existant
   * @param integer $id est un id de jeu de statistiques
   * @return boolean TRUE si le jeu a été effacé, FALSE sinon.
   */
  function delete_stats_raw($id) {
    global $db,$err,$quota,$cuid;
    $err->log("stats","delete_stats_raw",$id);
    $db->query("SELECT hostname FROM stats2 WHERE id='$id' and mid='$cuid';");
    if (!$db->num_rows()) {
      $err->raise("stats",8);
      return false;
    }
    $db->next_record();
    $db->query("DELETE FROM stats2 WHERE id='$id'");
    $quota->dec("stats");
    return true;
  }

  /* ----------------------------------------------------------------- */
  /** 
   * Crée un nouveau jeu de statistiques brutes apache.
   * @param string $hostname est le domaine concerné
   * @param string $dir est le chemin d'accès racine des stats dans le compte
   * @return boolean TRUE si le jeu a été créé, FALSE si un erreur est survenue
   */
  function add_stats_raw($hostname,$dir) {
    global $db,$err,$quota,$bro,$mem,$cuid;
    $err->log("stats","add_stats_raw",$hostname);
    // TODO : utiliser le second param de convertabsolute pour simplification.
    $dir=$bro->convertabsolute($dir);
    if (substr($dir,0,1)=="/") {
      $dir=substr($dir,1);
    }
    $lo=$mem->user["login"];
    $l=substr($lo,0,1);
    if ($quota->cancreate("stats")) {
      $quota->inc("stats");
      $db->query("INSERT INTO stats2 (hostname,folder,mid) VALUES ('$hostname','/var/alternc/html/$l/$lo/$dir','$cuid')");
      return true;
    } else {
      $err->raise("stats",5);
      return false;
    }
  }

  /* ----------------------------------------------------------------- */
  /** Effacement du fichier de conf webalizer du domaine $hostname
   * @access private
   */
  function _delconf($hostname) {
    @unlink($this->CONFDIR."/".$hostname.".conf");
  }

  /* ----------------------------------------------------------------- */
  /** Création du fichier de configuration Webalizer du domaine $id
   * @access private
   */
  function _createconf($id,$nochk=0) {
    global $db,$err,$cuid;
    $s=implode("",file($this->TEMPLATEFILE));
    if ($nochk) {
        $db->query("SELECT * FROM stats WHERE id='$id';");
    } else { 
        $db->query("SELECT * FROM stats WHERE id='$id' AND uid='$cuid';");
    }
    if (!$db->num_rows()) {
      $err->raise("stats",2);
      return false;
    }
    $db->next_record();
    $s=str_replace("%OUTPUTDIR%",$db->f("dir"),$s);
    $s=str_replace("%HOSTNAME%",$db->f("hostname"),$s);
    $f=fopen($this->CONFDIR."/".$db->f("hostname").".conf","wb");
    fputs($f,$s,strlen($s));
    fclose($f);
  }

} /* CLASSE m_stats */

?>