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
	'DLS_VERSION'			 => 'Current version',
	'NEWS_ID'				 => 'News id',
	'DLS_PAGINATION'		 => 'Pagination',
	'LIMIT'					 => 'News limit',
	'USER_LIMIT'			 => 'User limit',
	'LIMIT_EXPLAIN'			 => 'Default 5 & Max 10',
	'TITLE_LENGTH'			 => 'Trim title',
	'TITLE_LENGTH_EXPLAIN'	 => 'Default 26 & Max 50',
	'CONTENT_LENGTH'		 => 'Trim text',
	'CONTENT_LENGTH_EXPLAIN' => 'Default 150 & Max 250',
	'DLS_SECTIONS'			 => 'Global blocks settings',
	'DLS_BLOCKS'			 => 'Blocks',
	'DLS_BLOCKS_EXPLAIN'	 => 'Will hide or show blocks, where blocks are autoloaded by page settings.<br>Manual loading of blocks is not affected.',
	'DLS_SPECIAL'			 => 'Special',
	'DLS_RIGHT'				 => 'Right',
	'DLS_LEFT'				 => 'Left',
	'DLS_MIDDLE'			 => 'Middle',
	'DLS_TOP'				 => 'Top',
	'DLS_BOTTOM'			 => 'Bottom',
]);
