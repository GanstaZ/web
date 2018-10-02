<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	// Web module
	'ACP_DLS_WEB_TITLE' => 'DLS Web CMS',
	'ACP_DLS_WEB' => 'DLS Web settings',

	// Blocks module
	'ACP_DLS_BLOCKS_TITLE' => 'DLS Blocks module',
	'ACP_DLS_BLOCKS' => 'DLS blocks manager',

	'ACP_DLS_SETTINGS_SAVED' => 'Settings have been saved successfully!',
]);
