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
	'VERSION' => 'Current version',
	'NAME'	  => 'Core name',
	'NEWS_ID' => 'News id',
	'SHOW_PAGINATION' => 'Show pagination',
	'SHOW_NEWS'	   => 'Show news',
	'TITLE_LENGTH' => 'Trim title',
	'TITLE_LENGTH_EXPLAIN' => 'Default 26 & Max 50',
	'CONTENT_LENGTH' => 'Trim text',
	'CONTENT_LENGTH_EXPLAIN' => 'Default 150 & Max 250',
	'LIMIT' => 'News limit',
	'LIMIT_EXPLAIN' => 'Default 5 & Max 10',
	'USER_LIMIT'	=> 'User limit',
	'USER_LIMIT_EXPLAIN' => 'Default 5 & Max 20',
	'ACP_POINTS' => 'Points',
	'SET_EXP' => 'Set points for stage',
	'GET_MAX' => 'Max is 1000 points!',
]);
