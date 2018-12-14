<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dls.org/
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
	'SIDE'	 => 'Side blocks',
	'BOTTOM'	 => 'Bottom blocks',
	'DEFAULT_BLOCKS' => 'Default blocks',
	'UPDATE_BLOCKS'	 => 'New block(s) available: %s. Click submit to install.',
	'REMOVE_BLOCKS'	 => 'Block(s) unavailable: %s. Click submit to uninstall.',

	'DLS_MINI_PROFILE'	=> 'Enable mini profile',
	'DLS_INFORMATION'	=> 'Enable information',
	'DLS_THE_TEAM'		=> 'Enable the team',
	'DLS_TOP_POSTERS'	=> 'Enable top posters',
	'DLS_RECENT_POSTS'	=> 'Enable recent posts',
	'DLS_RECENT_TOPICS' => 'Enable recent topics',
	'DLS_WHOS_ONLINE'	=> 'Enable who is online',

	'BLOCKS_POSITION'	 => 'Set block position',
	'DUPLICATE_POSITION' => 'duplicate entry',
]);
