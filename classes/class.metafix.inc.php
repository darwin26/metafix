<?php

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class metafix
{
  public $missing_fields;
  public $orphaned_fields;
  public $table_metas;
  public $metainfo_metas;
  public $types;


  function __construct()
  {
    $this->types = array(
      'art_' =>'rex_article',
      'cat_' =>'rex_article',
      'med_' =>'rex_file'
      );
    $this->table_metas     = self::get_fields('tables');
    $this->metainfo_metas  = self::get_fields('metainfo');
    $this->missing_fields  = self::get_missmatched('missing');
    $this->orphaned_fields = self::get_missmatched('orphaned');
  }

  /**
   * undocumented function
   *
   * @return void
   * @author
   **/
  private function get_fields($source=null)
  {
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
          foreach($db->getDbArray('SELECT `name` FROM `rex_62_params` WHERE `name` LIKE \''.str_replace('_','\_',$prefix).'%\';') as $column)
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
   * undocumented function
   *
   * @return void
   * @author
   **/
  private function get_missmatched($type=null)
  {
    $missmatched = array();
    foreach ($this->types as $prefix => $table)
    {
      switch ($type)
      {
        case 'missing':
          $missmatched[$prefix] = array_diff($this->metainfo_metas[$prefix],$this->table_metas[$prefix]);
          break;

        case 'orphaned':
          $missmatched[$prefix] = array_diff($this->table_metas[$prefix],$this->metainfo_metas[$prefix]);
          break;

        default:
          return false;
          break;
      }
    }
    return $missmatched;
  }

  /**
   * undocumented function
   *
   * @return void
   * @author
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

} // END class metafix
