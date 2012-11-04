<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo 4.3.x/4.4.x
 * @version 0.3.2
 */


// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself   = 'metafix';
$myroot   = $REX['INCLUDE_PATH'].'/addons/metainfo/plugins/'.$myself.'/';
$subpage  = rex_request('subpage', 'string');
$func     = rex_request('func', 'string');
$prefix   = rex_request('prefix', 'string');
$name     = rex_request('name', 'string');
$field_id = rex_request('field_id', 'int');
$type     = rex_request('type', 'string');


// INCLUDES
////////////////////////////////////////////////////////////////////////////////
require_once $myroot.'/classes/class.metafix.inc.php';


// INIT
////////////////////////////////////////////////////////////////////////////////
$MF = new metafix;                                                              #FB::log($MF,'$MF');

$prefix_to_subpage = array(
  'art_' => '',
  'cat_' => 'categories',
  'med_' => 'media',
  );


// PAGE HEAD
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/layout/top.php';

rex_title('Metafix',$REX['ADDON']['metainfo']['SUBPAGES']);


// ACTIONS
////////////////////////////////////////////////////////////////////////////////
if($func=='insert')
{
  if($MF->insert_field($prefix,$name)===true)
  {
    $MF = new metafix;
  }
}

if($func=='delete')
{
  if($MF->delete_field($prefix,$name,$field_id,$type)===true)
  {
    $MF = new metafix;
  }
}

if($func=='reasign')
{
  $last_insert_id = $MF->reasign_field($prefix,$name);
  if($last_insert_id > 0 && $last_insert_id !== false)
  {
    header('Location: index.php?page=metainfo&subpage='.$prefix_to_subpage[$prefix].'&func=edit&field_id='.$last_insert_id.'&reasign='.str_replace($prefix,'',$name));
  }
}

if($func=='rebuild_type')
{
  if($MF->rebuild_type($name)!==false)
  {
    $MF = new metafix;
  }
}


// PAGE BODY
////////////////////////////////////////////////////////////////////////////////
$textile = '
 <div class="rex-addon-output">

h2(rex-hl2). Missing Fields %{color:gray;font-size:0.7em}(registered in Metainfo, missing in table)%

table(rex-table).
|_{width:30px;}. id|_{width:100px;}. missing in table|_{width:auto;}. name|_{width:50px;}. fix |_{width:50px;}. delete |
';

foreach ($MF->missing_fields as $prefix => $fields)
{
  $subpage = $prefix_to_subpage[$prefix];

  foreach ($fields as $key => $name)
  {
    $textile .= ' | '.$MF->metainfo_ids[$name].
                ' | '.$MF->types[$prefix].
                ' | *'.$name.'*
                  | "re-insert":index.php?page=metainfo&subpage=metafix&func=insert&prefix='.$prefix.'&name='.$name.
                ' | "(delete)delete":index.php?page=metainfo&subpage=metafix&func=delete&type=missing&prefix='.$prefix.'&name='.$name.'&field_id='.$MF->metainfo_ids[$name].
                ' | '.PHP_EOL;
  }
}

$textile .= '
 </div><!-- /.rex-addon-output -->

 <div class="rex-addon-output">

h2(rex-hl2). Orphaned Fields %{color:gray;font-size:0.7em;}(found in table, unknown to Metainfo)%

table(rex-table).
|_{width:30px;}. id|_{width:100px;}. found in table|_{width:auto;}. name|_{width:50px;}. fix |_{width:50px;}. delete |
';

foreach ($MF->orphaned_fields as $prefix => $fields)
{
  $subpage = $prefix_to_subpage[$prefix];

  foreach ($fields as $key => $name)
  {
    $textile .= ' |  - '.
                ' | '.$MF->types[$prefix].
                ' | *'.$name.'*
                  | "re-assign":index.php?page=metainfo&subpage=metafix&func=reasign&prefix='.$prefix.'&name='.$name.
                ' | "(delete)delete":index.php?page=metainfo&subpage=metafix&func=delete&type=orphaned&prefix='.$prefix.'&name='.$name.
                ' | '.PHP_EOL;
  }
}

$textile .= '
 </div><!-- /.rex-addon-output -->

 <div class="rex-addon-output">

h2(rex-hl2). Missing Types %{color:gray;font-size:0.7em;}(missing in rex_a62_types)%

table(rex-table).
|_{width:30px;}. id|_{width:auto;}. label|_{width:50px;}. fix |_{width:50px;}. delete |
';

foreach ($MF->missing_types as $label => $def)
{
  $subpage = $prefix_to_subpage[$prefix];

    $textile .= ' |  - '.
                ' | *'.$label.'*
                  | "rebuild":index.php?page=metainfo&subpage=metafix&func=rebuild_type&name='.$label.
                ' | '.
                ' | '.PHP_EOL;
}

$textile .= '
 </div><!-- /.rex-addon-output -->

notextile. <script>
  jQuery("a.delete").click(function(){
      if(confirm("sure?")){
        return true;
      } else {
        return false;
      }
  });
</script>
';

echo rex_a79_textile($textile);

require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
