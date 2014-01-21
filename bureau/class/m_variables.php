<?php

/*
 * $Id: variables.php,v 1.8 2005/04/02 00:26:36 anarcat Exp $
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
 */

/**
 * Persistent variable table
 *
 * @author Drupal Developpement Team
 * @link http://cvs.drupal.org/viewcvs/drupal/drupal/includes/bootstrap.inc?rev=1.38&view=auto
 */

class m_variables {
  var $strata_order = array('DEFAULT','GLOBAL','FQDN_CREATOR','FQDN','CREATOR','MEMBER','DOMAIN');

  // used by get_impersonated to merge array. Son value overwrite father's value
  private function variable_merge($father, $son) {
    if (! is_array($son)) return $father;
    foreach ($son as $k=>$v) {
      $father[$k] = $v;
    }
    return $father;
  }

  /**
   * Load the persistent variable table.
   *
   * The variable table is composed of values that have been saved in the table
   * with variable_set() as well as those explicitly specified in the configuration
   * file.
   */
  function variable_init() {
    global $cuid;
    if ($cuid > 1999) {
      $mid = $cuid;
    } else {
      $mid = null;
    }
    return $this->get_impersonated($_SERVER['HTTP_HOST'], $mid);
  }


  function get_impersonated($fqdn=null, $uid=null, $var=null) {
    global $db, $err;

    $arr_var=$this->variables_list();
  
    // Get some vars we are going to need.
    if ($fqdn != NULL) {
      $sub_infos=m_dom::get_sub_domain_id_and_member_by_name( strtolower($fqdn) );
    } else {
      $sub_infos=false;
    }

    if ( $uid != NULL ) {
      $creator=m_mem::get_creator_by_uid($uid);
    } else {
      $creator=false;
    }
   
    $variables = array();
    // Browse the array in the specific order of the strata
    foreach ( $this->strata_order as $strata) {
      if (! isset($arr_var[$strata]) || !is_array($arr_var[$strata])) continue;
      switch($strata) {
        case 'DEFAULT':
          $variables = $this->variable_merge(array(),$arr_var['DEFAULT'][NULL]);
          break;
        case 'GLOBAL':
          $variables = $this->variable_merge($variables, $arr_var['GLOBAL'][NULL]);
          break;
        case 'FQDN_CREATOR':
          if ( is_array($sub_infos) && isset($arr_var['FQDN_CREATOR'][$sub_infos['member_id']]) && is_array($arr_var['FQDN_CREATOR'][$sub_infos['member_id']])) {
            $variables = $this->variable_merge($variables, $arr_var['FQDN_CREATOR'][$sub_infos['member_id']]);
          }
          break;
        case 'FQDN':
          if ( is_array($sub_infos) && isset($arr_var['FQDN'][$sub_infos['sub_id']]) && is_array($arr_var['FQDN'][$sub_infos['sub_id']])) {
            $variables = $this->variable_merge($variables, $arr_var['FQDN'][$sub_infos['sub_id']]);
          }
          break;
        case 'CREATOR':
          if ( $creator && isset($arr_var['CREATOR'][$creator]) && is_array($arr_var['CREATOR'][$creator])) {
            $variables = $this->variable_merge($variables, $arr_var['CREATOR'][$creator] );
          }
          break;
        case 'MEMBER':
          if ( $uid && isset($arr_var['MEMBER'][$uid]) && is_array($arr_var['MEMBER'][$uid])) {
            $variables = $this->variable_merge($variables, $arr_var['MEMBER'][$uid] );
          }
          break;
        case 'DOMAIN':
          //FIXME TODO
          break;
      } //switch

    } //foreach

  #printvar($variables);die();
    if ($var && isset($variables[$var])) {
      return $variables[$var];
    } else {
       return $variables;
    }
  }

  /**
   * Initialize the global $conf array if necessary
   *
   * @global $conf the global conf array
   * @uses variable_init()
   */
  function variable_init_maybe($force=false) {
    global $conf;
    if ($force || !isset($conf)) {
      $conf = $this->variable_init();
    }
  }

  /**
   * Return a persistent variable.
   *
   * @param $name
   *   The name of the variable to return.
   * @param $default
   *   The default value to use if this variable has never been set.
   * @param $createit_comment 
   *   If variable doesn't exist, create it with the default value
   *   and createit_comment value as comment
   * @return
   *   The value of the variable.
   * @global $conf
   *   A cache of the configuration.
   */
  function variable_get($name, $default = null, $createit_comment = null) {
    global $conf;

    $this->variable_init_maybe();

    if (isset($conf[$name])) {
      return $conf[$name]['value'];
    } elseif (!is_null($createit_comment)) {
      $this->variable_update_or_create($name, $default, 'DEFAULT', 'null', 'null', $createit_comment);
    }
    return $default;
  }

  /**
   * Set a persistent variable.
   *
   * @param $name
   *   The name of the variable to set.
   * @param $value
   *   The value to set. This can be any PHP data type; these functions take care
   *   of serialization as necessary.
   */
  function variable_set($name, $value, $comment=null) {
    global $conf, $db, $err;
    $err->log('variable', 'variable_set', '+'.serialize($value).'+'.$comment.'+'); 

    $conf[$name] = $value;
    if (is_object($value) || is_array($value)) {
      $value = serialize($value);
    }

    if ( empty($comment) ) {
      $query = "INSERT INTO variable (name, value) values ('".$name."', '".$value."') on duplicate key update name='$name', value='$value';";
    } else {
      $comment=mysql_real_escape_string($comment);
      $query = "INSERT INTO variable (name, value, comment) values ('".$name."', '".$value."', '$comment') on duplicate key update name='$name', value='$value', comment='$comment';";
    }

  #  $db->query("$query");
    printvar($query);

    $this->variable_init();
  }

  function variable_update_or_create($var_name, $var_value, $strata=null, $strata_id=null, $var_id=null, $comment=null) {
    global $db, $err;
    $err->log('variable', 'variable_update_or_create');
    
    if ($var_id) {
      $sql="UPDATE variable SET value='".mysql_real_escape_string($var_value)."' WHERE id = ".intval($var_id);
    } else {
      if ( empty($strata) ) {
        $err->raise('variables', _("Err: Missing strata when creating var"));
        return false;
      }
      $sql="INSERT INTO 
              variable (name, value, strata, strata_id, comment) 
            VALUES (
              '".mysql_real_escape_string($var_name)."', 
              '".mysql_real_escape_string($var_value)."', 
              '".mysql_real_escape_string($strata)."', 
              '".mysql_real_escape_string($strata_id)."', 
              '".mysql_real_escape_string($comment)."' );";
    }

    $db->query("$sql");

    $this->variable_init_maybe(true);
    return true;
  }

  /**
   * Unset a persistent variable.
   *
   * @param $name
   *   The name of the variable to undefine.
   */
  function del($id) {
    global $db;
    $db->query("DELETE FROM `variable` WHERE id = '".intval($id)."'");
    $this->variable_init_maybe(true);
  }

  function variables_list_name() {
    global $db;

    $result = $db->query('SELECT name, comment FROM `variable` order by name');
    $t=array();
    while ($db->next_record($result)) {
      $tname = $db->f('name');
      // If not listed of if listed comment is shorter
      if ( ! isset( $t[$tname] ) || strlen($t[$tname]) < $db->f('comment') ) {
        $t[$db->f('name')] = $db->f('comment');
      }
    }
    return $t;
  }

  function variables_list() {
    global $db;

    $result = $db->query('SELECT * FROM `variable`');

    $arr_var=array();
    while ($db->next_record($result)) {
      // Unserialize value if needed
      if ( ($value = @unserialize($db->f('value'))) === FALSE) {
        $value=$db->f('value');
      }
      $arr_var[$db->f('strata')][$db->f('strata_id')][$db->f('name')] = array('id'=>$db->f('id') ,'name'=>$db->f('name'), 'value'=>$value, 'comment'=>$db->f('comment'));
    }
 
    return $arr_var;
  }

} /* Class m_variables */
?>
