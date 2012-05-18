<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo 5
 * @version 5.0.0
 */

$myself = 'metafix';
$myroot = $this->getBasePath();

$page = new rex_be_page('Metafix', array('page' => 'metainfo', 'subpage' => 'metafix'));
$page->setHref('index.php?page=metainfo&subpage=metafix');
$page->setPath($this->getBasePath('pages/index.inc.php'));
$this->setProperty('page', $page);#

// MAIN
////////////////////////////////////////////////////////////////////////////////
rex_extension::register('ADDONS_INCLUDED', 'metafix_init');
function metafix_init($params)
{
  // INJECT REASIGN JS
  //////////////////////////////////////////////////////////////////////////////
  $page    = rex_request('page'   , 'string');
  $func    = rex_request('func'   , 'string');
  $reasign = rex_request('reasign', 'string','none');

  if($page==='metainfo' && $func==='edit' && $reasign!=='none')
  {
    rex_extension::register('OUTPUT_FILTER','metafix_opf');
    function metafix_opf($params)
    {
      $js = '
<script>
  jQuery("#'.rex::getTablePrefix().'metainfo_params_Feld_bearbeiten_erstellen_name").val("'.rex_request('reasign', 'string').'");
  jQuery("#'.rex::getTablePrefix().'metainfo_params_Feld_bearbeiten_erstellen_name").attr("readonly", true);
  jQuery("#'.rex::getTablePrefix().'metainfo_params_Feld_bearbeiten_erstellen_name").css({
    borderColor:"silver",
    color:"gray"
    });
</script>
';
      return $params['subject'].$js;
    }
  }

}
