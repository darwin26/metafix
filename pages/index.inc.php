<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo 5
 * @version 5.0.0
 */

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself   = 'metafix';
$myroot   = $this->getBasePath();
$subpage  = rex_request('subpage', 'string');
$func     = rex_request('func', 'string');
$prefix   = rex_request('prefix', 'string');
$name     = rex_request('name', 'string');
$field_id = rex_request('field_id', 'int');
$type     = rex_request('type', 'string');


// INIT
////////////////////////////////////////////////////////////////////////////////
$MF = new metafix;

$prefix_to_subpage = array(
  'art_' => '',
  'cat_' => 'categories',
  'med_' => 'media',
  );

echo rex_view::title('Metafix');


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


// PAGE BODY
////////////////////////////////////////////////////////////////////////////////
$textile = '
 <section class="rex-content rex-content-block">

h2(rex-hl2). Missing Fields %{color:silver;font-size:0.7em}(registered in Metainfo, missing in table)%

table(rex-table).
|_{width:30px;}. id|_{width:100px;}. missing in table|_{width:auto;}. name|_{width:50px;}. fix |_{width:50px;}. delete |
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
 </section><!-- /.rex-content.rex-content-block -->

 <section class="rex-content rex-content-block">

h2(rex-hl2). Orphaned Fields %{color:silver;font-size:0.7em;}(found in table, unknown to Metainfo)%

table(rex-table).
|_{width:30px;}. id|_{width:100px;}. found in table|_{width:auto;}. name|_{width:50px;}. fix |_{width:50px;}. delete |
';

foreach ($MF->orphaned_fields as $prefix => $fields)
{
  $subpage = $prefix_to_subpage[$prefix];

  foreach ($fields as $key => $name)
  {
    $textile .= '| - '.
                '|'.$MF->types[$prefix].
                '|*'.$name.'*
                 |"re-assign":index.php?page=metainfo&subpage=metafix&func=reasign&prefix='.$prefix.'&name='.$name.
                '|"(delete)delete":index.php?page=metainfo&subpage=metafix&func=delete&type=orphaned&prefix='.$prefix.'&name='.$name.
                '|'.PHP_EOL;
  }
}

$textile .= '
 </section><!-- /.rex-content.rex-content-block -->

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

echo rex_textile::parse($textile);
