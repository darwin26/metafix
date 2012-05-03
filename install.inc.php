<?php
/**
 * Metafix - Metainfo Plugin
 *
 * @author http://rexdev.de
 *
 * @package redaxo4.3
 * @version 0.2.0
 */

$myself = 'metafix';


// CHECKS
////////////////////////////////////////////////////////////////////////////////
if(!OOAddon::isActivated('textile'))
{
  $REX['ADDON']['installmsg'][$myself] = 'Textile Addon required!';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}


$REX['ADDON']['install'][$myself] = 1;
