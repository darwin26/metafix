<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo4.3
 * @version 0.2.0
 */

ini_set('display_errors', 0);

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself             = 'metafix';
$myroot             = $REX['INCLUDE_PATH'].'/addons/metainfo/plugins/'.$myself.'/';
$subpage            = rex_request('subpage', 'string');
$func               = rex_request('func', 'string');


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
  if($MF->insert_field(rex_request('prefix', 'string'),rex_request('name', 'string'))===true)
  {
    $MF = new metafix;
  }
}

if($func=='delete')
{
  if($MF->delete_field(rex_request('prefix', 'string'),rex_request('name', 'string'),rex_request('field_id', 'int'),rex_request('type', 'string'))===true)
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
|_{width:30px;}. id|_{width:80px;}. table|_{width:auto;}. name|_{width:50px;}. fix |_{width:50px;}. delete |
';

foreach ($MF->missing_fields as $prefix => $fields)
{
  $subpage = $prefix_to_subpage[$prefix];

  foreach ($fields as $key => $name)
  {
    $textile .= '|'.$MF->metainfo_ids[$name].
                '|'.$MF->types[$prefix].
                '|*'.$name.'*
                 |"re-insert":index.php?page=metainfo&subpage=metafix&func=insert&prefix='.$prefix.'&name='.$name.
                '|"(delete)delete":index.php?page=metainfo&subpage=metafix&func=delete&type=missing&prefix='.$prefix.'&name='.$name.'&field_id='.$MF->metainfo_ids[$name].
                '|'.PHP_EOL;
  }
}

$textile .= '
 </div><!-- /.rex-addon-output -->

 <div class="rex-addon-output">

h2(rex-hl2). Orphaned Fields %{color:gray;font-size:0.7em;}(present in table, unknown to Metainfo)%

table(rex-table).
|_{width:30px;}. id|_{width:80px;}. table|_{width:auto;}. name|_{width:50px;}. fix |_{width:50px;}. delete |
';

foreach ($MF->orphaned_fields as $prefix => $fields)
{
  $subpage = $prefix_to_subpage[$prefix];

  foreach ($fields as $key => $name)
  {
    $textile .= '| - '.
                '|'.$MF->types[$prefix].
                '|*'.$name.'*
                 |"re-assign":index.php?page=metainfo&subpage='.$subpage.'&func=add&reasign='.$name.
                '|"(delete)delete":index.php?page=metainfo&subpage=metafix&func=delete&type=orphan&prefix='.$prefix.'&name='.$name.
                '|'.PHP_EOL;
  }
}

$textile .= '
 </div><!-- /.rex-addon-output -->
';

echo rex_a79_textile($textile);

require $REX['INCLUDE_PATH'] . '/layout/bottom.php';

echo '<script>
jQuery("a.delete").click(function(){
    if(confirm("sure?")){
      return true;
    } else {
      return false;
    }
});
</script>
';
