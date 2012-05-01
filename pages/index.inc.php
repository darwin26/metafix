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
$modul_root         = $myroot.'modules/';
$subpage            = rex_request('subpage', 'string');
$func               = rex_request('func', 'string');

// OUTPUT
////////////////////////////////////////////////////////////////////////////////
require $REX['INCLUDE_PATH'] . '/layout/top.php';

rex_title('Metafix',$REX['ADDON']['metainfo']['SUBPAGES']);

echo '
<div class="rex-addon-output">
  <h2 class="rex-hl2" style="font-size: 1em;">Missing fields</h2>

  <div class="rex-addon-content">
    <div class="markitup">
      <h1>Standard Modul</h1>
    </div><!-- /.markitup -->
  </div><!-- /.rex-addon-content -->

</div><!-- /.rex-addon-output -->';

echo '
<div class="rex-addon-output">
  <h2 class="rex-hl2" style="font-size: 1em;">Orphaned fields</h2>

  <div class="rex-addon-content">
    <div class="markitup">
      <h1>Standard Modul</h1>
    </div><!-- /.markitup -->
  </div><!-- /.rex-addon-content -->

</div><!-- /.rex-addon-output -->';

require $REX['INCLUDE_PATH'] . '/layout/bottom.php';
