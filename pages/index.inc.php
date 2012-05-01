<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo4.3
 * @version svn:$Id: index.inc.php 27 2012-03-19 19:33:17Z jeffe $:
 */

// GET PARAMS
////////////////////////////////////////////////////////////////////////////////
$myself             = 'metafix';
$myroot             = $REX['INCLUDE_PATH'].'/addons/metainfo/plugins/'.$myself.'/';
$subpage            = rex_request('subpage', 'string');
$func               = rex_request('func', 'string');

// INCLUDES
////////////////////////////////////////////////////////////////////////////////
require_once $myroot.'/classes/class.metafix.inc.php';

// OUTPUT
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/layout/top.php';

rex_title('Metafix',$REX['ADDON']['metainfo']['SUBPAGES']);

$MF = new metafix;

// REINSERT
////////////////////////////////////////////////////////////////////////////////
if($func=='insert')
{
  if($MF->insert_field(rex_request('prefix', 'string'),rex_request('name', 'string'))===true)
  {
    $MF = new metafix;
  }
}

echo '
<div class="rex-addon-output">
  <h2 class="rex-hl2">Missing Fields <span style="color:gray;font-size:0.7em;">(registered in Metainfo, missing in table)</span></h2>

  <div class="rex-addon-content">
    <div class="markitup">
';

$textile = '';
foreach ($MF->missing_fields as $prefix => $fields)
{
  $textile .= PHP_EOL.'h2. '.$prefix.PHP_EOL.PHP_EOL;
  foreach ($fields as $key => $name)
  {
    $textile .= '* '.$name.' [ "re-insert":index.php?page=metainfo&subpage=metafix&func=insert&prefix='.$prefix.'&name='.$name.' ]'.PHP_EOL;
  }
}
echo rex_a79_textile($textile);

echo '
    </div><!-- /.markitup -->
  </div><!-- /.rex-addon-content -->

</div><!-- /.rex-addon-output -->';

echo '
<div class="rex-addon-output">
  <h2 class="rex-hl2">Orphaned Fields <span style="color:gray;font-size:0.7em;">(present in table, unknown to Metainfo)</span></h2>

  <div class="rex-addon-content">
    <div class="markitup">
';

$textile = '';
foreach ($MF->orphaned_fields as $prefix => $fields)
{
  $subpage = '';
  $subpage = $prefix=='cat_' ? 'categories' : $subpage;
  $subpage = $prefix=='med_' ? 'media' : $subpage;

  $textile .= PHP_EOL.'h2. '.$prefix.PHP_EOL.PHP_EOL;
  foreach ($fields as $key => $name)
  {
    $textile .= '* '.$name.' [ "re-assign":index.php?page=metainfo&subpage='.$subpage.'&func=add&reasign='.$name.' ]'.PHP_EOL;
  }
}
echo rex_a79_textile($textile); // index.php?page=metainfo&subpage=&func=add

echo '
    </div><!-- /.markitup -->
  </div><!-- /.rex-addon-content -->

</div><!-- /.rex-addon-output -->';

require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
