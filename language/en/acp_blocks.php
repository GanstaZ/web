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
	'BLOCKS'  => ' blocks',
	'ENABLE'  => 'Enable ',
	'RIGHT'	  => 'Right side',
	'BOTTOM'  => 'Bottom',
	'SPECIAL' => 'Special',

	'DLS_NEWS'			=> 'news',
	'DLS_MINI_PROFILE'	=> 'mini profile',
	'DLS_INFORMATION'	=> 'information',
	'DLS_THE_TEAM'		=> 'the team',
	'DLS_TOP_POSTERS'	=> 'top posters',
	'DLS_RECENT_POSTS'	=> 'recent posts',
	'DLS_RECENT_TOPICS' => 'recent topics',
	'DLS_WHOS_ONLINE'	=> 'who is online',

	'BLOCK_POSITION'	 => 'Change position',
	'BLOCK_CATEGORY'	 => 'Change category',
	'DUPLICATE_POSITION' => 'Duplicate positions',
	'ADD_BLOCK'			 => 'Update available! Click submit to install: %s.',
	'PURGE_BLOCK'		 => 'Purge required! Click submit to remove: %s.',
]);
