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
	'BLOCKS'   => ' block%s',
	'ENABLE'   => 'Enable ',
	'CHECK_TO' => 'Check to install ',
	'RIGHT'	   => 'Right side',
	'BOTTOM'   => 'Bottom',
	'SPECIAL'  => 'Special',

	'DLS_NEWS'			=> 'News',
	'DLS_MINI_PROFILE'	=> 'Mini profile',
	'DLS_INFORMATION'	=> 'Information',
	'DLS_THE_TEAM'		=> 'The team',
	'DLS_TOP_POSTERS'	=> 'Top posters',
	'DLS_RECENT_POSTS'	=> 'Recent posts',
	'DLS_RECENT_TOPICS' => 'Recent topics',
	'DLS_WHOS_ONLINE'	=> 'Who is online',

	'BLOCK_POSITION'	 => 'Change position',
	'BLOCK_CATEGORY'	 => 'Change category',
	'DUPLICATE_POSITION' => 'Duplicate positions',
	'ADD_BLOCK'			 => '%s New block%s available',
	'PURGE_BLOCK'		 => 'Purge required! Click submit to remove: %s.',
]);
