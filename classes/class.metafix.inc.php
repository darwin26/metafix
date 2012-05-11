<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo4.3
 * @version 0.3.1
 */

/**
 * Handle mismatched Metainfo fields
 **/
class metafix
{
  public $missing_fields;
  public $orphaned_fields;
  public $table_fields;
  public $metainfo_fields;
  public $metainfo_ids;
  public $types;


  function __construct()
  {
    global $REX;

    $this->types = array(
      'art_' =>$REX['TABLE_PREFIX'].'article',
      'cat_' =>$REX['TABLE_PREFIX'].'article',
      'med_' =>$REX['TABLE_PREFIX'].'file'
      );

    $this->metainfo_ids    = self::get_metainfo_ids();
    $this->table_fields    = self::get_fields('tables');
    $this->metainfo_fields = self::get_fields('metainfo');
    $this->missing_fields  = self::get_mismatched('missing');
    $this->orphaned_fields = self::get_mismatched('orphaned');
  }

  /**
   * Get fields registered in Metainfo or present in tables
   *
   * @param string [$source] (tables|metainfo)
   * @return array containing all fields in $source
   **/
  private function get_fields($source=null)
  {
    global $REX;
    $metas = array();
    $db = new rex_sql;

    switch($source)
    {
      case 'tables':
        foreach ($this->types as $prefix => $table)
        {
          foreach($db->getDbArray('SHOW COLUMNS FROM `'.$table.'` LIKE \''.str_replace('_','\_',$prefix).'%\';') as $column)
          {
            $metas[$prefix][] = $column['Field'];
          }
        }
        break;

      case 'metainfo':
        foreach ($this->types as $prefix => $table)
        {
          foreach($db->getDbArray('SELECT `field_id`,`name` FROM `'.$REX['TABLE_PREFIX'].'62_params` WHERE `name` LIKE \''.str_replace('_','\_',$prefix).'%\';') as $column)
          {
            $metas[$prefix][] = $column['name'];
          }
        }
        break;

      default:
        return false;
        break;
    }

    foreach ($this->types as $prefix => $table)
    {
      $metas[$prefix] = !isset($metas[$prefix]) ? array() : $metas[$prefix];
      sort($metas[$prefix]);
    }
    ksort($metas);

    return $metas;
  }

  /**
   * Return all Metainfo fields with their DB id
   *
   * @return array (name=>field_id,...)
   **/
  function get_metainfo_ids()
  {
    global $REX;
    $metas = array();
    $db = new rex_sql;
    foreach($db->getDBArray('SELECT `field_id`,`name` FROM `'.$REX['TABLE_PREFIX'].'62_params`;') as $column)
    {
      $metas[$column['name']] = $column['field_id'];
    }
    return $metas;
  }

  /**
   * Get mismatched Metainfo fields
   *
   * @param string [$type] (missing|orphaned)
   * @return array containing mismatched fields sorted by prefix
   **/
  private function get_mismatched($type=null)
  {
    $missmatched = array();
    foreach ($this->types as $prefix => $table)
    {
      switch ($type)
      {
        case 'missing':
          $missmatched[$prefix] = array_diff($this->metainfo_fields[$prefix],$this->table_fields[$prefix]);
          break;

        case 'orphaned':
          $missmatched[$prefix] = array_diff($this->table_fields[$prefix],$this->metainfo_fields[$prefix]);
          break;

        default:
          return false;
          break;
      }
    }
    return $missmatched;
  }

  /**
   * Insert missing fields into their tables
   *
   * @param string [$prefix] (art_|cat_|med_)
   * @param string [$name] field name
   * @return bool
   **/
  public function insert_field($prefix=null,$name=null)
  {
    if(!$prefix && !$name)
      return false;

    if(in_array($name,$this->missing_fields[$prefix]))
    {
      $db = new rex_sql;
      if($db->setQuery('ALTER TABLE `'.$this->types[$prefix].'` ADD `'.$name.'` TEXT NOT NULL;'))
      {
        echo rex_info('Metainfo Field '.$name.' re-inserted.');
        return true;
      }
    }

    return false;
  }

  /**
   * Delete mismatched field from table or Metainfo
   *
   * @param string [$prefix] (art_|cat_|med_)
   * @param string [$name] field name
   * @param int [$field_id] field id in metainfo
   * @param string [$type] (missing|orphaned)
   * @return bool
   **/
  public function delete_field($prefix=null,$name=null,$field_id=null,$type=null)
  {
    if(!$prefix || !$name || !$type) {
      return false;
    }

    global $REX;
    $db = new rex_sql;

    switch ($type)
    {
      case 'missing':
        if(in_array($name,$this->missing_fields[$prefix]))
        {
          if($db->setQuery('DELETE FROM `'.$REX['TABLE_PREFIX'].'62_params` WHERE `field_id`='.$field_id.' AND `name`=\''.$name.'\';'))
          {
            echo rex_info('Missing Field ['.$field_id.'] '.$name.' deleted.');
            return true;
          }
        }
        break;

      case 'orphaned':
        if(in_array($name,$this->orphaned_fields[$prefix]))
        {
          if($db->setQuery('ALTER TABLE `'.$this->types[$prefix].'` DROP `'.$name.'`;'))
          {
            echo rex_info('Orphaned Field '.$name.' deleted.');
            return true;
          }
        }
        break;

      default:
        return false;
    }

    return false;
  }

  /**
   * Inser field into Metainfo with generic params
   *
   * @param string [$prefix] (art_|cat_|med_)
   * @param string [$name] field name
   * @return mixed (false|last insert id)
   **/
  function reasign_field($prefix=null,$name=null)
  {
    if(!$prefix || !$name) {
      return false;
    }

    if(in_array($name,$this->orphaned_fields[$prefix]))
    {
      global $REX;
      $db = new rex_sql;
      $db->setQuery('INSERT INTO `'.$REX['TABLE_PREFIX'].'62_params` VALUES(\'\', \'\', \''.$name.'\', 1, \'\', 1, \'\', \'\', NULL, \'\', \'metafix\', \'\', \'metafix\', \'\');');
      return $db->getLastId();
    }

    return false;
  }

} // END class metafix
