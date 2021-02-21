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
	'BLOCK'	   => ' Block name',
	'EXT_NAME' => ' Extension name',
	'SERVICE_NAME' => 'Service name',
	'NAME'		=> 'Name',
	'SERVICE'	=> 'Service',
	'SECTION'  => 'Section',
	'ADD_ERROR' => '%s Error%s',
	'CAT_ERROR' => ' (%s) does not exist',
	'VAR_EMPTY' => 'None',
	'EXT_ERROR' => ' (%s) is not enabled/available',
	'PRE_ERROR' => ' Prefix does not match',
	'SER_ERROR' => ' Incorrect service name',
	'NOT_AVAILABLE' => ' Service not available',

	'BLOCKS'   => ' block%s',
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

	'BLOCK_POSITION'	=> 'Position',
	'BLOCK_CATEGORY'	=> 'Change category',
	'DUPLICATE_POS'		=> 'Duplicate position',
	'ADD_BLOCK'			=> '%s New block%s available',
	'PURGE_BLOCK'		=> 'Purge required! Click submit to remove: %s.',
]);
