<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
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
	'ACP_DLS_WEB_TITLE'		  => 'DLS Web CMS',
	'ACP_DLS_WEB'			  => 'DLS Web settings',

	// Blocks module
	'ACP_DLS_BLOCKS_TITLE'	  => 'DLS Blocks module',
	'ACP_DLS_BLOCKS'		  => 'DLS blocks manager',

	// Blocks page module
	'ACP_DLS_PAGE_TITLE'	  => 'DLS Page module',
	'ACP_DLS_PAGE'			  => 'DLS page settings',

	// Plugin module
	'ACP_DLS_PLUGIN_TITLE'	  => 'DLS Plugin module',
	'ACP_DLS_PLUGIN'		  => 'DLS plugin manager',

	'ACP_DLS_SETTINGS_SAVED'  => 'Settings have been saved successfully!',

	// News settings
	'ACP_NEWS_LEGEND'		  => 'News settings',
	'ACP_NEWS_ENABLE'		  => 'Enable forum',
	'ACP_NEWS_ENABLE_EXPLAIN' => 'Set <strong>Yes</strong> to enable this forum for news.',
]);
