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
	'DLS_VERSION'	  => 'Current version',
	'NEWS_ID'		  => 'News id',
	'SHOW_PAGINATION' => 'Show pagination',
	'LIMIT'			  => 'News limit',
	'USER_LIMIT'	  => 'User limit',
	'LIMIT_EXPLAIN'	  => 'Default 5 & Max 10',
	'TITLE_LENGTH'			 => 'Trim title',
	'TITLE_LENGTH_EXPLAIN'	 => 'Default 26 & Max 50',
	'CONTENT_LENGTH'		 => 'Trim text',
	'CONTENT_LENGTH_EXPLAIN' => 'Default 150 & Max 250',
]);
