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
	'LATEST_NEWS' => 'Viewing category',
	'ARTICLE' => 'Viewing article',
	'READ_FULL' => 'Read full article',
	'NEWS' => 'News',
	'VIEW_NEWS'		=> 'News id - %s',
	'VIEW_ARTICLE'	=> 'Article id - %s',

	'UNKNOWN' => 'Unknown',
	'year'	  => '%d year',
	'month'	  => '%d month',
	'week'	  => '%d week',
	'day'	  => '%d day',
	'hour'	  => '%d hour',
	'minute'  => '%d minute',
	'second'  => '%d second',
	'dls_ago' => [
		1 => '%2$s ago',
		2 => '%2$ss ago',
	],

	'DLS_PER_DAY' => '%s per day <strong>%s</strong>',

	'LEADERS'		=> 'Top posters',
	'RECENT_POSTS'	=> 'Recent posts',
	'RECENT_TOPICS' => 'Recent topics',

	'WELCOME' => 'Welcome back, ',
	'NEW_PM'  => ' new message',
	'NEW_PMS' => ' new messages',

	'PHPBB_VERSION'	 => 'phpBB version: ',
	'PORTAL_VERSION' => 'System version: ',
	'PORTAL_STYLE'	 => 'Default style: ',

	'IN_TOPIC'	=> 'In ',

	'DAYS_HERE' => 'Membership',
	'PROGRESS'	=> 'Progress',
	'LEVEL'		=> 'Level',

	'STATUSES'	=> [
		0 => 'Fresh As A Mint',
		1 => 'Self Made',
	],

	'STATUS' => 'Status: %s',
]);
