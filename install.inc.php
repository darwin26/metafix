<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo4.3
 * @version svn:$Id: install.inc.php 38 2012-04-23 16:08:31Z jeffe $:
 */

$myself = 'metafix';


// XFORM PLUING CHEKCKS
////////////////////////////////////////////////////////////////////////////////
if(!OOPlugin::isActivated('xform','manager'))
{
  $REX['ADDON']['installmsg'][$myself] = 'Erst XFORM Tablemanager Plugin installieren & aktivieren!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}

if(!OOPlugin::isActivated('xform','geo'))
{
  $REX['ADDON']['installmsg'][$myself] = 'Erst XFORM Geo Plugin installieren & aktivieren!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


// DEV RESET QUERIES
////////////////////////////////////////////////////////////////////////////////
#$db = new rex_sql;
#switch(rex_request('nuke','string'))
#{
#  case'all':
#    $db->setQuery('DROP TABLE `rex_rexcal_categories`, `rex_rexcal_events`, `rex_rexcal_venues`;');
#    $db->setQuery('TRUNCATE TABLE `rex_xform_field`;');
#    $db->setQuery('TRUNCATE TABLE `rex_xform_table`;');
#    $db->setQuery('TRUNCATE TABLE `rex_xform_relation`;');
#  break;
#  case'metafix':
#    $db->setQuery('DROP TABLE `rex_rexcal_categories`, `rex_rexcal_events`, `rex_rexcal_venues`;');
#  break;
#  case'xform':
#    $db->setQuery('TRUNCATE TABLE `rex_xform_field`;');
#    $db->setQuery('TRUNCATE TABLE `rex_xform_table`;');
#    $db->setQuery('TRUNCATE TABLE `rex_xform_relation`;');
#  break;
#  default:
#    //
#}


$REX['ADDON']['install'][$myself] = 1;