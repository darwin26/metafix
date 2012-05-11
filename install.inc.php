<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo4.3
 * @version 0.3.1
 */

$myself = 'metafix';

// CHECK INSTALL AS PLUGIN
////////////////////////////////////////////////////////////////////////////////
if(!isset($ADDONSsic) || !isset($ADDONSsic['plugins']['metainfo']['install']['metafix']))
{
  $REX['ADDON']['installmsg'][$myself] .= 'Metafix is not an Addon - it\'s a Metainfo Plugin!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}

// REQUIRE TEXTILE
////////////////////////////////////////////////////////////////////////////////
if(!isset($ADDONSsic['version']['textile']))
{
  $REX['ADDON']['installmsg'][$myself] = 'Textile Addon required!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


$REX['ADDON']['install'][$myself] = 1;
